<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');

$id = $_REQUEST['id'];
$sql = "SELECT objectName FROM ".MOD_STATUS_OBJECT." WHERE id=".$id;
$res = db_query($sql);
list($name)=db_fetch_row($res);

?>

<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4>Object: <?php echo $name;?></h4>
            </th>
        </tr>
    </thead>
</table>
<form action="module.php?t=editobject&id=<?php echo $id;?>" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="do" value="renameobject" >
<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <tbody>
        <tr>
            <th colspan='2'>
                <em><b>Rename Object.</b></em>
            </th>
        </tr>

        <tr>
          <td width='220' class='required'>Object Name</td>
          <td><input type='text' name='objectname' required value='<?php echo $name;?>' size='64' maxlength='64' /></td>
        </tr>
	</tbody>
</table>
<p style="text-align:center">
    <input class="button" type="submit" name="submit" value="Save Changes">
    <input class="button" type="reset" name="reset" value="Reset Changes">
</p>
</form>

<form action="module.php?t=editobject&id=<?php echo $id;?>" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="do" value="editprop" >
<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <tbody>
        <tr>
            <th colspan='3'>
                <em><b>Edit the object's properties.</b></em>
            </th>
        </tr>

        <tr>
          <td width='150' class='required'>Property Name</td><td class='required'>Current Value</td><td>Remove Property</td>
        </tr>
<?php
  $sql = "SELECT id, propertyName, valueCurrent FROM ".MOD_STATUS_PROPERTY." WHERE objectId=".$id;
  $res = db_query($sql);
  //echo $sql;
  while(list($propid, $propname, $propval)=db_fetch_row($res)){
?>
        <tr>
          <td><input type='text' name='propname_<?php echo $propid;?>' required value='<?php echo $propname;?>' size='64' maxlength='64' /></td>
          <td><input type='text' name='curval_<?php echo $propid;?>' required value='<?php echo $propval;?>' size='16' maxlength='32' /></td>
          <td><input type='checkbox' name='remove_<?php echo $propid;?>'/>
        </tr>
<?php
  }
?>
	</tbody>
</table>
<p style="text-align:center">
    <input class="button" type="submit" name="submit" value="Save Changes">
    <input class="button" type="reset" name="reset" value="Reset Changes">
</p>
</form>


<form action="module.php?t=editobject&id=<?php echo $id;?>" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="do" value="newprop" >
<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <tbody>
        <tr>
            <th colspan='2'>
                <em><b>Add New Property</b></em>
            </th>
        </tr>

        <tr>
          <td width='220' class='required'>Object Name</td>
          <td class='required'>Current Value</td>
        </tr>
        <tr>
          <td><input type='text' name='propname' required placeholder='Property Name' size='64' maxlength='64' /></td>
          <td><input type='text' name='propval' required placeholder='Property Value' size='16' maxlength='32' /></td>
        </tr>
	</tbody>
</table>
<p style="text-align:center">
    <input class="button" type="submit" name="submit" value="Save Changes">
    <input class="button" type="reset" name="reset" value="Reset Changes">
</p>
</form>

<form action="module.php?t=objects" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="do" value="delobj" >
<input type="hidden" name="objid" value="<?php echo $id;?>" >
<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <tbody>
        <tr>
            <th colspan='2'>
                <em><b>Delete Object</b></em>
            </th>
        </tr>
        <tr>
          <td style='text-align:center;'><input type='checkbox' required />&nbsp;&nbsp;<em>Delete this Object.</em><br />
          I understand this action cannot be undone.</td>
        </tr>
	</tbody>
</table>
<p style="text-align:center">
    <input class="button" type="submit" name="submit" value="Save Changes">
    <input class="button" type="reset" name="reset" value="Reset Changes">
</p>
</form>