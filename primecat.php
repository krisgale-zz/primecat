<?php
/**
 * @package primecat
 * @version 1.0
 */
/*
Plugin Name: primecat
Plugin URI: https://github.com/krisgale/primecat
Description: allows specification of a primary category when authoring content
Author: krisgale
Version: 1.0
Author URI: krisgale.com
*/

function primecat_meta_box_html( $post ) {
    $val = get_post_meta( $post->ID, "primecat_id", TRUE );
    ?>
    <select name="primecat_id">
        <option value="">(None)</option>
    <?php foreach( get_categories( [ 'hide_empty' => FALSE ] ) as $cat ) { if( $cat->term_id != 1 ) { ?>
        <option value="<?php echo $cat->term_id; ?>" <?php selected( $val, $cat->term_id ); ?>><?php echo $cat->name; ?></option>
    <?php } } ?>
    </select>
    <?php
}

function primecat_add_meta_box( ) {
    $types = [ "post" ];
    foreach( get_post_types( [ ], "objects" ) as $type ) {
        if( property_exists( $type, "taxonomies" )
        && in_array( "category", $type->taxonomies )
        && !in_array( $type->name, $types ) ) {
            $types[ ] = $type->name;
        }
    }
    foreach( $types as $type ) {
        add_meta_box(
            "primecat_meta_box",
            "Primary Category",
            "primecat_meta_box_html",
            $type,
            "side",
            "high"
        );
    }
}

function primecat_save_post( $id ) {
    if( !array_key_exists( "primecat_id", $_POST ) ) {
        return;
    }
    update_post_meta(
        $id,
        "primecat_id",
        $val = $_POST[ 'primecat_id' ]
    );
    $cats = [ ];
    foreach( wp_get_post_categories( $id, [ 'fields' => "ids" ] ) as $cat ) {
        if( $cat != 1 && $cat != $val ) {
            $cats[ ] = $cat;
        }
    }
    if( $val ) {
        $cats[ ] = $val;
    }
    wp_set_post_categories( $id, $cats );
}

add_action( "add_meta_boxes", "primecat_add_meta_box" );

add_action( "save_post", "primecat_save_post" );
