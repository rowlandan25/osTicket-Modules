<?php
/*********************************************************************
    settings.php

    Handles all admin settings.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('admin.inc.php');
$errors=array();
$moduleOptions=array(
	'modlist' =>
		array('Module List', 'module.modlist'),
	'list' =>
		array('Status Module List', 'modules.list'),
	'objects' =>
		array('Status Module Objects', 'modules.objects'),
	'newstatus' =>
		array('New Ticket Status', 'modules.status'),
	'newobject' =>
		array('New Object', 'modules.object'),
	'editobject' =>
		array('Edit Object', 'modules.editobject'),
	'editstatus' =>
		array('Edit Status', 'modules.editstatus'),
);
//Handle a POST.
$target=($_REQUEST['t'] && $moduleOptions[$_REQUEST['t']])?$_REQUEST['t']:'modlist';
$page = false;
if (isset($moduleOptions[$target]))
    $page = $moduleOptions[$target];


if($page && $_POST && !$errors) {
	$data = $_POST;
  
  	switch($data['do']){
		case 'newstatus':
			$sql = "INSERT INTO ".MOD_STATUS."(statusName, colorBG, colorFG, colorBD) VALUES ('".$data['statusname']."','".$data['bg']."','".$data['fg']."','".$data['bd']."')";
			if($res = db_query($sql)) $msg = "Successfully Created Status!";
			else $errors['err'] = "Error Creating Status.";
			break;	
		case 'newobject':
			$sql = "INSERT INTO ".MOD_STATUS_OBJECT."(objectName) VALUES ('".$data['objectname']."')";
			if($res = db_query($sql)) $msg = "Successfully Created Display Object!";
			else $errors['err'] = "Error Creating Display Object.";
			break;	
		case 'newprop':
			$id = $_REQUEST['id'];
			$sql = "INSERT INTO ".MOD_STATUS_PROPERTY."(propertyName, objectId, valueCurrent) VALUES ('".$data['propname']."',".$id.",'".$data['propval']."')";
			if($res = db_query($sql)) $msg = "Successfully Added Property!";
			else $errors['err'] = "Error Adding Property: ".$sql;
			break;	
		case 'editprop':
			$id = $_REQUEST['id'];
			$sql = "SELECT id FROM ".MOD_STATUS_PROPERTY." WHERE objectId=".$id;
  			$res = db_query($sql);
  			while(list($propid)=db_fetch_row($res)){
			  if(strcmp($data['remove_'.$propid], 'on')==0){
				  $sql = "DELETE FROM ".MOD_STATUS_PROPERTY." WHERE id=".$propid;
				  db_query($sql);
			  }else{
				  $sql = "UPDATE ".MOD_STATUS_PROPERTY." SET propertyName='".$data['propname_'.$propid]."', valueCurrent='".$data['curval_'.$propid]."' WHERE id=".$propid;
				  db_query($sql);
			  }
			}
			break;
		case 'delobj':
			$sql = "DELETE FROM ".MOD_STATUS_PROPERTY." WHERE objectId=".$data['objid'];
			if($res = db_query($sql)){
				$sql = "DELETE FROM ".MOD_STATUS_OBJECT." WHERE id=".$data['objid'];
				if($res = db_query($sql)) $msg = "Successfully Removed Object!";
				else $errors['err'] = "Failed to remove Object";
			}else $errors['err'] = "Failed to remove Object Properties";
			break;
		case 'editstatus':
			$sql = "Update ".MOD_STATUS." SET statusName='".$data['statusname']."', colorBG='".$data['bg']."', colorFG='".$data['fg']."', colorBD='".$data['bd']."' WHERE id=".$data['statusid'];
			if($res = db_query($sql)) $msg = "Successfully Updated Status!";
			else $errors['err'] = "Failed to Update Status";
			break;
		case 'delstatus':
			$sql = "Update ".MOD_STATUS." SET active=0 WHERE id=".$data['statusid'];
			if($res = db_query($sql)) $msg = "Successfully Removed Status!";
			else $errors['err'] = "Failed to Remove Status";
			break;
	}
/*
    if($cfg && $cfg->updateSettings($_POST,$errors)) {
        $msg=Format::htmlchars($page[0]).' Updated Successfully';
    } elseif(!$errors['err']) {
        $errors['err']='Unable to update settings - correct errors below and try again';
    }
*/
}


$config=($errors && $_POST)?Format::input($_POST):Format::htmlchars($cfg->getConfigInfo());
$ost->addExtraHeader('<meta name="tip-namespace" content="'.$page[1].'" />',
    "$('#content').data('tipNamespace', '".$page[1]."');");
switch($target){
	case 'newstatus':
	case 'list':
		$nav->setTabActive('modules', ('module.php?t=list'));
		break;
	case 'editobject':
	case 'newobject':
	case 'objects':
		$nav->setTabActive('modules', ('module.php?t=objects'));
		break;
	case 'modlist':
		$nav->setTabActive('modules', ('module.php?t=modlist'));
		break;
}
require_once(STAFFINC_DIR.'header.inc.php');
include_once(STAFFINC_DIR."modules-$target.inc.php");
include_once(STAFFINC_DIR.'footer.inc.php');
?>
