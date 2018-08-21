<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<?php mnky_hook_head_top(); ?>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="format-detection" content="telephone=no">
	<meta name="theme-color" content="<?php echo ot_get_option('accent_color', '#e74c3c'); ?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php esc_url(bloginfo( 'pingback_url' )); ?>">
	<?php mnky_hook_head_bottom(); ?>	
	<?php wp_head(); ?>
</head>
	
<body <?php body_class(); ?> id="site-body" itemscope itemtype="http://schema.org/WebPage">
	<?php mnky_hook_body_top(); ?>	
	<div id="wrapper">
		<?php get_sidebar('top');?>
		
		<?php get_template_part( 'site-header' ); // Include site-header.php ?>

		<?php get_template_part( 'title' ); // Include title.php ?>
		
		<?php get_template_part( 'pre-content' ); // Include pre-content.php ?>
		
		<?php 
			$type = get_post_meta(get_the_ID(), 'pp_widgets_page_type', true);
			if ($type) {
				$shortcode = '[pp_widgets type="' . $type . '"]';
				echo do_shortcode($shortcode); 
			}
		?>
		<div id="main" class="clearfix">
