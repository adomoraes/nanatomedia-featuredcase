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
        'supports'           => array('title'), // Exercise focuses on Title and Custom Fields
        'show_in_rest'       => true, // Enables Gutenberg editor
    );

    register_post_type('featured_case', $args);
}
add_action('init', 'lwf_register_featured_case_cpt');
