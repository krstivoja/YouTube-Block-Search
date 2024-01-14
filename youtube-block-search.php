<?php
/*
Plugin Name: YouTube Search Plugin
Description: Search through all post types for YouTube provider slugs.
Version: 1.0
Author: Your Name
*/

// Hook for adding admin menus
add_action('admin_menu', 'youtube_search_menu');

// Action function for the above hook
function youtube_search_menu() {
    // Add a new top-level menu
    add_menu_page('YouTube Search', 'YouTube Search', 'manage_options', 'youtube_search_plugin', 'youtube_search_page');
}

// Display the search page
function youtube_search_page() {
    global $wpdb;

    echo '<div class="wrap">';
    echo '<h1>YouTube Search Results</h1>';

    $post_types = get_post_types();
    $results = array();

    foreach ($post_types as $post_type) {
        $query = $wpdb->prepare(
            "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type = %s AND post_content LIKE %s AND post_status = 'publish'",
            $post_type,
            '%providerNameSlug":"youtube%'
        );
        $post_results = $wpdb->get_results($query);

        if (!empty($post_results)) {
            $results[$post_type] = $post_results;
        }
    }

    if (!empty($results)) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead>';
        echo '<tr><th>Title</th><th>ID</th><th>Actions</th></tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($results as $type => $posts) {
            foreach ($posts as $post) {
                $edit_link = get_edit_post_link($post->ID);
                $view_link = get_permalink($post->ID);
                echo '<tr>';
                echo '<td><a href="' . esc_url($view_link) . '" target="_blank">' . esc_html($post->post_title) . '</a></td>';
                echo '<td>' . esc_html($post->ID) . '</td>';
                echo '<td><a href="' . esc_url($edit_link) . '">Edit</a></td>';
                echo '</tr>';
            }
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No results found.</p>';
    }

    echo '</div>';
}
?>
