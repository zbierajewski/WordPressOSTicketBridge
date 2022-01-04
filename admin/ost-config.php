<?php
/*
Template Name: ost-config
*/
require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/includes/udscript.php' ); ?>
<div class="key4ce_wrap">
<div class="key4ce_headtitle"><?php echo __("osTicket Data Configuration", 'key4ce-osticket-bridge'); ?></div>
<div style="clear: both"></div>
<?php require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/header_nav.php' ); ?>
<div id="key4ce_tboxwh" class="key4ce_pg1"><?php echo __("View or edit your OSTicket database information, you should already have osTicket installed to your server, view the osticket-folder/include/ost-config.php file. Look for DBHOST, DBNAME & DBUSER for the info required below.", 'key4ce-osticket-bridge'); ?><div style="padding:4px;"></div><?php echo __("Landing Page Name: The welcome page can be any name you want: Support, Helpdesk, Contact-Us, ext...the plugin will create this page. Note: If this page exists it will be over written, also this cannot be the same name as your osTicket folder.", 'key4ce-osticket-bridge'); ?></div>
<div style="clear: both"></div>
<?php
	if(isset($_REQUEST['submit'])) {
            @$host=$_REQUEST['host'];
            @$database=$_REQUEST['database'];
            @$username=$_REQUEST['username'];
            @$password=$_REQUEST['password'];
            @$keyost_prefix=$_REQUEST['keyost_prefix'];
            @$keyost_version=$_REQUEST['keyost_version'];
            @$keyost_usercloseticket = ($_REQUEST['keyost_usercloseticket']=="on") ? '1' : '0';
            @$supportpage=$_REQUEST['supportpage'];
            @$contactticketpage=$_REQUEST['contactticketpage'];
			@$knowledgebasepage=$_REQUEST['knowledgebasepage'];
            @$thankyoupage=$_REQUEST['thankyoupage'];
            @$googlesecretkey=$_REQUEST['googlesecretkey'];
            @$googlesitekey=$_REQUEST['googlesitekey'];
            @$keyost_departmentsetting = ($_REQUEST['keyost_departmentsetting']=="on") ? '1' : '0';
            @$keyost_defaultdepartmentsetting = $_REQUEST['keyost_defaultdepartmentsetting'];
            @$keyost_helptopicsetting = ($_REQUEST['keyost_helptopicsetting']=="on") ? '1' : '0';
            @$keyost_defaulthelptopicsetting = $_REQUEST['keyost_defaulthelptopicsetting'];
            @$keyost_contactfilestatus = ($_REQUEST['keyost_contactfilestatus']=="on") ? '1' : '0';
			@$keyost_kbcustomslg=$_REQUEST['keyost_kbcustomslg'];
            $config=array('host'=>$host, 'database'=>$database, 'username'=>$username,'password'=>$password,'keyost_prefix'=>$keyost_prefix,'keyost_version'=>$keyost_version,'keyost_usercloseticket'=>$keyost_usercloseticket,'supportpage'=>$supportpage,'contactticketpage'=>$contactticketpage,'knowledgebasepage'=>$knowledgebasepage,'thankyoupage'=>$thankyoupage,'googlesecretkey'=>$googlesecretkey,'googlesitekey'=>$googlesitekey,'keyost_departmentsetting'=>$keyost_departmentsetting,'keyost_defaultdepartmentsetting'=>$keyost_defaultdepartmentsetting,
                'keyost_helptopicsetting'=>$keyost_helptopicsetting,'keyost_defaulthelptopicsetting'=>$keyost_defaulthelptopicsetting,'keyost_contactfilestatus'=>$keyost_contactfilestatus,'keyost_kbcustomslg'=>$keyost_kbcustomslg);          
            if (($_REQUEST['host']=="") || ($_REQUEST['database']=="") || ($_REQUEST['username']=="") || ($_REQUEST['supportpage']=="") ){
                echo '<div id="failed"><b>'.__("Error:", 'key4ce-osticket-bridge').'</b>'.__("All fields are required below for the database...", 'key4ce-osticket-bridge').'</div><div style="clear: both"></div>';
            }else{
                $current_user = wp_get_current_user();
                global $wpdb;
                $osticketpagecheck = $wpdb->get_var("SELECT count(*) as no FROM $wpdb->posts WHERE post_content='[addosticket]' AND post_status='publish'");
                if ($osticketpagecheck == 0){
                    wp_insert_post(array(
                        'comment_status'	=>'closed',
                        'ping_status'		=>'closed',
                        'post_author'		=>$current_user->ID,
                        'post_name'		=>$supportpage,
                        'post_title'		=>$supportpage,
                        'post_content' 		=> '[addosticket]',
                        'post_status'		=>'publish',
                        'post_type'		=>'page'
                    ));
		}
		$contactticketpagecheck = $wpdb->get_var("SELECT count(*) as no FROM $wpdb->posts WHERE `post_content` LIKE '%[addoscontact]%' AND post_status='publish'");			
		if ($contactticketpagecheck <= 0){ 
		wp_insert_post(array(
                    'comment_status'		=>'closed',
                    'ping_status'		=>'closed',
                    'post_author'		=>$current_user->ID,
                    'post_name'                 =>get_the_title($contactticketpage),
                    'post_title'		=>get_the_title($contactticketpage),
                    'post_content' 		=> '[addoscontact]',
                    'post_status'		=>'publish',
                    'post_type'		=>'page'
                        ));
		}
		
	update_option('os_ticket_config', $config);
	$config = get_option('os_ticket_config');
	extract($config);
	$ost_wpdb = new wpdb($username, $password, $database, $host);
	$ticket_cdata=$keyost_prefix.'ticket__cdata';
	$crt_cdata="CREATE TABLE IF NOT EXISTS $ticket_cdata (
            `ticket_id` int(11) unsigned NOT NULL DEFAULT '0',
            `subject` mediumtext,
            `priority` mediumtext,
            PRIMARY KEY (`ticket_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $ost_wpdb->query($crt_cdata);
        global $ost;
        $ticket_cdata=$keyost_prefix."ticket__cdata";
        $osinstall="osTicket Installed!";
        $osticid=1;
        $prior=2;
        $result1=$ost_wpdb->get_results("SELECT ticket_id FROM $ticket_cdata WHERE subject = '".$osinstall."'");
        if (count ($result1) > 0) { 
            $row = current ($result1);
	} else {
            $ost_wpdb->query ("
                INSERT INTO $ticket_cdata (ticket_id,subject,priority)
                    VALUES ('".$osticid."', '".$osinstall."', '".$prior."')");
        } 
?>
<div id="key4ce_succes" class="key4ce_fade"><?php echo __("Your settings saved successfully...Thank you!", 'key4ce-osticket-bridge'); ?></div>
<div style="clear: both"></div>
<?php
} }
$config = get_option('os_ticket_config');
extract($config);
?>
<script type="text/javascript">
$(function(){
	<?php if($keyost_departmentsetting==0) { ?>
			$('.trdepartment').css("display:block");		
	<?php }if($keyost_departmentsetting==1){ ?>
			$('.trdepartment').css("display:none");
	<?php } ?>
	<?php if($keyost_helptopicsetting==0) { ?>
			$('.trhelptopic').css("display:block");		
	<?php }if($keyost_helptopicsetting==1){ ?>
			$('.trhelptopic').css("display:none");
	<?php } ?>
    $('#keyost_departmentsetting').click(function() {
        if(!$('#keyost_departmentsetting').is(':checked')){
			$('.trdepartment').toggle('show');
            alert("Please Select Department");	
		}
    });
	$('#keyost_helptopicsetting').click(function() {
        if(!$('#keyost_helptopicsetting').is(':checked')){
			$('.trhelptopic').toggle('show');
                        alert("Please Select Helptopic");
                    }		
});
        $("#ostconfig").click(function(){
        if(!$('#keyost_departmentsetting').is(':checked') && $( "#keyost_defaultdepartmentsetting" ).val()==0){
            alert("Please Select Department");
            return false;
        } else if(!$('#keyost_helptopicsetting').is(':checked') && $( "#keyost_helptopicsetting" ).val()==0){
        }else{
            $("#ostconfigform").submit();
        }
    });
});
</script>
<form name="mbform" action="admin.php?page=ost-config" method="post" id="ostconfigform">
<ul class="key4ce_cofigtb key4ce_respForm">
    <li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Host Name:", 'key4ce-osticket-bridge'); ?></label></div>
        <div class="key4ce_inputTextWrap"><input type="text" name="host" id="host" size="20" value="<?php echo @$host;?>"/>&nbsp;&nbsp;<?php echo __("( Normally this is localhost )", 'key4ce-osticket-bridge'); ?></div>
    </li>
    <li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Database Name:", 'key4ce-osticket-bridge'); ?></label></div>                
        <div class="key4ce_inputTextWrap"><input type="text" name="database" id="database" size="20" value="<?php echo @$database;?>"/>&nbsp;&nbsp;<?php echo __("( osTicket Database Name Goes Here )", 'key4ce-osticket-bridge'); ?></div>
    </li>
    <li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Database Username:", 'key4ce-osticket-bridge'); ?></label></div>
        <div class="key4ce_inputTextWrap"><input type="text" name="username" id="username" size="20" value="<?php echo @$username;?>"/>&nbsp;&nbsp;<?php echo __("( osTicket Database Username Goes Here )", 'key4ce-osticket-bridge'); ?></div>
    </li>
    <li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Database Password:", 'key4ce-osticket-bridge'); ?></label></div>
        <div class="key4ce_inputTextWrap"><input type="password" name="password" id="password" size="20" value="<?php echo @$password;?>"/>&nbsp;&nbsp;<?php echo __("( osTicket Database Password Goes Here )", 'key4ce-osticket-bridge'); ?></div>
    </li>
    <li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Database Prefix:", 'key4ce-osticket-bridge'); ?></label></div>
        <div class="key4ce_inputTextWrap"><input type="text" name="keyost_prefix" id="keyost_prefix" size="20" value="<?php echo @$keyost_prefix;?>"/>&nbsp;&nbsp;<?php echo __("( osTicket Database Prefix Goes Here )", 'key4ce-osticket-bridge'); ?></div>
    </li>
    <li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Osticket Version:", 'key4ce-osticket-bridge'); ?></label></div>
        <div class="key4ce_inputTextWrap">
            <?php 
                if(@$keyost_version==193)
                	   @$keyost_version_193="selected='selected'";
                else if(@$keyost_version==194)
                        @$keyost_version_194="selected='selected'";		
                else if(@$keyost_version==195)
                        @$keyost_version_195="selected='selected'";
                else if (@$keyost_version==1951)
                        @$keyost_version_1951="selected='selected'";
				else 
                        @$keyost_version_1914="selected='selected'";
            ?>
            <select name="keyost_version" id="keyost_version">
            <option value="193" <?php echo @$keyost_version_193; ?>>Ver. 1.9.3</option>
            <option value="194" <?php echo @$keyost_version_194; ?>>Ver. 1.9.4</option>
            <option value="195" <?php echo @$keyost_version_195; ?>>Ver. 1.9.5</option>
            <option value="1951" <?php echo @$keyost_version_1951; ?>>Ver. >=1.9.5.1</option>
			<option value="1914" <?php echo @$keyost_version_1914; ?>>Ver. >=1.9.14</option>
            </select>&nbsp;&nbsp;<?php echo __("(Select Osticket Version)", 'key4ce-osticket-bridge'); ?>
        </div>
    </li>
    <li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Enable Closing Ticket By User:", 'key4ce-osticket-bridge'); ?></label></div>
        <div class="key4ce_inputTextWrap"><input type="checkbox" name="keyost_usercloseticket" id="keyost_usercloseticket" <?php echo (@$keyost_usercloseticket=="1") ? 'checked' : ''; ?>/>&nbsp;&nbsp;</div>
    </li>
    <li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Landing Page Name:", 'key4ce-osticket-bridge'); ?></label></div>
        <div class="key4ce_inputTextWrap">
            <input type="text" name="supportpage" id="supportpage" size="20" value="<?php echo $supportpage;?>"/>&nbsp;&nbsp;<?php echo __("Landing Page Name( Create this page...read Landing Page Note above! )", 'key4ce-osticket-bridge'); ?></div>
    </li>
    <li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Contact Ticket Page:", 'key4ce-osticket-bridge'); ?></label></div>
        <div class="key4ce_inputTextWrap">
            <select name="contactticketpage" id="key4ce_contactticketpage">
                <?php $args = array(
                    'sort_order' => 'ASC',
                    'sort_column' => 'post_title',
                    'hierarchical' => 5,
                    'child_of' => 0,
                    'parent' => -1,
                    'offset' => 0,
                    'post_type' => 'page',
                    'post_status' => 'publish'
                    );
                $pages = get_pages($args);
                foreach($pages as $page){
                    if($contactticketpage==$page->ID)
                        $selectedpage="selected='selected'";
                    else
                        $selectedpage="";
                    ?>
                <option value="<?php echo $page->ID;?>" <?php echo $selectedpage;?>><?php echo $page->post_title; ?></option>
                    <?php	} ?>
            </select>&nbsp;&nbsp;<?php echo __("(Select contact ticket page)", 'key4ce-osticket-bridge'); ?>
        </div>
    </li>
    <li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Thank You Page:", 'key4ce-osticket-bridge'); ?></label></div>
        <div class="key4ce_inputTextWrap">
            <select name="thankyoupage" id="key4ce_thankyoupage">
                <?php $args = array(
                    'sort_order' => 'ASC',
                    'sort_column' => 'post_title',
                    'hierarchical' => 5,
                    'child_of' => 0,
                    'parent' => -1,
                    'offset' => 0,
                    'post_type' => 'page',
                    'post_status' => 'publish'
                     );
                $pages = get_pages($args);
                foreach($pages as $page){
                    if($thankyoupage==$page->ID)
                        $selectedpage="selected='selected'";
                    else
                        $selectedpage="";
                    ?>
                <option value="<?php echo $page->ID;?>" <?php echo $selectedpage;?>><?php echo $page->post_title; ?></option>
                    <?php	} ?>
            </select>&nbsp;&nbsp;<?php echo __("(Select thank you page)", 'key4ce-osticket-bridge'); ?>
        </div>
    </li>
	<li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Knowledgebase Page:", 'key4ce-osticket-bridge'); ?></label></div>
        <div class="key4ce_inputTextWrap">
            <select name="knowledgebasepage" id="key4ce_knowledgebasepage">
                <?php $args = array(
                    'sort_order' => 'ASC',
                    'sort_column' => 'post_title',
                    'hierarchical' => 5,
                    'child_of' => 0,
                    'parent' => -1,
                    'offset' => 0,
                    'post_type' => 'page',
                    'post_status' => 'publish'
                    );
                $pages = get_pages($args);
                foreach($pages as $page){
                    if($knowledgebasepage==$page->ID)
                        $selectedpagekb="selected='selected'";
                    else
                        $selectedpagekb="";
                    ?>
                <option value="<?php echo $page->ID;?>" <?php echo $selectedpagekb;?>><?php echo $page->post_title; ?></option>
                    <?php	} ?>
            </select>&nbsp;&nbsp;<?php echo __("(Select Knowledgebase page)", 'key4ce-osticket-bridge'); ?>
        </div>
    </li>
    <li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Google reCAPTCHA Secretkey:", 'key4ce-osticket-bridge'); ?></label></div>
        <div class="key4ce_inputTextWrap"><input type="text" name="googlesecretkey" id="googlesecretkey" size="40" value="<?php echo @$googlesecretkey;?>"/>&nbsp;&nbsp;<?php echo __("(Your Google reCAPTCHA Secretkey Goest Here)", 'key4ce-osticket-bridge'); ?></div>
    </li>
    <li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Google reCAPTCHA Sitekey:", 'key4ce-osticket-bridge'); ?></label></div>
        <div class="key4ce_inputTextWrap"><input type="text" name="googlesitekey" id="googlesitekey" size="40" value="<?php echo @$googlesitekey;?>"/>&nbsp;&nbsp;<?php echo __("(Your Google reCAPTCHA Sitekey Goest Here)", 'key4ce-osticket-bridge'); ?></div>
    </li>
<!-- Department Configuration for Contact page start here-->
    <li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Enable Department on Contact Page:", 'key4ce-osticket-bridge'); ?></label></div>
        <div class="key4ce_inputTextWrap"><input type="checkbox" name="keyost_departmentsetting" id="keyost_departmentsetting" <?php echo (@$keyost_departmentsetting=="1") ? 'checked' : ''; ?>/>&nbsp;&nbsp;</div>
    </li>
<?php
$config = get_option('os_ticket_config');
extract($config);
if($keyost_departmentsetting=="0")
		$trdepartment="display:table-row";
else
		$trdepartment="display:none";
//if((($host!="") || ($database!="") || ($username!="") || ($password!="")) && )
//{
?>
    <li  class="trdepartment" id="trdepartment">
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Default Department on Contact Page:", 'key4ce-osticket-bridge'); ?><font class="key4ce_error">&nbsp;*</font></label></div>                
        <div class="key4ce_inputTextWrap">
        <?php
            $ost_wpdb = new wpdb($username, $password, $database, $host);
            $dept_table=$keyost_prefix."department";
            if($keyost_version==1914)
			{
				$dept_opt=$ost_wpdb->get_results("SELECT name as dept_name,id as dept_id FROM $dept_table where ispublic=1");
			}
			else
			{
				$dept_opt=$ost_wpdb->get_results("SELECT dept_name,dept_id FROM $dept_table where ispublic=1");
			}
			//$dept_opt = $ost_wpdb->get_results("SELECT dept_name,dept_id FROM $dept_table where ispublic=1");
        ?>
        <select id="keyost_defaultdepartmentsetting" name="keyost_defaultdepartmentsetting">
            <option value="0" selected="selected"><?php echo __('Select a Department','key4ce-osticket-bridge'); ?></option>
        <?php
            foreach ($dept_opt as $dept) {
                if($keyost_defaultdepartmentsetting==$dept->dept_id)
                    $keyost_defaultdepartmentsetting_select="selected='selected'";
                else
                    $keyost_defaultdepartmentsetting_select="";
                echo '<option value="' . $dept->dept_id . '" '.$keyost_defaultdepartmentsetting_select.'>' . $dept->dept_name . '</option>';
                }
        ?>
        </select>&nbsp;&nbsp;<?php echo __("(Default Department on Contact Page)", 'key4ce-osticket-bridge'); ?>
        </div>
    </li>

<!-- Department Configuration for Contact page end here-->
<!-- Helptopic Configuration for Contact page start here-->
    <li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Enable Helptopic on Contact Page:", 'key4ce-osticket-bridge'); ?></label></div>
        <div class="key4ce_inputTextWrap"><input type="checkbox" name="keyost_helptopicsetting" id="keyost_helptopicsetting" <?php echo (@$keyost_helptopicsetting=="1") ? 'checked' : ''; ?>/>&nbsp;&nbsp;</div>
    </li>
        <?php
        if($keyost_helptopicsetting=="0")
		$trhelptopic="display:table-row";
        else
		$trhelptopic="display:none";
        //if((($host!="") || ($database!="") || ($username!="") || ($password!="")) && $keyost_helptopicsetting=="0")
        //{
        $topic_table=$keyost_prefix."help_topic";
        $topic_opt = $ost_wpdb->get_results("SELECT topic_id,topic FROM $topic_table  where ispublic=1 and isactive=1 ORDER BY `sort` ASC ");
        ?>
    <li class="trhelptopic" id="trhelptopic">
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Default Helptopic on Contact Page:", 'key4ce-osticket-bridge'); ?><font class="key4ce_error">&nbsp;*</font></label></div>
        <div class="key4ce_inputTextWrap">
            <select id="keyost_defaulthelptopicsetting" name="keyost_defaulthelptopicsetting">
                <option value="0" selected="selected"><?php echo __('Select a Help Topic','key4ce-osticket-bridge'); ?></option>
                    <?php foreach ($topic_opt as $topic) {
                        if($keyost_defaulthelptopicsetting==$topic->topic_id)
                            $keyost_defaulthelptopicsetting_select="selected='selected'";
                        else
                            $keyost_defaulthelptopicsetting_select="";
                            echo '<option value="' . $topic->topic_id . '" '.$keyost_defaulthelptopicsetting_select.'>' . $topic->topic . '</option>';
		}
		?>
            </select>&nbsp;&nbsp;<?php echo __("(Default Helptopic on Contact Page)", 'key4ce-osticket-bridge'); ?>
        </div>
    </li>
<!-- Helptopic Configuration for Contact page end here-->
<!-- Contact Page File Attachement Enable/Disable Start Here-->
    <li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Enable File Attachement on Contact Page:", 'key4ce-osticket-bridge'); ?></label></div>
        <div class="key4ce_inputTextWrap"><input type="checkbox" name="keyost_contactfilestatus" id="keyost_contactfilestatus" <?php echo (@$keyost_contactfilestatus=="1") ? 'checked' : ''; ?>/>&nbsp;&nbsp;</div>
    </li>
<!-- Contact Page File Attachement Enable/Disable End Here -->
<!--Knowledgebase Custom Slug Code Start Here -->
	<li>
        <div class="key4ce_config_td"><label class="key4ce_config_label"><?php echo __("Custom Knowledgebase Slug:", 'key4ce-osticket-bridge'); ?></label></div>
        <div class="key4ce_inputTextWrap"><input type="text" name="keyost_kbcustomslg" id="keyost_kbcustomslg" value="<?php echo @$keyost_kbcustomslg;?>" />&nbsp;&nbsp;</div>
    </li>
<!--Knowledgebase Custom Slug Code Start Here -->
</ul>
<div style="padding-left: 10px;">
    <input type="submit" name="submit" class="key4ce_button-primary button button-primary" id="ostconfig" value="<?php echo __('Save Changes', 'key4ce-osticket-bridge'); ?>" />
</div>
</form>
</div><!--End of wrap-->
<?php wp_enqueue_script('ost-bridge-fade',plugins_url('../js/fade.js',__FILE__));?>