<?php

/**
 * https://developer.wordpress.org/plugins/metadata/custom-meta-boxes/
 */
abstract class PPWidgets_Meta_Box
{
    public static function add()
    {
        $screens = ['page'];
        foreach ($screens as $screen) {
            add_meta_box(
                'pp_widgets_box_id',          // Unique ID
                'Choose PP Widget Type for this page', // Box title
                [self::class, 'html'],   // Content callback, must be of type callable
                $screen                  // Post type
            );
        }
    }
 
    public static function save($post_id)
    {
        if (array_key_exists('pp_widgets_page_type', $_POST)) {
            update_post_meta(
                $post_id,
                'pp_widgets_page_type',
                $_POST['pp_widgets_page_type']
            );
        }
    }
 
    public static function html($post)
    {
        $value = get_post_meta($post->ID, 'pp_widgets_page_type', true);
        ?>
        <select name="pp_widgets_page_type" class="postbox">
            <option value="">None</option>
            <option value="flights" <?php selected($value, 'flights'); ?>>Flights</option>
            <option value="hotels" <?php selected($value, 'hotels'); ?>>Hotels</option>
            <option value="cars" <?php selected($value, 'cars'); ?>>Cars</option>
        </select>
        <?php
    }
}
 
add_action('add_meta_boxes', ['PPWidgets_Meta_Box', 'add']);
add_action('save_post', ['PPWidgets_Meta_Box', 'save']);