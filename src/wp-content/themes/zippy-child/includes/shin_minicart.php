<?php
function custom_woocommerce_mini_cart() {
    if (is_admin()) {
        return;
    }

    $total_quantity = WC()->cart->get_cart_contents_count();
      
      $rule = get_minimum_rule_by_order_mode(); 

      $minimun_total_to_order = $rule['minimun_total_to_order'];
      $minimun_total_to_freeship = $rule['minimun_total_to_freeship'];
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
            if($minimun_total_to_order != 0){
              ?>
              <div class="inforMinimunRule">
              <div class="rule_checkout_mini_cart">
                <div class="minimum_order">
                  <p>$<span id="deliveryNeedMore"><?php echo $minimun_total_to_order; ?></span> more for minimum order</p>
                  <div id="minimunOrderProgress" class="bar_process_full">
                      <div id="minimunOrder" class="bar_process_custom" dataDelivery="<?php echo $minimun_total_to_order; ?>"></div>
                  </div>
                </div>
              </div>
              <?php
            }
            if($minimun_total_to_freeship != 0){
              ?>
              <div class="rule_checkout_mini_cart">
                <div class="minimum_order">
                  <p>$<span id="freeshipNeedMore"><?php echo $minimun_total_to_freeship; ?></span> more for freeship</p>
                  <div id="freeDeliveryProgress" class="bar_process_full">
                      <div id="freeDelivery" class="bar_process_custom" dataFreeship="<?php echo $minimun_total_to_freeship; ?>"></div>
                  </div>
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
  <?php
}
add_shortcode('mini_cart_sidebar', 'custom_woocommerce_mini_cart');
