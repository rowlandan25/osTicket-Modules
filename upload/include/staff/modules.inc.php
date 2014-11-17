<?php
/*********************************************************************
    modules.inc.php

    Update manager for osMods

	Andrew Rowland <osmod@msrltech.com>
	Edited: 2014/11/05
	Build: 1008 [Alpha]
**********************************************************************/
  define_tables(TABLE_PREFIX);

  function update_modules(){
	  	/****************************
			Are we updating or
			installing?
		****************************/
		$module = $_POST['module'];
		$sysbuild = getSystemBuild($module);
		$pacbuild = getPackageBuild($module);

		if($sysbuild == NULL){
			dbchecker();
			/****************************
				Initialize
			****************************/
			if(initialize($module))return true;
			else return false;
		}elseif ($sysbuild != $pacbuild && strcmp('on', $_POST['upgrade']) == 0){
			dbchecker();
			/****************************
				Upgrade System
			****************************/
			if(upgrade($module))return true;
			else return false;
		}else{
			/****************************
				Update Settings
			****************************/
			if(update($module, $_POST))return true;
			else return false;
		}
  }

  function define_tables($prefix){
	  	define('MOD_STATUS',$prefix.'mod_status'); //remove in Build 1009
		define('MOD_STATUS_OBJECT',$prefix.'mod_status_object');
		define('MOD_STATUS_PROPERTY',$prefix.'mod_status_object_properties');
		define('MOD_STATUS_ASSIGNMENTS',$prefix.'mod_status_assignments'); //remove in Build 1009
		define('MOD_STATUS_ACTIONS',$prefix.'mod_status_actions');
		define('MOD_LIST',$prefix.'mod_list');
  }

  function getPackageBuild($package){
		//returns the package build number
		$build = array();
		$build['pack'] = 1008;
  		$build['status'] = 1008;
  		$build['status_actions'] = 1008;
		return $build[$package];
  }

  function getSystemBuild($package){
		global $cfg;
		return $cfg->get('mod_'.$package.'_sysbuild');
  }

  function getInstalledModules(){
  }

  function getColorField($type){
		$sql = "SELECT id FROM ".FORM_SEC_TABLE." WHERE title='Ticket Status Properties' AND type='L1'";
		$res = db_query($sql);
		list($id) = db_fetch_row($res);

		$sql = "SELECT id FROM ".FORM_FIELD_TABLE." WHERE name='".$type."' AND form_id=".$id;
		$res = db_query($sql);
		list($field) = db_fetch_row($res);

		return $field;
  }

  function getFieldArray()
  {
	  	$fields = array();
		$sql = "SELECT * FROM ";
		return $fields;
  }
  
  function dbchecker(){
	    global $cfg;
		/****************************
			Check to make sure any
			system database items
			are present
		****************************/
		$sys = getSystemBuild('pack');
		if((!$sys) || $sys < getPackageBuild('pack')){
			switch (getSystemBuild('pack')){
				default:
					$sql = "SELECT COUNT(*) FROM information_schema.tables WHERE table_name = '".MOD_LIST."' AND table_schema='".DBNAME."' LIMIT 1 ";
					$res = db_query($sql);
					list($count) = db_fetch_row($res);
					if($count == 0){
					  $sql="CREATE TABLE ".MOD_LIST."(id INT(64) NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), moduleName VARCHAR (64), UNIQUE(moduleName), modulePath VARCHAR(64), icon VARCHAR(64))";
					  if(!$res = db_query($sql)) return false;
					}
					if(!$cfg->updateAll(array(
						'mod_pack_version'=>'v1.9.4-1008 (alpha)',
						'mod_pack_sysbuild'=>'1008',
					)))return false;
					return true;
					break;
			}
		}
  }
  
  function defaultShapes(){
	  $sql[0] = "INSERT INTO ".MOD_STATUS_OBJECT." (objectName, tag) VALUES ('Default Oval', 6)";	//Set Default Oval to Read/Write but no Delete
	  if(!$res = db_query($sql[0])) return false;
	  $objid = db_insert_id();
	  //Properties of Default Oval
	  $isql[0] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'height', '10px')";
	  $isql[1] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'width', '75px')";
	  $isql[2] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'border-width', '2px')";
	  $isql[3] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'border-style', 'solid')";
	  $isql[4] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'border-radius', '12px')";
	  $isql[5] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'font-size', '7pt')";
	  $isql[6] = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$objid.", 'text-align', 'center')";

	  for($i = 0; $i < count($isql); $i++){
		  if(!$res = db_query($isql[$i])) return false;
	  }
	  return true;
  }
  
  function initialize($module){
		/****************************
			New Install of Module
		****************************/
		global $cfg;

		switch($module){
		  case 'status':
			/****************************
				Modify Database
			****************************/
			$dbsql[0]="CREATE TABLE ".MOD_STATUS_OBJECT."(id INT(64) NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), objectName VARCHAR(64), tag INT(16) NULL DEFAULT NULL)";
			$dbsql[1]="CREATE TABLE ".MOD_STATUS_PROPERTY."(id INT(64) NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), objectId INT(64), propertyName VARCHAR(64), valueCurrent VARCHAR(64), valuePrevious VARCHAR(64) NULL DEFAULT NULL)";

			for($i=0; $i<2;$i++){
			  if(!$res = db_query($dbsql[$i])) return false;
			}

			if(!build1008()) return false;
			if(!$cfg->updateAll(array(
				'mod_status_version'=>'v1.9.4-1008 (alpha)',
				'mod_status_sysbuild'=>'1008',
			)))return false;
			else return true;
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
			  if(!$res = db_query($dbsql[$i])) return false;
			}

			/****************************
				Add Database Items
			****************************/
			$actionList = array(
				"Ticket Assigned", "Re-Open Ticket", "Close Ticket", "SLA Overdue", "Due Date Overdue", "Client Response"
			);

			for($x = 0; $x < count($actionList); $x++){
				$sql="INSERT INTO ".MOD_STATUS_ACTIONS."(actionName, isActive) VALUES ('" . $actionList[$x] . "', 0)";
				if(!$res = db_query($sql)) return false;	
			}

			/****************************
				Update Configuration
			****************************/		
			if(!$cfg->updateAll(array(
				'mod_status_actions_init'=>'1',
				'mod_status_actions_version'=>'v1.9.4-1008 (alpha)',
				'mod_status_actions_sysbuild'=>'1008',
			)))return false;
			else return true;

		  	break;
			/*
			##	End Module: Status Actions
			*/
		}
  }
  
  
  function upgrade($module){
   		/****************************
			Upgrade Module
		****************************/
		global $cfg;

		$pbuild = getPackageBuild($module);
		$sbuild = getSystemBuild($module);
		$errors = false;

		switch($module){
		  case 'status':
			switch($sbuild){
				default:
				case '1004':
					$sql="CREATE TABLE ".MOD_STATUS_PROPERTY."(id INT(64) NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), objectId INT(64), propertyName VARCHAR(64), valueCurrent VARCHAR(64))";
					if(!$res = db_query($sql)) return false;
					
					if(!$cfg->updateAll(array(
						'mod_status_version'=>'v1.9.3-1005 (alpha)',
						'mod_status_sysbuild'=>'1005',
					)))return false;
				case '1005':
					$sql="CREATE TABLE ".MOD_STATUS_ACTIONS."(id INT(64) NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), actionName VARCHAR(64), setStatus INT(64) DEFAULT NULL, isActive INT(1))";
					if(!$res = db_query($sql)) return false;
					
					$actionList = array ("Ticket Assigned", "Re-Open Ticket", "Close Ticket");
					for($x = 0; $x < count($actionList); $x++){
						$sql="INSERT INTO ".MOD_STATUS_ACTIONS."(actionName, isActive) VALUES ('" . $actionList[$x] . "', 0)";
						if(!$res = db_query($sql)) return false;
					}
					
					if(!$cfg->updateAll(array(
						'mod_status_version'=>'v1.9.3-1006 (alpha)',
						'mod_status_sysbuild'=>'1006',
					)))return false;
				case '1006':
					$sql[0] = "ALTER TABLE `ost_mod_status_object` ADD `tag` INT( 16 ) NULL DEFAULT NULL";
					$sql[1] = "ALTER TABLE `ost_mod_status_object_properties` ADD `valuePrevious` INT( 16 ) NULL DEFAULT NULL";
					for($i = 0; $i < 2; $i++){
					  if(!$res = db_query($sql[$i])) return false;
					}

					if(!build1008()) return false;
					/****************************
						Database Upgraded -
						Now for the changes
					****************************/
					$color['bg'] = $cfg->get('mod_status_bgcolor');
					$color['fg'] = $cfg->get('mod_status_bgcolor');
					$color['bd'] = $cfg->get('mod_status_bgcolor');
					$oldStat = array();	
					$sql = "SELECT id, statusName, colorBG, colorFG, colorBD FROM ".MOD_STATUS;
					$res = db_query($sql);
					while(list($id, $name, $bg, $fg, $bd) = db_fetch_row($res)){
						$isql = "INSERT INTO ".TICKET_STATUS_TABLE." (name, state, mode, flags, sort, properties, created, updated) VALUES ('".$name."', 'open', 1, 0, 6, '{\"allowreopen\":true,\"reopenstatus\":0,\"".$color['bg']."\":\"".$bg."\",\"".$color['fg']."\":\"".$fg."\",\"".$color['bd']."\":\"".$bd."\"}', NOW(), NOW())";
						if(!$ires = db_query($isql)) return false;
						$oldStat[$id] = db_insert_id();
					}

					$sql = "DROP TABLE ".MOD_STATUS;
					if(!$res = db_query($sql)) return false;

					/****************************
						Statuses Copied
						Migrate Ticket Status Data
					****************************/
					$sql = "SELECT ticketId, statusId FROM ".MOD_STATUS_ASSIGNMENTS." WHERE isActive=1";
					if(!$res = db_query($sql)) return false;
					while(list($ticket, $status) = db_fetch_row($res)){
						$newStat = $oldStat[$status];
						$isql = "UPDATE ".TICKET_TABLE. " SET status_id=".$newStat." WHERE ticket_id=".$ticket;
						if(!$ires = db_query($isql)) return false;
					}

					$sql = "DROP TABLE ".MOD_STATUS_ASSIGNMENTS;
					if(!$res = db_query($sql)) return false;
					
					/****************************
						Delete Config Keys
						mod_status_default_status
						mod_status_default_shape
					****************************/
					$sql[0] = "DELETE FROM ".CONFIG_TABLE." WHERE key='mod_status_default_status'";
					$sql[1] = "DELETE FROM ".CONFIG_TABLE." WHERE key='mod_status_default_shape'";
					
					for($i=0; $i<2; $i++){
					  	if(!$res = db_query($sql[$i])) return false;
					}

					if(!$cfg->updateAll(array(
						'mod_status_version'=>'v1.9.4-1008 (alpha)',
						'mod_status_sysbuild'=>'1008',
					)))return false;
					else return true;
					break;
			  }
			  return true;
			break; 
			/*
			##	End Module: Status
			*/
		}
  }
  
  function build1008(){
	  	global $cfg;
		$dbsql[0]="INSERT INTO ".MOD_LIST." (moduleName, modulePath) VALUES ('Statuses', 'modstat')";

		for($i=0; $i<1;$i++){
		  if(!$res = db_query($dbsql[$i])) return false;
		}

		/****************************
			Create Default Shapes
		****************************/
		if(!defaultShapes()) return false;
		$sql = "SELECT id FROM ".MOD_STATUS_OBJECT." LIMIT 1";
		$res = db_query($sql);
		list($shape) = db_fetch_row($res);

		/****************************
			Modify Status Properties
		****************************/
		$sql = "SELECT id FROM ".FORM_SEC_TABLE." WHERE type='L1' AND title='Ticket Status Properties'";
		if(!$res=db_query($sql)) return false;
		else list($table) = db_fetch_row($res);

		$isql[0] = "INSERT INTO ".FORM_FIELD_TABLE." (form_id, label, required, name, sort, created, updated) VALUES (".$table.", 'Background Color', 1, 'bgcolor', 4, NOW(), NOW())";
		$isql[1] = "INSERT INTO ".FORM_FIELD_TABLE." (form_id, label, required, name, sort, created, updated) VALUES (".$table.", 'Foreground Color', 1, 'fgcolor', 5, NOW(), NOW())";
		$isql[2] = "INSERT INTO ".FORM_FIELD_TABLE." (form_id, label, required, name, sort, created, updated) VALUES (".$table.", 'Shape Border Color', 1, 'bdcolor', 6, NOW(), NOW())";
		for($i = 0; $i < count($isql); $i++){
			if(!$res=db_query($isql[$i]))return false;
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
		)))return false;
		else return true;
  }
  
  function update($module, $data){
	  	/****************************
			Update Module
		****************************/
		global $cfg;
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
				)))return false;
				else return true;
			}else{
				if(!$cfg->updateAll(array(
					'mod_status_enabled'=>'0',
					'mod_status_show_column'=>'0',
				)))return false;
				else return true;
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
  }
?>