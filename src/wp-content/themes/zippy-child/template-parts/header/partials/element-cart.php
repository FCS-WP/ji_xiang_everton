<?php
/**
 * Cart element.
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.19.0
 */

if(is_woocommerce_activated() && flatsome_is_wc_cart_available() ) {
  // Get Cart replacement for catalog_mode
  if(get_theme_mod('catalog_mode')) { get_template_part('template-parts/header/partials/element','cart-replace'); return;}
  $cart_style = get_theme_mod('header_cart_style','dropdown');
  $custom_cart_content = get_theme_mod('html_cart_header');
  $icon_style = get_theme_mod('cart_icon_style');
  $icon = get_theme_mod('cart_icon','basket');
  $cart_title = get_theme_mod('header_cart_title', 1);
  $cart_total = get_theme_mod('header_cart_total', 1);
  $custom_cart_icon_id = get_theme_mod( 'custom_cart_icon' );
  $custom_cart_icon = wp_get_attachment_image_src( $custom_cart_icon_id, 'large' );
  $disable_mini_cart = apply_filters( 'flatsome_disable_mini_cart', is_cart() || is_checkout() );

  if ( $disable_mini_cart ) {
    $cart_style = 'link';
  }

	$link_atts = array(
		'href'  => is_customize_preview() ? '#' : esc_url( wc_get_cart_url() ), // Prevent none link mode to navigate in customizer.
		'class' => 'header-cart-link ' . get_flatsome_icon_class( $icon_style, 'small' ),
		'title' => esc_attr__( 'Cart', 'woocommerce' ),
	);

	if ( $cart_style === 'off-canvas' ) {
		$link_atts['class']     .= ' off-canvas-toggle nav-top-link';
		$link_atts['data-open']  = '#cart-popup';
		$link_atts['data-class'] = 'off-canvas-cart';
		$link_atts['data-pos']   = 'right';
	}

	if ( fl_woocommerce_version_check( '7.8.0' ) && ! wp_script_is( 'wc-cart-fragments' ) ) {
		wp_enqueue_script( 'wc-cart-fragments' );
	}
?>
<li class="cart-item has-icon<?php if($cart_style == 'dropdown') { ?> has-dropdown<?php } ?>">
<?php if($icon_style && $icon_style !== 'plain') { ?><div class="header-button"><?php } ?>

<a <?php echo flatsome_html_atts( $link_atts ); ?>>

<?php  if($cart_total || $cart_title) { ?>
<span class="header-cart-title">
  <?php if($cart_title) { ?> <?php _e('Cart', 'woocommerce'); ?> <?php } ?>
  <?php /* divider */ if($cart_total && $cart_title) { ?>/<?php } ?>
  <?php if($cart_total) { ?>
    <span class="cart-price"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
  <?php } ?>
</span>
<?php } ?>

<?php
if($custom_cart_icon) { ?>
  <span class="image-icon header-cart-icon" data-icon-label="<?php echo WC()->cart->get_cart_contents_count(); ?>">
	<img class="cart-img-icon" alt="<?php echo esc_attr__( 'Cart', 'woocommerce' ); ?>" src="<?php echo esc_url( $custom_cart_icon[0] ); ?>" width="<?php echo esc_attr( $custom_cart_icon[1] ); ?>" height="<?php echo esc_attr( $custom_cart_icon[2] ); ?>"/>
  </span>
<?php }
else { ?>
  <?php if(!$icon_style) { ?>
  <span class="cart-icon image-icon">
    <strong><?php echo WC()->cart->get_cart_contents_count(); ?></strong>
  </span>
  <?php } else { ?>
  <i class="icon-shopping-<?php echo $icon;?>"
    data-icon-label="<?php echo WC()->cart->get_cart_contents_count(); ?>">
  </i>
  <?php } ?>
<?php }  ?>
</a>
<?php if($icon_style && $icon_style !== 'plain') { ?></div><?php } ?>

<?php if($cart_style == 'dropdown') { ?>
 <ul class="nav-dropdown <?php flatsome_dropdown_classes(); ?>">
    <li class="html widget_shopping_cart">
      <div class="widget_shopping_cart_content">
        <?php woocommerce_mini_cart(); ?>
      </div>
    </li>
    <?php if($custom_cart_content){
      echo '<li class="html">'.do_shortcode($custom_cart_content).'</li>';
      }
    ?>
 </ul>
<?php }  ?>

<?php if($cart_style == 'off-canvas') { ?>

  <!-- Cart Sidebar Popup -->
  <div id="cart-popup" class="mfp-hide">
  <div class="cart-popup-inner inner-padding<?php echo get_theme_mod( 'header_cart_sticky_footer', 1 ) ? ' cart-popup-inner--sticky' : ''; ?>">
      <div class="cart-popup-title text-center">
          <!-- <span class="heading-font uppercase"><?php _e('Cart', 'woocommerce'); ?></span>
          <div class="is-divider"></div> -->
      </div>
      <?php 
      $total_quantity = WC()->cart->get_cart_contents_count();
      
      $rule = get_minimum_rule_by_order_mode(); 

      $minimum_total_to_order = $rule['minimum_total_to_order'];
      $minimum_total_to_freeship = $rule['minimum_total_to_freeship'];
    
    ?>
	  <div class="widget_shopping_cart">
        <div class="row_mini_cart_custom"> 
            <div class="title_mini_cart_custom">
                <h3>Your Cart</h3>
                <p><span id="total_quanity_cart"><?php echo $total_quantity; ?></span> items added</p>
            </div>
            <div class="icon_mini_cart_custom">
                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 354 348.89" style="enable-background:new 0 0 354 348.89;" xml:space="preserve">
                <g>
                    <path class="st1" d="M117.42,214.9h0.01c0.01,0,0.02,0,0.03,0h146.68c3.61,0,6.79-2.4,7.78-5.87l32.36-113.26
                        c0.7-2.44,0.21-5.07-1.32-7.09c-1.53-2.03-3.92-3.22-6.46-3.22H98.76l-5.78-26.03c-0.82-3.7-4.11-6.34-7.9-6.34H36.53
                        c-4.47,0-8.09,3.62-8.09,8.09s3.62,8.09,8.09,8.09h42.05c1.02,4.61,27.68,124.55,29.21,131.45c-8.6,3.74-14.63,12.31-14.63,22.27
                        c0,13.38,10.89,24.27,24.27,24.27h146.7c4.47,0,8.09-3.62,8.09-8.09c0-4.47-3.62-8.09-8.09-8.09h-146.7
                        c-4.46,0-8.09-3.63-8.09-8.09C109.35,218.53,112.96,214.91,117.42,214.9z M285.78,101.63l-27.74,97.08H123.93l-21.57-97.08H285.78z
                        "></path>
                    <path class="st1" d="M109.35,271.53c0,13.38,10.89,24.27,24.27,24.27s24.27-10.89,24.27-24.27s-10.89-24.27-24.27-24.27
                        S109.35,258.15,109.35,271.53z M133.62,263.44c4.46,0,8.09,3.63,8.09,8.09c0,4.46-3.63,8.09-8.09,8.09c-4.46,0-8.09-3.63-8.09-8.09
                        C125.53,267.07,129.16,263.44,133.62,263.44z"></path>
                    <path class="st1" d="M223.69,271.53c0,13.38,10.89,24.27,24.27,24.27c13.38,0,24.27-10.89,24.27-24.27s-10.89-24.27-24.27-24.27
                        C234.58,247.26,223.69,258.15,223.69,271.53z M247.96,263.44c4.46,0,8.09,3.63,8.09,8.09c0,4.46-3.63,8.09-8.09,8.09
                        c-4.46,0-8.09-3.63-8.09-8.09C239.87,267.07,243.5,263.44,247.96,263.44z"></path>
                </g>
                </svg>
            </div>
        </div>
        <?php if(!empty($_SESSION['order_mode'])){
          ?>
          <div class="box_infor_method_shipping">
            <div class="items_infor_method_shipping">
                <div class="text_items">
                    <h4>Order Mode:</h4>
                    <p><?php echo $_SESSION['order_mode'] ;?></p>
                </div>
                <div class="icon_items">
                  <div><button id="removeMethodShipping" >Remove</button></div>
                </div>
            </div>
            <div class="items_infor_method_shipping">
                <div class="text_items">
                    <h4>Select Outlet:</h4>
                    <p><?php echo $_SESSION['outlet_name'];?></p>
                </div>
            </div>
            <?php
            if($_SESSION['order_mode'] == 'delivery'){
              ?>
              <div class="items_infor_method_shipping">
                <div class="text_items">
                    <h4>Delivery Address:</h4>
                    <p><?php echo $_SESSION["delivery_address"];?></p>
                </div>
              </div>
              <?php
            }
            ?>
            <div class="items_infor_method_shipping">
                <div class="text_items">
                    <h4><?php if($_SESSION['order_mode'] == 'takeaway'){ echo 'Takeaway';}else{echo 'Delivery';}?> Time:</h4>
                    <p><?php echo $_SESSION['date']; ?><br><?php echo 'From ' . $_SESSION['time']['from'] . ' To ' . $_SESSION['time']['to'];?></p>
                </div>
            </div>
            <?php 
              if(!empty($_SESSION ["shipping_fee"])){
                ?>
                <div class="items_infor_method_shipping">
                  <div class="text_items">
                      <h4>Shipping Fee:</h4>
                      <p>$<?php echo $_SESSION ["shipping_fee"];?></p>
                  </div>
              </div>
                <?php
              }
            ?>
        </div>
          <?php
        }?>
        
          <?php
          if(!empty($_SESSION['order_mode']) && $_SESSION['order_mode'] == 'delivery'){
            if($minimum_total_to_order != 0){
              ?>
              <div class="rule_checkout_mini_cart">
                <div class="minimum_order">
                  <p>$<span id="deliveryNeedMore"><?php echo $minimum_total_to_order; ?></span> more for minimum order</p>
                  <div id="minimunOrderProgress" class="bar_process_full">
                      <div id="minimunOrder" class="bar_process_custom" dataDelivery="<?php echo $minimum_total_to_order; ?>"></div>
                  </div>
                </div>
              </div>
              <?php
            }
            if($minimum_total_to_freeship != 0){
              ?>
              <div class="rule_checkout_mini_cart">
                <div class="minimum_order">
                  <p>$<span id="freeshipNeedMore"><?php echo $minimum_total_to_freeship; ?></span> more for freeship</p>
                  <div id="freeDeliveryProgress" class="bar_process_full">
                      <div id="freeDelivery" class="bar_process_custom" dataFreeship="<?php echo $minimum_total_to_freeship; ?>"></div>
                  </div>
                </div>
              </div>
              <?php
            } 
          }
          ?>
        
        
		<div class="widget_shopping_cart_content">
			<?php woocommerce_mini_cart(); ?>
		</div>
	  </div>
      
      <?php if($custom_cart_content) {
        echo '<div class="header-cart-content">'.do_shortcode($custom_cart_content).'</div>'; }
      ?>
      
      <?php do_action('flatsome_cart_sidebar'); ?>
  </div>
  </div>

<?php } ?>
</li>
<?php } else {
	fl_header_element_error( 'woocommerce' );
}
?>
