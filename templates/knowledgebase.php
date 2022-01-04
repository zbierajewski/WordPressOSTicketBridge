<?php
@session_start();
/* Template Name: knowledgebase.php */	
require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/templates/kb_nav_bar.php');
$taxonomy = 'key4ce_kb_tax';
$faq_category_list = get_terms($taxonomy);
?>
<div class="kb">
<?php echo __("Click on the category to browse FAQs.", 'key4ce-osticket-bridge'); ?>
<ul>
		<?php  foreach($faq_category_list as $category) { ?>
			<li><span style="margin-right:10px;"><img src="<?php echo plugin_dir_url(__FILE__) . '../images/kb_large_folder.png'; ?>"></span>
			<a href="<?php echo esc_attr(get_term_link($category, $taxonomy)); ?>"><?php echo $category->name; ?> (<?php echo $category->count; ?>)</a>
			<br />
			<?php echo $category->description ; ?>
			</li>
			
	<?php } ?>
</ul>
</div>