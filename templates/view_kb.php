<?php
/* Template Name: view_kb.php */
@session_start();
$config = get_option('os_ticket_config');
extract($config);
$ost_wpdb = new wpdb($username, $password, $database, $host);
$ost_faq_category=$keyost_prefix . "faq_category";
$ost_faq_topic=$keyost_prefix . "faq_topic";
$ost_faq=$keyost_prefix . "faq";
require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/includes/functions.php');
	if(isset($_REQUEST['service']) && $_REQUEST['service']=="viewfaq")
	{
		if(isset($_REQUEST['sb_input']) && $_REQUEST['sb_input']!="")
		{
			if(isset($_REQUEST['categoryids']) && $_REQUEST['categoryids']!="")
				$category_search=" AND category_id IN(".$_REQUEST['categoryids'].")";
			else
				$category_search="";
			//echo "SELECT question,answer  FROM ".$ost_faq." WHERE `ispublished` = 1 AND (`question` LIKE '%".$_REQUEST['sb_input']."%' OR `answer` LIKE '%".$_REQUEST['sb_input']."%')" .$category_search;
			$faq_list=$ost_wpdb->get_results("SELECT question,answer  FROM ".$ost_faq." WHERE `ispublished` = 1 AND (`question` LIKE '%".$_REQUEST['sb_input']."%' OR `answer` LIKE '%".$_REQUEST['sb_input']."%')" .$category_search);
		}
		else
		{
			$faqcatid=$_REQUEST['faqcatid'];
			$faq_list=$ost_wpdb->get_results("SELECT question,answer FROM ".$ost_faq." WHERE ispublished=1 and category_id=".$faqcatid."");
			$faq_category=$ost_wpdb->get_results("SELECT category_id,name,description FROM ".$ost_faq_category." WHERE ispublic=1 AND category_id=".$faqcatid."");
		}
		
		
?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script>
  $(function() {
    $( "#accordion" ).accordion(
	{
		heightStyle: "content"
	}
	);
  });
  </script>
	<h3><?php echo $faq_category[0]->name; ?></h3>
	<p><?php echo $faq_category[0]->description; ?></p>
	<div id="accordion">
<?php
		foreach($faq_list as $faq)
		{
	?>
		<h3><?php echo $faq->question; ?></h3>
		<div><?php echo $faq->answer; ?></div>
	<?php
		}
?>
	</div>
<?php
	}
	else
	{
		$faqid=$_REQUEST['faqid'];
		$faq=$ost_wpdb->get_results("SELECT question,answer FROM ".$ost_faq." WHERE ispublished=1 and faq_id=".$faqid."");
		echo "<h3>".$faq[0]->question."</h3><br />";
		echo $faq[0]->answer;
	}
?>