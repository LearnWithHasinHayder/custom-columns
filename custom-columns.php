<?php
/**
 * Plugin Name: Custom Columns
 * Description: This is a plugin to add and manage custom columns to the post list
 * Version: 1.0.0
 * Author: Hasin Hayder
 * Author URI: http://hasin.me
 * Plugin URI: http://google.com
 */

class Custom_Columns {
    function __construct() {
        add_action('init', [$this, 'init']);
    }

    function init() {
        add_filter('manage_posts_columns', [$this, 'manage_posts_columns']);
        //display column data
        add_action('manage_posts_custom_column', [$this, 'manage_posts_custom_column'], 10, 2);

        add_filter('manage_pages_columns', [$this, 'manage_posts_columns']);
        //display column data
        add_action('manage_pages_custom_column', [$this, 'manage_posts_custom_column'], 10, 2);

        add_filter('manage_posts_columns', [$this, 'add_id_column']);
        add_action('manage_posts_custom_column', [$this, 'manage_id_column'], 10, 2);

        add_filter('manage_pages_columns', [$this, 'add_id_column']);
        add_action('manage_pages_custom_column', [$this, 'manage_id_column'], 10, 2);

        add_filter('manage_edit-post_sortable_columns', [$this, 'add_sortable_column']);
        add_filter('manage_edit-page_sortable_columns', [$this, 'add_sortable_column']);
        
        //add user sortable column
        add_filter('manage_users_sortable_columns', [$this, 'user_sortable_column']);

        //posts view count column
        add_filter('manage_posts_columns', [$this, 'add_view_count_column']);
        add_action('manage_posts_custom_column', [$this, 'manage_view_count_column'], 10, 2);

        //count view
        add_action('wp_head', [$this, 'count_view']);

        //display view count in the content
        add_filter('the_content', [$this, 'display_view_count'],9999);

        //add user registrtion column
        add_filter('manage_users_columns', [$this, 'add_user_reg_column']);
        add_action('manage_users_custom_column', [$this, 'manage_user_reg_column'], 10, 3);

    }

    function user_sortable_column($columns) {
        $columns['user_registered'] = 'user_registered';
        return $columns;
    }

    function manage_user_reg_column($value, $column_name, $user_id) {
        if ($column_name == 'user_registered') {
            $user = get_user_by('id', $user_id);
            $date = $user->user_registered;
            return $date;
        }
    }

    function add_user_reg_column($columns) {
        $columns['user_registered'] = 'Registered Date';
        return $columns;
    }

    function display_view_count($content) {
        $id = get_the_ID();
        $view_count = get_post_meta(get_the_ID(), 'view_count', true);
        $view_count = $view_count ? $view_count : 0;

        $custom_content = '<div style="border: 1px solid #ddd; padding: 10px; margin: 20px 0;">';
        $custom_content .= '<p>Total View: ' . ($view_count+1) . '</p>';
        $custom_content .= '</div>';
        return $content . $custom_content;
    }


    function count_view() {
        // delete_post_meta(get_the_ID(), 'view_count');
        if (is_single()) {
            $view_count = get_post_meta(get_the_ID(), 'view_count', true);
            // $view_count = get_post_meta(get_the_ID(), 'view_count', true)??0;
            $view_count = $view_count ? $view_count : 0;
            $view_count++;
            update_post_meta(get_the_ID(), 'view_count', $view_count);
        }
    }

    function add_view_count_column($columns) {
        $columns['view_count'] = 'View Count';
        return $columns;
    }

    function manage_view_count_column($column, $post_id) {
        if ($column == 'view_count') {
            $view_count = get_post_meta($post_id, 'view_count', true);
            $view_count = $view_count ? $view_count : 0;
            echo $view_count;
        }
    }

    function add_id_column($columns) {
        $new_columns = [];
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key == 'cb') {
                $new_columns['id'] = 'ID';
            }
        }
        return $new_columns;
    }

    function manage_id_column($column, $post_id) {
        if ($column == 'id') {
            echo $post_id;
        }
    }

    function add_sortable_column($columns) {
        $columns['id'] = 'ID';
        $columns['view_count'] = 'View Count';
        return $columns;
    }

    function manage_posts_columns($columns) {
        $new_columns = [];
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key == 'title') {
                $new_columns['thumbnail'] = 'Thumbnail';
            }
        }
        return $new_columns;
    }

    function manage_posts_custom_column($column, $post_id) {
        $has_thumbnail = has_post_thumbnail($post_id);
        if ($column == 'thumbnail') {
            echo get_the_post_thumbnail($post_id, [50, 50]);
        }
        // if($has_thumbnail){
        //     // echo "Yes";
        //     //display thumbnail
        //     echo get_the_post_thumbnail($post_id, [50,50]);
        // }else{
        //     echo "No";
        // }
    }
}

new Custom_Columns();

