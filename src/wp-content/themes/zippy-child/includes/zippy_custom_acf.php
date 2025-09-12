<?php
add_filter('acf/fields/post_object/query', 'acf_limit_product_group_choices', 10, 3);
function acf_limit_product_group_choices($args, $field, $post_id) {

    if ($field['key'] !== 'field_68c279f4f20c1') {
        return $args;
    }

    $product_ids = [];

    if (have_rows('product_combo', $post_id)) {
        while (have_rows('product_combo', $post_id)) {
            the_row();
            $product = get_sub_field('product');
            if ($product) {
                if (is_object($product)) {
                    $product_ids[] = $product->ID;
                } elseif (is_numeric($product)) {
                    $product_ids[] = $product;
                }
            }
        }
    }

    if (!empty($product_ids)) {
        $args['post__in'] = $product_ids;
    } else {
        $args['post__in'] = [0];
    }

    return $args;
}

