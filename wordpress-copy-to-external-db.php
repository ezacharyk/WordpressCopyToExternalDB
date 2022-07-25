<?php/*
Plugin Name:  Petra Copy To External DB Plugin
Plugin URI:   https://www.petra.com
Description:  Copies Wordpress Blogs To Magento When Published
Version:      20180921
Author:       Petra.com
Author URI:   https://www.petra.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  wporg
Domain Path:  /languages
*/

const DB_NAME = 'database_name';
const DB_USER = 'database_user';
const DB_PASSWORD = 'database_password';
const DB_HOST = 'database_host_ip';
const DB_TABLE = 'database_table';

function connectToDB() {
    try {
        $db = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
        return $db;
    } catch (Exception $e) {    // Database Error
        echo $e->getMessage();
    }
}

function saveToExternalDB ($post_ID, $post) {
    $post_categories = wp_get_post_categories( $post_ID );
    $cats = '';

    if ( $post_categories ) {
        foreach ($post_categories as $c) {
            $cat = get_category($c);
            $cats .= $cat->name . ',';
        }
    }

    $post_tags = get_the_tags($post_ID);
    $tags = '';
    if ( $post_tags ) {
        foreach( $post_tags as $tag ) {
            $tags .= $tag->name . ',';
        }
    }

    $db = connectToDB();
    $db->insert(
        DB_TABLE,
        array(
            'post_id' => $post->ID,
            'guid' => $post->guid,
            'post_title' => $post->post_title,
            'image' => $post->image,
            'tag' => $tags,
            'category' => $cats,
            'post_content' => $post->post_content,
            'post_date' => $post->post_date
        )
    );
}

add_action ( 'publish_post', 'saveToExternalDB', 20, 2 );