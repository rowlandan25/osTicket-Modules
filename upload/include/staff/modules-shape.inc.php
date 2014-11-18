<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');

if(!$objid) $objid = $_REQUEST['id'];
if($objid){
  $sql = "SELECT objectName FROM ".MOD_STATUS_OBJECT." WHERE id=".$objid;
  $res = db_query($sql);
  list($name) = db_fetch_row($res);
  $title=$name;
}else{
  $title="New Shape";
  $name="";
}
?>

<script language="javascript">
  function confirmDelete(property, propid){
	  var ans = confirm("Are you sure you want to delete "+property+"?");
	  if(ans==1){
		 document.getElementById("propid").value = propid;
		 document.getElementById("delprop").submit();
	  }
  }
  
  function deleteShape(){
	  var ans = confirm("Are you sure you want to delete the shape <?php echo $title;?>?");
	  if(ans==1){
		 document.getElementById("delshape").submit();
	  }
  }
</script>

<h2>Create/Edit a Shape</h2>

<h4><?php echo $title;?></h4>
<form action='module.php?t=shape' method='POST' id='shape'>
<?php csrf_token(); ?>
<input type="hidden" name="opt" value="shape" >
<input type="hidden" name="act" value="save" >

<?php if($objid){ ?><input type="hidden" name="oid" value="<?php echo $objid;?>" ><?php } ?>
  <table>
    <tr><th width='220'>Shape Name</th><td><input type='text' name='shapeName' placeholder='Shape Name' size='64' value='<?php echo $name;?>'/></td><?php if($objid){?><td><input type='button'title="Delete Shape" onclick="deleteShape();" value='Delete Shape'/></td><?php }?></tr>
  </table>

<h5>Current Properties</h5>
<p>Be sure not to include any quotes (") or apostrophes (') in the property value field.</p>
  <table>
    <tr><th width='220' style='text-align:left;'>Property Name</th><th width='480' style='text-align:left;'>Property Value</th><th style='text-align:left;'>Options</th></tr>
<?php
  $sql = "SELECT id, propertyName, valueCurrent FROM ".MOD_STATUS_PROPERTY." WHERE objectId=".$objid;
  $res = db_query($sql);
  while(list($pid, $prop, $val) = db_fetch_row($res)){
?>
    <tr><td><?php echo $prop;?></td><td><input type='text' name='prop_<?php echo $pid;?>' value='<?php echo $val;?>' width='80' /></td><td><input type='button'title="Delete Property '<?php echo $prop;?>'" onclick="confirmDelete('<?php echo $prop;?>', <?php echo $pid;?>);" value='D'/></td></tr>
<?php
  }
?>
  </table>
  
  <div style='text-align:center;'><input type='submit' value='Save Changes' /></div>
</form>

<?php
  if($objid){
?>
<h5>Add Properties</h5>
<form action='module.php?t=shape' method='POST' id='addprop'>
<?php csrf_token(); ?>
<input type="hidden" name="opt" value="shape" >
<input type="hidden" name="act" value="addprop" >
<input type="hidden" name="oid" value="<?php echo $objid;?>" >
  <table>
    <tr>
      <th width='220'>Property Name</th><td><input type='text' name='property' placeholder='Property Name' /></td>
      <th width='220'>Property Value</th><td><input type='text' name='val' placeholder='Property Value' /></td>
      <td width='200' style='text-align:center;'><input type='submit' value='Add Property' /></td>
    </tr>
  </table>
</form>

<form action='module.php?t=shape' method='POST' id='delprop'>
<?php csrf_token(); ?>
<input type="hidden" name="opt" value="shape" >
<input type="hidden" name="act" value="delprop" >
<input type="hidden" name="oid" value="<?php echo $objid;?>" >
<input type="hidden" name="propid" id="propid" value="" >
</form>

<form action='module.php?t=modstat' method='POST' id='delshape'>
<?php csrf_token(); ?>
<input type="hidden" name="opt" value="shape" >
<input type="hidden" name="act" value="delshape" >
<input type="hidden" name="oid" value="<?php echo $objid;?>" >
</form>

<?php
  }
?>