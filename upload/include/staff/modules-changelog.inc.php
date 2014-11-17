<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');
?>

<h2>Module Change Logs</h2>

<table class="form_table settings_table" width="900" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><font style='color:#009;'>v1.9.4-1008 (alpha)</font></h4>
                <em>Upgraded to osTicket v1.9.4.  This release starts the process to modify as few osTicket files as possible, moving those changes instead into new files more easily integrated into future osTicket releases.</em>
            </th>
        </tr>
    </thead>
    <tbody>
      <tr><th width='220'>osTicket Version</th><td>1.9.4</td></tr>
      <tr><th>Release Date</th><td>2014-11-04</td></tr>
      <tr><th>Module Builds</th><td>
        <ul style='list-style:none; font-size: 12px; margin: 0; padding: 0; padding-left: 5px;'>
            <li>Status: <?php echo getPackageBuild('status');?></li>
            <li>Status Actions: <?php echo getPackageBuild('status_actions');?></li>
        </ul>
      </td></tr>
      <tr><th>Release Type</th><td>Alpha</td></tr>
      <tr><th>Developers</th><td>Andrew Rowland</td></tr>
      <tr><th>Contributors</th><td></td></tr>
    </tbody>
</table>
<h5>Affected Modules</h5>
<em>Ticket Status</em>
<p>The ticket status module has been upgraded to utilize osTicket's new status system.  The module connects with osTicket's status list and expands on its capabilities by adding colors and shapes.</p>
<em>Status Actions</em>
<p>The status actions module has been pulled out of the ticket status module and is now seperate.  This means that if you do not want or need the color/shape module enabled, you don't need to use it.  The status actions module allows a ticket's status to change automatically based upon certain conditions - such as when a ticket is opened, assigned, or missed a due date.</p>

<h5>Modified Files</h5>
This is the list of files modified by this version.  Please note, I will not go into details on what I changed in them (as it would be chaotic to list every change).  Also note, a file name with a <sup>*</sup> denotes a new file.

<ul>
  <li>Main Directory
    <ul>
      <li>include
        <ul>
          <li>client
            <ul>
              <li></li>
            </ul>
          </li>
          <li>staff
            <ul>
              <li>settings-modules.inc.php<sup>*</sup></li>
              <li>modules.inc.php<sup>*</sup></li>
              <li>modules-changelog.inc.php<sup>*</sup></li>
              <li>modules-shape.inc.php<sup>*</sup></li>
              <li>modules-modstat.inc.php<sup>*</sup></li>
              <li>tickets.inc.php</li>
            </ul>
          </li>
          <li>class.nav.php</li>
          <li>class.ticket.php</li>
        </ul>
      </li>
      <li>scp
        <ul>
          <li>admin.inc.php</li>
          <li>module.php</li>
          <li>settings.php</li>
          <li>tickets.php</li>
        </ul>
      </li>
    </ul>
  </li>
</ul>

<h5>Orphaned Files</h5>
<p>The following files have been orphaned as of Build 1008 and can be removed after upgrading:</p>
<ul>
  <li>includes\staff\modules-editobject.inc.php</li>
  <li>includes\staff\modules-editstatus.inc.php</li>
  <li>includes\staff\modules-list.inc.php</li>
  <li>includes\staff\modules-modlist.inc.php</li>
  <li>includes\staff\modules-newobject.inc.php</li>
  <li>includes\staff\modules-newstatus.inc.php</li>
  <li>includes\staff\modules-objects.inc.php</li>
</ul>

<h5>Major Changes</h5>
<ul>
  <li>Upgraded the code base to osTicket v1.9.4</li>
  <li>Moved upgrade files & operations out of class.config.php into modules.inc.php</li>
  <li>Removed SQL Table Definitions out of bootstrap.php into modules.inc.php</li>
  <li>Integrated Status module with osTicket's built in Statuses part of v1.9.4</li>
</ul>

<h5>Bug Fixes</h5>
<ul>
  <li>GitHub Issue #5 - Corrected an issue where ticket would not appear to update</li>
  <li>GitHub Issue #8 - Corrected an issue where changing the sort would change the status a ticket showed</li>
</ul>
<hr />

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

