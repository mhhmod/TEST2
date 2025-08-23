<?php
/**
 * Custom Post Types and Taxonomies
 *
 * @package GrindCTRL
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register custom post types
 */
function grindctrl_register_post_types(): void {
    // Testimonials Post Type
    $testimonial_labels = array(
        'name'                     => esc_html_x('Testimonials', 'Post type general name', 'grindctrl'),
        'singular_name'            => esc_html_x('Testimonial', 'Post type singular name', 'grindctrl'),
        'menu_name'                => esc_html_x('Testimonials', 'Admin Menu text', 'grindctrl'),
        'name_admin_bar'           => esc_html_x('Testimonial', 'Add New on Toolbar', 'grindctrl'),
        'add_new'                  => esc_html__('Add New', 'grindctrl'),
        'add_new_item'             => esc_html__('Add New Testimonial', 'grindctrl'),
        'new_item'                 => esc_html__('New Testimonial', 'grindctrl'),
        'edit_item'                => esc_html__('Edit Testimonial', 'grindctrl'),
        'view_item'                => esc_html__('View Testimonial', 'grindctrl'),
        'all_items'                => esc_html__('All Testimonials', 'grindctrl'),
        'search_items'             => esc_html__('Search Testimonials', 'grindctrl'),
        'parent_item_colon'        => esc_html__('Parent Testimonials:', 'grindctrl'),
        'not_found'                => esc_html__('No testimonials found.', 'grindctrl'),
        'not_found_in_trash'       => esc_html__('No testimonials found in Trash.', 'grindctrl'),
        'featured_image'           => esc_html_x('Customer Image', 'Overrides the "Featured Image" phrase for this post type. Added in 4.3', 'grindctrl'),
        'set_featured_image'       => esc_html_x('Set customer image', 'Overrides the "Set featured image" phrase for this post type. Added in 4.3', 'grindctrl'),
        'remove_featured_image'    => esc_html_x('Remove customer image', 'Overrides the "Remove featured image" phrase for this post type. Added in 4.3', 'grindctrl'),
        'use_featured_image'       => esc_html_x('Use as customer image', 'Overrides the "Use as featured image" phrase for this post type. Added in 4.3', 'grindctrl'),
        'archives'                 => esc_html_x('Testimonial archives', 'The post type archive label used in nav menus. Default "Post Archives". Added in 4.4', 'grindctrl'),
        'insert_into_item'         => esc_html_x('Insert into testimonial', 'Overrides the "Insert into post"/"Insert into page" phrase (used when inserting media into a post). Added in 4.4', 'grindctrl'),
        'uploaded_to_this_item'    => esc_html_x('Uploaded to this testimonial', 'Overrides the "Uploaded to this post"/"Uploaded to this page" phrase (used when viewing media attached to a post). Added in 4.4', 'grindctrl'),
        'filter_items_list'        => esc_html_x('Filter testimonials list', 'Screen reader text for the filter links heading on the post type listing screen. Default "Filter posts list"/"Filter pages list". Added in 4.4', 'grindctrl'),
        'items_list_navigation'    => esc_html_x('Testimonials list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default "Posts list navigation"/"Pages list navigation". Added in 4.4', 'grindctrl'),
        'items_list'               => esc_html_x('Testimonials list', 'Screen reader text for the items list heading on the post type listing screen. Default "Posts list"/"Pages list". Added in 4.4', 'grindctrl'),
    );

    $testimonial_args = array(
        'labels'             => $testimonial_labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'testimonial'),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-format-quote',
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'show_in_rest'       => true,
    );

    register_post_type('testimonial', $testimonial_args);

    // Team Members Post Type
    $team_labels = array(
        'name'                     => esc_html_x('Team Members', 'Post type general name', 'grindctrl'),
        'singular_name'            => esc_html_x('Team Member', 'Post type singular name', 'grindctrl'),
        'menu_name'                => esc_html_x('Team', 'Admin Menu text', 'grindctrl'),
        'name_admin_bar'           => esc_html_x('Team Member', 'Add New on Toolbar', 'grindctrl'),
        'add_new'                  => esc_html__('Add New', 'grindctrl'),
        'add_new_item'             => esc_html__('Add New Team Member', 'grindctrl'),
        'new_item'                 => esc_html__('New Team Member', 'grindctrl'),
        'edit_item'                => esc_html__('Edit Team Member', 'grindctrl'),
        'view_item'                => esc_html__('View Team Member', 'grindctrl'),
        'all_items'                => esc_html__('All Team Members', 'grindctrl'),
        'search_items'             => esc_html__('Search Team Members', 'grindctrl'),
        'not_found'                => esc_html__('No team members found.', 'grindctrl'),
        'not_found_in_trash'       => esc_html__('No team members found in Trash.', 'grindctrl'),
    );

    $team_args = array(
        'labels'             => $team_labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'team'),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 21,
        'menu_icon'          => 'dashicons-groups',
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'show_in_rest'       => true,
    );

    register_post_type('team_member', $team_args);
}
add_action('init', 'grindctrl_register_post_types');

