<?php
add_action('woocommerce_before_add_to_cart_button', 'render_min_max_option_selector');
function render_min_max_option_selector()
{
  global $product;
  $options = get_field('min_max_options', $product->get_id());

  if (empty($options)) return;

  echo '<div class="akk-minmax-option">';
  echo '<label for="min_max_option"><strong>Select Pieces:</strong></label>';
  echo '<select id="min_max_option" name="min_max_option" class="min-max-select">';
  echo '<option value="">-- Select Pieces --</option>';

  foreach ($options as $opt) {
    $val = is_array($opt) ? $opt['value'] : $opt;
    echo '<option value="' . esc_attr($val) . '">' . esc_html($val) . '</option>';
  }

  echo '</select>';
  echo '</div>';
}

add_action('woocommerce_before_add_to_cart_button', 'combo_display_sub_products_on_frontend');
function combo_display_sub_products_on_frontend()
{
  global $product;

  $list_sub_products = get_field('product_combo', $product->get_id());
  $combo_name = get_field('combo_name', $product->get_id());
  $min_order = get_field('min_order', $product->get_id());
  $is_composite_product = is_composite_product($product);
  if (empty($min_order)) {
    $min_order = 0;
  }
  $groups = get_field('products_group', $product->get_id()) ?: [];

?>
  <?php if (!empty($list_sub_products)): ?>
    <div class="akk-accordion">
      <div class="akk-accordion-header"><?php echo $combo_name; ?></div>
      <div class="akk-accordion-body">
        <div class="combo-warning akk-warning">Please select at least <?php echo $min_order ?> <?php echo $combo_name; ?>!</div>
        <div class="product-combo"
          data-min-order="<?php echo esc_attr($min_order); ?>"
          data-combo-name="<?php echo esc_attr($combo_name); ?>">
          <?php
          foreach ($list_sub_products as $sub_products) {
            if (empty($sub_products) || !is_array($sub_products)) continue;

            echo '<div class="akk-sub-products">';
            foreach ($sub_products as $sub_product_post) {
              $product_id = is_object($sub_product_post) ? $sub_product_post->ID : $sub_product_post;
              $sub_product = wc_get_product($product_id);
              if (!$sub_product) continue;

              $stock_level = $sub_products['stock_level'] ?? 999;
              $min_qty = $sub_products['minimum_quantity'] ?? 0;
              $image_url = get_the_post_thumbnail_url($sub_product->get_id(), 'full');

              // $data_group = $group_id !== null ? ' data-group="' . esc_attr($group_id) . '"' : '';

              echo '<div class="akk-sub-product">';
              echo '<div class="sub-product-image">';
              echo '<a data-fancybox="img-' . esc_attr($sub_product->get_id()) . '" href="' . esc_url($image_url) . '">';
              echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($sub_product->get_name()) . '" width="60">';
              echo '</a>';
              echo '<label>' . esc_html($sub_product->get_name()) . ' (' . $sub_product->get_price_html() . ')</label><br>';
              echo '</div>';

              echo '<div class="sub-product-info">';
              echo render_flatsome_quantity_input($sub_product, $stock_level, $min_qty, $groups, $is_composite_product);
              echo '</div>';

              echo '</div>';
            }
            echo '</div>';
          }
          ?>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <div class="akk-accordion">
    <div class="akk-accordion-header">Packing Instructions</div>
    <div class="akk-accordion-body">
      <input id="packing_instructions" name="packing_instructions" class="packing-input" type="text" placeholder="Leave your packing instructions here. Example: 5 per box">
    </div>
  </div>

  <?php
  if ($product->get_type() == 'simple')   require_once('js/combo-js.php');
  if ($is_composite_product) require_once('js/composite-js.php');
  ?>

<?php
}

add_filter('woocommerce_add_cart_item_data', 'capture_selected_sub_products', 10, 2);
function capture_selected_sub_products($cart_item_data, $product_id)
{
  if (!empty($_POST['akk_sub_products']) && is_array($_POST['akk_sub_products'])) {
    $selected = [];
    foreach ($_POST['akk_sub_products'] as $product_id => $qty) {
      $qty = intval($qty);
      if ($qty > 0) {
        $product = wc_get_product($product_id);
        $selected[$product_id] = [$qty, get_product_pricing_rules($product, 1)]; // [0 -> quantity , 1 -> price]
      }
    }

    if (!empty($selected)) {
      $cart_item_data['akk_selected'] = $selected;
      $cart_item_data['unique_key'] = md5(json_encode($selected));
    }
  }
  if (!empty($_POST['packing_instructions'])) {
    $cart_item_data['packing_instructions'] = $_POST['packing_instructions'];
  }

  return $cart_item_data;
}

add_action('woocommerce_cart_loaded_from_session', 'restore_combo_price_from_session');
function restore_combo_price_from_session($cart)
{
  if ((defined('REST_REQUEST') && REST_REQUEST) || is_admin()) {
    return;
  }

  foreach ($cart->get_cart() as $cart_item_key => $item) {
    $product = $item['data'];
    if (is_composite_product($product)) continue;
    if (isset($item['akk_selected'])) {
      $total_price = 0;
      foreach ($item['akk_selected'] as $product_id => $qty) {
        $product = wc_get_product($product_id); // is product_addon
        if ($product && $qty[0] > 0) {
          $product_price = get_product_pricing_rules($product, 1);
          $total_price += floatval($product_price) * $qty[0];
        }
      }
      if ($total_price > 0) {
        $cart->cart_contents[$cart_item_key]['data']->set_price($total_price);
      }
    }
  }
}
