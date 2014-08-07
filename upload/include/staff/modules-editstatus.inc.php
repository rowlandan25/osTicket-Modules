<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');

$id = $_REQUEST['id'];
$sql = "SELECT statusName, colorBG, colorFG, colorBD FROM ".MOD_STATUS." WHERE id=".$id;
$res = db_query($sql);
list($name, $bg, $fg, $bd)=db_fetch_row($res);

?>

<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4>Status: <?php echo $name;?></h4>
            </th>
        </tr>
    </thead>
</table>
<form action="module.php?t=list" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="do" value="editstatus" >
<input type="hidden" name="statusid" value="<?php echo $id;?>" >
<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <tbody>
        <tr>
            <th colspan='2'>
                <em><b>Modify Properties</b></em>
            </th>
        </tr>

        <tr>
          <td width='220' class='required'>Status Name</td>
          <td><input type='text' name='statusname' required value='<?php echo $name;?>' size='64' maxlength='64' /></td>
        </tr>
        <tr>
          <td width='220' class='required'>Background Color</td>
          <td><input type='text' name='bg' required value='<?php echo $bg;?>' size='64' maxlength='64' /></td>
        </tr>
        <tr>
          <td width='220' class='required'>Foreground Color</td>
          <td><input type='text' name='fg' required value='<?php echo $fg;?>' size='64' maxlength='64' /></td>
        </tr>
        <tr>
          <td width='220' class='required'>Border Color</td>
          <td><input type='text' name='bd' required value='<?php echo $bd;?>' size='64' maxlength='64' /></td>
        </tr>
	</tbody>
</table>
<p style="text-align:center">
    <input class="button" type="submit" name="submit" value="Save Changes">
    <input class="button" type="reset" name="reset" value="Reset Changes">
</p>
</form>

<form action="module.php?t=list" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="do" value="delstatus" >
<input type="hidden" name="statusid" value="<?php echo $id;?>" >
<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <tbody>
        <tr>
            <th colspan='2'>
                <em><b>Delete Status</b></em>
            </th>
        </tr>
        <tr>
          <td style='text-align:center;'><input type='checkbox' required />&nbsp;&nbsp;<em>Delete this Status.</em><br />
          I understand this action cannot be undone.</td>
        </tr>
	</tbody>
</table>
<p style="text-align:center">
    <input class="button" type="submit" name="submit" value="Save Changes">
    <input class="button" type="reset" name="reset" value="Reset Changes">
</p>
</form>