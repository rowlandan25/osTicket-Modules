<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');
if(!($maxfileuploads=ini_get('max_file_uploads')))
    $maxfileuploads=DEFAULT_MAX_FILE_UPLOADS;
?>
<h2>Module Settings and Options</h2>
<form action="settings.php?t=modules" method="post" id="save">
<?php csrf_token(); ?>
<?php 
  $build = 1003;
  $ver = 'v1.9.2-1.003 (alpha)';
?>

<input type="hidden" name="t" value="modules" >
<input type='hidden' name='curbuild' value='<?php echo $build;?>'/>
<input type='hidden' name='curver' value='<?php echo $ver;?>'/>
<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4>Ticket Status Module</h4>
                <em>Create a custom list of statuses and change how they are displayed on the ticket queue page.</em>
            </th>
        </tr>
    </thead>
    <tbody
		
        <?php 
		  if($cfg->exists('mod_status_init') && 1 == 1){
		    if($build > $cfg->get('mod_status_sysbuild')){
		?>
         <tr><td width="220" class='required'>Update Required</td>
         	<td><input type='checkbox' name='update_status' required/> Update to <font color='#0000FF' style='font-weight:bold;'><?php echo $ver;?></font></td>
         </tr>
        <?php
		      $disabled = true;
			}
		  ?>
          </td>
        </tr>
    	<tr><td width="220">Module Version</td>
        	<td>
            	<font color='#990000'><?php echo $config['mod_status_version'];?></font>
            </td>
        </tr>
 
        <tr><td class="required">Enable Module</td>
            <td>
                <input type="checkbox" name="enable_ticket_status" <?php
                echo $config['mod_status_enabled']?'checked="checked"':''; ?> <?php if($disabled)echo "disabled";?>>
            </td>
        </tr>
        <tr>
        	<td>Default Status</td>
            <td>
                <select <?php if(!$cfg->exists('mod_status_enabled')|| $disabled){ echo "disabled";}?> name="default_ticket_status" <?php
                echo $config['mod_status_display']?'checked="checked"':''; ?>>
                <?php
				  $defstat = $cfg->get('mod_status_default_status');
				  $sql = "SELECT id, statusName FROM ".MOD_STATUS." WHERE active=1";
				  $res = db_query($sql);
  				  while(list($id, $status)=db_fetch_row($res)){
				?>
                	  <option value='<?php echo $id;?>' <?php if($id == $defstat) echo "selected='selected'";?>><?php echo $status;?></option>
                <?php
				  }
				?>
                </select>
                Which status should be the default status?
            </td>
        </tr>
        <tr>
        	<td>Active Shape</td>
            <td>
                <select <?php if(!$cfg->exists('mod_status_enabled')|| $disabled){ echo "disabled";}?> name="default_shape" <?php
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
                Which shape should be used to display the ticket status?
            </td>
        </tr>
        <tr>
        	<td>Display Option</td>
            <td>
            	<?php
                	$display = $cfg->get('mod_status_display_text');
				?>
                <select <?php if(!$cfg->exists('mod_status_enabled')|| $disabled){ echo "disabled";}?> name="display_option">
                  <option value="0" <?php if(0 == $display) echo "selected='selected'";?>>Title Only</option>
                  <option value="1" <?php if(1 == $display) echo "selected='selected'";?>>In-Shape Text</option>
                  <option value="2" <?php if(2 == $display) echo "selected='selected'";?>>Both</option>
                </select>
                How should we display information - Title (hover over the shape), In-Shape Text (Text displaying the status), or both?
            </td>
        </tr>
        <?php }else{?>
            	<tr><td width="220" class='required'>Initialize Module</td>
            <td>
                <input type="checkbox" name="init_ticket_status"> Initialize Now
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<p style="padding-left:250px;">
    <input class="button" type="submit" name="submit" value="Save Changes">
    <input class="button" type="reset" name="reset" value="Reset Changes">
</p>
</form>
