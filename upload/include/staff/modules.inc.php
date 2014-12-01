<?php
/*********************************************************************
    modules.inc.php

    Update manager for osMods

	Andrew Rowland <osmod@msrltech.com>
	Edited: 2014/11/18
	Build: 1009 [Alpha]
**********************************************************************/
  define_tables(TABLE_PREFIX);
  
  function update_modules(){
	if(!dbchecker()) return false;

	/****************************
		Are we updating or
		installing?
	****************************/
	$module = $_POST['module'];
	$sys = getSystemExists($module);
	if(!$sys){
		/****************************
			Initialize
		****************************/
		if(!initialize($module)){if($debug>=2) logEntry('Failed to Initialize', 'Failed to initialize module $module.', 1); return false;}
	}elseif ($sys && strcmp('on', $_POST['upgrade']) == 0){
		/****************************
			Upgrade System
		****************************/
		if(!upgrade($module)){if($debug>=2) logEntry('Failed to Upgrade', 'Failed to upgrade module $module to'.getPackageBuild($module).'.', 1); return false;}
	}else{
		/****************************
			Update Settings
		****************************/
		if(!update($module, $_POST)){if($debug>=2) logEntry('Failed to Update', 'Failed to update settings for module $module.', 1); return false;}
	}
	return true;
  }

  function define_tables($prefix){
	define('MOD_STATUS',$prefix.'mod_status'); //obsolete in Build 1008
	define('MOD_STATUS_OBJECT',$prefix.'mod_status_object');
	define('MOD_STATUS_PROPERTY',$prefix.'mod_status_object_properties');
	define('MOD_STATUS_ASSIGNMENTS',$prefix.'mod_status_assignments'); //obsolete in Build 1008
	define('MOD_STATUS_ACTIONS',$prefix.'mod_status_actions');
	define('MOD_LIST',$prefix.'mod_list');
	define('MOD_LOG',$prefix.'mod_logs');
  }

  function getPackageBuild($package){
	$build = array();
	$build['pack'] = 1010;
	$build['status'] = 1010;
	$build['status_actions'] = 1000;
	$build['equipment'] = 1000;
	return $build[$package];
  }

  function getSystemBuild($package){
	global $cfg;
	return $cfg->get('mod_'.$package.'_sysbuild');
  }
  
  function getSystemExists($package){
	global $cfg;
	return $cfg->exists('mod_'.$package.'_sysbuild');
  }

  function getInstalledModules(){
	  
  }
  
  function logEntry($name, $data, $type){
	$sql = "INSERT INTO ".MOD_LOG." (logName, logDetail, logType) VALUES ('$name', '$data', $type)";
	$res = db_query($sql);  
  }
  
  function dbCreateTable($table, $fields){
        $sql = "SELECT COUNT(table_name) FROM information_schema.tables WHERE table_name = '$table' AND table_schema='".DBNAME."' LIMIT 1";
        if(!$res = db_query($sql)) return false;        //unable to query database schema table

        $count = db_result($res);
        if($count==0){
          $sql="CREATE TABLE $table (";
          for($i = 0; $i<count($fields); $i++){
                $sql.=$fields[$i];
                if($i<count($fields)-1) $sql.=", ";
          }
          $sql.=")";
          if(!$res = db_query($sql)) return false;      //table failed to create
          logEntry('Table Created', 'Table [$table] created in database successfully.', 3);
        }
        return true;
  }
  
  function dbDropTable($table){
	    $sql = "SELECT COUNT(table_name) FROM information_schema.tables WHERE table_name = '$table' AND table_schema='".DBNAME."' LIMIT 1";
        if(!$res = db_query($sql)) return false;        //unable to query database schema table

        $count = db_result($res);
        if($count==1){
          $sql="DROP TABLE $table";
          if(!$res = db_query($sql)) return false;      //table failed to drop
          logEntry('Table Dropped', 'Table [$table] dropped from database successfully.', 3);
        }
        return true;
  }
  
  function dbchecker(){
        global $cfg;
        /****************************
                Database Check
                Module Log Table
        ****************************/
        $tfields = array('id INT(64) NOT NULL AUTO_INCREMENT', 'PRIMARY KEY(id)', 'logName VARCHAR (64)', 'logDetail VARCHAR(255)', 'logType INT(3)');
        if(!dbCreateTable(MOD_LOG, $tfields)) return false;

        /****************************
                Database Check
                Module List Table
        ****************************/
        $tfields = array('id INT(64) NOT NULL AUTO_INCREMENT', 'PRIMARY KEY(id)', 'moduleName VARCHAR (64)', 'UNIQUE(moduleName)', 'modulePath VARCHAR(64)', 'icon VARCHAR(64)');
        if(!dbCreateTable(MOD_LIST, $tfields)) return false;

        if(!$cfg->updateAll(array(
                'mod_pack_version'=>'v1.1.06-alpha.1010',
                'mod_pack_sysbuild'=>'1010',
        ))){logEntry('Configuration Update Failed', 'Failed to update configuration in function dbchecker()', 1); return false;}
        return true;
  }

  function defaultShapes(){
	  $sql = "INSERT INTO ".MOD_STATUS_OBJECT." (objectName, tag) VALUES ('Default Oval', 6)";	//Set Default Oval to Read/Write but no Delete
	  if(!$res = db_query($sql)){logEntry('Query Failed', 'Failed to execute query: $sql', 3); return false;}
	  $objid = db_insert_id();
	  //Properties of Default Oval
	  $isql[0] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'height', '10px')";
	  $isql[1] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'width', '75px')";
	  $isql[2] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'border-width', '2px')";
	  $isql[3] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'border-style', 'solid')";
	  $isql[4] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'border-radius', '12px')";
	  $isql[5] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'font-size', '6pt')";
	  $isql[6] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'text-align', 'center')";
	  $isql[7] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'valign', 'middle')";

	  $count = count($isql);
	  for($i = 0; $i < $count; $i++){
		  if(!$res = db_query($isql[$i])){logEntry('Query Failed', 'Failed to execute query: '.$isql[$i], 3); return false;}
	  }
	  logEntry('Shapes Imported', 'Successfully imported shape and properties.', 1);
	  return true;
  }
  
  function initialize($module){
	global $cfg;
	/****************************
		New Install of Module
	****************************/
	switch($module){
	  case 'status':
		/****************************
			Modify Database
		****************************/
		$tfields = array('id INT(64) NOT NULL AUTO_INCREMENT', 'PRIMARY KEY(id)', 'objectName VARCHAR(64)', 'tag INT(16) NULL DEFAULT NULL');
		dbCreateTable(MOD_STATUS_OBJECT, $tfields);
		
		$tfields = array('id INT(64) NOT NULL AUTO_INCREMENT', 'PRIMARY KEY(id)', 'objectId INT(64)', 'propertyName VARCHAR(64)', 'valueCurrent VARCHAR(64)', 'valuePrevious VARCHAR(64) NULL DEFAULT NULL');
		dbCreateTable(MOD_STATUS_PROPERTY, $tfields);

		if(!build1008()){logEntry('Function Returned False', 'Function build1008() returned false.  Function initialize($module) returning false.', 2); return false;}
		if(!$cfg->updateAll(array(
			'mod_status_version'=>'v1.1.06-alpha.1010',
			'mod_status_sysbuild'=>'1010',
		))){logEntry('Configuration Update Failed', 'Failed to update configuration in function initialize($module)', 1); return false;}
		logEntry('Module Initialized - $module', 'Module $module successfully initialized at build '.getPackageBuild($module), 1);
		break;
		/*
		##	End Module: Status
		*/
	}
	return true;
  }

  function upgrade($module){
	global $cfg;
	/****************************
		Upgrade Module
	****************************/
	$pbuild = getPackageBuild($module);
	$sbuild = getSystemBuild($module);
	$errors = false;

	switch($module){
	  case 'status':
		switch($sbuild){
			default:
			case '1004':
				$tfields = array('id INT(64) NOT NULL AUTO_INCREMENT', 'PRIMARY KEY(id)', 'objectId INT(64)', 'propertyName VARCHAR(64)', 'valueCurrent VARCHAR(64)', 'valuePrevious VARCHAR(64) NULL DEFAULT NULL');
				dbCreateTable(MOD_STATUS_PROPERTY, $tfields);
				if(!$cfg->updateAll(array(
					'mod_status_version'=>'v1.9.3-1005 (alpha)',
					'mod_status_sysbuild'=>'1005',
				))){logEntry('Configuration Update Failed', 'Failed to update configuration in function upgrade($module) for Case 1004', 1);; return false;}
			case '1005':
				if(!$cfg->updateAll(array(
					'mod_status_version'=>'v1.9.3-1006 (alpha)',
					'mod_status_sysbuild'=>'1006',
				))){logEntry('Configuration Update Failed', 'Failed to update configuration in function upgrade($module) Case 1005', 1); return false;}
			case '1006':
				$sql = "ALTER TABLE `ost_mod_status_object` ADD `tag` INT( 16 ) NULL DEFAULT NULL";
				if(!$res = db_query($sql)){logEntry('Query Failed', 'Failed to execute query: $sql', 3); return false;}
				$sql = "ALTER TABLE `ost_mod_status_object_properties` ADD `valuePrevious` INT( 16 ) NULL DEFAULT NULL";
				if(!$res = db_query($sql)){logEntry('Query Failed', 'Failed to execute query: $sql', 3); return false;}

				if(!build1008()){logEntry('Function Returned False', 'Function build1008() returned false.  Function upgrade($module) returning false.', 2);  return false;}
				/****************************
					Database Upgraded -
					Now for the changes
				****************************/
				$color['bg'] = $cfg->get('mod_status_bgcolor');
				$color['fg'] = $cfg->get('mod_status_fgcolor');
				$color['bd'] = $cfg->get('mod_status_bdcolor');
				$oldStat = array();

				$sql = "SELECT id, statusName, colorBG, colorFG, colorBD FROM ".MOD_STATUS;
				if(!$res = db_query($sql)){logEntry('Query Failed', 'Failed to execute query: $sql', 3); return false;}
				while(list($id, $name, $bg, $fg, $bd) = db_fetch_row($res)){
					$isql = "INSERT INTO ".TICKET_STATUS_TABLE." (name, state, mode, flags, sort, properties, created, updated) VALUES ('".$name."', 'open', 1, 0, 6, '{\"allowreopen\":true,\"reopenstatus\":0,\"".$color['bg']."\":\"".$bg."\",\"".$color['fg']."\":\"".$fg."\",\"".$color['bd']."\":\"".$bd."\"}', NOW(), NOW())";
					if(!$ires = db_query($isql)){logEntry('Query Failed', 'Failed to execute query: $isql', 3); return false;}
					$oldStat[$id] = db_insert_id();
				}

				dbDropTable(MOD_STATUS);

				/****************************
					Statuses Copied
					Migrate Ticket Status Data
				****************************/
				$sql = "SELECT ticketId, statusId FROM ".MOD_STATUS_ASSIGNMENTS." WHERE isActive=1";
				if(!$res = db_query($sql)){logEntry('Query Failed', 'Failed to execute query: $sql', 3); return false;}

				while(list($ticket, $status) = db_fetch_row($res)){
					$newStat = $oldStat[$status];
					$isql = "UPDATE ".TICKET_TABLE. " SET status_id=".$newStat." WHERE ticket_id=".$ticket;
					if(!$ires = db_query($isql)){logEntry('Query Failed', 'Failed to execute query: $isql', 3); return false;}
				}

				dbDropTable(MOD_STATUS_ASSIGNMENTS);

				/****************************
					Delete Config Keys
					mod_status_default_status
					mod_status_default_shape
				****************************/
				$sql = "DELETE FROM ".CONFIG_TABLE." WHERE `key`='mod_status_default_status'";
				if(!$res = db_query($sql)){logEntry('Query Failed', 'Failed to execute query: $sql', 3); return false;}
				$sql = "DELETE FROM ".CONFIG_TABLE." WHERE `key`='mod_status_default_shape'";
				if(!$res = db_query($sql)){logEntry('Query Failed', 'Failed to execute query: $sql', 3); return false;}
			case '1008':
				/****************************
					GitHub Issue #15
					Build 1008 had a bug where the Form Field insert was done wrong. 
					This should only be done if we are upgrading from Build 1008.  The upgrader for 1008 has been corrected for anyone upgrading from a build earlier than 1008.
				****************************/
				if($sbuild == 1008){
					$usql[0] = "UPDATE ".FORM_FIELD_TABLE." SET edit_mask=15, name='bgcolor', sort=5 WHERE id=".$cfg->get('mod_status_bgcolor');
					$usql[1] = "UPDATE ".FORM_FIELD_TABLE." SET edit_mask=15, name='fgcolor', sort=5 WHERE id=".$cfg->get('mod_status_fgcolor');
					$usql[2] = "UPDATE ".FORM_FIELD_TABLE." SET edit_mask=15, name='bdcolor', sort=5 WHERE id=".$cfg->get('mod_status_bdcolor');
	
					$count = count($usql);
					for($i = 0; $i <$count; $i++){
						if($debug>=3) echo "SQL Query: ".$usql[$i]."<br />";
						if(!$res = db_query($usql[$i])){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function upgrade($module) Return Value: <b>FALSE</b><br />"; return false;}
						if($debug>=3) echo "SQL Query Results: Successfully Updated Field Table<br />";
					}
				}

				/****************************
					Build 1005 began implementing Ticket Statuses, but this was removed from future builds.  As such, this table should be removed.
					This should only be done if we are upgrading from Build 1005 - 1008.  The upgrader for 1008 has been corrected for anyone upgrading from a build earlier than 1008.
				****************************/
				if($sbuild >= 1005 && $sbuild <= 1008){
					dbDropTable(MOD_STATUS_ACTIONS);
				}
				

				break;
		  }
		if(!$cfg->updateAll(array(
			'mod_status_version'=>'v1.1.06-alpha.1010',
			'mod_status_sysbuild'=>'1010',
			))){logEntry('Configuration Update Failed', 'Failed to update configuration in function upgrade($module) Case 1008', 1); return false;}
		logEntry('Upgrade Successful', 'Module $module Upgrade to Build '.$pbuild.' from Build '.$sbuild, 2);
		break; 
		/*
		##	End Module: Status
		*/
	}
	if($debug>=2) echo "Function upgrade($module) Return Value: <b>TRUE</b><br />"; return true;
  }
  
  function build1008(){
	global $cfg;
	$sql="INSERT INTO ".MOD_LIST." (moduleName, modulePath) VALUES ('Statuses', 'modstat')";
	if(!$res = db_query($sql)){logEntry('Query Failed', 'Failed to execute query: $sql', 3); return false;}

	/****************************
		Create Default Shapes
	****************************/
	if(!defaultShapes()){logEntry('Function Returned False', 'Function defaultShapes() returned false.  Function build1008() returning false.', 2); return false;}
	$sql = "SELECT id FROM ".MOD_STATUS_OBJECT." LIMIT 1";
	if(!$res = db_query($sql)){logEntry('Query Failed', 'Failed to execute query: $sql', 3); return false;}
	$shape = db_result($res);

	/****************************
		Modify Status Properties
	****************************/
	$sql = "SELECT id FROM ".FORM_SEC_TABLE." WHERE type='L1' AND title='Ticket Status Properties'";
	if(!$res=db_query($sql)){logEntry('Query Failed', 'Failed to execute query: $sql', 3); return false;}
	$table = db_result($res);

	$isql[0] = "INSERT INTO ".FORM_FIELD_TABLE." (form_id, label, required, name, sort, edit_mask, created, updated) VALUES (".$table.", 'Background Color', 1, 'bgcolor', 4, 15, NOW(), NOW())";
	$isql[1] = "INSERT INTO ".FORM_FIELD_TABLE." (form_id, label, required, name, sort, edit_mask, created, updated) VALUES (".$table.", 'Foreground Color', 1, 'fgcolor', 5, 15, NOW(), NOW())";
	$isql[2] = "INSERT INTO ".FORM_FIELD_TABLE." (form_id, label, required, name, sort, edit_mask, created, updated) VALUES (".$table.", 'Shape Border Color', 1, 'bdcolor', 6, 15, NOW(), NOW())";

	$count = count($isql);
	for($i = 0; $i < $count; $i++){
		if(!$res=db_query($isql[$i])){logEntry('Query Failed', 'Failed to execute query: '.$isql[$i], 3); return false;}
		$color[$i] = db_insert_id();
	} 

	if($cfg->exists('mod_status_enabled'))$staten = $cfg->get('mod_status_enabled'); else $staten = 0;
	if($cfg->exists('mod_status_show_column'))$shcol = $cfg->get('mod_status_show_column'); else $shcol = 0;
	if($cfg->exists('mod_status_display_text'))$disp = $cfg->get('mod_status_display_text'); else $disp = 2;

	/****************************
		Update Configuration
	****************************/		
	if(!$cfg->updateAll(array(
		'mod_status_init'=>'1',
		'mod_status_enabled'=>$staten,
		'mod_status_show_column'=>$shcol,
		'mod_status_display_text'=>$disp,
		'mod_status_display'=>$shape,
		'mod_status_bgcolor'=>$color[0],
		'mod_status_fgcolor'=>$color[1],
		'mod_status_bdcolor'=>$color[2],
	))){logEntry('Configuration Update Failed', 'Failed to update configuration in function build1008()', 1); return false;}
	
	logEntry('Function Success', 'Function build() successfully completed changes.', 2);
	return true;
  }
  
  function update($module, $data){
	global $debug, $cfg;
	if($debug>=1) echo "Function Called: update($module, $data)<br />";
	/****************************
		Update Module
	****************************/
	$errors = false;
	//print_r($data);
	
	switch($module){
	  case 'status':
		$enabled = $data['enable'];
		$active_shape = $data['active_shape'];
		$display_option = $data['display_option'];
		if($active_shape == NULL) $active_shape = 0;
		if($display_option == NULL) $display_option = 0;
		if(strcmp($data['columnview'], 'on')==0) $showcol = 1; else $showcol = 0;

		if(strcmp('on', $enabled)==0){
			if(!$cfg->updateAll(array(
				'mod_status_enabled'=>'1',
				'mod_status_display'=>$active_shape,
				'mod_status_display_text'=>$display_option,
				'mod_status_show_column'=>$showcol,
			))){logEntry('Configuration Update Failed', 'Failed to update configuration in function update($module, $data)', 1); return false;}
		}else{
			if(!$cfg->updateAll(array(
				'mod_status_enabled'=>'0',
				'mod_status_show_column'=>'0',
			))){logEntry('Configuration Update Failed', 'Failed to update configuration in function update($module, $data)', 1); return false;}
		}
		break;
		/*
		##	End Module: Status
		*/
	}

	logEntry('Settings Updated', 'Staff Member succesfully updated settings.  update($module, $data)', 2);
	return true;
  }
?>