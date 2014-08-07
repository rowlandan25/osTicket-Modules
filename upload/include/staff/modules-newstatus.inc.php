<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');

?>
<form action="module.php?t=list" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="do" value="newstatus" >
<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4>New Ticket Status</h4>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th colspan='2'>
                <em><b>Create a new ticket status.</b></em>
            </th>
        </tr>
        <tr>
          <td width='220' class='required'>Status Name</td>
          <td><input type='text' name='statusname' required placeholder='New Ticket Status Name' size='64' maxlength='64' /></td>
        </tr>
        <tr>
          <td class='required'>Background Color</td>
          <td><input type='text' name='bg' required placeholder='Shape Background Color' size='32' maxlength='32' /></td>
        </tr>
        <tr>
          <td class='required'>Foreground Color</td>
          <td><input type='text' name='fg' required placeholder='Shape Foreground Color' size='32' maxlength='32' /></td>
        </tr>
        <tr>
          <td class='required'>Border Color</td>
          <td><input type='text' name='bd' required placeholder='Shape Border Color' size='32' maxlength='32' /></td>
        </tr>
	</tbody>
</table>
<p style="text-align:center">
    <input class="button" type="submit" name="submit" value="Save Changes">
    <input class="button" type="reset" name="reset" value="Reset Changes">
</p>
</form>
