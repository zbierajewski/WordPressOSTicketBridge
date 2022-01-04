<?php
require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/db-settings.php' );
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
if(isset($_POST['delete'])){	
	$delete_ticket_list=$_POST['tickets'];	
	$i=0;
	foreach($delete_ticket_list as $delete_ticket){			
		$ost_wpdb->query("DELETE FROM $ticket_table WHERE ticket_id =".$delete_ticket);
		$ost_wpdb->query("DELETE FROM $thread_table WHERE ticket_id =".$delete_ticket);	
		$ost_wpdb->query("DELETE FROM $ticket_cdata WHERE ticket_id =".$delete_ticket);
		$file_id = $ost_wpdb->get_var("SELECT file_id from $ost_ticket_attachment WHERE ticket_id = '$delete_ticket'");	
		$ost_wpdb->query("DELETE FROM $ost_file WHERE id =".$file_id);
		$ost_wpdb->query("DELETE FROM $ost_ticket_attachment WHERE ticket_id =".$delete_ticket);				
		$i++;
	}
	$deletedstr = sprintf( __( '%d record(s) has been deleted successfully', 'key4ce-osticket-bridge' ),$i);
	echo '<div style="color: red;font-size: 15px;font-weight: bold;margin-top: 20px;text-align: center;">'.$deletedstr.'</div>';
	echo "<script>window.location.href=location.href;</script>";	
}
if(isset($_POST['close'])){		
	$close_ticket_list=$_POST['tickets'];
	$i=0;
	foreach($close_ticket_list as $close_ticket){			
		if($keyost_version==194 || $keyost_version==195 || $keyost_version==1951 || $keyost_version==1914)
			$ost_wpdb->update($ticket_table, array('status_id'=>'3'), array('ticket_id'=>$close_ticket), array('%s'));
		else
			$ost_wpdb->update($ticket_table, array('status'=>'closed'), array('ticket_id'=>$close_ticket), array('%s'));
		$i++;
	}
	$closedstr = sprintf( __( '%d record(s) has been closed successfully', 'key4ce-osticket-bridge' ),$i);
	echo "<div style=' color: red;font-size: 15px;font-weight: bold;margin-top: 20px;text-align: center;'>".$closedstr."</div>";
	echo "<script>window.location.href=location.href;</script>";	
}
if(isset($_POST['reopen'])){			
	$reopen_ticket_list=$_POST['tickets'];
	$i=0;
	foreach($reopen_ticket_list as $reopen_ticket){				
		if($keyost_version==194 || $keyost_version==195 || $keyost_version==1951 || $keyost_version==1914)
			$ost_wpdb->update($ticket_table, array('status_id'=>'1'), array('ticket_id'=>$reopen_ticket), array('%s'));
		else
			$ost_wpdb->update($ticket_table, array('status'=>'open'), array('ticket_id'=>$reopen_ticket), array('%s'));
		$i++;
	}
	$reopenedstr = sprintf( __( '%d record(s) has been re-opened successfully', 'key4ce-osticket-bridge' ),$i);
	echo "<div style=' color: red;font-size: 15px;font-weight: bold;margin-top: 20px;text-align: center;'>".$reopenedstr."</div>";
	echo "<script>window.location.href=location.href;</script>";	
}
?>
<style>
	#ui-id-7{cursor : pointer;}
	.key4ce_subject{text-align:left;}
	.key4ce_department{text-align:left;}
	.key4ce_topic{text-align:left;}
	table { 
		width: 98%; 
		border-collapse: collapse; 
	}
	/* Zebra striping */
	tr:nth-of-type(odd) { 
		background: #eee; 
	}
	th { 
		background: #333; 
		color: white; 
		font-weight: 400; 
		padding: 6px;
		text-align: center; 
	}
	td{ 
		padding: 6px; 
		border: 1px solid #ccc; 
		text-align: center; 
	}
	.key4ce_priority_red,.key4ce_priority_orange,.key4ce_priority_green,.key4ce_priority_black{display:none;}
