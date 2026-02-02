<?php

/**
 * Plugin Name: Law Firm Featured Cases
 * Description: Registers Featured Cases CPT and provides a shortcode for listing them.
 * Version: 1.0
 * Author: Eduardo Moraes
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Custom Post Type (CPT)
 */
function lwf_register_featured_case_cpt()
{
    $labels = array(
        'name'               => 'Featured Cases',
        'singular_name'      => 'Featured Case',
        'menu_name'          => 'Featured Cases',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Case',
        'edit_item'          => 'Edit Case',
        'all_items'          => 'All Cases',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'menu_icon'          => 'dashicons-awards',
        'supports'           => array('title'),
        'show_in_rest'       => true,
    );

    register_post_type('featured_case', $args);
}
add_action('init', 'lwf_register_featured_case_cpt');

/**
 * Add Meta Boxes for Custom Fields
 */
function lwf_add_featured_case_metaboxes()
{
    add_meta_box(
        'lwf_case_details',
        'Case Details',
        'lwf_render_case_metabox',
        'featured_case',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'lwf_add_featured_case_metaboxes');

/**
 * Helper function to provide Case Types
 * Hardcoded as requested
 */
function lwf_get_case_types()
{
    return array(
        'car-accident'   => 'Car Accident',
        'work-injury'    => 'Work Injury',
        'medical-mal'    => 'Medical Malpractice',
        'slip-and-fall'  => 'Slip and Fall',
        'product-liab'   => 'Product Liability'
    );
}

function lwf_render_case_metabox($post)
{
    $current_type = get_post_meta($post->ID, '_lwf_case_type', true);
    $settlement_amount = get_post_meta($post->ID, '_lwf_settlement_amount', true);
    $options = lwf_get_case_types();

    wp_nonce_field('lwf_save_case_meta', 'lwf_case_nonce');
?>
    <p>
        <label for="lwf_case_type"><strong>Case Type:</strong></label>
        <select id="lwf_case_type" name="lwf_case_type" class="widefat">
            <option value=""><?php _e('Select a case type', 'textdomain'); ?></option>
            <?php foreach ($options as $value => $label) : ?>
                <option value="<?php echo esc_attr($value); ?>" <?php selected($current_type, $value); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label for="lwf_settlement_amount"><strong>Settlement Amount (USD):</strong></label>
        <input type="number" id="lwf_settlement_amount" name="lwf_settlement_amount"
            value="<?php echo esc_attr($settlement_amount); ?>"
            step="0.01" min="0" class="widefat" placeholder="e.g. 25000">
    </p>
<?php
}

function lwf_save_case_metadata($post_id)
{
    // 1. Security check: Nonce verification
    if (!isset($_POST['lwf_case_nonce']) || !wp_verify_nonce($_POST['lwf_case_nonce'], 'lwf_save_case_meta')) {
        return;
    }

    // 2. Prevent autosave from overwriting data
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // 3. Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // 4. Save Case Type (Select Field)
    if (isset($_POST['lwf_case_type'])) {
        $allowed_types = lwf_get_case_types();
        $selected_type = sanitize_text_field($_POST['lwf_case_type']);

        // Only save if the value exists in our hardcoded options (Validation)
        if (array_key_exists($selected_type, $allowed_types) || $selected_type === '') {
            update_post_meta($post_id, '_lwf_case_type', $selected_type);
        }
    }

    // 5. Save Settlement Amount (Text Field)
    if (isset($_POST['lwf_settlement_amount'])) {
        // Sanitize as a float number
        $amount = filter_var($_POST['lwf_settlement_amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        update_post_meta($post_id, '_lwf_settlement_amount', $amount);
    }
}
add_action('save_post', 'lwf_save_case_metadata');

/**
 * Shortcode to display cases
 * Usage: [display_featured_cases]
 */
function lwf_featured_cases_shortcode()
{
    $query = new WP_Query(array(
        'post_type'      => 'featured_case',
        'posts_per_page' => 3,
        'orderby'        => 'date',
        'order'          => 'DESC'
    ));

    $case_types = lwf_get_case_types();
    $html = '<div class="featured-cases-container">';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $type_slug = get_post_meta(get_the_ID(), '_lwf_case_type', true);
            $amount    = get_post_meta(get_the_ID(), '_lwf_settlement_amount', true);

            // Format number to US Standard: $25,000.00
            $formatted_amount = !empty($amount) ? '$' . number_format((float)$amount, 2, '.', ',') : 'N/A';

            // Get the readable label from our hardcoded array
            $type_label = isset($case_types[$type_slug]) ? $case_types[$type_slug] : $type_slug;

            $html .= '<article class="case-item" style="border: 1px solid #ddd; margin-bottom: 20px; padding: 15px; border-radius: 5px;">';
            $html .= '<h3 style="margin-top: 0;">' . get_the_title() . '</h3>';
            $html .= '<p><strong>Type:</strong> ' . esc_html($type_label) . '<br>';
            $html .= '<strong>Settlement:</strong> ' . esc_html($formatted_amount) . '</p>';
            $html .= '</article>';
        }
        wp_reset_postdata();
    } else {
        $html .= '<p>No cases found.</p>';
    }

    $html .= '</div>';
    return $html;
}
add_shortcode('display_featured_cases', 'lwf_featured_cases_shortcode');

/**
 * Create 3 Dummy Posts on Plugin Activation
 */
function lwf_create_dummy_cases()
{
    // Array of dummy data
    $dummy_cases = array(
        array(
            'title'      => 'High-Speed Highway Collision',
            'type'       => 'car-accident',
            'settlement' => 450000,
        ),
        array(
            'title'      => 'Construction Site Safety Failure',
            'type'       => 'work-injury',
            'settlement' => 1250000,
        ),
        array(
            'title'      => 'Pharmacy Prescription Error',
            'type'       => 'medical-mal',
            'settlement' => 85000,
        ),
    );

    foreach ($dummy_cases as $case) {
        // Check if a post with this title already exists to avoid duplicates
        if (!get_page_by_title($case['title'], OBJECT, 'featured_case')) {

            $post_id = wp_insert_post(array(
                'post_title'   => $case['title'],
                'post_type'    => 'featured_case',
                'post_status'  => 'publish',
                'post_content' => 'Dummy description for ' . $case['title'],
            ));

            if ($post_id) {
                update_post_meta($post_id, '_lwf_case_type', $case['type']);
                update_post_meta($post_id, '_lwf_settlement_amount', $case['settlement']);
            }
        }
    }
}

register_activation_hook(__FILE__, 'lwf_create_dummy_cases');
