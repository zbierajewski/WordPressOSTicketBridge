<?php
/* Template Name: view_ticket.php */
if (is_user_logged_in()){
global $current_user;
get_currentuserinfo();
$date_formats=key4ce_getKeyValue('date_formats');
$datetime_format=key4ce_getKeyValue('datetime_format');
if($date_formats==24)
{
	$datetime_format = str_replace(array('a', 'h'), array('', 'H'),$datetime_format);
	$datetime_format = getStrftimeFormat($datetime_format);
}
else
{
	$datetime_format=getStrftimeFormat($datetime_format);
}
$user_id=$ost_wpdb->get_var("SELECT user_id FROM " . $keyost_prefix . "user_email WHERE `address` = '" . $current_user->user_email . "'");
if(isset($_REQUEST['post-reply']))
{
?>
 <div class="key4ce_clear" style="padding: 5px;"></div>
<p id="key4ce_msg_notice"><?php echo __('A new request has been created successfully!','key4ce-osticket-bridge'); ?></p>
<p align="center">
	<br />
	<i><?php echo __('We are currently notifying the selected department staff...','key4ce-osticket-bridge'); ?></i>
</p><br /><br />
<center><script language="javascript" src="<?php echo plugin_dir_url(__FILE__) . '../js/timerbar.js'; ?>"></script></center>
<br />
<center><?php echo __('Thank you for contacting us!','key4ce-osticket-bridge'); ?></center>
<?php
} else {
if ($ticketinfo->address == $current_user->user_email) {
if($keyost_version==193){
$attachement_status=key4ce_getKeyValue('allow_attachments');
$max_user_file_uploads=key4ce_getKeyValue('max_user_file_uploads');
if($max_user_file_uploads==""){	
$max_user_file_uploads="unlimited";
} else {	
$max_user_file_uploads=$max_user_file_uploads;
}
$max_file_size=key4ce_getKeyValue('max_file_size');
$fileextesnions=key4ce_getKeyValue('allowed_filetypes');
} else {
$fileconfig=key4ce_FileConfigValue();
$filedata=json_decode($fileconfig);
$attachement_status=$filedata->attachments;
$max_user_file_uploads=$filedata->max;
if($max_user_file_uploads==""){	
$max_user_file_uploads="unlimited";
} else {	
$max_user_file_uploads=$max_user_file_uploads;
}
$max_file_size=$filedata->size;
$fileextesnions=$filedata->extensions;
}
$alowaray = explode(".",str_replace(' ', '',$fileextesnions));
$strplc = str_replace(".", "",str_replace(' ', '',$fileextesnions));
$allowedExts = explode(",", $strplc);
    function add_quotes($str) {
        return sprintf("'%s'", $str);
    }
    $extimp = implode(',', array_map('add_quotes', $allowedExts));
    $finalary = "'" . $extimp . "'";
if($keyost_version==1914)
{
	$thread_id = $ost_wpdb->get_var("SELECT id from $thread_table WHERE `object_id` ='$ticketinfo->ticket_id'");
}
    ?>
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
				} else {
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
                alert("<?php echo  __("Please make sure your file is less than", 'key4ce-osticket-bridge'); ?><?php echo ($max_file_size * .0009765625) * .0009765625; ?>MB.");
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
	<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) . '../css/ticketview.css'; ?>">
    <div id="key4ce_ticket_view">
        <div id="key4ce_tic_number"># <?php echo $ticketinfo->number; ?> - <?php echo @Format::stripslashes($ticketinfo->subject); ?></div>
        <div style="clear: both"></div>
    </div>
    <div id="key4ce_tic_info_box">
		 <div class="key4ce_fullcol">
			 <div class="key4ce_leftcol">
				<div class="key4ce_col1"><?php echo __("Name", 'key4ce-osticket-bridge'); ?>:</div>
				<div class="key4ce_col2"><?php echo $current_user->display_name;  //echo $ticketinfo->name; ?></div>
			 </div>
			 <div class="key4ce_rightcol">
				<div class="key4ce_col1"><?php echo __("Email", 'key4ce-osticket-bridge'); ?>:</div>
				<div class="key4ce_col2"><?php echo $ticketinfo->address; ?></div>
			 </div>
		 </div>
         <div class="key4ce_fullcol">
			 <div class="key4ce_leftcol">
				<div class="key4ce_col1"><?php echo __("Department", 'key4ce-osticket-bridge'); ?>:</div>
				<div class="key4ce_col2"><?php echo $ticketinfo->dept_name; ?></div>
			 </div>
			 <div class="key4ce_rightcol">
				<div class="key4ce_col1"><?php echo __("Help Topic", 'key4ce-osticket-bridge'); ?>:</div>
				<div class="key4ce_col2"><?php echo $ticketinfo->topic; ?></div>
			 </div>
		 </div>
		  <div class="key4ce_fullcol">
			 <div class="key4ce_leftcol">
				<div class="key4ce_col1"><?php echo __("Ticket Status", 'key4ce-osticket-bridge'); ?>:</div>
				<div class="key4ce_col2"><?php
            if ($ticketinfo->status == 'closed') {
                echo '<font color=red>'.__("Closed", 'key4ce-osticket-bridge').'</font>';
            } elseif ($ticketinfo->status == 'open' && $ticketinfo->isanswered == '0') {
                echo '<font color=green>'.__("Open", 'key4ce-osticket-bridge').'</font>';
            } elseif ($ticketinfo->status == 'open' && $ticketinfo->isanswered == '1') {
                echo '<font color=orange>'.__("Answered", 'key4ce-osticket-bridge').'</font>';
            }
            ?></div>
			 </div>
			 <div class="key4ce_rightcol">
				<div class="key4ce_col1"><?php echo __("Priority", 'key4ce-osticket-bridge'); ?>:</div>
				<div class="key4ce_col2"><?php
			if($keyost_version==193){
				if ($ticketinfo->priority_id == '4') {
					echo '<div style="color: Red;"><strong>'.__("Emergency", 'key4ce-osticket-bridge').'</strong></div>';
				} elseif ($ticketinfo->priority_id == '3') {
					echo '<div style="color: Orange;"><strong>'.__("High", 'key4ce-osticket-bridge').'</strong></div>';
				} elseif ($ticketinfo->priority_id == '2') {
					echo '<div style="color: Green;"><strong>'.__("Normal", 'key4ce-osticket-bridge').'</strong></div>';
				} elseif ($ticketinfo->priority_id == '1') {
					echo '<div style="color: Black;">'.__("Low", 'key4ce-osticket-bridge').'</div>';
				} elseif ($ticketinfo->priority_id == '') {
					echo '<div style="color: Green;">'.__("Normal", 'key4ce-osticket-bridge').'</div>';
				}
			} else {
				if ($ticketinfo->priority == '4') {
					echo '<div style="color: Red;"><strong>'.__("Emergency", 'key4ce-osticket-bridge').'</strong></div>';
				} elseif ($ticketinfo->priority == '3') {
					echo '<div style="color: Orange;"><strong>'.__("High", 'key4ce-osticket-bridge').'</strong></div>';
				} elseif ($ticketinfo->priority == '2') {
					echo '<div style="color: Green;"><strong>'.__("Normal", 'key4ce-osticket-bridge').'</strong></div>';
				} elseif ($ticketinfo->priority == '1') {
					echo '<div style="color: Black;">'.__("Low", 'key4ce-osticket-bridge').'</div>';
				} elseif ($ticketinfo->priority == '') {
					echo '<div style="color: Green;">'.__("Normal", 'key4ce-osticket-bridge').'</div>';
				}
			}
            ?></div>
			 </div>
		 </div>
		 <div class="key4ce_fullcol">
		 <div class="key4ce_leftcol">
		 <div class="key4ce_col1"><?php echo __("Create Date", 'key4ce-osticket-bridge'); ?>:</div>
		 <div class="key4ce_col2"><?php echo __formatDate($datetime_format, $ticketinfo->created); ?></div>
		 </div>
		 </div>
		 </div>
    </div>
    <div id="key4ce_thContainer">
        <div id="key4ce_ticketThread">
    <?php
    foreach ($threadinfo as $thread_info) {
		if($keyost_version==1914)
		{
			$file_ids = $ost_wpdb->get_results("SELECT file_id from $ost_attachment WHERE `object_id` ='$thread_info->id'");	 
		}
		else
		{
			$file_ids = $ost_wpdb->get_results("SELECT file_id from $ost_ticket_attachment WHERE `ref_id` ='$thread_info->id'");
		}
		$type=$thread_info->thread_type;
        ?>
		<?php if($type==="M")
		{
			$getEmailFromUserID=getIDTOEmail($thread_info->user_id);
		?>
		<div id="key4ce_thread-entry-<?php echo $thread_info->id; ?>">
			<div class="key4ce_thread-entry key4ce_message key4ce_avatar">
				<span class="key4ce_pull-right key4ce_avatar">
				<?php echo get_avatar( $getEmailFromUserID,48); ?>
				</span>
				<div class="key4ce_header">
						<div class="key4ce_pull-right">
						<span class="textra light"></span>
						</div>
						<b>
						<?php if ($hidename == 1 && $thread_info->thread_type <> "M") 
							{
									echo $ticketinfo->dept_name;
							} else 
							{
									echo $thread_info->poster;
							} 
						?>
						</b> <?php echo __("posted", 'key4ce-osticket-bridge'); ?> <time datetime="<?php echo __formatDate($datetime_format, $thread_info->created); ?>" data-toggle="tooltip" title="" data-original-title="<?php echo __formatDate($datetime_format, $thread_info->created); ?>"><?php echo __formatDate($datetime_format, $thread_info->created); ?> </time>
						<span style="max-width:400px" class="faded title truncate"></span>	
				</div>
				<div class="key4ce_thread-body key4ce_no-pjax">
					<div><?php echo $thread_info->body; ?></div>
				<div class="clear"></div>
				<?php
                                if (count($file_ids) > 0) {
                                    foreach ($file_ids as $file_id) {
                                        $filedetails = $ost_wpdb->get_row("SELECT * FROM `$ost_file` WHERE `id` =" . $file_id->file_id);
										$filesize=formatSizeUnits($filedetails->size);
                                        ?>
										<div class="key4ce_attachments">        
						<span class="key4ce_attachment-info">
                                        <form action="<?php echo WP_PLUGIN_URL; ?>/key4ce-osticket-bridge/lib/attachment/download.php" method="post" style="float: left;">
                                            <input type="hidden" name="service" value="download"/>
                                            <input type="hidden" name="ticket" value="<?php echo $ticketinfo->number; ?>"/>
                                            <input type="hidden" name="key" value="<?php echo $filedetails->key; ?>"/>
                                            <input type="hidden" name="id" value="<?php echo $filedetails->id; ?>"/>
                                            <input type="hidden" name="type" value="<?php echo $filedetails->type; ?>"/>
                                            <input type="hidden" name="name" value="<?php echo $filedetails->name; ?>"/>
                                            <input type="hidden" name="h" value="<?php echo session_id(); ?>"/>
                                            <input type="hidden" name="filepath" value="<?php echo key4ce_getKeyValue('uploadpath'); ?>"/>
                                            <span class="key4ce_Icon key4ce_attachment"></span>
											<input type="submit" name="download" class="key4ce_no-pjax key4ce_truncate key4ce_filename" value="<?php echo $filedetails->name; ?>"><small class="key4ce_filesize key4ce_faded"><?php echo $filesize; ?></small>
                                        </form>
										</span>
						</div>
                                        <?php
                                    }
                                }
                                ?>
				</div>
			</div>
		</div>
		<?php
		}
		else
		{
			$getEmailFromUserID=getStaffIDTOEmail($thread_info->staff_id);
		?>
			<div id="key4ce_thread-entry-<?php echo $thread_info->id; ?>">
			<div class="key4ce_thread-entry key4ce_response avatar">
				<span class="key4ce_pull-left key4ce_avatar-right">
				<?php echo get_avatar( $getEmailFromUserID,48); ?>
				</span>
				<div class="key4ce_header key4ce_hright">
					<div class="key4ce_pull-right">
					<span class="textra light">
					</span>
					</div>
					<b>
					<?php if ($hidename == 1 && $thread_info->thread_type <> "M") 
							{
									echo $ticketinfo->dept_name;
							} else 
							{
									echo $thread_info->poster;
							} 
					?>
					</b> <?php echo __("posted", 'key4ce-osticket-bridge'); ?> <time datetime="<?php echo __formatDate($datetime_format, $thread_info->created); ?>" data-toggle="tooltip" title="" data-original-title="<?php echo __formatDate($datetime_format, $thread_info->created); ?>"><?php echo __formatDate($datetime_format, $thread_info->created); ?></time> 
					<span style="max-width:400px" class="faded title truncate"></span>	
				</div>
				<div class="key4ce_thread-body-right key4ce_no-pjax">
					<div><?php echo $thread_info->body; ?></div>
					<div class="clear"></div>
					<?php
                                if (count($file_ids) > 0) {
                                    foreach ($file_ids as $file_id) {
                                        $filedetails = $ost_wpdb->get_row("SELECT * FROM `$ost_file` WHERE `id` =" . $file_id->file_id);
                                        ?>
										<div class="key4ce_attachments">        
						<span class="key4ce_attachment-info">
                                        <form action="<?php echo WP_PLUGIN_URL; ?>/key4ce-osticket-bridge/lib/attachment/download.php" method="post" style="float: left;">
                                            <input type="hidden" name="service" value="download"/>
                                            <input type="hidden" name="ticket" value="<?php echo $ticketinfo->number; ?>"/>
                                            <input type="hidden" name="key" value="<?php echo $filedetails->key; ?>"/>
                                            <input type="hidden" name="id" value="<?php echo $filedetails->id; ?>"/>
                                            <input type="hidden" name="type" value="<?php echo $filedetails->type; ?>"/>
                                            <input type="hidden" name="name" value="<?php echo $filedetails->name; ?>"/>
                                            <input type="hidden" name="h" value="<?php echo session_id(); ?>"/>
                                            <input type="hidden" name="filepath" value="<?php echo key4ce_getKeyValue('uploadpath'); ?>"/>
                                            <span class="key4ce_Icon key4ce_attachment"></span>
											<input type="submit" name="download" class="key4ce_no-pjax key4ce_truncate key4ce_filename" value="<?php echo $filedetails->name; ?>"><small class="key4ce_filesize key4ce_faded">612 kb</small>
                                        </form>
										</span>
						</div>
                                        <?php
                                    }
                                }
                                ?>
				</div>
			</div>
		</div>
		<?php		
		}
	} ?>
            <div style="clear: both"></div>
        </div>
        <div id="key4ce_tic_post">
            <div id="key4ce_tic_post_reply"><?php echo __("Post a Reply", 'key4ce-osticket-bridge'); ?></div>
            <div id="key4ce_tic_post_detail"><?php echo __("To best assist you, please be specific and detailed in your reply.", 'key4ce-osticket-bridge'); ?></div>
            <div style="clear: both"></div>
        </div>
        <?php
        $id_ademail = $ost_wpdb->get_var("SELECT id FROM $config_table WHERE $config_table.key like ('%admin_email%');");
        $os_admin_email = $ost_wpdb->get_row("SELECT id,namespace,$config_table.key,$config_table.value,updated FROM $config_table where id = $id_ademail");
        $os_admin_email_admin = $os_admin_email->value;
        ?><form id="reply" action="" name="reply" method="post" enctype="multipart/form-data" onsubmit="return validateFormReply()">
        <table class="key4ce_welcome key4ce_nobd" align="left" width="95%" cellspacing="0" cellpadding="3">            
                <tr>
                    <td class="key4ce_nobd" align="center">						
					<?php						//Note : $poconsubmail variable coming from /osticket-wp.php file						if($poconsubmail!="")							$userpost_subject=$poconsubmail;						else							$userpost_subject=$ticketinfo->subject;						?>
                        <input type="hidden" value="<?php echo $thread_info->id; ?>" name="tic_id">
						<?php 
						if($keyost_version==1914)
						{
						?>
						<input type="hidden" value="<?php echo $thread_id; ?>" name="thread_id">
						<?php } ?>
						 <input type="hidden" value="<?php echo $ticketinfo->ticket_id; ?>" name="ticket_id">
                        <input type="hidden" value="reply" name="a"> 
                        <input type="hidden" name="usticketid" value="<?php echo $ticketinfo->number; ?>"/>
                        <input type="hidden" name="usid" value="<?php echo $user_id; ?>"/>
                        <input type="hidden" name="usname" value="<?php echo $ticketinfo->name; ?>"/>
                        <input type="hidden" name="usemail" value="<?php echo $ticketinfo->address; ?>"/>
                        <input type="hidden" name="usdepartment" value="<?php echo $ticketinfo->dept_name; ?>"/>
                        <input type="hidden" name="uscategories" value="<?php echo $ticketinfo->topic; ?>"/>
                        <input type="hidden" name="ussubject" value="<?php echo $poconsubmail; ?>"/>
                        <input type="hidden" name="ustopicid" value="<?php echo $ticketinfo->topic_id; ?>"/>
                        <input type="hidden" name="ademail" value="<?php echo $os_admin_email_admin; ?>"/>
                        <input type="hidden" name="stitle" value="<?php echo $title_name; ?>"/>
                        <input type="hidden" name="sdirna" value="<?php echo $dirname; ?>"/>
                        <input type="hidden" name="postconfirmtemp" value="<?php echo $postconfirm; ?>"/>
                <center>
                    <?php
                    $content = '';
                    $editor_id = 'message';
                    $settings = array('media_buttons' => false);
                    wp_editor($content, $editor_id, $settings);
                    ?></center>
                </td>
                </tr>
        <?php 
        if ($attachement_status==1 || $attachement_status==true)  {
	if(key4ce_getPluginValue('Attachments on the filesystem')==1){ ?>
            <tr><td>
                    <div id="addinput">
                        <p>
                            <span style="color:#000;"><?php echo __("Attachment", 'key4ce-osticket-bridge'); ?> 1:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="file" id="p_new" name="file[]" onchange="return checkFile(this);"/>&nbsp;&nbsp;&nbsp;<a href="#" id="addNew"><?php echo __("Add", 'key4ce-osticket-bridge'); ?></a>&nbsp;&nbsp;&nbsp;<span style="color: red;font-size: 11px;"><?php echo __("Max file upload size :", 'key4ce-osticket-bridge'); ?><?php echo ($max_file_size * .0009765625) * .0009765625; ?>MB</span>
                        </p>
                    </div>
                </td></tr>
    <?php } else { ?>
	 <tr><td><?php echo __("Attachments on the Filesystem plugin can be downloaded here :", 'key4ce-osticket-bridge'); ?><a href="http://osticket.com/download/go?dl=plugin%2Fstorage-fs.phar" title="Attachement Filesystem Plugin" target="_blank"><?php echo __("Attachement Filesystem Plugin", 'key4ce-osticket-bridge'); ?></a></td></tr>
    <?php } } ?>
                <tr>
                    <td class="key4ce_nobd" align="center"><div class="key4ce_clear" style="padding: 5px;"></div>
                        <?php
                        if ($ticketinfo->status == 'closed') {
                            echo '<center><label><input type="checkbox" name="open_ticket_status" id="open_ticket_status" value="open" checked>&nbsp;&nbsp;<font color=green>'.__("Reopen", 'key4ce-osticket-bridge').'</font>'.__(" Ticket On Reply", 'key4ce-osticket-bridge').'</label></center>';
                        } elseif ($ticketinfo->status == 'open') {
							if($keyost_usercloseticket==1)
                            echo '<center><label><input type="checkbox" name="close_ticket_status" id="close_ticket_status" value="closed">&nbsp;&nbsp;<font color=red>'.__("Close", 'key4ce-osticket-bridge').'</font>'.__(" Ticket On Reply", 'key4ce-osticket-bridge').'</label></center>';
                        }
                        ?>
                        <div class="key4ce_clear" style="padding: 5px;"></div></td>
                </tr>
                <tr>
                    <td class="key4ce_nobd" align="center">
                    <center><input type="submit" value="<?php echo __("Post Reply", 'key4ce-osticket-bridge'); ?>" name="post-reply"/>
                    &nbsp;&nbsp;<input type="reset" value="<?php echo __("Reset", 'key4ce-osticket-bridge'); ?>"/>&nbsp;&nbsp;
                    <input type="button" value="<?php echo __("Cancel", 'key4ce-osticket-bridge'); ?>" onClick="history.go(-1)"/></center>
                </td>
                </tr>            
        </table>
    </form>
    <div style="clear: both"></div>
    </div>
    <div class="clear" style="padding: 10px;"></div>
<?php } else { ?>
    <div style="width: 100%; margin: 20px; font-size: 20px;" align="center"><?php echo __("No such ticket available.", 'key4ce-osticket-bridge'); ?></div> 
<?php } } 
} else {
auth_redirect();
}
?>