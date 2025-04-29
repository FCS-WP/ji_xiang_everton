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

function script_rule_popup_session()
{
?>
  <script>
    jQuery(document).ready(function($) {
      <?php if (empty(WC()->session->get('status_popup'))) : ?>
        let productId = $('.lightbox-zippy-btn').data('product_id');

        if (productId) {
          $('.image-fade_in_back a, .woocommerce-loop-product__title a')
            .attr({
              'data-product_id': productId,
              'href': '#lightbox-zippy-form'
            })
            .addClass('lightbox-zippy-btn');
        }

        $('.quick-view').hide();
      <?php else : ?>
        $('.image-fade_in_back a, .woocommerce-loop-product__title a').on('click', function(event) {
          event.preventDefault();
        });

        $('.quick-view').show();
      <?php endif; ?>
    });
  </script>
<?php
}
add_action('wp_head', 'script_rule_popup_session');