</style>
<!--[if !IE]><!-->
	<style>
	
	/* 
	Max width before this PARTICULAR table gets nasty
	This query will take effect for any screen smaller than 760px
	and also iPads specifically.
	*/
	@media 
	only screen and (max-width: 760px),
	(min-device-width: 768px) and (max-device-width: 1024px)  {

		/* Hide table headers (but not display: none;, for accessibility) */
		thead tr { 
			position: absolute;
			top: -9999px;
			left: -9999px;
		}
		
		tr { border: 1px solid #ccc; }
		
		td { 
			/* Behave  like a "row" */
			border: none;
			border-bottom: 1px solid #eee; 
			position: relative;
		}
		
		td:before { 
			/* Now like a table header */
			position: absolute;
			/* Top/left values mimic padding */
			top: 6px;
			left: 6px;
			width: 45%; 
			padding-right: 10px; 
			white-space: nowrap;
		}
		/*
		Label the data
		*/
		/*td:nth-of-type(1):before { content: "Checkall"; }
		td:nth-of-type(2):before { content: "Ticket#"; }
		td:nth-of-type(3):before { content: "Subject"; }
		td:nth-of-type(4):before { content: "Priority"; }
		td:nth-of-type(5):before { content: "Department"; }
		td:nth-of-type(6):before { content: "Help Topic"; }
		td:nth-of-type(7):before { content: "Date"; }*/
	}
	
	/* Smartphones (portrait and landscape) ----------- */
	@media only screen
	and (max-width: 760px){
		body { 
			padding: 0; 
			margin: 0; }
		.noEdit{display:none;}
		.key4ce_department , .key4ce_topic , .key4ce_datetime , .key4ce_priority{display:none;}
		.key4ce_subject{width:100%;text-align:left;padding-left: 25px;}
		.key4ce_table {overflow: hidden;}
		.key4ce_priority_red
		{
			display:block;
			background-color:red;
			float: right;
			margin-right: -16px;
			width: 5px;
		}
		.key4ce_priority_orange
		{
			display:block;
			background-color:orange;
			float: right;
			margin-right: -16px;
			width: 5px;
		}
		.key4ce_priority_green
		{
			display:block;
			background-color:green;
			color: #fff;
			float: right;
			margin-right: -16px;
			width: 5px;
		}
		.key4ce_priority_black
		{
			display:block;
			background-color:black;
			float: right;
			margin-right: -16px;
			width: 5px;
		}
		}
	
	/* iPads (portrait and landscape) ----------- */
	@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
		body { 
			width: 495px; 
		}
	}
	
	</style>
	<!--<![endif]-->
<script type="text/javascript">
function checkAll(ele) {
     var checkboxes = document.getElementsByTagName('input');
     if (ele.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = true;
             }
         }
     } else {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = false;
             }
         }
     }
 }
 function editThis(id){
	location.href='admin.php?page=ost-tickets&service=view&ticket='+id;
 }
 jQuery(document).ready(function() {
  jQuery('#ui-id-7 td.noEdit').click(function(e){
   e.stopPropagation();
});
});
</script>
<div class="key4ce_wrap" id="page-wrap">

<div class="key4ce_headtitle"><?php echo __("Support/Request List", 'key4ce-osticket-bridge'); ?></div>

<div style="clear: both"></div>
<?php require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/header_nav_ticket.php' ); ?>
	<div style="clear: both"></div>
        <div align="center" style="padding-top:20px;"></div>
		<form name="ticketview" id="ticketview" method="post" onSubmit="if(!confirm('<?php echo __("Are you sure you want to continue?", 'key4ce-osticket-bridge'); ?>')){return false;}">
        <table class="key4ce_table">
