<?php
/**
 * Template name: WooCommerce - My Account
 *
 * This template adds My account to the sidebar.
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.19.0
 */

get_header(); ?>

<?php do_action("flatsome_before_page"); ?>

<?php wc_get_template("myaccount/header.php"); ?>

<div class="page-wrapper my-account mb">
	<div class="container" role="main">
    <?php
    $path_URL = esc_url($_SERVER["REQUEST_URI"]);
    $last_segment = basename($path_URL);
    ?>
		<?php if (is_user_logged_in()) { ?>
            <?php switch ($last_segment) { case "my-account": ?>
                            <div class="center_element_theme">
                                <div class="col_main_page">
                                    <div class="list_tab_myaccount">
                                        <div class="item_tab_myaccount">
                                            <a href="/my-account/edit-account">
                                                <div class="icon_item">
                                                    <i class="icon-user"></i>
                                                </div>
                                                <div class="content_item"> 
                                                    <h5>Update My Profile</h5>
                                                    <p>Access your account details</p>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="item_tab_myaccount">
                                            <a href="/my-account/edit-account#password_current">
                                                <div class="icon_item">
                                                    <i class="icon-lock"></i>
                                                </div>
                                                <div class="content_item"> 
                                                    <h5>Update My Password</h5>
                                                    <p>Keeps your security accesses in check</p>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="item_tab_myaccount">
                                            <a href="/my-account/orders/">
                                                <div class="icon_item">
                                                    <i class="icon-shopping-basket"></i>
                                                </div>
                                                <div class="content_item"> 
                                                    <h5>My Order History</h5>
                                                    <p>Keeps track of your orders</p>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="item_tab_myaccount">
                                            <a href="/my-account/edit-address">
                                                <div class="icon_item">
                                                    <i class="icon-map-pin-fill"></i>
                                                </div>
                                                <div class="content_item"> 
                                                    <h5>My Address Book</h5>
                                                    <p>Keeps track of your addresses</p>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php break;case "orders":
                    wc_get_template("myaccount/orders.php");
                    break;
                case "edit-account":
                    wc_get_template("myaccount/form-edit-account.php");
                    break;
                case "edit-address":
                    wc_get_template("./templates/edit-address-page.php");
                    break;

                default:
                    return;
            } ?> 
            

		<?php } else { ?>
        <?php if ($last_segment == "?register") { ?>
            <div class="section_register_form">
                <form method="POST">
                    <div class="row_form_register">
                                        <div class="col_form_register">
                                            <input id="input_firstname" name="firstname" type="text" class="form-control " value="" placeholder="*First Name" autocomplete="new-password">
                                        </div>
                                        <div class="col_form_register">
                                            <input id="input_lastname" name="lastname" type="text" class="form-control " value="" placeholder="*Last Name" autocomplete="new-password">
                                        </div>
                                    </div>
                                    <div class="row_form_register">
                                        <div class="col_form_register">
                                            <input id="input_email" name="email" type="email" class="form-control " value="" placeholder="*E-Mail" autocomplete="new-password">
                                        </div>
                                        <div class="col_form_register">
                                            <input id="input_telephone" name="telephone" type="text" class="form-control input-number" value="" placeholder="*Mobile No." autocomplete="new-password">
                                        </div>
                                    </div>
                                    <div class="row_form_register">
                                        <div class="col_form_register">
                                            <input id="input_birthday" name="birthday" type="date" class="form-control " data-date-format="YYYY-MM-DD" value="" placeholder="*Birthday" autocomplete="off">
                                        </div>
                                        <div class="col_form_register">
                                            <select id="input_gender" name="gender" class="form-control "><option value="">*Gender</option><option value="Male">Male</option><option value="Female">Female</option></select>
                                        </div>
                                    </div>
                                    <div class="row_form_register">
                                        <div class="col_form_register">
                                            <input id="input_password" name="password" type="password" class="form-control " value="" placeholder="*Password" autocomplete="new-password" style="padding-right: 46px;">
                                        </div>
                                        <div class="col_form_register">
                                            <input id="input_confirm" name="confirm" type="password" class="form-control " value="" placeholder="*Password Confirm" autocomplete="new-password" style="padding-right: 46px;">
                                        </div>
                                    </div>
                                    <div class="row_form_register">
                                        <div class="col_form_register" id="billing_postcode_field">
                                            <input id="input_postcode" name="postcode" type="text" class="form-control " value="" placeholder="*Postal Code" autocomplete="new-password">
                                        </div>
                                        <div class="col_form_register">
                                            <input id="input_address_1" name="address_1" type="text" class="form-control " value="" placeholder="*Address 1" autocomplete="new-password">
                                            <input id="input_latitude_1" name="input_latitude_1" type="hidden" class="form-control " value="" placeholder="*Address 1" autocomplete="new-password">
                                            <input id="input_longitude_1" name="input_longitude_1" type="hidden" class="form-control " value="" placeholder="*Address 1" autocomplete="new-password">
                                        </div>
                                    </div>
                                    <div class="row_form_register">
                                        <div class="confirm_item">
                                            <label><input type="checkbox" class="form-control" name="newsletter" value="1"> Subscribe me to the newsletter</label>
                                        </div>
                                        <div class="confirm_item">
                                            <label><input type="checkbox" name="agree" value="1" class="form-control">I have read and agree to the <a href="https://jixiangeverton.com.sg/index.php?route=information/information/agree&amp;information_id=3" class="agree"><b>Privacy Policy</b></a></label>
                                        </div>
                                    </div>
                                    <div class="row_form_register">
                                        <button class="btn btn-primary btn-submit float-sm-right">Proceed</button>
                                    </div>
                                </form>
                            </div>

        <?php } else { ?>
            <div class="login">
            <div class="login-title">
                <h3>
                    <?php
                    $login = isset($_GET["login"]) ? $_GET["login"] : 0;
                    if ($login === "failed") {
                        echo "Wrong account or password.";
                    } elseif ($login === "empty") {
                        echo "Account and password cannot be blank";
                    } elseif ($login === "false") {
                        echo "You are logged out.";
                    } else {
                        echo "Log in";
                    }
                    ?>
                </h3>
            </div>
            <div>
                
                <div class="mod-login clearfix">
                    <div class="mod-login-col1 clearfix">
                        <?php
                        $args = [
                            "redirect" => site_url($_SERVER["REQUEST_URI"]),
                            "form_id" => "loginform",
                            "label_username" => __("Username"),
                            "label_password" => __("Password"),
                            "label_remember" => __("Remember Login"),
                            "label_log_in" => __("Log In"),
                        ];
                        wp_login_form($args);
                        ?>
                        <div class="forgot">
                            <a href="<?php echo home_url("/reset-password"); ?>">Forgot password ?</a>
                        </div>
                        <div class="register">
                            <a href="<?php echo home_url("/my-account/?register"); ?>">Don't have an account? Register one!</a>
                        </div>
                    </div>
                </div>
                
            </div>
	    </div>
        <?php } ?>

		<?php } ?>

	</div>
</div>

<?php do_action("flatsome_after_page"); ?>

<?php get_footer(); ?>

