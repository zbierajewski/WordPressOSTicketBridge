<?php/*Template Name: header_nav*/$config = get_option('os_ticket_config');extract($config);$ost_wpdb = new wpdb($username, $password, $database, $host);$license_data=$ost_wpdb->get_var("SELECT value FROM ".$keyost_prefix."config WHERE `key` LIKE 'key4celicensekey'");$l_ary=explode("|",$license_data);$license_activate=$l_ary[2];echo '<link rel="stylesheet" type="text/css" media="all" href="'.plugin_dir_url(__FILE__).'../css/stylesheet.css">';$parurl=$_SERVER['QUERY_STRING'];if($parurl=="page=ost-config") { $active1="active"; } if($parurl=="page=ost-settings") { $active2="active"; }if($parurl=="page=ost-emailtemp") { $active3="active"; }if($parurl=="page=ost-tickets" || $parurl=="page=ost-tickets&service=list&status=open" || $parurl=="page=ost-tickets&service=list&status=closed") { $active4="active"; }if($license_activate=="activated"){	if($parurl=="page=ost-kb") { $active5="active"; }	if($parurl=="page=ost-departments") { $active6="active"; }}if($parurl=="page=ost-licensekey") { $active7="active"; }?><div style="padding-top:0px;"></div><ul class="key4ce_adostmenu">  <li><a href="admin.php?page=ost-config" class="<?php echo $active1; ?>"><span><?php echo __("Data Config", 'key4ce-osticket-bridge'); ?></span></a></li>  <li><a href="admin.php?page=ost-settings" class="<?php echo $active2; ?>"><span><?php echo __("osT-Settings", 'key4ce-osticket-bridge'); ?></span></a></li>  <li><a href="admin.php?page=ost-emailtemp" class="<?php echo $active3; ?>"><span><?php echo __("Email Templates", 'key4ce-osticket-bridge'); ?></span></a></li>  <li><a href="admin.php?page=ost-tickets" class="<?php echo $active4; ?>"><span><?php echo __("Support Tickets", 'key4ce-osticket-bridge'); ?></span></a></li>    <?php if($license_activate=="activated") { ?>  <li><a href="admin.php?page=ost-kb" class="<?php echo $active5; ?>"><span><?php echo __("Knowledge Base Synchronous", 'key4ce-osticket-bridge'); ?></span></a></li>  <li><a href="admin.php?page=ost-departments" class="<?php echo $active6; ?>"><span><?php echo __("Departments", 'key4ce-osticket-bridge'); ?></span></a></li>  <?php } ?>  <li><a href="admin.php?page=ost-licensekey" class="<?php echo $active7; ?>"><span><?php echo __("Key4ce License Key", 'key4ce-osticket-bridge'); ?></span></a></li></ul><div style="padding-bottom:15px;"></div><?phpif($parurl=="page=ost-tickets") { if (($adminreply=="") || ($postconfirm=="")  || ($adminnewticket=="")){    echo '<div id="warning"><b>Warning:</b>'.__('1 or more of your email templates is not setup', 'key4ce-osticket-bridge').'&nbsp;&raquo;&nbsp;<a href="admin.php?page=ost-emailtemp">'.__('Click Here', 'key4ce-osticket-bridge').'</a></div>';    }}?>