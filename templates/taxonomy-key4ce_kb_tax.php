<?php get_header(); ?>
<div style="margin-left: 350px;">
  <?php 
  require_once( WP_PLUGIN_DIR . '/key4ce-osticket-bridge/templates/kb_nav_bar.php');
  $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'key4ce_kb_tax' ) );
  $tax = $wp_query->get_queried_object(); 
  ?>
 </div>
<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main" style="width: 800px; margin-left: 350px;">
      <h1><?php echo $tax->name; ?> Archives</h1>
      <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>

          <div class="post type-post hentry">
            <h2 class="entry-title">
              <a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>" rel="bookmark">
                <?php the_title(); ?>
              </a>
            </h2>
			<div class="entry-summary">
              <?php the_excerpt(); ?>
            </div><!-- .entry-summary -->
          </div>

        <?php endwhile; ?>
      <?php endif; ?>

		</main><!-- .site-main -->
	</section><!-- .content-area -->

  <?php get_sidebar(); ?>
<?php get_footer(); ?>