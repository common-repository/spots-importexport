<?php
/*
Plugin Name: Spots Import/Export
Plugin URI:
Version: 0.7.2
Description: Tool too easily import and export all existing Spots as CSV files
Author: Ben Lobaugh w/freshmuse
Author URI: http://ben.lobaugh.net
*/

add_action( 'init', 'spots_imex_download_csv' );

function spots_imex_download_csv() {
    if( isset( $_GET['export'] ) && 'csv' == $_GET['export'] ) {
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=spots.csv");
        header("Content-Type: text/csv");
        header("Content-Transfer-Encoding: binary");
        
        $output = fopen('php://output', 'w');
        $spots = spots_imex_get_spots();
         foreach( $spots as $s ) {
            $row = array(
                $s->post_title,
                $s->post_content,
                $s->post_status,
                $s->post_name
            );
             fputcsv($output, $row);
        }
        
        // Stop WP from loading any more
        die();
    }
}


add_action( 'admin_menu', 'spots_imex_menu' );
function spots_imex_menu() {
    add_submenu_page( 'edit.php?post_type=spot', 'Import/Export Spots', 'Import/Export', 'manage_options', 'spots-imex', 'spots_imex_render_menu_page' );
}

function spots_imex_render_menu_page() {
    if( isset( $_POST['submit'])) {
        spots_imex_import_csv( $_FILES['spots-csv']['tmp_name'] ); 
    }
    require_once( 'imex_menu_page.php' );
}

function spots_imex_import_csv( $csv_file ) {
    
    $fh = fopen( $csv_file, 'r');
    while ($line = fgetcsv($fh)) {
        $args = array(
            'post_title'        => $line[0],
            'post_content'      => $line[1],
            'post_status'       => $line[2],
            'post_name'         => $line[3],
            'post_type'         => 'spot',
            'comment_status '   => 'closed',
            'ping_status'       => 'closed'
        );
        
        $result = wp_insert_post( $args );
    }

    fclose($fh);
    
    echo '<div class="updated" style="padding:10px">Spots imported</div>';
}


function spots_imex_create_csv( $spots ) {
    
    //$csv = "post_title, post_content, post_status, post_name";
    
    foreach( $spots as $s ) {
        $csv .= "\n{$s->post_title}, {$s->post_content}, {$s->post_status}, {$s->post_name}";
    }
    
    return $csv;
}


function spots_imex_get_spots() {
    $args = array(
        'post_type'     => 'spot',
        'post_status'   => 'any',
        'showposts'     => '-1'
    );
    
    $results = new WP_Query( $args );
    return $results->get_posts();
}
