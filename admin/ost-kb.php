<?php
/*
Template Name: ost-kb
*/
?>
<?php require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/includes/udscript.php' ); ?>
<div class="key4ce_wrap">
<div class="key4ce_headtitle"><?php echo __("Knowledge Base Synchronous", 'key4ce-osticket-bridge'); ?></div>
<div style="clear: both"></div>
<?php require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/header_nav.php' ); ?>
<div id="key4ce_tboxwh" class="key4ce_pg1"><?php echo __("Please select category from following list for synchronous", 'key4ce-osticket-bridge'); ?></div>
<div style="clear: both"></div>
<?php 
require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/db-settings.php' ); 
?>
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
 jQuery(function ($) {
	 $(".kberror").css("display", "none");
    //form submit handler
    $('#ost-kb-syncs').submit(function (e) {
        //check atleat 1 checkbox is checked
        if (!$('.kbcheck').is(':checked')) {
			$(".kberror").css("display", "block");
            //prevent the default form submit if it is not checked
            e.preventDefault();
        }
    })
})
</script>

<div id="main3" style="display: block;">
<form name="ost-kb-syncs" action="admin.php?page=ost-kb" method="post" id="ost-kb-syncs">
<?php
$faq_category_list=$ost_wpdb->get_results("SELECT category_id,name,description,ispublic FROM ".$ost_faq_category."");
?>
<input type="hidden" name="kbsync" id="kbsync" value="kbsyncvalue" class="form_newticket_subject"></div>    
<div align="left" style="padding-left:10px;">
<div class="kberror" style="color: red; font-weight: bold; font-size: 14px;">Please select atleast one checkbox.</div>
<ul>
<li><input type="checkbox" name="catcheck[]" onchange="checkAll(this)"/><label for="all"><strong><?php echo __("All Categories", 'key4ce-osticket-bridge'); ?></strong></label></li>
<?php
		foreach($faq_category_list as $category)
		{
			
?>
			<li><input type="checkbox" value="<?php echo $category->name."|".$category->description."|".$category->category_id; ?>" name="createcat[]>" class="kbcheck"/><label for="<?php echo $category->name; ?>"><?php echo $category->name; ?> ( <?php echo $category->ispublic==1?"Public":"Private"; ?> ) </label></li>	
<?php
		}
?>
</ul>

<input type="submit" name="ost-kb-syncs" class="key4ce_button-primary button button-primary" value="<?php echo __("Start Synchronous", 'key4ce-osticket-bridge'); ?>" />

</div>
</form>
</div>
<div style="padding-top:40px;"></div>
<div style="clear: both"></div>
</div><!--End wrap-->