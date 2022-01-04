<?php
/*
Template Name: ost-licensekey
*/
require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/includes/udscript.php' ); ?>
<script type='text/javascript'>

function formValidator(){
	// Make quick references to our fields
	var key4ce_licensekey = document.getElementById('key4ce_licensekey');
	var key4ce_licenseemail = document.getElementById('key4ce_licenseemail');
	// Check each input in the order that it appears in the form!
	if(notEmpty(key4ce_licensekey, "Please enter license key")){
					if(notEmpty(key4ce_licenseemail,"Please enter email address")){
						if(emailValidator(key4ce_licenseemail, "Please enter a valid email address")){
							return true;
						}
					}
	}
	return false;
}
function notEmpty(elem, helperMsg){
	if(elem.value.length == 0){
		alert(helperMsg);
		elem.focus(); // set the focus to this input
		return false;
	}
	return true;
}
function emailValidator(elem, helperMsg){
	var emailExp = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
	if(elem.value.match(emailExp)){
		return true;
	}else{
		alert(helperMsg);
		elem.focus();
		return false;
	}
}
</script>
<div class="key4ce_wrap">
<div class="key4ce_headtitle"><?php echo __("osTicket Settings", 'key4ce-osticket-bridge'); ?></div>
<div style="clear: both"></div>
<?php require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/header_nav.php' );
if($key4ce_license_status!='activated')
{
?>

<div id="key4ce_tboxwh" class="key4ce_pg1"><?php echo __("To activate the plugins Pro features such as: <br> <u><li>osTicket Knowledge base sync</li><li>Departments selection per Wordpress site</li><li>Professional support</li><li>And more to come soon..</li></u> Visit <a href=\"https://www.dev4ce.com/\">https://www.dev4ce.com</a> and get your license for 25 euro's to continue supporting this plugn! <br/><br/>Please configure following setting to activate license key.", 'key4ce-osticket-bridge'); ?></div>
<?php } else { ?>
<div id="key4ce_tboxwh" class="key4ce_pg1"><?php echo __("Thank you for supporting this plugin! It is appriciated very much by our team.", 'key4ce-osticket-bridge'); ?></div>
<?php } ?>
<div style="clear: both"></div>
<?php 
require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/db-settings.php' );
$key4celicensekey_data=explode("|",$key4celicensekey);
$key4ce_license_key=$key4celicensekey_data[0];
$key4ce_license_email=$key4celicensekey_data[1];
$key4ce_license_status=$key4celicensekey_data[2];
$key4ce_license_expirydate=$key4celicensekey_data[3];
$key4ce_license_instance=$key4celicensekey_data[4];
if($key4ce_license_status=='activated')
{
	$status="Activated";
}
else if($key4ce_license_status=='error103')
{
	$status="Exceeded maximum number of activations";
}
else if($key4ce_license_status=='error101')
{
	$status="Invalid License Key";
}
else if($key4ce_license_status=='reset')
{
	$status="Deactive";
}
if($key4ce_license_expirydate!="")
{
	$expirydate=date("d-m-Y",$key4ce_license_expirydate);
}
else
{
	$expirydate="No Expiry Date";
}
	
	
?>
<form name="ost-licensekey" action="admin.php?page=ost-licensekey" method="post" onsubmit='return formValidator()' >
<ul class="key4ce_cofigtb key4ce_respForm">
    
    <li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Key4ce License Key:", 'key4ce-osticket-bridge'); ?></label></div>
        <div class="key4ce_inputTextWrap"><input type="text" name="key4ce_licensekey" id="key4ce_licensekey" size="20" value="<?php echo $key4ce_license_key; ?>"/>&nbsp;&nbsp;<?php echo __("( Key4ce License Key Goes Here )", 'key4ce-osticket-bridge'); ?></td>
    </li>
    <li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Email Address:", 'key4ce-osticket-bridge'); ?></label></div>
        <div class="key4ce_inputTextWrap"><input type="text" name="key4ce_licenseemail" id="key4ce_licenseemail" size="20" value="<?php echo $key4ce_license_email; ?>"/>&nbsp;&nbsp;<?php echo __("( Enter email while purchased license key )", 'key4ce-osticket-bridge'); ?></td>
    </li>
    <li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("License Status:", 'key4ce-osticket-bridge'); ?></label></div>
        <div class="key4ce_inputTextWrap"><span style="background-color: <?php echo $status=="Activated"?"green":"red"; ?>;color: #fff;font-weight: bold;padding: 5px;font-size: 14px;"><?php echo $status; ?></span></div>
    </li>
	<li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Expiry Date:", 'key4ce-osticket-bridge'); ?></label></div>
        <div class="key4ce_inputTextWrap"><span style="font-size: 15px;font-weight: bold;"><?php echo $expirydate; ?></span></div>
    </li>
</ul>
<input type="hidden" name="key4ce_instance" value="<?php echo $key4ce_license_instance;?>" />
<?php if($key4ce_license_status=="activated") { ?>
<div style="padding-left: 10px;">
    <input type="submit" name="ost-licensekey" class="key4ce_button-primary button button-primary" value="<?php echo __("Deactive License", 'key4ce-osticket-bridge'); ?>" />
</div>
<?php } else { ?>
<div style="padding-left: 10px;">
    <input type="submit" name="ost-licensekey" class="key4ce_button-primary button button-primary" value="<?php echo __("Active License", 'key4ce-osticket-bridge'); ?>" />
</div>
<?php 
}
?>
</form>
</div><!--End wrap-->