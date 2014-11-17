<?php
/*********************************************************************
    module.php

    Handles all module actions

    Andy Rowland <osmod@msrltech.com>

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.
**********************************************************************/
require('admin.inc.php');

$errors=array();
$moduleOptions=array(
	'changelog' =>
		array('Change Logs', 'modules.changelog'),
	'shape' =>
		array('Status Shapes', 'modules.shape'),
);


$sql = "SELECT moduleName, modulePath FROM ".MOD_LIST." ORDER BY moduleName ASC";
$res = db_query($sql);

while(list($name, $path) = db_fetch_row($res)){
        $newarray = array($name, 'modules.'.$path);
        $moduleOptions[$path] = $newarray;
}

//Handle a POST.
$target=($_REQUEST['t'] && $moduleOptions[$_REQUEST['t']])?$_REQUEST['t']:'changelog';
$page = false;
if (isset($moduleOptions[$target]))
    $page = $moduleOptions[$target];

if($page && $_POST && !$errors) {
	$data = $_POST;
	switch($data['opt']){
	  case 'shape':
	  	switch ($data['act']){
			case 'save':
				//	Save a shape's data
				if($data['oid']){ //if there is an ID number sent...
				  $objid = $data['oid'];
				  $sql = "UPDATE ".MOD_STATUS_OBJECT." SET objectName='".$data['shapeName']."' WHERE id=".$objid;
				  $res = db_query($sql);

				  $sql = "SELECT id, propertyName, valueCurrent FROM ".MOD_STATUS_PROPERTY." WHERE objectId=".$objid;
				  if($res = db_query($sql)){
					  while(list($prop, $name, $val)=db_fetch_row($res)){
						$isql = "UPDATE ".MOD_STATUS_PROPERTY." SET valueCurrent='".$data['prop_'.$prop]."', valuePrevious='".$val."' WHERE id=".$prop;
						if(!$ires = db_query($isql)){
							$errors['err']=__('Unable to Update Shape Property');
						}
					  }
				  }else{
					 $errors['err']=__('Unable to Update Shape');
				  }
				  
				  if(!$errors){
					  $msg=sprintf(__('Successfully Updated Shape'));
				  }
				}else{
				  $sql = "INSERT INTO ".MOD_STATUS_OBJECT." (objectName) VALUES ('".$data['shapeName']."')";
				  if($res = db_query($sql)){
					$msg=sprintf(__('Successfully Created Shape'));
				  }else{
					$errors['err']=__('Unable to Create Shape');
				  }
				  $objid = db_insert_id();
				}
				break;	
			case 'addprop':
				$objid = $data['oid'];
				$sql = "INSERT INTO ".MOD_STATUS_PROPERTY." (objectId, propertyName, valueCurrent) VALUES (".$data['oid'].", '".$data['property']."', '".$data['val']."')";
				if($res = db_query($sql)){
					$msg=sprintf(__('Successfully Added Object Property'));
				}else{
					$errors['err']=__('Unable to Add New Object Property');
				}
				break;
			case 'delprop':
				$objid = $data['oid'];
				$sql = "DELETE FROM ".MOD_STATUS_PROPERTY." WHERE id=".$data['propid'];
				if($res = db_query($sql)){
					$msg=sprintf(__('Successfully Deleted Object Property'));
				}else{
					$errors['err']=__('Unable to Delete Object Property');
				}
				break;
			case 'delshape':
				$objid = $data['oid'];
				$sql = "DELETE FROM ".MOD_STATUS_PROPERTY." WHERE objectId=".$objid;
				if($res = db_query($sql)){
					$sql = "DELETE FROM ".MOD_STATUS_OBJECT." WHERE id=".$objid;
					if($res = db_query($sql)){
						$msg=sprintf(__('Successfully Deleted Shape'));
					}else{
						$errors['err']=__('Unable to Delete Shape');
					}
				}else{
					$errors['err']=__('Unable to Delete Object Properties');
				}
				break;
		}
	  	break;	
	}
}

$config=($errors && $_POST)?Format::input($_POST):Format::htmlchars($cfg->getConfigInfo());
$ost->addExtraHeader('<meta name="tip-namespace" content="'.$page[1].'" />',
    "$('#content').data('tipNamespace', '".$page[1]."');");

$sql = "SELECT modulePath FROM ".MOD_LIST;
$res = db_query($sql);
$found = 0;

while(!$found && list($path) = db_fetch_row($res)){
  if(strcmp($target, $path) == 0){
		$nav->setTabActive('modules', ('module.php?t='.$path));
		$found = 1;
  }
}

if(!$found){
	switch($target){
	  case 'shape':
		$nav->setTabActive('modules', ('module.php?t=modstat'));
		break;	
	  default:
		$nav->setTabActive('modules', ('module.php?t=changelog'));
		break;	
	}
}

require_once(STAFFINC_DIR.'header.inc.php');
include_once(STAFFINC_DIR."modules-$target.inc.php");
include_once(STAFFINC_DIR.'footer.inc.php');
?>