<?php  // $Id: exportjavame.php,v 1.17 2011/08/03 20:04:32 bdaloukas Exp $
/**
 * This page export the game to javame for mobile phones
 * 
 * @author  bdaloukas
 * @version $Id: exportjavame.php,v 1.17 2011/08/03 20:04:32 bdaloukas Exp $
 * @package game
 **/
    
    function game_OnExportJavaME( $game, $javame){
        global $CFG, $DB;
                
        $courseid = $game->course;
        $course = $DB->get_record( 'course', array( 'id' => $courseid));
        
        $destdir = game_export_createtempdir();
                
        if( $javame->type == 'hangmanp'){
            $destmobiledir = 'hangmanp';
        }else{
            $destmobiledir = 'hangman';
        }
        $src = $CFG->dirroot.'/mod/game/export/javame/'.$destmobiledir.'/simple';
        
        if( $javame->filename == ''){
            $javame->filename = 'moodle'.$destmobiledir;
        }
                
		$handle = opendir( $src);
		while (false!==($item = readdir($handle))) {
			if($item != '.' && $item != '..') {
				if(!is_dir($src.'/'.$item)) {
				    $itemdest = $item;
				    
				    if( substr( $item, -5) == '.java'){
				        continue;   //don't copy the java source code files
				    }
				    
				    if( substr( $itemdest, -8) == '-1.class'){
				        $itemdest = substr( $itemdest, 0, -8).'$1.class';
				    }
				
					copy( $src.'/'.$item, $destdir.'/'.$itemdest);
				}
			}
		}
		
		mkdir( $destdir.'/META-INF');
		
		game_exportjavame_exportdata( $src, $destmobiledir, $destdir, $game, $javame->maxpicturewidth, $javame->maxpictureheight);
		
		game_create_manifest_mf( $destdir.'/META-INF', $javame, $destmobiledir);
		
		$filejar = game_create_jar( $destdir, $course, $javame);
		if( $filejar == ''){
    		$filezip = game_create_zip( $destdir, $course->id, $javame->filename.'.zip');
        }else{
            $filezip = '';
        }

        if( $destdir != ''){
            remove_dir( $destdir);
        }
        
        if( $filezip != ''){
            echo "unzip the $filezip in a directory and when you are in this directory use the command <br><b>jar cvfm {$javame->filename}.jar META-INF/MANIFEST.MF<br></b> to produce the jar files<br><br>";
        }
        
        $file = ($filejar != '' ? $filejar : $filezip);
        $fullfile = "{$CFG->dataroot}/$courseid/export/$file";
        game_send_stored_file( $fullfile);
    }
    
    function game_exportjavame_exportdata( $src, $destmobiledir, $destdir, $game, $maxwidth, $maxheight){
        global $CFG;
        
		mkdir( $destdir.'/'.$destmobiledir);
                        
		$handle = opendir( $src);
		while (false!==($item = readdir($handle))) {
			if($item != '.' && $item != '..') {
				if(!is_dir($src.'/'.$item)) {
				    if( substr( $item, -4) == '.jpg'){
    					copy( $src.'/'.$item, $destdir."/$destmobiledir/".$item);
                    }
				}
			}
		}

        $lang = $game->language;
        if( $lang == '')
            $lang = current_language();
		copy( $src. '/lang/'.$lang.'/language.txt',  $destdir."/$destmobiledir/language.txt");

        $export_attachment = ( $destmobiledir == 'hangmanp');
		        
        $map = game_exmportjavame_getanswers( $game, $export_attachment);
        if( $map == false){
            error( 'No Questions');
        }
        
        if( $destmobiledir == 'hangmanp'){
            game_exportjavame_exportdata_hangmanp( $src, $destmobiledir, $destdir, $game, $map, $maxwidth, $maxheight);
            return;
        }
        
        $fp = fopen( $destdir."/$destmobiledir/hangman.txt","w");
            fputs( $fp, "1.txt=$destmobiledir\r\n");
        fclose( $fp);
        
        $fp = fopen( $destdir."/$destmobiledir/1.txt","w");
            foreach( $map as $line){
                $s = game_upper( $line->answer) . '=' . $line->question;
                fputs( $fp, "$s\r\n");
            }
        fclose( $fp);
    }
    
    function game_exportjavame_exportdata_hangmanp( $src, $destmobiledir, $destdir, $game, $map, $maxwidth, $maxheight)
    {
        global $CFG;
        
        $fp = fopen( $destdir."/$destmobiledir/$destmobiledir.txt","w");
            fputs( $fp, "01=01\r\n");
        fclose( $fp);
        
        $destdirphoto = $destdir.'/'.$destmobiledir.'/01';
        mkdir( $destdirphoto);
        
        $fp = fopen( $destdirphoto.'/photo.txt',"w");
            foreach( $map as $line){
                
                $file = $line->attachment;
                $pos = strrpos( $file, '.');
                if( $pos != false){
                    $file = $line->id.substr( $file, $pos);
                    $src = $CFG->dataroot.'/'.$game->course.'/moddata/'.$line->attachment;
                    game_export_javame_smartcopyimage( $src, $destdirphoto.'/'.$file, $maxwidth, $maxheight);
                    
                    $s = $file . '=' . game_upper( $line->answer);
                    fputs( $fp, "$s\r\n");
                }
            }
        fclose( $fp);
    }
    
    function game_exmportjavame_getanswers( $game, $context, $export_attachment, $dest, &$files){
        $map = array();
        $files = array();
        
        switch( $game->sourcemodule){
        case 'question':
            return game_exmportjavame_getanswers_question( $game, $context, $dest, $files);
        case 'glossary':
            return game_exmportjavame_getanswers_glossary( $game, $export_attachment);
        case 'quiz':
            return game_exmportjavame_getanswers_quiz( $game, $context, $dest, $files);
        }
        
        return false;
    }
    
    function game_exmportjavame_getanswers_question( $game, $context, $destdir, &$files){
        $select = 'hidden = 0 AND category='.$game->questioncategoryid;
    
        $select .= game_showanswers_appendselect( $game);
    
        return game_exmportjavame_getanswers_question_select( $game, $context, 'question', $select, '*', $game->course, $destdir, $files);
    }
    
    function game_exmportjavame_getanswers_quiz( $game, $context, $destdir, $files)
    {
        global $CFG;

	    $select = "quiz='$game->quizid' ".
			  " AND qqi.question=q.id".
			  " AND q.hidden=0".
              game_showanswers_appendselect( $game);
    	$table = "question q,{$CFG->prefix}quiz_question_instances qqi";
	
        return game_exmportjavame_getanswers_question_select( $game, $context, $table, $select, "q.*", $game->course, $destdir, $files);
    }
    
    function game_exmportjavame_getanswers_question_select( $game, $context, $table, $select, $fields, $courseid, $destdir, &$files)
    {
        global $CFG, $DB;
    echo "files1=$files<br>";
        $sql = "SELECT $fields FROM {$CFG->prefix}$table WHERE $select";
        if( ($questions = $DB->get_records_sql( $sql)) === false){
            return;
        }
        
        $line = 0;
        $map = array();
        
        foreach( $questions as $question){
            unset( $ret);
            $ret->qtype = $question->qtype;
            $ret->question = $question->questiontext;
            $ret->question = str_replace( array( '"', '#'), array( "'", ' '), 
    	                game_export_split_files( $game->course, $context, 'questiontext', $question->id, $ret->question, $destdir, $files));	            
        
            switch( $question->qtype){
            case 'shortanswer':
	            $rec = $DB->get_record( 'question_answers', array( 'question' => $question->id), 'id,answer,feedback');
	            $ret->answer = $rec->answer;
	            $ret->feedback = $rec->feedback;
	            $map[] = $ret;
                break;
            default:
                break;
            }
        }
        
        return $map;
    }
    
    function game_exmportjavame_getanswers_glossary( $game, $export_attachment)
    {
        global $CFG, $DB;
    
    	$table = '{glossary_entries} ge';
        $select = "glossaryid={$game->glossaryid}";
        if( $game->glossarycategoryid){
	    	$select .= " AND gec.entryid = ge.id ".
					   " AND gec.categoryid = {$game->glossarycategoryid}";
	    	$table .= ",{glossary_entries_categories} gec";		
    	}
    	
    	if( $export_attachment){
    	    $select .= " AND attachment <> ''";
    	}
 
        $fields = 'ge.id,definition,concept';
        if( $export_attachment){
            $fields .= ',attachment';
        }
        $sql = "SELECT $fields FROM $table WHERE $select ORDER BY definition";
        if( ($questions = $DB->get_records_sql( $sql)) === false){
            return false;
        }
    
        $map = array();
        foreach( $questions as $question){
            unset( $ret);
            $ret->id = $question->id;
            $ret->qtype = 'shortanswer';
            $ret->question = strip_tags( $question->definition);
            $ret->answer = $question->concept;
            $ret->feedback = '';
            
            if( $export_attachment){
                if( $question->attachment != ''){
                    $ret->attachment = "glossary/{$game->glossaryid}/$question->id/$question->attachment";
                }else{
                    $ret->attachment = '';
                }
            }
            
            $map[] = $ret;
        }
        
        return $map;
    }
            
    function game_create_manifest_mf( $dir, $javame, $destmobiledir){
                        
        $fp = fopen( $dir.'/MANIFEST.MF',"w");
        fputs( $fp, "Manifest-Version: 1.0\r\n");
        fputs( $fp, "Ant-Version: Apache Ant 1.7.0\r\n");
        fputs( $fp, "Created-By: {$javame->createdby}\r\n");
        fputs( $fp, "MIDlet-1: MoodleHangman,,$destmobiledir\r\n");
        fputs( $fp, "MIDlet-Vendor: {$javame->vendor}\r\n");
        fputs( $fp, "MIDlet-Name: {$javame->vendor}\r\n");
        fputs( $fp, "MIDlet-Description: {$javame->description}\r\n");
        fputs( $fp, "MIDlet-Version: {$javame->version}\r\n");
        fputs( $fp, "MicroEdition-Configuration: CLDC-1.0\r\n");
        fputs( $fp, "MicroEdition-Profile: MIDP-1.0\r\n");
        
        fclose( $fp);
    }
    
    function game_create_jar( $srcdir, $course, $javame){
        global $CFG;
        
        $dir = $CFG->dataroot . '/' . $course->id;
        $filejar = $dir . "/export/{$javame->filename}.jar";
        if (!file_exists( $dir)){
            mkdir( $dir);
        }

        if (!file_exists( $dir.'/export')){
            mkdir( $dir.'/export');
        }

        if (file_exists( $filejar)){
            unlink( $filejar);
        }
    
        $cmd = "cd $srcdir;jar cvfm $filejar META-INF/MANIFEST.MF *";
        exec( $cmd);

        return (file_exists( $filejar) ? "{$javame->filename}.jar" : '');
    }    
    

function game_showanswers_appendselect( $form)
{
    switch( $form->gamekind){
    case 'hangman':
    case 'cross':
    case 'crypto':
        return " AND qtype='shortanswer'";
    case 'millionaire':
        return " AND qtype = 'multichoice'";
    case 'sudoku':
    case 'bookquiz':
    case 'snakes':
        return " AND qtype in ('shortanswer', 'truefalse', 'multichoice')";
    }
    
    return '';
}
