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
        if ($('.quick-view').length > 0) $('.quick-view').hide();
      <?php endif; ?>
    });
  </script>
<?php
}
add_action('wp_head', 'script_rule_popup_session');
