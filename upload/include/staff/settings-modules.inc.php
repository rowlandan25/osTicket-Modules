<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');
if(!($maxfileuploads=ini_get('max_file_uploads')))
  $maxfileuploads=DEFAULT_MAX_FILE_UPLOADS;
?>
<script language="javascript">
  function confirmInit(module){
	  var ans = confirm("Before this module ("+module+") is initialized, you should backup your database.  Should we continue?");
	  if(ans==1){
		 document.getElementById(module).submit();
	  }
  }
</script>

<h2>Module Settings and Options</h2>
<form action="settings.php?t=modules" method="post" id="Status Module">
<?php csrf_token(); ?>
<input type="hidden" name="t" value="modules" >
<input type="hidden" name="optm" value="1142" >
<input type='hidden' name='module' value='status' />
<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
  <thead>
	  <tr>
		  <th colspan="2">
			  <h4>Ticket Status Module</h4>
			  <em>Ticket statuses are now part of the base osTicket install.  This module expands on the ticket status, adding shapes and colors to view the status in the ticket queue.</em>
		  </th>
	  </tr>
  </thead>
  <tbody>
	<tr><td width="220">Package Build </td><td><?php echo getPackageBuild('status');?></td></tr>
<?php
/****************************
  Check if Package has
  been previously
  initialized.
****************************/
  if($cfg->exists('mod_status_init')){
	  /****************************
		  Check if the system
		  needs to update
	  ****************************/
	  if(getPackageBuild('status') > $cfg->get('mod_status_sysbuild')){
?>
		  <tr><td width="220" class='required'>Upgrade Required</td>
			  <td><input type='checkbox' name='upgrade' onclick="confirmInit('Status Module');"/> Upgrade to <font color='#0000FF' style='font-weight:bold;'><?php echo getPackageBuild('status');?></font></td>
		  </tr>
<?php
		  $disabled = true;
	  }else{
?>
	  <tr><td width="220">System Version</td><td><font color='#990000'><?php echo $config['mod_status_version'];?></font></td></tr>
	  <tr><td class="required">Enable Module</td><td><input type="checkbox" name="enable" <?php echo $cfg->get('mod_status_enabled')?'checked="checked"':''; ?> <?php if($disabled)echo "disabled";?>></td></tr>
	  <tr><td>Active Shape</td><td>
		  <select <?php if(!$cfg->exists('mod_status_enabled')|| $disabled){ echo "disabled";}?> name="active_shape" <?php
		  echo $config['mod_status_display']?'checked="checked"':''; ?>>
		  <?php
			$defshape = $cfg->get('mod_status_default_shape');
			$sql = "SELECT id, objectName FROM ".MOD_STATUS_OBJECT;
			$res = db_query($sql);
			while(list($id, $object)=db_fetch_row($res)){
		  ?>
				<option value='<?php echo $id;?>' <?php if($id == $defshape) echo "selected='selected'";?>><?php echo $object;?></option>
		  <?php
			}
		  ?>
		  </select>
		  <img src='images/icons/kb.gif' title="Which shape should be used to display the ticket status?"/>
	  </td></tr>
	  <tr><td>Display Option</td><td>
			  <?php
				  $display = $cfg->get('mod_status_display_text');
			  ?>
			  <select <?php if($disabled){ echo "disabled";}?> name="display_option">
              	<option value="0" <?php if(0 == $display) echo "selected='selected'";?>>Both In-Shape Text & Title</option>
				<option value="1" <?php if(1 == $display) echo "selected='selected'";?>>Title Only</option>
				<option value="2" <?php if(2 == $display) echo "selected='selected'";?>>In-Shape Text</option>
			  </select>
			  <img src='images/icons/kb.gif' title="How should we display information - Title (hover over the shape), In-Shape Text (Text displaying the status), or both?"/>
	  </td></tr>
      <tr><td>Show Column in Queue</td><td><input type='checkbox' name="columnview" <?php echo $cfg->get('mod_status_show_column')?'checked="checked"':''; ?> <?php if($disabled)echo "disabled";?>></td></tr>
	  <tr><th colspan='2'><em>This is purely informational.  These values are not able to be changed unless done manually in the database.</em></th></tr>
	  <tr><td>Foreground Color Field</td><td><?php echo $cfg->get('mod_status_bgcolor');?></td></tr>
	  <tr><td>Background Color Field</td><td><?php echo $cfg->get('mod_status_fgcolor');?></td></tr>
	  <tr><td>Shape Border Color Field</td><td><?php echo $cfg->get('mod_status_bdcolor');?></td></tr>
  </tbody>
</table>
<p style="padding-left:250px;">
  <input class="button" type="submit" name="submit" value="Save Changes">
  <input class="button" type="reset" name="reset" value="Reset Changes">
</p>
<?php
	  }
}else{
?>
	<tr><td colspan='2' style='text-align:center'><input type="checkbox" name="initialize" onclick="confirmInit('Status Module');"> Initialize Module Package Now</td></tr>
  </tbody>
</table>
<?php 
}
?>
</form>

