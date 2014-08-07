<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');
?>
<h2>Status Module - Manage Statuses</h2>

<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4>Status List</h4>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>
                <em><b>Manage the list of statuses.</b></em>
            </th>
            <th style='text-align:right;'>
            	<em><a href='module.php?t=newstatus'><img src='images/icons/icon-list-add.png' style='height:15px;'/>&nbsp;&nbsp;Add New Status</a></em>
            </th>
        </tr>
	</tbody>
</table>
<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <th width='75'>Status ID</th><th>Status Name</th><th>View</th>
  </tr>
  <?php
    $sql = "SELECT * FROM ".MOD_STATUS." WHERE active=1";
	$res = db_query($sql);
	while(list($id,$name, $bg, $fg, $bd)=db_fetch_row($res)){
  ?>
  <tr>
    <td style='text-align:center;'><?php echo $id;?></td><td><?php echo $name;?>&nbsp;&nbsp;[<a href='module.php?t=editstatus&id=<?php echo $id;?>'>Edit</a>]</td>
    <td>
      <?php
	    if($cfg->exists('mod_status_default_shape') && $cfg->get('mod_status_default_shape') != NULL){
	  ?>
        <center>
        <div name='shape' style='background-color:<?php echo $bg;?>; color:<?php echo $fg;?>; border-color:<?php echo $bd;?>;
      <?php
	    $sql = "SELECT propertyName, valueCurrent FROM ".MOD_STATUS_PROPERTY." WHERE objectId=".$cfg->get('mod_status_default_shape');
		$res2 = db_query($sql);
		while(list($property, $val)=db_fetch_row($res2)){
			echo " " . $property . ": " . $val . ";";
		}
	  ?>  
        '><?php echo $name;?></div>
        </center>
      <?php	
		}
	  ?>
    </td>
  </tr>
  <?php
	}
  ?>
</table>