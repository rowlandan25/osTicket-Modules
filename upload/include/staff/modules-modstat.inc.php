<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');
?>
<h2>Status Module - Manage Statuses</h2>

<table class="form_table settings_table" width="900" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="6">
                <h4>Status List</h4>
                <em>Manage the list of statuses.</em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
          <th width='400'>Status Name</th><th width='200'>Status State</th><th width='75'>Foreground</th><th width='75'>Background</th><th width='75'>Border</th><th width='150'>Preview</th></tr>
<?php
        $prop = array();
        $fg = $cfg->get('mod_status_bgcolor');
        $bg = $cfg->get('mod_status_fgcolor');
        $bd = $cfg->get('mod_status_bdcolor');
        
        $sql = "SELECT id, name, state, properties FROM ".TICKET_STATUS_TABLE;
        $mres = db_query($sql);
        
        while(list($status, $name, $state, $var) = db_fetch_row($mres)){
            $remove = array('"', '{', '}');
            $dump = str_replace($remove, "", $var);
            $dump2 = str_replace(",", ":", $dump);
            $vardump = explode(':', $dump2);

            for($i = 0; $i<count($vardump); $i+=2){
                $prop[$vardump[$i]] = $vardump[$i+1];
            }
?>
            <tr>
              <td><?php echo $name;?></td>
              <td><?php echo $state;?></td>
              <td>&nbsp;<?php echo $prop[$fg];?>&nbsp;</td>
              <td>&nbsp;<?php echo $prop[$bg];?>&nbsp;</td>
              <td>&nbsp;<?php echo $prop[$bd];?>&nbsp;</td>
              <td>
              	<div style='<?php $sql = "SELECT propertyName, valueCurrent FROM ".MOD_STATUS_PROPERTY." WHERE objectId=".$cfg->get('mod_status_display'); $res = db_query($sql); while(list($pname, $val)=db_fetch_row($res)){ echo $pname . ": " . $val . "; ";} ?> background-color:<?php echo $prop[$bg];?>; color:<?php echo $prop[$fg];?>; border-color:<?php echo $prop[$bd];?>' <?php if($cfg->get('mod_status_display_text')!=2){echo "title='".$name."'";}?>>
                <?php if($cfg->get('mod_status_display_text')!=1){echo $name;}?>
              </div>
              </td>
            </tr>

<?php	
        }
?>
    </tbody>
</table>
    
<table class="form_table settings_table" width="900" border="0" cellspacing="0" cellpadding="2">
        <thead>
            <tr>
                <th colspan="5">
                    <h4>Objects List</h4>
                    <em>Manage your objects</em>
                </th>
            </tr>
        </thead>
        <tbody>
          <tr>
            <th width='500'>Object Name </th><th width='300'>Preview</th><th width='100'>Delete</th><th width='100'>Export</th>
          </tr>
          <?php
            $isql = "SELECT * FROM ".MOD_STATUS_OBJECT;
            $ires = db_query($isql);
            while(list($id,$name)=db_fetch_row($ires)){
          ?>
          <tr>
            <td><?php echo $name;?>&nbsp;&nbsp;[<a href='module.php?t=shape&id=<?php echo $id;?>'>Edit</a>]</td>
            <td>
              <div style='<?php $sql = "SELECT propertyName, valueCurrent FROM ".MOD_STATUS_PROPERTY." WHERE objectId=".$id; $res = db_query($sql); while(list($prop, $val)=db_fetch_row($res)){ echo $prop . ": " . $val . "; ";} ?>'>
                Test Span
              </div>
            </td>
            <td></td>
            <td></td>
          </tr>
          <?php
            }
          ?>
          <tr>
            <td colspan='4' style='text-align:center;'><a href='module.php?t=shape'>Add New Shape</a></td>
          </tr>
        </tbody>
    </table>