<form action="settings.php?t=modules" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="t" value="modules" >
<input type="hidden" name="optm" value="1142" >
<input type='hidden' name='module' value='status_actions' />
<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
  <thead>
	  <tr>
		  <th colspan="3">
			  <h4>Ticket Status Module - Custom Actions</h4>
			  <em>Change the ticket status based on actions performed throughout the system.</em>
		  </th>
	  </tr>
  </thead>
  <tbody>
  <tr><td width="220">Package Build </td><td><?php echo getPackageBuild('status_actions');?></td></tr>
<?php
/****************************
  Check if Package has
  been previously
  initialized.
****************************/
  if($cfg->exists('mod_status_actions_init')){
	  /****************************
		  Check if the system
		  needs to update
	  ****************************/
	  if(getPackageBuild('status_actions') > $cfg->get('mod_status_actions_sysbuild')){
	  /****************************
		  Needs to be Upgraded
	  ****************************/
?>
		  <tr><td width="220" class='required'>Upgrade Required</td>
			  <td><input type='checkbox' name='upgrade' required/> Upgrade to <font color='#0000FF' style='font-weight:bold;'><?php echo getPackageBuild('status_actions');?></font></td>
		  </tr>
<?php
		  $disabled = true;
	  }else{
?>
	  <tr><td width="220">System Version</td><td><font color='#990000'><?php echo $config['mod_status_actions_version'];?></font></td></tr>
	  <tr><td class="required">Enable Module</td><td><input type="checkbox" name="enable" <?php echo $config['mod_status_actions_enabled']?'checked="checked"':''; ?> <?php if($disabled)echo "disabled";?>></td></tr>
	  <tr><th style='text-align:center;' width='75'>Enabled</th><th style='text-align:center;'>Action Performed</th><th style='text-align:center;'>Set Status To</th></tr>
<?php
		$sql = "SELECT id, actionName, setStatus, isActive FROM ".MOD_STATUS_ACTIONS;
		$res = db_query($sql);
		while(list($actionId, $actionName, $actionStatus, $actionActive) = db_fetch_row($res)){
?>
		  <tr>
			<td style='text-align:center;'><input type='checkbox' name='action_<?php echo $actionId;?>' <?php if($actionActive) echo "checked";?> <?php if($disabled)echo "disabled";?> /></td>
			<td><?php echo $actionName;?></td>
			<td>
			  <select <?php if(!$cfg->exists('mod_status_enabled')|| $disabled){ echo "disabled";}?> name="new_status_<?php echo $actionId;?>">
				<?php
				  $sql2 = "SELECT id, statusName FROM ".MOD_STATUS." WHERE active=1";
				  $res2 = db_query($sql2);
				  while(list($id, $status)=db_fetch_row($res2)){
				?>
					  <option value='<?php echo $id;?>' <?php if($id == $actionStatus) echo "selected='selected'";?>><?php echo $status;?></option>
				<?php
				  }
				?>
			  </select>
			</td>
		  </tr>

<?php
		}
?>
  </tbody>
</table>

<p style="padding-left:250px;">
  <input class="button" type="submit" name="submit" value="Save Changes">
  <input class="button" type="reset" name="reset" value="Reset Changes">
</p>
<?php
	  }
  }else{
	  /****************************
		  Needs to be Initialized
	  ****************************/
?>
	<tr><td colspan='2' style='text-align:center'><input type="checkbox" name="initialize" disabled="disabled"> Initialize Module Package Now</td></tr>
      </tbody>
</table>
<?php 
}
?>
</form>