/**
 * Register custom taxonomies
 */
function grindctrl_register_taxonomies(): void {
    // Product Category (if WooCommerce is not active)
    if (!class_exists('WooCommerce')) {
        $category_labels = array(
            'name'              => esc_html_x('Product Categories', 'taxonomy general name', 'grindctrl'),
            'singular_name'     => esc_html_x('Product Category', 'taxonomy singular name', 'grindctrl'),
            'search_items'      => esc_html__('Search Product Categories', 'grindctrl'),
            'all_items'         => esc_html__('All Product Categories', 'grindctrl'),
            'parent_item'       => esc_html__('Parent Product Category', 'grindctrl'),
            'parent_item_colon' => esc_html__('Parent Product Category:', 'grindctrl'),
            'edit_item'         => esc_html__('Edit Product Category', 'grindctrl'),
            'update_item'       => esc_html__('Update Product Category', 'grindctrl'),
            'add_new_item'      => esc_html__('Add New Product Category', 'grindctrl'),
            'new_item_name'     => esc_html__('New Product Category Name', 'grindctrl'),
            'menu_name'         => esc_html__('Product Categories', 'grindctrl'),
        );

        $category_args = array(
            'hierarchical'      => true,
            'labels'            => $category_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'product-category'),
            'show_in_rest'      => true,
        );

        register_taxonomy('product_category', array('post'), $category_args);
    }

    // Testimonial Categories
    $testimonial_category_labels = array(
        'name'              => esc_html_x('Testimonial Categories', 'taxonomy general name', 'grindctrl'),
        'singular_name'     => esc_html_x('Testimonial Category', 'taxonomy singular name', 'grindctrl'),
        'search_items'      => esc_html__('Search Testimonial Categories', 'grindctrl'),
        'all_items'         => esc_html__('All Testimonial Categories', 'grindctrl'),
        'parent_item'       => esc_html__('Parent Testimonial Category', 'grindctrl'),
        'parent_item_colon' => esc_html__('Parent Testimonial Category:', 'grindctrl'),
        'edit_item'         => esc_html__('Edit Testimonial Category', 'grindctrl'),
        'update_item'       => esc_html__('Update Testimonial Category', 'grindctrl'),
        'add_new_item'      => esc_html__('Add New Testimonial Category', 'grindctrl'),
        'new_item_name'     => esc_html__('New Testimonial Category Name', 'grindctrl'),
        'menu_name'         => esc_html__('Categories', 'grindctrl'),
    );

    $testimonial_category_args = array(
        'hierarchical'      => true,
        'labels'            => $testimonial_category_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'testimonial-category'),
        'show_in_rest'      => true,
    );

    register_taxonomy('testimonial_category', array('testimonial'), $testimonial_category_args);
}
add_action('init', 'grindctrl_register_taxonomies');

