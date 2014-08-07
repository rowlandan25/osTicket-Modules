<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');
?>
<h2>Module Change Logs</h2>
Current Version: <font color='#990000'><?php echo $cfg->get('mod_status_version');?></font>

<h5>Configuration Variables</h5>
It was requested at one point that I list the variables I use in the module, so those will be listed here.
<ul>
  <li>mod_status_enabled: [On|NULL] Tells the module whether it is enabled or disabled.  If disabled, no ticket status is shown and no status is assigned on ticket creation. Changed in Settings -> Modules.</li>
  <li>mod_status_display_text: [0|1|2] Tells the module which method should be used to display the status (Currently: Title Only, In-Shape Text, Both).  Changed in Settings -> Modules.</li>
  <li>mod_status_version: [version number] Holds the current version to be displayed at various locations. Not able to be changed outside of programming.</li>
  <li>mod_status_init: [on|NULL] Tells the module whether it has been initialized.  This is important as it changes the behavior of setup.  Can only be enabled once in Settings -> Modules.</li>
  <li>mod_status_default_status: [status ID] Tells the module which status should be used as the default status for new tickets.  Can be changed in Settings -> Modules.</li>
  <li>mod_status_default_shape: [object ID] Tells the module which object to display in ticket queue. Can be changed in Settings -> Modules.</li>
  <li>mod_status_sysbuild: [build number] Number to compare update versions against to see if an update is necessary. Changed at the time of an update to the module.</li>
</ul>

<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><font style='color:#009;'>v1.9.2-1.003 (alpha)</font></h4>
                <em>First release for osTicket 1.9.2.</em>
            </th>
        </tr>
    </thead>
    <tbody>
      <tr><th width='120'>osTicket Version</th><td>1.9.2</td></tr>
      <tr><th>Release Date</th><td>2014-08-07</td></tr>
      <tr><th>Release Type</th><td>Alpha</td></tr>
      <tr><th>Developers</th><td>Andrew Rowland</td></tr>
      <tr><th>Contributors</th><td></td></tr>
      <tr><th>Affected Modules</th><td><ul style='list-style:none; font-size: 10px; margin: 0; padding: 0; padding-left: 5px;'><li>Ticket Status (Shapes, Statuses & Colors)</li></ul></td></tr>
    </tbody>
</table>

<h5>Modified Files</h5>
This is the list of files modified by this version.  Please note, I will not go into details on what I changed in them (as it would be chaotic to list every change).  Also note, a file name with a <sup>*</sup> denotes a new file.

<ul>
  <li>Main Directory
    <ul>
      <li>include
        <ul>
          <li>staff
            <ul>
              <li>module-editobject.inc.php<sup>*</sup></li>
              <li>module-editstatus.inc.php<sup>*</sup></li>
              <li>module-list.inc.php<sup>*</sup></li>
              <li>module-modlist.inc.php<sup>*</sup></li>
              <li>module-newobject.inc.php<sup>*</sup></li>
              <li>module-newstatus.inc.php<sup>*</sup></li>
              <li>module-objects.inc.php<sup>*</sup></li>
              <li>setting-modules.inc.php<sup>*</sup></li>
              <li>ticket-open.inc.php</li>
              <li>ticket-view.inc.php</li>
              <li>tickets.inc.php</li>
            </ul>
          </li>
          <li>class.config.php</li>
          <li>class.nav.php</li>
          <li>class.ticket.php</li>
        </ul>
      </li>
      <li>scp
        <ul>
          <li>modules.php<sup>*</sup></li>
          <li>settings.php</li>
          <li>tickets.php</li>
        </ul>
      </li>
      <li>bootstrap.php</li>
    </ul>
  </li>
</ul>

<h5>Assigning Status</h5>
There was a pretty substantial shift on how status assignments are being handled.  This change was done to make future features easier to implement as well as seperating the module aspects from the built in features of osTicket (to hopefully prevent any issues with updating in the future, regardless of changes made by osTicket developers).

<h5>Database</h5>
<ul>
  <li>Table naming scheme was changed to $prefix_mod_'modname'_'tablename'.  This will group all mods together, and all tables for a specific mod together.</li>
  <li>A table was added to the database ($prefix_mod_status) to house all created statuses instead of using osTicket's list feature.  This will remove the need to choose which list is being used for statuses.  This table also includes the status colors (Foreground, Background and Border).</li>
  <li>The shape color table ($prefix_status_colors) was removed as it was incorporated into mod_status table</li>
  <li>The display option table ($prefix_status_display_options) was removed as the three option (Title Only, Text Only, and Both) were hardcoded into the system.  There is no need for this table as those options do not change.</li>
  <li>The objects table ($prefix_status_objects) was renamed ($prefix_mod_status_object) to match current naming scheme</li>
  <li>The properties table ($prefix_status_properties) was removed (see two notes down).</li>
  <li>The objects properties table ($prefix_status_op) was removed (see one note down).</li>
  <li>New table ($prefix_mod_status_object_properties) was created, which will merge the two properties tables from 1.8.  It will house the object's id, the property name, and current value.  An optional recommended value is also present for future use.</li>
  <li>New table created to hold assignments of ticket status.
</ul> 

<h5>Admin Panel</h5>
<ul>
  <li>New page under Settings added named Modules.  This page will allow complete access to enabling, disabling and choosing specific module settings for all installed modules, in one location.</li>
  <li>Modules -> Ticket Status
    <ul>
      <li>Renamed page to Status List</li>
      <li>Removed the need to do a setup to get the statuses to work.</li>
      <li>All statuses are listed here, with the ability to add a new status or edit an existing status.</li>
      <li>Added page to allow for creating and editing existing statuses, including the assignment of colors.</li>
      <li>When creating or editing a status, colors are chosen at that point rather than having to go to the Status Colors page.</li>
    </ul>
  </li>
  <li>Modules -> Status Colors
    <ul>
      <li>Renamed page to Status Objects</li>
      <li>Removed individual assignment for display options on shapes.  The site default is assigned on the Settings -> Module page.</li>
      <li>Removed the ability to add shape properties.</li>
      <li>New page added to edit a specific shape, which allows for the entry of individual properties assigned only to that shape.</li>
      <li>Status colors are no longer assigned on this page.</li>
    </ul>
  </li>
</ul>

<hr />