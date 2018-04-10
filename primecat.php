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

function primecat_meta_box_html( ) {
    ?>
    <select name="primecat_select">
        <option value="">(None)</option>
    <?php foreach( get_categories( [ 'hide_empty' => FALSE ] ) as $cat ) { ?>
        <option value="<?php echo $cat->term_id; ?>"><?php echo $cat->name; ?></option>
    <?php } ?>
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

add_action( "add_meta_boxes", "primecat_add_meta_box" );