<?php
if($list_opt){
?>
<thead>
    <tr>
		<th><input type="checkbox" value="1" name="chk[]" onchange="checkAll(this)"></th>
        <th><?php echo __("Ticket#", 'key4ce-osticket-bridge'); ?></th>
        <th class="key4ce_subject"><?php echo __("Subject", 'key4ce-osticket-bridge'); ?></th>
        <th><?php echo __("Priority", 'key4ce-osticket-bridge'); ?></th>
        <th class="key4ce_department"><?php echo __("Department", 'key4ce-osticket-bridge'); ?></th>
        <th class="key4ce_topic"><?php echo __("Help Topic", 'key4ce-osticket-bridge'); ?></th>
        <th><?php echo __("Date", 'key4ce-osticket-bridge'); ?></th>        
    </tr>
</thead>
<tbody>
    <?php
    foreach($list_opt as $list){
                if ($list->subject==""){
                    @$sub_str=Format::stripslashes('Ticket subject not found');
                    } else {
                        @$sub_str=Format::stripslashes($list->subject);
                    }
                    if($keyost_version==194 || $keyost_version==195 || $keyost_version==1951 || $keyost_version==1914)
                        $priority=$list->priority;
                    else
                        $priority=$list->priority_id;
                    if($priority==4)
                        $color="red";
                    elseif($priority==3)
                        $color="orange";
                    elseif($priority==2 || $priority=="")
                        $color="green";
                    elseif($priority==1)
                        $color="black";
					if($keyost_version==1914)
					{
						$dept_name=$list->name;
					}
					else
					{
						$dept_name=$list->dept_name;
					}
						
                    ?>
                    
                   
        <tr id="ui-id-7" onclick="editThis(<?php echo $list->number; ?>);">
			 <td class="noEdit"><input type='checkbox' name='tickets[]' value=<?php echo $list->ticket_id; ?>></td>
			<?php if($priority==4){ ?>
            <td><?php echo $list->number; ?><span class="key4ce_priority_red">&nbsp;</span></td>
			 <?php } elseif($priority==3){ ?>
			   <td><?php echo $list->number; ?><span class="key4ce_priority_orange">&nbsp;</span></td>
			  <?php } elseif($priority==2 || $priority==""){ ?>
			    <td><?php echo $list->number; ?><span class="key4ce_priority_green">&nbsp;</span></td>
			  <?php } elseif($priority==1){ ?>
			    <td><?php echo $list->number; ?><span class="key4ce_priority_black">&nbsp;</span></td>
			 <?php } ?>
            <td class="key4ce_subject"><?php echo key4ce_truncate($sub_str,60,'...'); ?></td>
            <td class="key4ce_priority">
            <?php if($priority==4){ ?>
            <div class="key4ce_ticketPriority" style="background-color:red;"><?php echo __("Emergency", 'key4ce-osticket-bridge'); ?></div>
            <?php } elseif($priority==3){ ?>
            <div class="key4ce_ticketPriority" style="background-color:orange;"><?php echo __("High", 'key4ce-osticket-bridge'); ?></div>
            <?php } elseif($priority==2 || $priority==""){ ?>
            <div class="key4ce_ticketPriority" style="background-color:green;"><?php echo __("Normal", 'key4ce-osticket-bridge'); ?></div>
            <?php } elseif($priority==1){ ?>
                <div class="key4ce_ticketPriority" style="background-color:black;"><?php echo __("Low", 'key4ce-osticket-bridge'); ?></div>
            <?php } ?>
            </td>
            <td class="key4ce_department"><?php echo $dept_name; ?></td>
            <td class="key4ce_topic"><?php echo $list->topic; ?></td>
            <td class="key4ce_datetime">
            <?php 
            if ($list->updated=='0000-00-00 00:00:00')
			{
            $date_str  = $list->created;
            }else{
            $date_str  = $list->updated; 
            }
           echo __formatDate($datetime_format,$date_str);
            ?>
            </td>
           
        </tr>
	  <?php	} ?>
    </tbody>
<?php } else { echo "No Records Found"; } ?>
</table>

<div align="center" style="padding-top:15px;"></div>

<div style="clear: both"></div>

<?php if(count($list_opt) > 0) { ?>
<div>
<input type="submit" class="button button-primary" name="delete" value="<?php echo __("Delete", 'key4ce-osticket-bridge'); ?>">
<?php if(@$_REQUEST['status']=="open" || @$_REQUEST['status']=="answered") { ?>
<input type="submit" class="button button-primary" name="close" value="<?php echo __("Close", 'key4ce-osticket-bridge'); ?>">
<?php } else if (@$_REQUEST['status']=="all") {?>
<input type="submit" class="button button-primary" name="close" value="<?php echo __("Close", 'key4ce-osticket-bridge'); ?>">
<input type="submit" class="button button-primary" name="reopen" value="<?php echo __("Reopen", 'key4ce-osticket-bridge'); ?>">
<?php } else if (@$_REQUEST['status']=="closed") { ?>
<input type="submit" class="button button-primary" name="reopen" value="<?php echo __("Reopen", 'key4ce-osticket-bridge'); ?>">
<?php  } else {?>
<input type="submit" class="button button-primary" name="close" value="<?php echo __("Close", 'key4ce-osticket-bridge'); ?>">
<?php } ?>
</div>
<?php } ?>
<?php require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/includes/pagination.php' ); ?>
</div><!--End wrap-->
</form>