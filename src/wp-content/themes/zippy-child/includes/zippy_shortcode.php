<?php
function flatsome_custom_quickview_button($atts)
{

  $button = '<div class="cta_add_to_cart"><a href="#" class="quick-view" 
                  data-prod="' . $atts['id'] . '" 
                  data-toggle="quick-view">
                  Add
               </a></div>';

  return $button;
}
add_shortcode('quickview_button', 'flatsome_custom_quickview_button');


function lightbox_zippy_form()
{
  echo do_shortcode('[lightbox id="lightbox-zippy-form" width="600px" padding="20px 0px"][zippy_form][/lightbox]');
}

add_shortcode('lightbox_zippy_form', 'lightbox_zippy_form');


add_action('wp_footer', 'display_form_shipping_method');

function display_form_shipping_method()
{
  if (is_admin() || is_checkout() || is_cart()) return;

  echo do_shortcode('[lightbox_zippy_form]');
}
function script_rule_popup_session()
{
?>
  <script>
    jQuery(document).ready(function($) {
      <?php if (empty(WC()->session->get('status_popup'))) : ?>
        if ($('.quick-view').length > 0) $('.quick-view').hide();
      <?php endif; ?>
    });
  </script>
<?php
}
add_action('wp_head', 'script_rule_popup_session');

add_shortcode('categories_render_mobile', 'categories_render_mobile_callback');

function categories_render_mobile_callback()
{
  $restricted_categories = ['combo-6', 'ala-carte', 'festive'];

  $current_user = wp_get_current_user();
  $is_vendor_tier_1 = in_array('vendor_tier_1', (array) $current_user->roles);

  $terms = get_terms(array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'orderby'    => 'menu_order',
    'order'      => 'ASC',
  ));

  if (is_wp_error($terms) || empty($terms)) {
    return '';
  }
  $i = 0;
  ob_start();
?>
  <div class="categoryBar">

    <?php foreach ($terms as $term): ?>

      <?php
      // Vendor Tier 1 → ONLY see restricted categories
      if ($is_vendor_tier_1 && !in_array($term->slug, $restricted_categories)) {
        continue;
      }

      // Normal users → CANNOT see restricted categories
      if (!$is_vendor_tier_1 && in_array($term->slug, $restricted_categories)) {
        continue;
      }
      ?>

      <?php if ($i === 0): ?>
        <div class="sticky_menu_product_category">
          <div id="currentCategory"><?php echo esc_html($term->name); ?></div>
          <div id="accordingCategoryMenu">
            <button id="openCateSticky">More</button>
          </div>
        </div>
      <?php endif; ?>

      <?php $i++; ?>

    <?php endforeach; ?>

    <ul id="categoryList" style="display: none;">

      <?php foreach ($terms as $term): ?>


        <?php
        // Vendor Tier 1 → ONLY see restricted categories
        if ($is_vendor_tier_1 && !in_array($term->slug, $restricted_categories)) {
          continue;
        }

        // Normal users → CANNOT see restricted categories
        if (!$is_vendor_tier_1 && in_array($term->slug, $restricted_categories)) {
          continue;
        }
        ?>

        <li><a class="" href="#<?php echo esc_attr($term->slug); ?>"> <?php echo esc_html($term->name); ?></a></li>
      <?php endforeach; ?>
    </ul>

  </div>

<?php

  return ob_get_clean();
}
add_shortcode('categories_render', 'categories_render_callback');

function categories_render_callback()
{
  $restricted_categories = ['combo-6', 'ala-carte', 'festive'];

  $current_user = wp_get_current_user();
  $is_vendor_tier_1 = in_array('vendor_tier_1', (array) $current_user->roles);

  $terms = get_terms(array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'orderby'    => 'menu_order',
    'order'      => 'ASC',
  ));

  if (is_wp_error($terms) || empty($terms)) {
    return '';
  }

  ob_start();
?>
  <div class="ux-menu stack stack-col justify-start menu_link_category">
    <?php foreach ($terms as $term): ?>

      <?php
      // Vendor Tier 1 → ONLY see restricted categories
      if ($is_vendor_tier_1 && !in_array($term->slug, $restricted_categories)) {
        continue;
      }

      // Normal users → CANNOT see restricted categories
      if (!$is_vendor_tier_1 && in_array($term->slug, $restricted_categories)) {
        continue;
      }
      ?>

      <div class="ux-menu-link flex menu-item">
        <a class="ux-menu-link__link flex" href="#<?php echo esc_attr($term->slug); ?>">
          <span class="ux-menu-link__text">
            <?php echo esc_html($term->name); ?>
          </span>
        </a>
      </div>

    <?php endforeach; ?>
  </div>
<?php

  return ob_get_clean();
}



add_filter('body_class', function ($classes) {
  $current_user = wp_get_current_user();

  $is_vendor_tier_1 = in_array('vendor_tier_1', (array) $current_user->roles);

  if (!$is_vendor_tier_1) {
    $classes[] = 'not-vendor-tier-1';
  } else {
    $classes[] = 'vendor-tier-1';
  }

  return $classes;
});
