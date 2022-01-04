<?php
/*
Template Name: ost-emailtemp
*/
?>
<style>.form_newticket_subject{ border: 2px solid #ccc !important;border-radius: 7px;width: 400px;}</style>
<?php require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/includes/udscript.php' ); ?>
<div class="key4ce_wrap">
<div class="key4ce_headtitle"><?php echo __("osTicket Email Templates", 'key4ce-osticket-bridge'); ?></div>
<div style="clear: both"></div>
<?php require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/header_nav.php' ); ?>
<div id="key4ce_tboxwh" class="key4ce_pg1"><?php echo __("The \"Email Templates\" sent from site to client: new tickets confirmations, user post confirmations & admin post replies.", 'key4ce-osticket-bridge'); ?><br /><?php echo __("Note: Each input field will have a suggested default template, you can place any text & use the variables listed below.", 'key4ce-osticket-bridge'); ?></div>
<div style="clear: both"></div>
<div class="key4ce_InfoLine">
	<b><?php echo __("\$Variables You can use In Email", 'key4ce-osticket-bridge'); ?> &darr;</b>
</div>
<div id="key4ce_tboxyl" class="key4ce_pg1">
<?php echo __("\$username=\"User Name\"", 'key4ce-osticket-bridge'); ?>&nbsp;|&nbsp;
<?php echo __("\$usermail=\"User Email\"", 'key4ce-osticket-bridge'); ?>&nbsp;|&nbsp;
<?php echo __("\$ticketid=\"Ticket #\"", 'key4ce-osticket-bridge'); ?>&nbsp;|&nbsp;
<?php echo __("\$ticketurl=\"Ticket Url\"", 'key4ce-osticket-bridge'); ?>&nbsp;|&nbsp;
<?php echo __("\$ostitle=\"Support Title\"", 'key4ce-osticket-bridge'); ?><br>
<?php echo __("\$siteurl=\"Support Url\"", 'key4ce-osticket-bridge'); ?>&nbsp;|&nbsp;
<?php echo __("\$dname=\"Landing Page\"", 'key4ce-osticket-bridge'); ?>&nbsp;|&nbsp;
<?php echo __("\$user_message=\"Users Message\"", 'key4ce-osticket-bridge'); ?>&nbsp;|&nbsp;
<?php echo __("\$admin_response=\"Staff Message\"", 'key4ce-osticket-bridge'); ?>&nbsp;|&nbsp;
<?php echo __("\$signature=\"Staff/ Department Signature\"", 'key4ce-osticket-bridge'); ?>&nbsp;|&nbsp;
<?php echo __("\$ussubject=\"Ticket subject\"", 'key4ce-osticket-bridge'); ?>
</div>
<div style="clear: both"></div>
<?php 
require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/db-settings.php' ); 
?>
<!--New Ticket Email Template-->
<div class="key4ce_InfoLine">
    <b>*<?php echo __("Admin Create Ticket Email Template", 'key4ce-osticket-bridge'); ?> &raquo;</b>
    <a href="javascript:doMenu('main2');" id="xmain2">[<?php echo __("Open", 'key4ce-osticket-bridge'); ?>]</a>
    <span><?php echo __("We suggest using this template in the admin create ticket email below.", 'key4ce-osticket-bridge'); ?></span>
</div>

<div style="clear: both"></div>

<div id="main2" style="display: none;">

<div id="key4ce_tboxgr" class="key4ce_pg1">
    Hello $username,<br /><br />Your Ticket ID #$ticketid has been created successfully.<br />
    Ticket ID # : $ticketid<br />
    <br />----------------------<br />
    Email: $usermail<br />
    Priority: $priordesc<br />
    Department: $dept_name<br />
    Subject: $ussubject<br />
    <br />----------------------<br />
    Message: $user_message<br />
    <br />----------------------<br />
    To respond to the ticket, please login to the support ticket system.
    <br /><br />
    $siteurl
    <br />
    Your friendly Customer Support System
</div>
</div>

<div style="clear: both"></div>

<div class="key4ce_InfoLine">
    <b>*<?php echo __("Admin Create Ticket Email", 'key4ce-osticket-bridge'); ?> &raquo; </b>
    <a href="javascript:doMenu('main3');" id="xmain3">[<?php echo __("Close", 'key4ce-osticket-bridge'); ?>]</a>
</div>

<div style="clear: both"></div>

<div id="main3" style="display: block;">
    <form name="ost-admin-new-ticket" action="admin.php?page=ost-emailtemp" method="post">
    
        <div class="key4ce_InfoLine">
        
        <b>Admin Create Ticket Email Template Subject:</b>
        
        <input type="text" name="form_adminnewticket_subject" id="form_adminnewticket_subject" value="<?php echo $adminnewticket_subject = (empty($adminnewticket_subject)) ? '' :$adminnewticket_subject;?>" class="form_newticket_subject"></div>    
        <textarea class="key4ce_textarea" name="form_adminnewticket" rows="18" id="emailtemp" value=""><?php echo $adminnewticket_text; ?></textarea>
        
        <div style="clear: both"></div>
        
        <input type="submit" name="ost-admin-new-ticket" class="key4ce_button-primary button button-primary marLeft" value="<?php echo __("Save - Admin Create Ticket Email", 'key4ce-osticket-bridge'); ?>" />
    
    </form>
</div>


<!--Admin Ticket Reply Email Template-->
<div class="key4ce_InfoLine">
    <b>*<?php echo __("Admin Response Email Template", 'key4ce-osticket-bridge'); ?></b> &raquo 
    <a href="javascript:doMenu('main4');" id="xmain4">[<?php echo __("Open", 'key4ce-osticket-bridge'); ?>]</a>
    <span><?php echo __("We suggest using this template in your post reply to user below.", 'key4ce-osticket-bridge'); ?></span>
</div>

<div style="clear: both"></div>

<div id="main4" style="display: none;">
    <div id="key4ce_tboxgr" class="pg1">Hello $username,<br />A staff member has updated your Ticket ID #$ticketid with the following response:<br /><br />&raquo;&raquo; $admin_response<br /><br />We hope this response has sufficiently answered your questions. If you wish to send additional comments or information regarding this issue, please don't open a new ticket. Simply login to your account and update this ticket.<br /><br />View Ticket: $ticketurl<br /><br />Best Regards,<br /><?php echo $title_name; ?> - <?php bloginfo( 'name' ); ?><br /><br />$signature</div>
</div>

<div style="clear: both"></div>


<div class="key4ce_InfoLine">
    <b>*<?php echo __("Admin Response Email", 'key4ce-osticket-bridge'); ?></b> &raquo; 
    <a href="javascript:doMenu('main5');" id="xmain5">[<?php echo __("Close", 'key4ce-osticket-bridge'); ?>]</a>
</div>

<div style="clear: both"></div>

<div id="main5" style="display: block;">

    <form name="ost-admin-reply" action="admin.php?page=ost-emailtemp" method="post">
		<div class="key4ce_InfoLine"><?php echo $form_admintreply_subject; ?></div>
        
        <div class="key4ce_InfoLine">
            <b>Admin Response Template Subject :  </b>
            <input type="text" name="form_admintreply_subject" id="form_admintreply_subject" value="<?php echo $form_admintreply_subject = (empty($form_admintreply_subject)) ? '' :$form_admintreply_subject;?>" class="form_newticket_subject">
        </div>    
        
        <textarea class="key4ce_textarea" name="form_admintreply" rows="16" id="emailtemp" value=""><?php echo $adminreply; ?></textarea>
        
        <div style="clear: both"></div>
        
        <input type="submit" name="ost-admin-reply" class="key4ce_button-primary button button-primary marLeft" value="Save - Admin Response Email" />
    </form>
</div>

<!--Post Confirmation Email Template-->
<div class="key4ce_InfoLine">
    <b>*<?php echo __("User Post Confirmation Email Template", 'key4ce-osticket-bridge'); ?></b> &raquo 
    <a href="javascript:doMenu('main6');" id="xmain6">[<?php echo __("Open", 'key4ce-osticket-bridge'); ?>]</a>
    <span><?php echo __("We suggest using this template in the user confirmation email below.", 'key4ce-osticket-bridge'); ?></span>
</div>

<div style="clear: both"></div>

<div id="main6" style="display: none;">
    <div id="key4ce_tboxgr" class="key4ce_pg1">Hello $username,<br /><br />We have received your post reply for: Ticket ID #$ticketid<br />A representative will follow-up with you as soon as possible, if needed.<br /><br />&raquo;&raquo; $user_message<br /><br />You can view this ticket's progress online here:<br />$ticketurl<br /><br />If you wish to send additional comments or information regarding this issue, please don't open a new ticket. Simply login using the link above and update this ticket.<br /><br />Best Regards,<br /><?php echo $title_name; ?> - <?php bloginfo( 'name' ); ?><br /><br />$signature</div>
</div>

<div style="clear: both"></div>

<div class="key4ce_InfoLine">
    <b>*<?php echo __("User Post Confirmation Email", 'key4ce-osticket-bridge'); ?></b> &raquo; 
    <a href="javascript:doMenu('main7');" id="xmain7">[<?php echo __("Close", 'key4ce-osticket-bridge'); ?>]</a>
</div>

<div style="clear: both"></div>

<div id="main7" style="display: block;">
    <form name="ost-post-confirmed" action="admin.php?page=ost-emailtemp" method="post">
    
    <div class="key4ce_InfoLine">
        <b>User Post Confirmation Template Subject :  </b>
        <input type="text" name="form_postconfirmed_subject" id="form_postconfirmed_subject" value="<?php echo $form_postconfirmed_subject = (empty($form_postconfirmed_subject)) ? '' :$form_postconfirmed_subject;?>" class="form_newticket_subject">
    </div> 
    
    <textarea class="key4ce_textarea" name="form_postconfirmed" rows="18" id="emailtemp" value=""><?php echo $postconfirm; ?></textarea>
    
    <div style="clear: both"></div>
    
    <input type="submit" name="ost-post-confirmed" class="key4ce_button-primary button button-primary marLeft" value="<?php echo __("Save - User Post Confirmation Email", 'key4ce-osticket-bridge'); ?>" />
    </form>
</div>
<div style="padding-top:40px;"></div>
<div style="clear: both"></div>
</div><!--End wrap-->