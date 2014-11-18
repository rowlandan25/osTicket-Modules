<?php
/*********************************************************************
    modules.inc.php

    Update manager for osMods

	Andrew Rowland <osmod@msrltech.com>
	Edited: 2014/11/05
	Build: 1008 [Alpha]
**********************************************************************/
  define_tables(TABLE_PREFIX);
  $debug = 0;	//Debug Level -> 0: None -- 1: Include Function Calls/Returns -- 2: Include Sub Function Messages -- 3: Include SQL Statements & Results -- 4: Include Variable Value Changes

  function update_modules(){
	global $debug;
	if($debug>=1) echo "Function Called: update_modules()<br />";
	/****************************
		Are we updating or
		installing?
	****************************/
	$module = $_POST['module'];
	if($debug>=4) echo "Variable module set to <b>$module</b><br />";
	$sys = getSystemExists($module);
	if($debug>=4) echo "Variable sys set to <b>$sys</b><br />";
	if(!$sys){
		if($debug>=2) echo "Package does not exist on system, Initializing Package.<br />";
		dbchecker();
		/****************************
			Initialize
		****************************/
		if(!initialize($module)){if($debug>=2) echo "Function update_modules() Return Value: <b>FALSE</b><br />"; return false;}
	}elseif ($sys && strcmp('on', $_POST['upgrade']) == 0){
		if($debug>=2) echo "Package exists on system and requires upgrade, Upgrading Package.<br />";
		dbchecker();
		/****************************
			Upgrade System
		****************************/
		if(!upgrade($module)){if($debug>=2) echo "Function update_modules() Return Value: <b>FALSE</b><br />"; return false;}
	}else{
		if($debug>=2) echo "Package exists on system and no upgrade is required.  Saving Settings.<br />";
		/****************************
			Update Settings
		****************************/
		if(!update($module, $_POST)){if($debug>=2) echo "Function update_modules() Return Value: <b>FALSE</b><br />"; return false;}
	}
	if($debug>=2) echo "Function update_modules() Return Value: <b>TRUE</b><br />"; return true;
  }

  function define_tables($prefix){
	global $debug;
	if($debug>=1) echo "Function Called: define_tables($prefix)<br />";
	define('MOD_STATUS',$prefix.'mod_status'); //remove in Build 1009
	define('MOD_STATUS_OBJECT',$prefix.'mod_status_object');
	define('MOD_STATUS_PROPERTY',$prefix.'mod_status_object_properties');
	define('MOD_STATUS_ASSIGNMENTS',$prefix.'mod_status_assignments'); //remove in Build 1009
	define('MOD_STATUS_ACTIONS',$prefix.'mod_status_actions');
	define('MOD_LIST',$prefix.'mod_list');
  }

  function getPackageBuild($package){
	global $debug;
	if($debug>=1) echo "Function Called: getPackageBuild($package)<br />";
	//returns the package build number
	$build = array();
	$build['pack'] = 1008;
	$build['status'] = 1008;
	//$build['status_actions'] = 1008;
	if($debug>=4) echo "Variable build set to:<br />";
	if($debug>=4) print_r($build);
	if($debug>=4) echo "<br />";
	if($debug>=2) echo "Function getPackageBuild($package) Return Value: <b>".$build[$package]."</b><br />";
	return $build[$package];
  }

  function getSystemBuild($package){
	global $debug, $cfg;
	if($debug>=1) echo "Function Called: getSystemBuild($package)<br />";
	$var = $cfg->get('mod_'.$package.'_sysbuild');
	if($debug>=2) echo "Function getSystemBuild($package) Return Value: <b>$var</b><br />";
	return $var;
  }
  
  function getSystemExists($package){
	global $debug, $cfg;
	if($debug>=1) echo "Function Called: getSystemExists($package)<br />";
	$var = $cfg->exists('mod_'.$package.'_sysbuild');
	if($debug>=2) echo "Function getSystemExists($package) Return Value: <b>$var</b><br />";
	return $var;
  }

  function getInstalledModules(){
	global $debug;
	if($debug>=1) echo "Function Called: getInstalledModules()<br />";
  }
  
  function dbchecker(){
	global $debug, $cfg;
	if($debug>=1) echo "Function Called: dbchecker()<br />";
	/****************************
		Check to make sure any
		system database items
		are present
	****************************/
	$sys = getSystemBuild('pack');
	if($debug>=4) echo "Variable sys set to <b>$sys</b><br />";
	if((!$sys) || $sys < getPackageBuild('pack')){
		switch ($sys){
			default:
				$sql = "SELECT COUNT(table_name) FROM information_schema.tables WHERE table_name = '".MOD_LIST."' AND table_schema='".DBNAME."' LIMIT 1 ";
				if($debug>=3) echo "SQL Query: $sql<br />";
				if(!$res = db_query($sql)){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function Return Value: <b>FALSE</b><br />"; return false;}
				if($debug>=3) echo "SQL Query Results: Lookup Successful<br />";
				$count = db_result($res);
				if($debug>=4) echo "Variable count set to <b>$count</b><br />";
				if($count<1){
				  $sql="CREATE TABLE ".MOD_LIST."(id INT(64) NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), moduleName VARCHAR (64), UNIQUE(moduleName), modulePath VARCHAR(64), icon VARCHAR(64))";
				  if($debug>=3) echo "SQL Query: $sql<br />";
				  if(!$res = db_query($sql)){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function dbchecker() Return Value: <b>FALSE</b><br />"; return false;}
				  if($debug>=3) echo "SQL Query Results: Table Successfully Created<br />";
				}
				if(!$cfg->updateAll(array(
					'mod_pack_version'=>'v1.9.4-1008 (alpha)',
					'mod_pack_sysbuild'=>'1008',
				))){if($debug>=2) echo "Function dbchecker() Return Value: <b>FALSE</b><br />"; return false;}
				break;
		}
	}
	if($debug>=2) echo "Function dbchecker() Return Value: <b>TRUE</b><br />"; return true;
  }
  
  function defaultShapes(){
	  global $debug;
	  if($debug>=1) echo "Function Called: defaultShapes()<br />";
	  $sql = "INSERT INTO ".MOD_STATUS_OBJECT." (objectName, tag) VALUES ('Default Oval', 6)";	//Set Default Oval to Read/Write but no Delete
	  if($debug>=3) echo "SQL Query: $sql<br />";
	  if(!$res = db_query($sql)){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function Return Value: <b>FALSE</b><br />"; return false;}
	  if($debug>=3) echo "SQL Query Results: Successfully Inserted Row<br />";
	  $objid = db_insert_id();
	  if($debug>=4) echo "Variable objid set to <b>$objid</b><br />";
	  //Properties of Default Oval
	  $isql[0] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'height', '10px')";
	  $isql[1] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'width', '75px')";
	  $isql[2] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'border-width', '2px')";
	  $isql[3] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'border-style', 'solid')";
	  $isql[4] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'border-radius', '12px')";
	  $isql[5] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'font-size', '7pt')";
	  $isql[6] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'text-align', 'center')";

	  $count = count($isql);
	  if($debug>=4) echo "Variable count set to <b>$count</b><br />";
	  for($i = 0; $i < $count; $i++){
		  if($debug>=3) echo "SQL Query: ".$isql[$i]."<br />";
		  if(!$res = db_query($isql[$i])){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function defaultShapes() Return Value: <b>FALSE</b><br />"; return false;}
		  if($debug>=3) echo "SQL Query Results: Successfully Inserted Row<br />";
	  }
	  if($debug>=2) echo "Function defaultShapes() Return Value: <b>TRUE</b><br />"; return true;
  }
  
  function initialize($module){
	global $debug, $cfg;
	if($debug>=1) echo "Function Called: initialize($module)<br />";
	/****************************
		New Install of Module
	****************************/
	switch($module){
	  case 'status':
		/****************************
			Modify Database
		****************************/
		$dbsql[0]="CREATE TABLE ".MOD_STATUS_OBJECT."(id INT(64) NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), objectName VARCHAR(64), tag INT(16) NULL DEFAULT NULL)";
		$dbsql[1]="CREATE TABLE ".MOD_STATUS_PROPERTY."(id INT(64) NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), objectId INT(64), propertyName VARCHAR(64), valueCurrent VARCHAR(64), valuePrevious VARCHAR(64) NULL DEFAULT NULL)";

		for($i=0; $i<2;$i++){
		  if($debug>=3) echo "SQL Query: ".$dbsql[$i]."<br />";
		  if(!$res = db_query($dbsql[$i])){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function Return Value: <b>FALSE</b><br />"; return false;}
		  if($debug>=3) echo "SQL Query Results: Table Successfully Created<br />";
		}

		if(!build1008()){if($debug>=2) echo "Function initialize($module) Return Value: <b>FALSE</b><br />"; return false;}
		if(!$cfg->updateAll(array(
			'mod_status_version'=>'v1.9.4-1008 (alpha)',
			'mod_status_sysbuild'=>'1008',
		))){if($debug>=2) echo "Function initialize($module) Return Value: <b>FALSE</b><br />"; return false;}
		break;
		/*
		##	End Module: Status
		*/
	  case 'status_actions':
		/****************************
			Modify Database
		****************************/
		$dbsql[0]="CREATE TABLE ".MOD_STATUS_ACTIONS."(id INT(64) NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), actionName VARCHAR(64), setStatus INT(64) DEFAULT NULL, isActive INT(1))";
		$dbsql[1]="INSERT INTO ".MOD_LIST." (moduleName, modulePath) VALUES ('Status Actions', 'modacts')";

		for($i=0; $i<2;$i++){
		  if(!$res = db_query($dbsql[$i])){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function Return Value: <b>FALSE</b><br />"; return false;}
		}

		/****************************
			Add Database Items
		****************************/
		$actionList = array(
			"Ticket Assigned", "Re-Open Ticket", "Close Ticket", "SLA Overdue", "Due Date Overdue", "Client Response"
		);

		for($x = 0; $x < count($actionList); $x++){
			$sql="INSERT INTO ".MOD_STATUS_ACTIONS."(actionName, isActive) VALUES ('" . $actionList[$x] . "', 0)";
			if(!$res = db_query($sql)){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function Return Value: <b>FALSE</b><br />"; return false;}
		}

		/****************************
			Update Configuration
		****************************/		
		if(!$cfg->updateAll(array(
			'mod_status_actions_init'=>'1',
			'mod_status_actions_version'=>'v1.9.4-1008 (alpha)',
			'mod_status_actions_sysbuild'=>'1008',
		)))return false;
		break;
		/*
		##	End Module: Status Actions
		*/
	}
	if($debug>=2) echo "Function initialize($module) Return Value: <b>TRUE</b><br />"; return true;
  }

  function upgrade($module){
	global $debug, $cfg;
	if($debug>=1) echo "Function Called: upgrade($module)<br />";
	/****************************
		Upgrade Module
	****************************/
	$pbuild = getPackageBuild($module);
	if($debug>=4) echo "Variable pbuild set to <b>$pbuild</b><br />";
	$sbuild = getSystemBuild($module);
	if($debug>=4) echo "Variable sbuild set to <b>$sbuild</b><br />";
	$errors = false;

	switch($module){
	  case 'status':
		switch($sbuild){
			default:
			case '1004':
				$sql="CREATE TABLE ".MOD_STATUS_PROPERTY."(id INT(64) NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), objectId INT(64), propertyName VARCHAR(64), valueCurrent VARCHAR(64))";
				if($debug>=3) echo "SQL Query: $sql<br />";
				if(!$res = db_query($sql)){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function upgrade($module) Return Value: <b>FALSE</b><br />"; return false;}
				if($debug>=3) echo "SQL Query Results: Table Successfully Created<br />";

				if(!$cfg->updateAll(array(
					'mod_status_version'=>'v1.9.3-1005 (alpha)',
					'mod_status_sysbuild'=>'1005',
				))){if($debug>=2) echo "Function Return Value: <b>FALSE</b><br />"; return false;}
			case '1005':
				$sql="CREATE TABLE ".MOD_STATUS_ACTIONS."(id INT(64) NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), actionName VARCHAR(64), setStatus INT(64) DEFAULT NULL, isActive INT(1))";
				if($debug>=3) echo "SQL Query: $sql<br />";
				if(!$res = db_query($sql)){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function upgrade($module) Return Value: <b>FALSE</b><br />"; return false;}
				if($debug>=3) echo "SQL Query Results: Table Successfully Created<br />";

				$actionList = array ("Ticket Assigned", "Re-Open Ticket", "Close Ticket");
				for($x = 0; $x < count($actionList); $x++){
					$sql="INSERT INTO ".MOD_STATUS_ACTIONS."(actionName, isActive) VALUES ('" . $actionList[$x] . "', 0)";
					if($debug>=3) echo "SQL Query: $sql<br />";
					if(!$res = db_query($sql)){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function upgrade($module) Return Value: <b>FALSE</b><br />"; return false;}
					if($debug>=3) echo "SQL Query Results: Successfully Inserted Row<br />";
				}
				
				if(!$cfg->updateAll(array(
					'mod_status_version'=>'v1.9.3-1006 (alpha)',
					'mod_status_sysbuild'=>'1006',
				))){if($debug>=2) echo "Function Return Value: <b>FALSE</b><br />"; return false;}
			case '1006':
				$sql[0] = "ALTER TABLE `ost_mod_status_object` ADD `tag` INT( 16 ) NULL DEFAULT NULL";
				$sql[1] = "ALTER TABLE `ost_mod_status_object_properties` ADD `valuePrevious` INT( 16 ) NULL DEFAULT NULL";
				for($i = 0; $i < 2; $i++){
				  if($debug>=3) echo "SQL Query: ".$sql[$i]."<br />";
				  if(!$res = db_query($sql[$i])){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function upgrade($module) Return Value: <b>FALSE</b><br />"; return false;}
				  if($debug>=3) echo "SQL Query Results: Table Successfully Altered<br />";
				}

				if(!build1008()){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function upgrade($module) Return Value: <b>FALSE</b><br />"; return false;}
				/****************************
					Database Upgraded -
					Now for the changes
				****************************/
				$color['bg'] = $cfg->get('mod_status_bgcolor');
				if($debug>=4) echo "Variable color['bg'] set to <b>".$color['bg']."</b><br />";
				$color['fg'] = $cfg->get('mod_status_fgcolor');
				if($debug>=4) echo "Variable color['fg'] set to <b>".$color['fg']."</b><br />";
				$color['bd'] = $cfg->get('mod_status_bdcolor');
				if($debug>=4) echo "Variable color['bd'] set to <b>".$color['bd']."</b><br />";
				$oldStat = array();
				if($debug>=4) echo "Variable oldStat created<br />";

				$sql = "SELECT id, statusName, colorBG, colorFG, colorBD FROM ".MOD_STATUS;
				if($debug>=3) echo "SQL Query: $sql<br />";
				$res = db_query($sql);
				if($debug>=3) echo "SQL Query Results: Lookup Succesful<br />";
				while(list($id, $name, $bg, $fg, $bd) = db_fetch_row($res)){
					$isql = "INSERT INTO ".TICKET_STATUS_TABLE." (name, state, mode, flags, sort, properties, created, updated) VALUES ('".$name."', 'open', 1, 0, 6, '{\"allowreopen\":true,\"reopenstatus\":0,\"".$color['bg']."\":\"".$bg."\",\"".$color['fg']."\":\"".$fg."\",\"".$color['bd']."\":\"".$bd."\"}', NOW(), NOW())";
					if($debug>=3) echo "SQL Query: $isql<br />";
					if(!$ires = db_query($isql)){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function upgrade($module) Return Value: <b>FALSE</b><br />"; return false;}
					if($debug>=3) echo "SQL Query Results: Successfully Inserted Row<br />";
					$oldStat[$id] = db_insert_id();
					if($debug>=4) echo "Variable oldstat[$id] set to <b>".$oldStat[$id]."</b><br />";
				}

				$sql = "DROP TABLE ".MOD_STATUS;
				if($debug>=3) echo "SQL Query: $sql<br />";
				if(!$res = db_query($sql)){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function upgrade($module) Return Value: <b>FALSE</b><br />"; return false;}
				if($debug>=3) echo "SQL Query Results: Table Successfully Dropped<br />";

				/****************************
					Statuses Copied
					Migrate Ticket Status Data
				****************************/
				$sql = "SELECT ticketId, statusId FROM ".MOD_STATUS_ASSIGNMENTS." WHERE isActive=1";
				if($debug>=3) echo "SQL Query: $sql<br />";
				if(!$res = db_query($sql)){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function upgrade($module) Return Value: <b>FALSE</b><br />"; return false;}
				if($debug>=3) echo "SQL Query Results: Lookup Successful<br />";
				while(list($ticket, $status) = db_fetch_row($res)){
					if($debug>=4) echo "Variable ticket set to <b>$ticket</b><br />";
					if($debug>=4) echo "Variable status set to <b>$status</b><br />";
					$newStat = $oldStat[$status];
					if($debug>=4) echo "Variable newStat set to <b>$newStat</b><br />";
					$isql = "UPDATE ".TICKET_TABLE. " SET status_id=".$newStat." WHERE ticket_id=".$ticket;
					if($debug>=3) echo "SQL Query: $isql<br />";
					if(!$ires = db_query($isql)){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function upgrade($module) Return Value: <b>FALSE</b><br />"; return false;}
					if($debug>=3) echo "SQL Query Results: Successfully Updated Row<br />";
				}

				$sql = "DROP TABLE ".MOD_STATUS_ASSIGNMENTS;
				if($debug>=3) echo "SQL Query: $sql<br />";
				if(!$res = db_query($sql)){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function upgrade($module) Return Value: <b>FALSE</b><br />"; return false;}
				if($debug>=3) echo "SQL Query Results: Successfully Dropped Table<br />";

				/****************************
					Delete Config Keys
					mod_status_default_status
					mod_status_default_shape
				****************************/
				$dsql[0] = "DELETE FROM ".CONFIG_TABLE." WHERE `key`='mod_status_default_status'";
				$dsql[1] = "DELETE FROM ".CONFIG_TABLE." WHERE `key`='mod_status_default_shape'";
				
				for($i=0; $i<2; $i++){
					if($debug>=3) echo "SQL Query: ".$dsql[$i]."<br />";
					if(!$res = db_query($dsql[$i])){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function upgrade($module) Return Value: <b>FALSE</b><br />"; return false;}
					if($debug>=3) echo "SQL Query Results: Successfully Deleted Unused Key<br />";
				}

				if(!$cfg->updateAll(array(
					'mod_status_version'=>'v1.9.4-1008 (alpha)',
					'mod_status_sysbuild'=>'1008',
				))){if($debug>=2) echo "Function upgrade($module) Return Value: <b>FALSE</b><br />"; return false;}
				break;
		  }
		break; 
		/*
		##	End Module: Status
		*/
	}
	if($debug>=2) echo "Function upgrade($module) Return Value: <b>TRUE</b><br />"; return true;
  }
  
  function build1008(){
	global $debug, $cfg;
	if($debug>=1) echo "Function Called: build1008()<br />";
	$sql="INSERT INTO ".MOD_LIST." (moduleName, modulePath) VALUES ('Statuses', 'modstat')";
	if($debug>=3) echo "SQL Query: $sql<br />";
	if(!$res = db_query($sql)){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function build1008() Return Value: <b>FALSE</b><br />"; return false;}
	if($debug>=3) echo "SQL Query Results: Successfully Inserted Row<br />";

	/****************************
		Create Default Shapes
	****************************/
	if(!defaultShapes()) return false;
	$sql = "SELECT id FROM ".MOD_STATUS_OBJECT." LIMIT 1";
	if($debug>=3) echo "SQL Query: $sql<br />";
	if(!$res = db_query($sql)){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function build1008() Return Value: <b>FALSE</b><br />"; return false;}
	if($debug>=3) echo "SQL Query Results: Lookup Successful<br />";
	$shape = db_result($res);
	if($debug>=4) echo "Variable shape set to <b>$shape</b><br />";

	/****************************
		Modify Status Properties
	****************************/
	$sql = "SELECT id FROM ".FORM_SEC_TABLE." WHERE type='L1' AND title='Ticket Status Properties'";
	if($debug>=3) echo "SQL Query: $sql<br />";
	if(!$res=db_query($sql)){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function build1008() Return Value: <b>FALSE</b><br />"; return false;}
	if($debug>=3) echo "SQL Query Results: Lookup Successful<br />";
	$table = db_result($res);
	if($debug>=4) echo "Variable table set to <b>$table</b><br />";

	$isql[0] = "INSERT INTO ".FORM_FIELD_TABLE." (form_id, label, required, name, sort, edit_mask, created, updated) VALUES (".$table.", 'Background Color', 1, 15, 'bgcolor', 4, NOW(), NOW())";
	$isql[1] = "INSERT INTO ".FORM_FIELD_TABLE." (form_id, label, required, name, sort, edit_mask, created, updated) VALUES (".$table.", 'Foreground Color', 1, 15, 'fgcolor', 5, NOW(), NOW())";
	$isql[2] = "INSERT INTO ".FORM_FIELD_TABLE." (form_id, label, required, name, sort, edit_mask, created, updated) VALUES (".$table.", 'Shape Border Color', 1, 15, 'bdcolor', 6, NOW(), NOW())";
	$count = count($isql);
	if($debug>=4) echo "Variable count set to <b>$count</b><br />";
	for($i = 0; $i < $count; $i++){
		if($debug>=3) echo "SQL Query: ".$isql[$i]."<br />";
		if(!$res=db_query($isql[$i])){if($debug>=3) echo "SQL Query Errors: [".db_errno()."] ".db_error()."<br />"; if($debug>=2) echo "Function build1008() Return Value: <b>FALSE</b><br />"; return false;}
		if($debug>=3) echo "SQL Query Results: Successfully Inserted Row<br />";
		$color[$i] = db_insert_id();
		if($debug>=4) echo "Variable color[$i] set to <b>".$color[$i]."</b><br />";
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
	))){if($debug>=2) echo "Function build1008() Return Value: <b>FALSE</b><br />"; return false;}
	
	if($debug>=2) echo "Function build1008() Return Value: <b>TRUE</b><br />"; return true;
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
		if($debug>=4) echo "Variable enabled set to <b>$enabled</b><br />";
		$active_shape = $data['active_shape'];
		$display_option = $data['display_option'];
		if($active_shape == NULL) $active_shape = 0;
		if($debug>=4) echo "Variable active_shape set to <b>$active_shape</b><br />";
		if($display_option == NULL) $display_option = 0;
		if($debug>=4) echo "Variable display_option set to <b>$display_option</b><br />";
		if(strcmp($data['columnview'], 'on')==0) $showcol = 1; else $showcol = 0;
		if($debug>=4) echo "Variable showcol set to <b>$showcol</b><br />";

		if(strcmp('on', $enabled)==0){
			if(!$cfg->updateAll(array(
				'mod_status_enabled'=>'1',
				'mod_status_display'=>$active_shape,
				'mod_status_display_text'=>$display_option,
				'mod_status_show_column'=>$showcol,
			))){if($debug>=2) echo "Function update($module, $data) Return Value: <b>FALSE</b><br />"; return false;}
		}else{
			if(!$cfg->updateAll(array(
				'mod_status_enabled'=>'0',
				'mod_status_show_column'=>'0',
			))){if($debug>=2) echo "Function update($module, $data) Return Value: <b>FALSE</b><br />"; return false;}
		}
		break;
		/*
		##	End Module: Status
		*/
	  case 'status_actions':
		$sql = "SELECT id FROM ".MOD_STATUS_ACTIONS;
		$res = db_query($sql);

		while(list($actionId) = db_fetch_row($res)){
			if(strcmp('on', $data['action_'.$actionId]) == 0)
				$active = 1;
			else
				$active = 0;
			$sql = "UPDATE ".MOD_STATUS_ACTIONS." SET setStatus='".$data['new_status_'.$actionId]."', isActive=".$active." WHERE id=".$actionId;
			db_query($sql);
		}
		break;
		/*
		##	End Module: Status Actions
		*/
	}
	if($debug>=2) echo "Function update($module, $data) Return Value: <b>TRUE</b><br />"; return true;
  }
?>