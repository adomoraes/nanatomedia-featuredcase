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

function lwf_render_case_metabox($post)
{
    $case_type = get_post_meta($post->ID, '_lwf_case_type', true);
    $settlement_amount = get_post_meta($post->ID, '_lwf_settlement_amount', true);

    wp_nonce_field('lwf_save_case_meta', 'lwf_case_nonce');

?>
    <p>
        <label for="lwf_case_type"><strong>Case Type:</strong> (e.g. Car Accident)</label>
        <input type="text" id="lwf_case_type" name="lwf_case_type" value="<?php echo esc_attr($case_type); ?>" class="widefat">
    </p>
    <p>
        <label for="lwf_settlement_amount"><strong>Settlement Amount:</strong> (e.g. $25,000)</label>
        <input type="text" id="lwf_settlement_amount" name="lwf_settlement_amount" value="<?php echo esc_attr($settlement_amount); ?>" class="widefat">
    </p>
<?php
}

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

    $html = '<div class="featured-cases-container">';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $type   = get_post_meta(get_the_ID(), '_lwf_case_type', true);
            $amount = get_post_meta(get_the_ID(), '_lwf_settlement_amount', true);

            $html .= '<article class="case-item" style="border-bottom: 1px solid #eee; margin-bottom: 20px; padding-bottom: 10px;">';
            $html .= '<h3>' . get_the_title() . '</h3>';
            $html .= '<p><strong>Type:</strong> ' . esc_html($type) . '<br>';
            $html .= '<strong>Settlement:</strong> ' . esc_html($amount) . '</p>';
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