<table class="form_table settings_table" width="900" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><font style='color:#900;'>v1.9.3-1007 (alpha)</font></h4>
                <em>Expanded Status Assignments</em>
            </th>
        </tr>
    </thead>
    <tbody>
      <tr><th width='220'>osTicket Version</th><td>1.9.3</td></tr>
      <tr><th>Module Pack Build</th><td>1007</td></tr>
      <tr><th>Release Date</th><td>2014-08-21</td></tr>
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
          <li>client
            <ul>
              <li>view.inc.php</li>
            </ul>
          </li>
          <li>staff
            <ul>
              <li>tickets-view.inc.php</li>
            </ul>
          </li>
          <li>class.ticket.php</li>
        </ul>
      </li>
    </ul>
  </li>
</ul>

<h5>Major Changes</h5>
<ul>
  <li>Change status when Posting A Reply or Internal Note</li>
</ul>

<h5>Bug Fixes</h5>
<ul>
  <li>None</li>
</ul>
<hr />

<table class="form_table settings_table" width="900" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><font style='color:#900;'>v1.9.3-1006 (alpha)</font></h4>
                <em>Added client side functionality</em>
            </th>
        </tr>
    </thead>
    <tbody>
      <tr><th width='220'>osTicket Version</th><td>1.9.3</td></tr>
      <tr><th>Module Pack Build</th><td>1006</td></tr>
      <tr><th>Release Date</th><td></td></tr>
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
          <li>client
            <ul>
              <li>tickets.inc.php</li>
              <li>view.inc.php</li>
            </ul>
          </li>
          <li>staff
            <ul>
              <li>settings-modules.inc.php</li>
              <li>tickets.inc.php</li>
            </ul>
          </li>
          <li>class.config.php</li>
          <li>class.ticket.php</li>
        </ul>
      </li>
      <li>bootstrap.php</li>
    </ul>
  </li>
</ul>

<h5>Major Changes</h5>
<ul>
  <li>Client can now view the ticket status when they look at their ticket (GitHub Request #2)</li>
  <li>Ticket Actions: When an action is performed in a ticket (closed, re-opened and assigned), a status can automatically be assigned.</li>
</ul>

<h5>Bug Fixes</h5>
<ul>
  <li>GitHub Issue (1): Display option does not change how the status is displayed in the ticket queue.</li>
</ul>
<hr />

<table class="form_table settings_table" width="900" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><font style='color:#900;'>v1.9.3-1005 (alpha)</font></h4>
                <em>Fixed a bug causing the object property table from being created.</em>
            </th>
        </tr>
    </thead>
    <tbody>
      <tr><th width='220'>osTicket Version</th><td>1.9.3</td></tr>
      <tr><th>Module Pack Build</th><td>1005</td></tr>
      <tr><th>Release Date</th><td>2014-08-14</td></tr>
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
          <li>class.config.php</li>
        </ul>
      </li>
      <li>bootstrap.php</li>
    </ul>
  </li>
</ul>

<h5>Bug Fixes</h5>
<ul>
  <li>GitHub Issue (3, 4): Fixed an issue causing the Object Properties database not to be created, causing issues when attempting to create objects and assign properties.</li>
</ul>
<hr />

<table class="form_table settings_table" width="900" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><font style='color:#900;'>v1.9.3-1004 (alpha)</font></h4>
                <em>Update build 1003 to be compatible with osTicket 1.9.3</em>
            </th>
        </tr>
    </thead>
    <tbody>
      <tr><th width='120'>osTicket Version</th><td>1.9.3</td></tr>
      <tr><th>Module Pack Build</th><td>1004</td></tr>
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

<h5>Misc Cleanup</h5>
While converting the code to osTicket v1.9.3, I cleaned up some no longer used code that was implented while testing some stuff (such as echo's of sql statements) as well as some code used to test various other mod ideas.
<hr />

<table class="form_table settings_table" width="900" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><font style='color:#900;'>v1.9.2-1003 (alpha)</font></h4>
                <em>First release for osTicket 1.9.2</em>
            </th>
        </tr>
    </thead>
    <tbody>
      <tr><th width='220'>osTicket Version</th><td>1.9.2</td></tr>
      <tr><th>Module Pack Build</th><td>1003</td></tr>
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