<?php
@session_start();
/* Template Name: contact_ticket.php */
$config = get_option('os_ticket_config');
extract($config);
$ost_wpdb = new wpdb($username, $password, $database, $host);
global $current_user;
$config_table=$keyost_prefix."config";
$dept_table=$keyost_prefix."department";
$topic_table=$keyost_prefix."help_topic";
$ticket_table=$keyost_prefix."ticket";
$ticket_event_table=$keyost_prefix."ticket_event";
$priority_table=$keyost_prefix."ticket_priority";
$thread_table=$keyost_prefix."ticket_thread";
$ticket_cdata=$keyost_prefix."ticket__cdata";
$ost_user=$keyost_prefix."user";
$ost_staff=$keyost_prefix."staff";
$ost_useremail=$keyost_prefix."user_email";
require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/includes/versionData.php'); 
require_once(WP_PLUGIN_DIR .'/key4ce-osticket-bridge/osticket-wp.php' );
require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/templates/contact_nav_bar.php'); 
$secret = $googlesecretkey;
$siteKey=$googlesitekey;
$url = plugins_url();
$default_email_id = $ost_wpdb->get_var("SELECT value FROM " . $keyost_prefix . "config WHERE `key` LIKE 'default_email_id'");
$default_email_id_data = $ost_wpdb->get_row("SELECT name FROM " . $keyost_prefix . "email WHERE `email_id` =$default_email_id");
$title_name = $default_email_id_data->name;
if($keyost_version==1914)
{
	$dept_sql="SELECT name as dept_name,id as dept_id FROM $dept_table where ispublic=1";
}
else
{
	$dept_sql="SELECT dept_name,dept_id FROM $dept_table where ispublic=1";
}

if(checkLicense()=="activated")
{
	$display_to_value=key4ce_getKeyValue('dipslay_department_to');
	if($display_to_value!="")
	{
		if($keyost_version==1914)
		{
			$dept_sql.=" AND id IN ($display_to_value)";	
		}
		else
		{
			$dept_sql.=" AND dept_id IN ($display_to_value)";	
		}
		
	}	
}
$dept_opt = $ost_wpdb->get_results($dept_sql);
if($keyost_version==193){
$attachement_status=key4ce_getKeyValue('allow_attachments');
$max_user_file_uploads=key4ce_getKeyValue('max_user_file_uploads');if($max_user_file_uploads==""){	$max_user_file_uploads="unlimited";}else{	$max_user_file_uploads=$max_user_file_uploads;}
$max_file_size=key4ce_getKeyValue('max_file_size');
$fileextesnions=key4ce_getKeyValue('allowed_filetypes');
} else {
$fileconfig=key4ce_FileConfigValue();
$filedata=json_decode($fileconfig);
$attachement_status=$filedata->attachments;
$max_user_file_uploads=$filedata->max;if($max_user_file_uploads==""){	$max_user_file_uploads="unlimited";}else{	$max_user_file_uploads=$max_user_file_uploads;}
$max_file_size=$filedata->size;
$fileextesnions=$filedata->extensions;
}
$alowaray = explode(".",str_replace(' ', '',$fileextesnions));
$strplc = str_replace(".", "",str_replace(' ', '',$fileextesnions));
$allowedExts = explode(",", $strplc);
if(!function_exists('add_quotes')) {
	function add_quotes($str) 
	{
    return sprintf("'%s'", $str);
	}
}
$extimp = implode(',', array_map('add_quotes', $allowedExts));
$finalary = "'" . $extimp . "'";
?>
<script type="text/javascript">
var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>
<script language="javascript" src="<?php echo $url.'/key4ce-osticket-bridge/js/validate.js'; ?>"></script>
<script language="javascript" src="<?php echo plugin_dir_url(__FILE__) . '../js/jquery_1_7_2.js'; ?>"></script>
<script type="text/javascript">
    $(function() {
        var addDiv = $('#addinput');
        var i = $('#addinput p').size() + 1;
        var MaxFileInputs = '<?php echo $max_user_file_uploads; ?>';
        $('#addNew').live('click', function() {
			if(MaxFileInputs=="unlimited"){
				$('<p><span style="color:#000;"><?php echo __("Attachment", 'key4ce-osticket-bridge'); ?> ' + i + ':</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="file" id="p_new_' + i + '" name="file[]" onchange="return checkFile(this);"/>&nbsp;&nbsp;&nbsp;<a href="#" id="remNew"><?php echo __("Remove", 'key4ce-osticket-bridge'); ?></a>&nbsp;&nbsp;&nbsp;<span style="color: red;font-size: 11px;"><?php echo __("Max file upload size", 'key4ce-osticket-bridge'); ?> : <?php echo ($max_file_size * .0009765625) * .0009765625; ?>MB</span></p>').appendTo(addDiv);
				i++;
			} else {
                            if (i <= MaxFileInputs){
					$('<p><span style="color:#000;"><?php echo __("Attachment", 'key4ce-osticket-bridge'); ?> ' + i + ':</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="file" id="p_new_' + i + '" name="file[]" onchange="return checkFile(this);"/>&nbsp;&nbsp;&nbsp;<a href="#" id="remNew"><?php echo __("Remove", 'key4ce-osticket-bridge'); ?></a>&nbsp;&nbsp;&nbsp;<span style="color: red;font-size: 11px;"><?php echo __("Max file upload size", 'key4ce-osticket-bridge'); ?> : <?php echo ($max_file_size * .0009765625) * .0009765625; ?>MB</span></p>').appendTo(addDiv);
					i++;
				}else{
					alert("<?php echo __("You have exceeds your file upload limit", 'key4ce-osticket-bridge'); ?>");
					return false;
				}
			}
            return false;
        });

        $('#remNew').live('click', function() {
            if (i > 2) {
                $(this).parents('p').remove();
                i--;
            }
            return false;
        });
		$("#deptId").change(function() {		$("#key4ce_new_ticket_helptopic_loader").css("display", "block");		
		var dept_final_id=$("#deptId").val();
		var data = {
			action: 'get_helptopic_dropdown',
			dept_id: dept_final_id
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
		if(response!=""){								$("#key4ce_new_ticket_helptopic_loader").css("display", "none");
			$("#key4ce_new_ticket_helptopic").css("display", "block"); 
			$("#key4ce_new_ticket_helptopic_input").html(response);	
			$("#key4ce_new_ticket_helptopic_input").css("display", "block"); 			
		}
		else if(response==""){
                        $("#key4ce_new_ticket_helptopic_loader").css("display", "none");		
			$("#key4ce_new_ticket_helptopic").css("display", "none");
			$("#key4ce_new_ticket_helptopic_input").css("display", "none"); 
		}		
		});
        }); 
    });
