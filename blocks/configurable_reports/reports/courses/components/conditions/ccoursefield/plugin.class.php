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

require_once($CFG->dirroot.'/blocks/configurable_reports/components/conditions/plugin.class.php');

class plugin_ccoursefield extends conditions_plugin{
		
	function summary($instance){
	    if(! ($data = $instance->configdata)){
	        return '';
	    }
		return get_string($data->field).' '.$data->operator.' '.$data->value;
	}
	
	function has_form(){
	    return true;
	}
	
	function execute($instance){
	    if(! ($data = $instance->configdata)){
	        return '';
	    }
		global $DB;
	
		list($select, $params) = $this->operator_sql($data);
		
		return $DB->get_fieldset_select('course', 'id', $select, $params);
	}
	
}

?>