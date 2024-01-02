<?php

function cpd_get_tax_attribute( $taxonomy ) {
    global $wpdb;

    $attr = substr( $taxonomy, 3 );
    $query = $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = %s",
        $attr
    );
    $attribute = $wpdb->get_row( $query );

    return $attribute;
}
