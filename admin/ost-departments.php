<?php
/*
Template Name: ost-departments
*/
?>
<?php require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/includes/udscript.php' ); ?>
<div class="key4ce_wrap">
<div class="key4ce_headtitle"><?php echo __("Departments", 'key4ce-osticket-bridge'); ?></div>
<div style="clear: both"></div>
<?php require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/header_nav.php' ); ?>
<div id="key4ce_tboxwh" class="key4ce_pg1">
	<?php echo __("Please select departments from following list to display frontend", 'key4ce-osticket-bridge'); ?>
</div>
<div style="clear: both"></div>
<?php 
require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/admin/db-settings.php' );
if(($dipslay_department_from=="" && $dipslay_department_to=="") || checkLicense()=="activated")
{
	$dept_from = $ost_wpdb->get_results("SELECT dept_name,dept_id FROM $dept_table where ispublic=1");
}
else
{
	if($dipslay_department_from!="")
		$dept_from = $ost_wpdb->get_results("SELECT dept_name,dept_id FROM $dept_table where dept_id IN (".$dipslay_department_from.") AND ispublic=1");
	if($dipslay_department_to!="")
		$dept_to = $ost_wpdb->get_results("SELECT dept_name,dept_id FROM $dept_table where dept_id IN (".$dipslay_department_to.") AND ispublic=1");
}

?>
<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__).'../css/bootstrap.min334.css'; ?>">
<div id="main3" style="display: block;">
<form name="ost-departments" class="ost-departments key4ce_cofigtb key4ce_ost_depForm" action="admin.php?page=ost-departments" method="post">
            <div class="row">
                <div class="col-sm-3">
				<h4 id="demo-zero-configuration"><?php echo __("Available Departments", 'key4ce-osticket-bridge'); ?></h4>
                    <select name="from[]" id="multiselect" class="form-control" size="8" multiple="multiple">
					<?php
                    foreach ($dept_from as $deptfr) {
					?>
					<option value="<?php echo $deptfr->dept_id; ?>" data-position="<?php echo $deptfr->dept_id; ?>"><?php echo $deptfr->dept_name; ?></option>
					<?php
					}?>
                    </select>
                </div>
                <div class="col-sm-2">
				<h4 id="demo-zero-configuration"><?php echo __("Navigation", 'key4ce-osticket-bridge'); ?></h4>
                    <button type="button" id="multiselect_rightAll" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
                    <button type="button" id="multiselect_rightSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
                    <button type="button" id="multiselect_leftSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
                    <button type="button" id="multiselect_leftAll" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
                </div>              
                <div class="col-sm-3">
				 <h4 id="demo-zero-configuration"><?php echo __("Selected Departments", 'key4ce-osticket-bridge'); ?></h4>
                    <select name="to[]" id="multiselect_to" class="form-control" size="8" multiple="multiple">
					<?php
                    foreach ($dept_to as $deptto) {
					?>
					<option value="<?php echo $deptto->dept_id; ?>" data-position="<?php echo $deptto->dept_id; ?>"><?php echo $deptto->dept_name; ?></option>
					<?php
					}?>
					</select>
                </div>
				<div class="col-sm-3"><h4 id="demo-zero-configuration"><?php echo __("Display Name", 'key4ce-osticket-bridge'); ?></h4></div>
            </div>
		<div style="margin-top: 20px;">
    <input type="submit" name="ost-departments" class="key4ce_button-primary button button-primary" value="<?php echo __("Save Departments", 'key4ce-osticket-bridge'); ?>" />
</div>
</form>
<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__).'../js/jquery.min191.js'; ?>"></script>
<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__).'../js/bootstrap.min334.js'; ?>"></script>
	<?php wp_enqueue_script('ost-bridge-validate',plugins_url('../js/multiselect.min.js', __FILE__)); ?>
  <script type="text/javascript">
  jMultino=jQuery.noConflict();
    jMultino(document).ready(function() {
        jMultino('#multiselect').multiselect();
    });
    </script>
</div>
<div style="padding-top:40px;"></div>
<div style="clear: both"></div>
</div><!--End wrap-->