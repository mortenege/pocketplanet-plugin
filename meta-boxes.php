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
                $screen,                  // Post type,
                'side'
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

        if (array_key_exists('pp_widgets_background_image', $_POST)) {
            update_post_meta(
                $post_id,
                'pp_widgets_background_image',
                $_POST['pp_widgets_background_image']
            );
        }
    }
 
    public static function html($post)
    {
        // get page type
        $page_type = get_post_meta($post->ID, 'pp_widgets_page_type', true);

        // set up image upload
        wp_enqueue_media();
        wp_register_script( 'pp_widgets_admin', plugins_url('static/pp-widgets-admin.js', __FILE__), array('jquery'), null,true );
        wp_enqueue_script('pp_widgets_admin');

        // $image_id = get_option('pp_widgets_background_image', 0);
        $image_id = get_post_meta($post->ID, 'pp_widgets_background_image', true);
        $image_url = wp_get_attachment_url( $image_id );
        ?>
        <select name="pp_widgets_page_type" class="postbox">
            <option value="">None</option>
            <option value="flights" <?php selected($page_type, 'flights'); ?>>Flights</option>
            <option value="hotels" <?php selected($page_type, 'hotels'); ?>>Hotels</option>
            <option value="cars" <?php selected($page_type, 'cars'); ?>>Cars</option>
        </select>

        <div class='image-preview-wrapper'>
            <img id='image-preview' src='<?php echo $image_url; ?>' width='100' height='100' style='max-height: 100px; width: 100px;'>
        </div>
        <input id="upload_image_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
        <input type='hidden' name='pp_widgets_background_image' id='image_attachment_id' value=<?= $image_id; ?>>
        <div>
            <small><i>Remember to press 'update'</i></small>
        </div>
        <?php
    }
}
 
add_action('add_meta_boxes', ['PPWidgets_Meta_Box', 'add']);
add_action('save_post', ['PPWidgets_Meta_Box', 'save']);