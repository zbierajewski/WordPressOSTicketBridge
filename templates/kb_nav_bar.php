<?php
echo '<link rel="stylesheet" type="text/css" media="all" href="'.plugin_dir_url(__FILE__).'../css/searchstyle.css">';
@session_start();
$config = get_option('os_ticket_config');
extract($config);
$ost_wpdb = new wpdb($username, $password, $database, $host);
$ost_faq_category=$keyost_prefix . "faq_category";
$ost_faq_topic=$keyost_prefix . "faq_topic";
$ost_faq=$keyost_prefix . "faq";
require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/includes/functions.php');
// KB SQL Query Start Here
$taxonomy = 'key4ce_kb_tax';
$faq_category_list = get_terms($taxonomy);
?>
	<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__).'../js/jquery.min142.js'; ?>"></script>
	<script type="text/javascript">
		$(function() {
			var $ui 		= $('#ui_element');
			$( ".sb_down" ).click(function() {
				updatecategory();
				$('label[for="all"]').parent().siblings().find(':checkbox').prop('checked',true).prop('disabled',true);
				$( "#checkAll" ).prop('checked',true);
				});
			$ui.find('.sb_down').bind('click',function(){
				$ui.find('.sb_down')
				   .addClass('sb_up')
				   .removeClass('sb_down')
				   .andSelf()
				   .find('.sb_dropdown')
				   .show();
			});
			$ui.bind('mouseleave',function(){
				$ui.find('.sb_up')
				   .addClass('sb_down')
				   .removeClass('sb_up')
				   .andSelf()
				   .find('.sb_dropdown')
				   .hide();
			});
			$ui.find('.sb_dropdown').find('label[for="all"]').prev().bind('click',function(){
				$(this).parent().siblings().find(':checkbox').prop('checked',true).prop('disabled',this.checked);
			});
			
		});
	</script>
  <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__).'../css/jquery-ui.css'; ?>">
  <script src="<?php echo plugin_dir_url(__FILE__).'../js/jquery1102.js'; ?>"></script>
  <script src="<?php echo plugin_dir_url(__FILE__).'../js/jqueryui-1114.js'; ?>"></script>
    <script>
	 function updatecategory() {
            var allVals = [];
            $('.sb_dropdown > li :checked').each(function () {
                allVals.push($(this).val());
            });
            $('#categoryids').val(allVals)
        }
	$(function() {
		$('.sb_dropdown > li input').click(updatecategory);
	$("#sb_input").autocomplete({
        source: function(request, response){
			//	var categoryidsvalue = $('#categoryidsdata').val();
            $.get("<?php echo admin_url( 'admin-ajax.php' ); ?>?action=faq_autocomplete", {
                term:request.term,
				category_ids:$('#categoryids').val()
                }, function(data){
					if(!data.length)
					{
						var result = [
						   {
						   label: '<?php echo __("No result found", 'key4ce-osticket-bridge'); ?>', 
						   value: "0"
						   }
						 ];
						   response(result);
					}
					else
					{
						response($.map(data, function(item) {
                            return {
                               label: item.label,
							   value: item.link
                            }
						}))
					}
                
            }, "json");
        },
		select: function( event, ui ) { 
		$(event.target).val(ui.item.label);
				if(ui.item.value==0)
				{
					return false;
				}
				else
				{
					window.location = ui.item.value;
				}
				
				return false;
        },
        minLength: 2,
        dataType: "json",
        cache: false
    });
	});
  </script>
	<div class="searchkb">
	<form id="ui_element" class="sb_wrapper" action="<?php echo site_url('/'); ?>" method="get">
                    <p>
						<input class="sb_input" type="text" id="sb_input" name="s"/>
						<span class="sb_down"></span>
						<input type="hidden" name="categoryids" value="" id="categoryids">
						<input type="hidden" name="post_type" value="key4ce_kb">
						<input class="sb_search" type="submit" value="" name="sb_search"/>
					</p>
					<ul class="sb_dropdown" style="display:none;">
						<li class="sb_filter"><?php echo __("Filter your search", 'key4ce-osticket-bridge'); ?></li>
						<li><input type="checkbox" value="0" id="checkAll"/><label for="all"><strong><?php echo __("All Categories", 'key4ce-osticket-bridge'); ?></strong></label></li>
						<?php
								foreach($faq_category_list as $category)
								{
						?>
									<li><input type="checkbox" value="<?php echo $category->term_id; ?>" name="<?php echo $category->name; ?>" class="chk"/><label for="<?php echo $category->name; ?>"><?php echo $category->name; ?></label></li>	
						<?php
								}
						?>
					</ul>
                </form>
	</div>