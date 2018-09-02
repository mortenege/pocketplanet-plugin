<?php
/*
Author:       Morten Ege Jensen <ege.morten@gmail.com>
Author URI:   https://github.com/mortenege
License:      GPLv2 <https://www.gnu.org/licenses/gpl-2.0.html>
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * NOTE from Ege: Copied from themes/mag
 * The template for displaying all pages
 */
?>

<?php /*get_header(); // original code */ ?>
<?php include __DIR__ . "/header-pocketplanet.php"; // added code ?>

		<div id="container">
		<?php mnky_hook_page_top(); ?>
			<div id="content">
			<?php mnky_hook_page_content_top(); ?>

				<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> >
					<div class="entry-content clearfix">
					<?php
					the_content();
					wp_link_pages( array(
						'before'      => '<nav class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'mag' ) . '</span>',
						'after'       => '</nav>',
						'link_before' => '<span>',
						'link_after'  => '</span>',
					) );
					?>
					</div><!-- .entry-content -->
				</article>

				<?php if ( comments_open() ) {
					comments_template( '', true );
				} ?>
				<?php endwhile; ?>
				
			<?php mnky_hook_page_content_bottom(); ?>
			</div><!-- #content -->
		<?php mnky_hook_page_bottom(); ?>
		</div><!-- #container -->
		
<?php get_footer(); ?>