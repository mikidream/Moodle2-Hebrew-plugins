<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/** Configurable Reports
  * A Moodle block for creating customizable reports
  * @package blocks
  * @author: Juan leyva <http://www.twitter.com/jleyvadelgado>
  * @date: 2009
  */ 

require_once($CFG->dirroot.'/blocks/configurable_reports/components/columns/plugin.class.php');

class plugin_userstats extends columns_plugin{

	function execute($instance, $row, $starttime=0, $endtime=0){
		global $DB, $COURSE;
					
		$filter_starttime = optional_param_array('filter_starttime', 0, PARAM_RAW);
		$filter_endtime = optional_param_array('filter_endtime', 0, PARAM_RAW);
		
		// Do not apply filters in timeline report (filters yet applied)
		if($starttime && $endtime){
			$filter_starttime = 0;
			$filter_endtime = 0;
		}
		
		if($filter_starttime and $filter_endtime){
			$filter_starttime = make_timestamp($filter_starttime['year'],$filter_starttime['month'],$filter_starttime['day']);
			$filter_endtime = make_timestamp($filter_endtime['year'],$filter_endtime['month'],$filter_endtime['day']);
		}
		
		$starttime = ($filter_starttime)? $filter_starttime : $starttime;
		$endtime = ($filter_endtime)? $filter_endtime : $endtime;
		
		if($data->stat == 'coursededicationtime'){
			$sql = "userid = ?";
			$params =  array($row->id);
			
			if($COURSE->id != 1){
				$sql .= " AND course = ?";
				$params = array_merge($params, array($COURSE->id));
			}
				
			if($starttime and $endtime){
				$starttime = usergetmidnight($starttime) + 24*60*60;
				$endtime = usergetmidnight($endtime) + 24*60*60;
				$sql .= " AND time >= ? AND time <= ?";
				$params = array_merge($params, array($starttime, $endtime));
			}	
									
			// Code from Course Dedication Block
			if ($logs = $DB->get_records_select("log", $sql, $params, "time ASC", "id,time")) {
				// This should be a config value in some where
				$limitinseconds = 30*60;
				$previouslog = array_shift($logs);
                $previouslogtime = $previouslog->time;
                $sessionstart = $previouslogtime;
                $totaldedication = 0;             

                foreach ($logs as $log) {
                    if (($log->time - $previouslogtime) > $limitinseconds) {                        
						$dedication = $previouslogtime - $sessionstart;
                        $totaldedication += $dedication;                        
                        $sessionstart = $log->time;
                    }
                    $previouslogtime = $log->time;
                }

                $dedication = $previouslogtime - $sessionstart;
                $totaldedication += $dedication;
                
				return format_time($totaldedication);
			}
			
			// Code from Course Dedication Block
			return 0;
		}		
		
		switch($data->stat){
			case 'activityview':
    			$total = 'statsreads';
    			$stattype = 'activity';
    			break;
			case 'activitypost':
				$total = 'statswrites';
				$stattype = 'activity';
				break;
			case 'logins':			
			default:
				$total = 'statsreads';
				$stattype = 'logins';
		}
		$sql = "SELECT SUM($total) as total FROM {stats_user_daily} WHERE stattype = ? AND userid = ?";
		$params = array($stattype, $row->id);
		
		if($starttime and $endtime){
			$starttime = usergetmidnight($starttime) + 24*60*60;
			$endtime = usergetmidnight($endtime) + 24*60*60;
			$sql .= " AND timeend >= $starttime AND timeend <= $endtime";
			$params = array_merge($params, array($starttime,$endtime));
		}
		
		if($res = $DB->get_records_sql($sql, $params)){				
			$res = array_shift($res);
			if ($res->total != NULL) {
				return $res->total;
			} else {
				return 0;
			}
		}		
		
		return '--';
	}	
}

?>