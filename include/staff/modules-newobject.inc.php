<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');

?>
<form action="module.php?t=objects" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="do" value="newobject" >
<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4>New Display Object</h4>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th colspan='2'>
                <em><b>Create a new display object.</b></em>
            </th>
        </tr>
        <tr>
          <td width='220' class='required'>Object Name</td>
          <td><input type='text' name='objectname' required placeholder='New Display Object Name' size='64' maxlength='64' /></td>
        </tr>
	</tbody>
</table>
<p style="text-align:center">
    <input class="button" type="submit" name="submit" value="Save Changes">
    <input class="button" type="reset" name="reset" value="Reset Changes">
</p>
</form>
