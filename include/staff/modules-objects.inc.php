<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');

?>
<h2>Status Module - Objects</h2>
<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4>Manage Display Objects</h4>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>
                <em><b>Manage the various objects for the statuses.</b></em>
            </th>
            <th style='text-align:right;'>
            	<em><a href='module.php?t=newobject'><img src='images/icons/icon-list-add.png' style='height:15px;'/>&nbsp;&nbsp;Add New Object</a></em>
            </th>
        </tr>
	</tbody>
</table>
<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <th width='75'>Object ID</th><th>Object Name</th><th>Preview</th>
  </tr>
  <?php
    $sql = "SELECT * FROM ".MOD_STATUS_OBJECT;
	$res = db_query($sql);
	while(list($id,$name)=db_fetch_row($res)){
  ?>
  <tr>
    <td style='text-align:center;'><?php echo $id;?></td><td><?php echo $name;?>&nbsp;&nbsp;[<a href='module.php?t=editobject&id=<?php echo $id;?>'>Edit</a>]</td><td></td>
  </tr>
  <?php
	}
  ?>
</table>