</script>
<script type="text/javascript">
    function checkFile(fieldObj){
        var FileName = fieldObj.value;
        var FileId = fieldObj.id;
        var FileExt = FileName.substr(FileName.lastIndexOf('.') + 1);
        var FileSize = fieldObj.files[0].size;
        var FileSizeMB = (FileSize / 10485760).toFixed(2);
        var FileExts = new Array(<?php echo $extimp; ?>);
        if ((FileSize > <?php echo $max_file_size; ?>)){
            alert("<?php echo __("Please make sure your file is less than", 'key4ce-osticket-bridge'); ?> <?php echo ($max_file_size* .0009765625) * .0009765625; ?>MB.");
            document.getElementById(FileId).value = "";
            return false;
        }
	if(FileExts!=""){
			if (FileExts.indexOf(FileExt) < 0){
				error = "<?php echo __("Please make sure your file extension should be :", 'key4ce-osticket-bridge'); ?> \n";
				error += FileExts;
				alert(error);
				document.getElementById(FileId).value = "";
				return false;
			}
		}
        return true;
    }
</script>
<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) . '../css/contact-ticket.css'; ?>">
<?php
if(isset($_POST['g-recaptcha-response'])){
$captcha=$_POST['g-recaptcha-response'];
$response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
?>
<div class="key4ce_clear" style="padding: 5px;"></div>
<p id="key4ce_msg_notice"><?php echo __('A new request has been created successfully!','key4ce-osticket-bridge'); ?></p>
<p align="center">
<br />
 <i><?php echo __('We are currently notifying the selected department staff...','key4ce-osticket-bridge'); ?></i>
<br /><br />
<center><script language="javascript" src="<?php echo plugin_dir_url(__FILE__).'../js/timerbar.js';?>"></script></center>
<br />
<center><?php echo __('Thank you for contacting us!','key4ce-osticket-bridge'); ?></center>
</p>
<?php
} else {
		if($keyost_departmentsetting==1){
			$validationFunction="validateFormContactTicket();";
		}else{
			$validationFunction="validateFormContactSelectTicket();";
		}
?>
<form id="ContactticketForm" name="contactticket" method="post" enctype="multipart/form-data" onsubmit="return <?php echo $validationFunction; ?>">
<div id="key4ce_thContainer">
<div id="key4ce_new_ticket">
<div id="key4ce_new_ticket_text2"><?php echo __('Please fill in the form below to open a new ticket. All fields mark with [<font color=red>*</font>] <em>Are Required!','key4ce-osticket-bridge'); ?></em></div>
<div style="clear: both"></div>
            <input type="hidden" name="stitle" value="<?php echo $title_name; ?>"/>
            <input type="hidden" name="sdirna" value="<?php echo $dirname; ?>"/>
            <input type="hidden" name="newtickettemp" value="<?php echo $newticket; ?>"/>
            <input type="hidden" value="2" name="priorityId" id="priority"/>
<div class="key4ce_coltable">
<div class="key4ce_fullcol">
<div class="key4ce_leftcol">
<div class="key4ce_contact_title"><?php echo __('Full Name:','key4ce-osticket-bridge'); ?></div>
<div><input class="ost" id="cur-name" type="text" name="cur-name" style="width:100%;"></div>
</div>
<div class="key4ce_rightcol">
<div class="key4ce_contact_title"><?php echo __('Your Email:','key4ce-osticket-bridge'); ?></div>
<div><input class="ost" id="email" type="text" name="email" style="width:100%;"></div>
</div>
</div>
<div class="key4ce_fullcol">
<?php  
if($keyost_departmentsetting==1){
?>
<div class="key4ce_leftcol">
<div class="key4ce_contact_title"><?php echo __('Catagories:','key4ce-osticket-bridge'); ?><font class="key4ce_error">&nbsp;*</font></div>
<div>
<select id="deptId" name="deptId" style="width:100%;">
<option value="" selected="selected"><?php echo __('Select a Category','key4ce-osticket-bridge'); ?></option>
<?php
	foreach($dept_opt as $dept){ 
	echo '<option value="'.$dept->dept_id .'">'.$dept->dept_name .'</option>'; 
        } 
?>
</select></div>
</div>
<div class="key4ce_rightcol">
<?php }
if($keyost_helptopicsetting==1){
?>	
<!-- Help Topic Start Here --><div id="key4ce_new_ticket_helptopic_loader" style="display:none;text-align: center;width:100%;">
<img src="<?php echo plugins_url('images/key4ce_loader.gif',dirname(__FILE__)); ?>" alt="<?php echo __("Loading...", 'key4ce-osticket-bridge'); ?>"></div>
<div id="key4ce_new_ticket_helptopic" style="display:none;" class="key4ce_contact_title"><?php echo __('Help Topic','key4ce-osticket-bridge'); ?>:</div>
<div id="key4ce_new_ticket_helptopic_input" style="display:none;"></div>
<!-- Help Topic End Here -->
<?php } ?>
</div>
</div>
<div class="key4ce_fullcol">
<div class="key4ce_contact_title"><?php echo __('Subject','key4ce-osticket-bridge'); ?>:</div>
<div>
<input class="key4ce_ost" id="subject" type="text" name="subject" style="width: 100%;"></div>
</div>
</div>
</div>
<table class="key4ce_welcome key4ce_nobd" align="center" cellpadding="3" cellspacing="3" border="0">
<tr>
<td class="key4ce_nobd" align="center"><div align="center" style="padding-bottom: 5px;" class="contact_title"><?php echo __('To best assist you, please be specific and detailed in your message','key4ce-osticket-bridge'); ?><font class="key4ce_error">&nbsp;*</font></div></td>
</tr>
<tr>
<td class="key4ce_nobd" align="center">
<center> <?php
$content = @$_POST['message'];
$editor_id = 'message';
$settings = array( 'media_buttons' => false );
wp_editor( $content, $editor_id , $settings );?> </center>
<div class="key4ce_clear" style="padding: 5px;"></div></td>
</tr>
    <?php
	if($keyost_contactfilestatus==1){
            if ($attachement_status==1 || $attachement_status==true) {
                if(key4ce_getPluginValue('Attachments on the filesystem')==1){
        ?>
            <tr><td>
                    <div id="addinput">
                        <p>
                            <span style="color:#000;"><?php echo __('Attachment 1:','key4ce-osticket-bridge'); ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="file" id="p_new" name="file[]" onchange="return checkFile(this);"/>&nbsp;&nbsp;&nbsp;<a href="#" id="addNew"><?php echo __('Add','key4ce-osticket-bridge'); ?></a>&nbsp;&nbsp;&nbsp;<span style="color: red;font-size: 11px;"><?php echo __('Max file upload size :','key4ce-osticket-bridge'); ?><?php echo ($max_file_size * .0009765625) * .0009765625; ?>MB</span>
                        </p>
                    </div>
            </td></tr>
    <?php } else { ?>
	 <tr><td><?php echo __('Attachments on the Filesystem plugin can be downloaded here:','key4ce-osticket-bridge'); ?><a href="http://osticket.com/download/go?dl=plugin%2Fstorage-fs.phar" title="Attachement Filesystem Plugin" target="_blank"><?php echo __('Attachement Filesystem Plugin','key4ce-osticket-bridge'); ?></a></td></tr>
	<?php } } }
if($googlesecretkey!="" && $googlesitekey!=""){
$langary=explode("_",get_locale());
$lang=$langary[0];
?>
<script src="https://www.google.com/recaptcha/api.js?hl=<?php echo $lang; ?>"></script>
<tr><td style="text-align: center;">
<div class="g-recaptcha" data-sitekey="<?php echo $siteKey; ?>"></div>
</td>
</tr>
<?php } ?>
<tr>
<td class="key4ce_nobd" align="center">
<p align="center" style="padding-top: 5px;"><input type="submit" name="create-contact-ticket" value="<?php echo __('Submit','key4ce-osticket-bridge'); ?>">
&nbsp;&nbsp;<input type="reset" value="<?php echo __('Reset','key4ce-osticket-bridge'); ?>"></p>
</td>
</tr>
</table>
</div>
</form>
<?php } ?>
<div class="key4ce_clear" style="padding: 10px;"></div>