/**
 * Add custom meta boxes
 */
function grindctrl_add_meta_boxes(): void {
    // Testimonial meta box
    add_meta_box(
        'testimonial_details',
        esc_html__('Testimonial Details', 'grindctrl'),
        'grindctrl_testimonial_meta_box_callback',
        'testimonial',
        'normal',
        'high'
    );

    // Team member meta box
    add_meta_box(
        'team_member_details',
        esc_html__('Team Member Details', 'grindctrl'),
        'grindctrl_team_member_meta_box_callback',
        'team_member',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'grindctrl_add_meta_boxes');

/**
 * Testimonial meta box callback
 */
function grindctrl_testimonial_meta_box_callback(WP_Post $post): void {
    // Add nonce for security
    wp_nonce_field('grindctrl_testimonial_meta_box', 'grindctrl_testimonial_meta_box_nonce');

    // Get current values
    $customer_name = get_post_meta($post->ID, '_testimonial_customer_name', true);
    $customer_title = get_post_meta($post->ID, '_testimonial_customer_title', true);
    $rating = get_post_meta($post->ID, '_testimonial_rating', true);
    $featured = get_post_meta($post->ID, '_testimonial_featured', true);

    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="testimonial_customer_name"><?php esc_html_e('Customer Name', 'grindctrl'); ?></label>
            </th>
            <td>
                <input type="text" id="testimonial_customer_name" name="testimonial_customer_name" 
                       value="<?php echo esc_attr($customer_name); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="testimonial_customer_title"><?php esc_html_e('Customer Title/Company', 'grindctrl'); ?></label>
            </th>
            <td>
                <input type="text" id="testimonial_customer_title" name="testimonial_customer_title" 
                       value="<?php echo esc_attr($customer_title); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="testimonial_rating"><?php esc_html_e('Rating', 'grindctrl'); ?></label>
            </th>
            <td>
                <select id="testimonial_rating" name="testimonial_rating">
                    <option value=""><?php esc_html_e('Select Rating', 'grindctrl'); ?></option>
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <option value="<?php echo esc_attr($i); ?>" <?php selected($rating, $i); ?>>
                            <?php echo esc_html($i); ?> <?php echo esc_html(_n('Star', 'Stars', $i, 'grindctrl')); ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="testimonial_featured"><?php esc_html_e('Featured Testimonial', 'grindctrl'); ?></label>
            </th>
            <td>
                <input type="checkbox" id="testimonial_featured" name="testimonial_featured" 
                       value="1" <?php checked($featured, '1'); ?> />
                <label for="testimonial_featured"><?php esc_html_e('Mark as featured', 'grindctrl'); ?></label>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Team member meta box callback
 */
function grindctrl_team_member_meta_box_callback(WP_Post $post): void {
    // Add nonce for security
    wp_nonce_field('grindctrl_team_member_meta_box', 'grindctrl_team_member_meta_box_nonce');

    // Get current values
    $position = get_post_meta($post->ID, '_team_member_position', true);
    $email = get_post_meta($post->ID, '_team_member_email', true);
    $linkedin = get_post_meta($post->ID, '_team_member_linkedin', true);
    $twitter = get_post_meta($post->ID, '_team_member_twitter', true);

    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="team_member_position"><?php esc_html_e('Position', 'grindctrl'); ?></label>
            </th>
            <td>
                <input type="text" id="team_member_position" name="team_member_position" 
                       value="<?php echo esc_attr($position); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="team_member_email"><?php esc_html_e('Email', 'grindctrl'); ?></label>
            </th>
            <td>
                <input type="email" id="team_member_email" name="team_member_email" 
                       value="<?php echo esc_attr($email); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="team_member_linkedin"><?php esc_html_e('LinkedIn URL', 'grindctrl'); ?></label>
            </th>
            <td>
                <input type="url" id="team_member_linkedin" name="team_member_linkedin" 
                       value="<?php echo esc_attr($linkedin); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="team_member_twitter"><?php esc_html_e('Twitter URL', 'grindctrl'); ?></label>
            </th>
            <td>
                <input type="url" id="team_member_twitter" name="team_member_twitter" 
                       value="<?php echo esc_attr($twitter); ?>" class="regular-text" />
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save meta box data
 */
function grindctrl_save_meta_box_data(int $post_id): void {
    // Check if our nonce is set
    if (!isset($_POST['grindctrl_testimonial_meta_box_nonce']) && !isset($_POST['grindctrl_team_member_meta_box_nonce'])) {
        return;
    }

    // Verify nonce
    $testimonial_nonce_valid = isset($_POST['grindctrl_testimonial_meta_box_nonce']) && 
                              wp_verify_nonce($_POST['grindctrl_testimonial_meta_box_nonce'], 'grindctrl_testimonial_meta_box');
    
    $team_nonce_valid = isset($_POST['grindctrl_team_member_meta_box_nonce']) && 
                       wp_verify_nonce($_POST['grindctrl_team_member_meta_box_nonce'], 'grindctrl_team_member_meta_box');

    if (!$testimonial_nonce_valid && !$team_nonce_valid) {
        return;
    }

    // Check if user has permission to edit the post
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Save testimonial data
    if ($testimonial_nonce_valid && get_post_type($post_id) === 'testimonial') {
        $fields = array(
            'testimonial_customer_name' => '_testimonial_customer_name',
            'testimonial_customer_title' => '_testimonial_customer_title',
            'testimonial_rating' => '_testimonial_rating',
            'testimonial_featured' => '_testimonial_featured',
        );

        foreach ($fields as $field => $meta_key) {
            if (isset($_POST[$field])) {
                $value = $field === 'testimonial_featured' ? '1' : sanitize_text_field($_POST[$field]);
                update_post_meta($post_id, $meta_key, $value);
            } else if ($field === 'testimonial_featured') {
                delete_post_meta($post_id, $meta_key);
            }
        }
    }

    // Save team member data
    if ($team_nonce_valid && get_post_type($post_id) === 'team_member') {
        $fields = array(
            'team_member_position' => '_team_member_position',
            'team_member_email' => '_team_member_email',
            'team_member_linkedin' => '_team_member_linkedin',
            'team_member_twitter' => '_team_member_twitter',
        );

        foreach ($fields as $field => $meta_key) {
            if (isset($_POST[$field])) {
                $value = in_array($field, array('team_member_email', 'team_member_linkedin', 'team_member_twitter')) 
                        ? sanitize_url($_POST[$field]) 
                        : sanitize_text_field($_POST[$field]);
                update_post_meta($post_id, $meta_key, $value);
            }
        }
    }
}
add_action('save_post', 'grindctrl_save_meta_box_data');

/**
 * Add custom columns to admin lists
 */
function grindctrl_add_admin_columns(array $columns): array {
    $columns['featured'] = esc_html__('Featured', 'grindctrl');
    $columns['rating'] = esc_html__('Rating', 'grindctrl');
    return $columns;
}
add_filter('manage_testimonial_posts_columns', 'grindctrl_add_admin_columns');

/**
 * Populate custom columns
 */
function grindctrl_populate_admin_columns(string $column, int $post_id): void {
    switch ($column) {
        case 'featured':
            $featured = get_post_meta($post_id, '_testimonial_featured', true);
            echo $featured === '1' ? esc_html__('Yes', 'grindctrl') : esc_html__('No', 'grindctrl');
            break;
        case 'rating':
            $rating = get_post_meta($post_id, '_testimonial_rating', true);
            if ($rating) {
                echo str_repeat('★', (int) $rating) . str_repeat('☆', 5 - (int) $rating);
            } else {
                echo esc_html__('No rating', 'grindctrl');
            }
            break;
    }
}
add_action('manage_testimonial_posts_custom_column', 'grindctrl_populate_admin_columns', 10, 2);
