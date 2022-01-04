<?php
require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/includes/functions.php' );
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
global $current_user;
get_currentuserinfo();
// Start File system changes
if($keyost_version==193){
$attachement_status=key4ce_getKeyValue('allow_attachments');
$max_user_file_uploads=key4ce_getKeyValue('max_user_file_uploads');if($max_user_file_uploads==""){	$max_user_file_uploads="unlimited";}else{	$max_user_file_uploads=$max_user_file_uploads;}
$max_file_size=key4ce_getKeyValue('max_file_size');
$fileextesnions=key4ce_getKeyValue('allowed_filetypes');
}else{
$fileconfig=key4ce_FileConfigValue();
$filedata=json_decode($fileconfig);
$attachement_status=$filedata->attachments;
$max_user_file_uploads=$filedata->max;if($max_user_file_uploads==""){	$max_user_file_uploads="unlimited";}else{	$max_user_file_uploads=$max_user_file_uploads;}
$max_file_size=$filedata->size;
$fileextesnions=$filedata->extensions;
}
// End file system changes
$alowaray = explode(".",str_replace(' ', '',$fileextesnions));
$strplc = str_replace(".", "",str_replace(' ', '',$fileextesnions));
$allowedExts = explode(",", $strplc);

function add_quotes($str) {
    return sprintf("'%s'", $str);
}
$extimp = implode(',', array_map('add_quotes', $allowedExts));
$finalary = "'" . $extimp . "'";
?>
<script language="javascript" src="<?php echo plugin_dir_url(__FILE__) . '../js/jquery_1_7_2.js'; ?>"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) . '../css/admin-ticketview.css'; ?>">
<script type="text/javascript">
	$(function() {
		$( "#tabs" ).tabs();
        var addDiv = $('#addinput');
        var i = $('#addinput p').size() + 1;
        var MaxFileInputs = '<?php echo $max_user_file_uploads; ?>';
        $('#addNew').live('click', function() {
            if(MaxFileInputs=="unlimited")			{				$('<p><span style="color:#000;"><?php echo __("Attachment", 'key4ce-osticket-bridge'); ?> ' + i + ':</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="file" id="p_new_' + i + '" name="file[]" onchange="return checkFile(this);"/>&nbsp;&nbsp;&nbsp;<a href="#" id="remNew"><?php echo __("Remove", 'key4ce-osticket-bridge'); ?></a>&nbsp;&nbsp;&nbsp;<span style="color: red;font-size: 11px;"><?php echo __("Max file upload size", 'key4ce-osticket-bridge'); ?> : <?php echo ($max_file_size * .0009765625) * .0009765625; ?>MB</span></p>').appendTo(addDiv);				i++;			}			else			{				if (i <= MaxFileInputs)				{					$('<p><span style="color:#000;"><?php echo __("Attachment", 'key4ce-osticket-bridge'); ?> ' + i + ':</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="file" id="p_new_' + i + '" name="file[]" onchange="return checkFile(this);"/>&nbsp;&nbsp;&nbsp;<a href="#" id="remNew"><?php echo __("Remove", 'key4ce-osticket-bridge'); ?></a>&nbsp;&nbsp;&nbsp;<span style="color: red;font-size: 11px;"><?php echo __("Max file upload size", 'key4ce-osticket-bridge'); ?> : <?php echo ($max_file_size * .0009765625) * .0009765625; ?>MB</span></p>').appendTo(addDiv);					i++;				}								else				{					alert("<?php echo __("You have exceeds your file upload limit", 'key4ce-osticket-bridge'); ?>");					return false;				}			}
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
            alert("<?php echo __("Please make sure your file is less than", 'key4ce-osticket-bridge'); ?><?php echo ($max_file_size * .0009765625) * .0009765625; ?>MB.");
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
<div class="key4ce_wrap">
    <div class="key4ce_headtitle"><?php echo __("Reply to Support Request", 'key4ce-osticket-bridge'); ?></div>
    <div style="clear: both"></div>
    <?php
    require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/db-settings.php');
    require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/header_nav_ticket.php');
    ?>
	<script>
  $(function() {
	$("#cannedResp").change(function() {
		var cannedResp=$("#cannedResp").val();
		var data = {
			action: "get_cannedresponce_dropdown",
			cannedResp: cannedResp,
			ticketid : <?php echo $ticketinfo->number; ?>
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
		if(response!=""){
			tinyMCE.init({selector:'textarea'});
			//tinyMCE.activeEditor.setContent(response);
			tinyMCE.get("message").setContent(response);			
		}			
		});
            });
  });
  </script>
    <div id="key4ce_ticket_view">
        <div id="key4ce_tic_number"><?php echo __("Ticket ID", 'key4ce-osticket-bridge'); ?> #<?php echo $ticketinfo->number; ?></div>
        <div id="key4ce_tic_icon"><a href="admin.php?page=ost-tickets&service=view&ticket=<?php echo $ticketinfo->number; ?>" title="Reload"><span class="Icon refresh"></span></a><span class="preply">&darr; <a href="#post"><?php echo __("Post Reply", 'key4ce-osticket-bridge'); ?></a></span></div>
        <div style="clear: both"></div>
    </div>
    <div id="key4ce_tic_info_box">
		<table>
            <tr> 
				<td><b><?php echo __("Name", 'key4ce-osticket-bridge'); ?>:</b></td>
				<td><div><?php echo $current_user->display_name; //echo $ticketinfo->name; ?></div></td>
				<td><b><?php echo __("User Email", 'key4ce-osticket-bridge'); ?>:</b></td>
                <td><div><?php echo $ticketinfo->address; ?></div></td>
            </tr>
            <tr> 
                <td><b><?php echo __("Department", 'key4ce-osticket-bridge'); ?>:</b></td>
                <td><div><?php echo $ticketinfo->dept_name; ?></div></td>
                 <td><b><?php echo __("Help Topic", 'key4ce-osticket-bridge'); ?>:</b></td>
                <td><div><?php echo $ticketinfo->topic; ?></div></td>
            </tr>
            <tr> 
			<td><b><?php echo __("Ticket Status", 'key4ce-osticket-bridge'); ?>:</b></td>
                <td><div>
                        <?php
                        if ($ticketinfo->status == 'closed') {
                            echo '<font color=red>'. __("Closed", 'key4ce-osticket-bridge').'</font>';
                        } elseif ($ticketinfo->status == 'open' && $ticketinfo->isanswered == '0') {
                            echo '<font color=green>'.__("Open", 'key4ce-osticket-bridge').'</font>';
                        } elseif ($ticketinfo->status == 'open' && $ticketinfo->isanswered == '1') {
                            echo '<font color=orange>'.__("Answered", 'key4ce-osticket-bridge').'</font>';
                        }
                        ?>
                    </div>
				</td>
                <td><b><?php echo __("Priority", 'key4ce-osticket-bridge'); ?>:</b></td>
                <td> 
                    <div><?php if($keyost_version==194 || $keyost_version==195 || $keyost_version==1951 || $keyost_version==1914)
								$priority=$ticketinfo->priority;
						else	
								$priority=$ticketinfo->priority_id;
                        if ($priority == '4') {
                            echo '<div style="color: Red;"><strong>'.__("Emergency", 'key4ce-osticket-bridge').'</strong></div>';
                        } elseif ($priority== '3') {
                            echo '<div style="color: Orange;"><strong>'.__("High", 'key4ce-osticket-bridge').'</strong></div>';
                        } elseif ($priority == '2') {
                            echo '<div style="color: Green;"><strong>'.__("Normal", 'key4ce-osticket-bridge').'</strong></div>';
                        } elseif ($priority == '1') {
                            echo '<div style="color: Black;">'.__("Low", 'key4ce-osticket-bridge').'</div>';
                        } elseif ($priority == '') {
                            echo '<div style="color: Black;">'.__("Normal", 'key4ce-osticket-bridge').'</div>';
                        }
                        ?>
                    </div>
                </td>
            </tr>
            <tr> 
			  <td><b><?php echo __("Date Create", 'key4ce-osticket-bridge'); ?>:</b></td>
                <td><div><?php echo __formatDate($datetime_format,$ticketinfo->created); ?></div></td>
            </tr>
        </table>
    </div>
    <div style="clear: both"></div>
    <div id="key4ce_tic_sub">
        <div id="key4ce_tic_subject"><?php echo __("Subject", 'key4ce-osticket-bridge'); ?>:</div>
        <div id="key4ce_tic_subject_info"><?php echo $ticketinfo->subject; ?></div>
        <div style="clear: both"></div>
    </div>
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
		<div id="thread-entry-<?php echo $thread_info->id; ?>">
			<div class="key4ce_thread-entry key4ce_message key4ce_avatar">
				<span class="key4ce_pull-right key4ce_avatar">
				<?php echo get_avatar( $getEmailFromUserID,48); ?>
					<!--<img class="avatar" alt="Avatar" src="//www.gravatar.com/avatar/<?php //echo $img_hash; ?>?s=80&d=mm">-->
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
						</b> <?php echo __("posted", 'key4ce-osticket-bridge'); ?> 
						<time datetime="<?php echo __formatDate($datetime_format, $thread_info->created); ?>" data-toggle="tooltip" title="" data-original-title="<?php echo __formatDate($datetime_format, $thread_info->created); ?>"><?php echo __formatDate($datetime_format, $thread_info->created); ?> </time>
						<span style="max-width:400px" class="faded title truncate"></span>	
				</div>
				<div class="key4ce_thread-body no-pjax">
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
											<!--<a class="no-pjax truncate filename" href="/ticket1910/file.php?key=rdpxy88bekgth-nqjphoy1kgbzj2qephs&amp;expires=1480464000&amp;signature=fa477c91e5b9c8ed7c336a17ac42a263f97c3e17" download="Untitled.png" target="_blank">Untitled.png</a><small class="filesize faded">612 kb</small>-->        
											<input type="submit" name="download" class="key4ce_no-pjax key4ce_truncate key4ce_filename" value="<?php echo $filedetails->name; ?>"><small class="key4ce_filesize faded"><?php echo $filesize; ?></small>
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
			<div id="thread-entry-<?php echo $thread_info->id; ?>">
			<div class="key4ce_thread-entry key4ce_response key4ce_avatar">
				<span class="key4ce_pull-left key4ce_avatar-right">
				<?php echo get_avatar( $getEmailFromUserID,48); ?>
					<!--<img class="avatar" alt="Avatar" src="//www.gravatar.com/avatar/<?php //echo $img_hash; ?>?s=80&d=mm">-->
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
					</b> <?php echo __("posted", 'key4ce-osticket-bridge'); ?>  <time datetime="<?php echo __formatDate($datetime_format, $thread_info->created); ?>" data-toggle="tooltip" title="" data-original-title="<?php echo __formatDate($datetime_format, $thread_info->created); ?>"><?php echo __formatDate($datetime_format, $thread_info->created); ?></time> 
					<span style="max-width:400px;margin-left:15px;" class="faded title truncate"><?php echo $type=="N"?$thread_info->title:""; ?></span>	
				</div>
				<div class="key4ce_thread-body-right no-pjax">
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
											<!--<a class="no-pjax truncate filename" href="/ticket1910/file.php?key=rdpxy88bekgth-nqjphoy1kgbzj2qephs&amp;expires=1480464000&amp;signature=fa477c91e5b9c8ed7c336a17ac42a263f97c3e17" download="Untitled.png" target="_blank">Untitled.png</a><small class="filesize faded">612 kb</small>-->        
											<input type="submit" name="download" class="no-pjax truncate key4ce_filename" value="<?php echo $filedetails->name; ?>"><small class="key4ce_filesize faded">612 kb</small>
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
    
  <div id="tabs">
  <ul>
    <li><a href="#tabs-1"><?php echo __("Post a Reply", 'key4ce-osticket-bridge'); ?></a></li>
    <li><a href="#tabs-2"><?php echo __("Post Internal Note", 'key4ce-osticket-bridge'); ?></a></li>
  </ul>
  <div id="tabs-1">
    <form name="ost-post-reply" id="ost-reply" action="admin.php?page=ost-tickets&service=view&ticket=<?php echo $ticketinfo->number; ?>" method="post" enctype="multipart/form-data">
    <table align="center" width="95%" cellspacing="0" cellpadding="3" border="0">
        <tr>
            <td align="center">       
                    <input type="hidden" value="<?php echo $thread_info->id; ?>" name="tic_id">
						<?php 
						if($keyost_version==1914)
						{
						?>
						<input type="hidden" value="<?php echo $thread_id; ?>" name="thread_id">
						<?php } ?>
						 <input type="hidden" value="<?php echo $ticketinfo->ticket_id; ?>" name="ticket_id">
                    <input type="hidden" value="reply" name="a">
					<input type="hidden" value="R" name="thread_type">
                    <input type="hidden" name="usticketid" value="<?php echo $ticketinfo->number; ?>"/>
                    <input type="hidden" name="usname" value="<?php echo $ticketinfo->name; ?>"/>
                    <input type="hidden" name="usemail" value="<?php echo $ticketinfo->address; ?>"/>
                    <input type="hidden" name="usdepartment" value="<?php echo $ticketinfo->dept_name; ?>"/>
                    <input type="hidden" name="uscategories" value="<?php echo $ticketinfo->topic; ?>"/>
                        <?php if($form_admintreply_subject!="")
                            $admin_reply_subject=$form_admintreply_subject;
                        else
                            $admin_reply_subject=$ticketinfo->subject;
						?>
						
					 <input type="hidden" name="staff_id" value="<?php echo getEmailToStaffID($current_user->user_email); ?>"/>
                    <input type="hidden" name="ussubject" value="<?php echo $admin_reply_subject; ?>"/>
                    <input type="hidden" name="ustopicid" value="<?php //echo $ticketinfo->topic_id; ?>"/>
                    <input type="hidden" name="ademail" value="<?php echo $os_admin_email; ?>"/>
                    <input type="hidden" name="adname" value="<?php echo $admin_fname; ?> <?php echo $admin_lname; ?>"/>
                    <input type="hidden" name="stitle" value="<?php echo $title_name; ?>"/>
                    <input type="hidden" name="sdirna" value="<?php echo $dirname; ?>"/>
                    <input type="hidden" name="adreply" value="<?php echo $adminreply; ?>"/>
            </td>
        </tr>
		<tr>
			<td><?php echo __("Response", 'key4ce-osticket-bridge'); ?> : 
					<select name="cannedResp" id="cannedResp">
					<option value=""><?php echo __("Select a canned response", 'key4ce-osticket-bridge'); ?></option>
					<option value="original">Original Message</option>
                    <option value="lastmessage">Last Message</option>
					<option disabled="disabled" value="0">---- Premade Replies ----</option>
					<?php foreach($canned_responces as $canned_res)
					{
					?>
						<option value="<?php echo $canned_res->canned_id; ?>"><?php echo $canned_res->title; ?></option>
					<?php	
					}
					?>
					</select>
			</td>
		</tr>
		<?php
                $content = '';
                $editor_id = 'message';
                $settings = array('media_buttons' => false);
		?>
				<tr>
					<td>
						<?php wp_editor($content, $editor_id, $settings); ?>
					</td>
				</tr>
<?php             
if ($attachement_status==1 || $attachement_status==true) {
	if(key4ce_getPluginValue('Attachments on the filesystem')==1){ ?>
            <tr><td>
                    <div id="addinput">
                        <p>
                            <span style="color:#000;"><?php echo __("Attachment 1:", 'key4ce-osticket-bridge'); ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="file" id="p_new" name="file[]" onchange="return checkFile(this);"/>&nbsp;&nbsp;&nbsp;<a href="#" id="addNew"><?php echo __("Add", 'key4ce-osticket-bridge'); ?></a>&nbsp;&nbsp;&nbsp;<span style="color: red;font-size: 11px;"><?php echo __("Max file upload size :", 'key4ce-osticket-bridge'); ?><?php echo ($max_file_size * .0009765625) * .0009765625; ?>MB</span>
                        </p>
                    </div>
                </td></tr>
    <?php } else { ?>
	 <tr><td><?php echo __("Attachments on the Filesystem plugin can be downloaded here:", 'key4ce-osticket-bridge'); ?> <a href="http://osticket.com/download/go?dl=plugin%2Fstorage-fs.phar" title="Attachement Filesystem Plugin" target="_blank"><?php echo __("Attachement Filesystem Plugin", 'key4ce-osticket-bridge'); ?></a></td></tr>
	<?php } } ?>
        <tr>
            <td align="center">
        <?php
        if ($ticketinfo->status == 'closed') {
            echo '<center><label><input type="checkbox" name="open_ticket_status" id="open_ticket_status" value="open" checked>&nbsp;&nbsp;<font color=green>'.__("Reopen", 'key4ce-osticket-bridge').'</font>'.__(" Ticket On Reply", 'key4ce-osticket-bridge').'</label></center>';
        } elseif ($ticketinfo->status == 'open') {
            echo '<center><label><input type="checkbox" name="close_ticket_status" id="close_ticket_status" value="closed">&nbsp;&nbsp;<font color=red>'.__("Close", 'key4ce-osticket-bridge').'</font>'.__(" Ticket On Reply", 'key4ce-osticket-bridge').'</label></center>';
        }
        ?>
            </td>
        </tr>
        <tr>
            <td align="center">
       <label><input type="radio" name="signature" value="mine" checked>&nbsp;&nbsp;<?php echo __("My signature", 'key4ce-osticket-bridge'); ?></label>
            <label><input type="radio" name="signature" value="dept">&nbsp;&nbsp;<?php echo __("Dept. Signature", 'key4ce-osticket-bridge'); ?>(<?php echo $ticketinfo->dept_name; ?>)</label>
        </td>
        </tr>
        <tr>
            <td align="center">        
            <input type="submit" name="ost-post-reply" value="<?php echo __("Post Reply", 'key4ce-osticket-bridge'); ?>" class="button button-primary" />&nbsp;&nbsp;
            <input type="button" value="<?php echo __("Cancel - Go Back", 'key4ce-osticket-bridge'); ?>" class="button button-warning" onClick="history.go(-1)"/>               
        </td>
        </tr>
    </table>
        </form>
  </div>
  <div id="tabs-2">
	<form name="ost-post-note" id="ost-note" action="admin.php?page=ost-tickets&service=view&ticket=<?php echo $ticketinfo->number; ?>" method="post" enctype="multipart/form-data">
    <table align="center" width="95%" cellspacing="0" cellpadding="3" border="0">
        <tr>
            <td align="center">       
                   <input type="hidden" value="<?php echo $thread_info->id; ?>" name="tic_id">
						<?php 
						if($keyost_version==1914)
						{
						?>
						<input type="hidden" value="<?php echo $thread_id; ?>" name="thread_id">
						<?php } ?>
						 <input type="hidden" value="<?php echo $ticketinfo->ticket_id; ?>" name="ticket_id">
                    <input type="hidden" value="reply" name="a">
					<input type="hidden" value="N" name="thread_type">
                    <input type="hidden" name="usticketid" value="<?php echo $ticketinfo->number; ?>"/>
                    <input type="hidden" name="usname" value="<?php echo $ticketinfo->name; ?>"/>
                    <input type="hidden" name="usemail" value="<?php echo $ticketinfo->address; ?>"/>
                    <input type="hidden" name="usdepartment" value="<?php echo $ticketinfo->dept_name; ?>"/>
                    <input type="hidden" name="uscategories" value="<?php echo $ticketinfo->topic; ?>"/>
                        <?php if($form_admintreply_subject!="")
                            $admin_reply_subject=$form_admintreply_subject;
                        else
                            $admin_reply_subject=$ticketinfo->subject;
                        ?>
					<input type="hidden" name="staff_id" value="<?php echo getEmailToStaffID($current_user->user_email); ?>"/>
                    <input type="hidden" name="ussubject" value="<?php echo $admin_reply_subject; ?>"/>
                    <input type="hidden" name="ustopicid" value="<?php //echo $ticketinfo->topic_id; ?>"/>
                    <input type="hidden" name="ademail" value="<?php echo $os_admin_email; ?>"/>
                    <input type="hidden" name="adname" value="<?php echo $admin_fname; ?> <?php echo $admin_lname; ?>"/>
                    <input type="hidden" name="stitle" value="<?php echo $title_name; ?>"/>
                    <input type="hidden" name="sdirna" value="<?php echo $dirname; ?>"/>
                    <input type="hidden" name="adreply" value="<?php echo $adminreply; ?>"/>
            </td>
        </tr>
		<tr>
			<td>
				<?php echo __("Internal Note title", 'key4ce-osticket-bridge'); ?> : (<?php echo __("Optional", 'key4ce-osticket-bridge'); ?>) <input type="text" name="internal_subject" id="internal_subject" />		
			</td>
		</tr>
                 <?php
                $content = '';
                $editor_id = 'note';
                $settings = array('media_buttons' => false);
				?>
				<tr>
					<td>
						<?php echo __("Internal Note", 'key4ce-osticket-bridge'); ?>:  <span style="color: red;font-size: 11px;">*</span> <?php wp_editor($content, $editor_id, $settings); ?>
					</td>
				</tr>
				<?php
if ($attachement_status==1 || $attachement_status==true) {
	if(key4ce_getPluginValue('Attachments on the filesystem')==1){ ?>
            <tr><td>
                    <div id="addinput">
                        <p>
                            <span style="color:#000;"><?php echo __("Attachment 1:", 'key4ce-osticket-bridge'); ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="file" id="p_new" name="file[]" onchange="return checkFile(this);"/>&nbsp;&nbsp;&nbsp;<a href="#" id="addNew"><?php echo __("Add", 'key4ce-osticket-bridge'); ?></a>&nbsp;&nbsp;&nbsp;<span style="color: red;font-size: 11px;"><?php echo __("Max file upload size :", 'key4ce-osticket-bridge'); ?><?php echo ($max_file_size * .0009765625) * .0009765625; ?>MB</span>
                        </p>
                    </div>
                </td></tr>
    <?php } else { ?>
	 <tr><td><?php echo __("Attachments on the Filesystem plugin can be downloaded here:", 'key4ce-osticket-bridge'); ?> <a href="http://osticket.com/download/go?dl=plugin%2Fstorage-fs.phar" title="Attachement Filesystem Plugin" target="_blank"><?php echo __("Attachement Filesystem Plugin", 'key4ce-osticket-bridge'); ?></a></td></tr>
	<?php } } ?>
        <tr>
            <td align="center">
        <?php
        if ($ticketinfo->status == 'closed') {
            echo '<center><label><input type="checkbox" name="open_ticket_status" id="open_ticket_status" value="open" checked>&nbsp;&nbsp;<font color=green>'.__("Reopen", 'key4ce-osticket-bridge').'</font>'.__(" Ticket On Reply", 'key4ce-osticket-bridge').'</label></center>';
        } elseif ($ticketinfo->status == 'open') {
            echo '<center><label><input type="checkbox" name="close_ticket_status" id="close_ticket_status" value="closed">&nbsp;&nbsp;<font color=red>'.__("Close", 'key4ce-osticket-bridge').'</font>'.__(" Ticket On Reply", 'key4ce-osticket-bridge').'</label></center>';
        }
        ?>
            </td>
        </tr>
        <tr>
            <td align="center">        
            <input type="submit" name="ost-post-note" value="<?php echo __("Post Note", 'key4ce-osticket-bridge'); ?>" class="button button-primary" />&nbsp;&nbsp;
            <input type="button" value="<?php echo __("Cancel - Go Back", 'key4ce-osticket-bridge'); ?>" class="button button-warning" onClick="history.go(-1)"/>               
        </td>
        </tr>
    </table>
        </form>
  </div>
</div>
</div><!--End wrap-->
<?php wp_enqueue_script('ost-bridge-fade', plugins_url('../js/fade.js', __FILE__)); ?>
