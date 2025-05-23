<?php


function handle_user_registration()
{
  if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['password'], $_POST['confirm'], $_POST['input_latitude_1'], $_POST['input_longitude_1'])) {

      $firstname = sanitize_text_field($_POST['firstname']);
      $lastname = sanitize_text_field($_POST['lastname']);
      $email = sanitize_email($_POST['email']);
      $telephone = sanitize_text_field($_POST['telephone']);
      $birthday = sanitize_text_field($_POST['birthday']);
      $gender = sanitize_text_field($_POST['gender']);
      $password = $_POST['password'];
      $confirm_password = $_POST['confirm'];
      $postcode = sanitize_text_field($_POST['postcode']);
      $address_1 = sanitize_text_field($_POST['address_1']);
      $input_latitude_1 = sanitize_text_field($_POST['input_latitude_1']);
      $input_longitude_1 = sanitize_text_field($_POST['input_longitude_1']);
      $newsletter = isset($_POST['newsletter']) ? 1 : 0;
      $agree = isset($_POST['agree']) ? 1 : 0;

      if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($confirm_password)) {
        echo "<script>alert('Please fill in all required information.');</script>";
        return;
      }

      if (!is_email($email)) {
        echo "<script>alert('Invalid email.');</script>";
        return;
      }

      if ($password !== $confirm_password) {
        echo "<script>alert('Confirmation password does not match.');</script>";
        return;
      }


      if (email_exists($email)) {
        echo "<script>alert('This email has been registered.');</script>";
        return;
      }


      $user_id = wp_create_user($email, $password, $email);


      $user_infors = [
        'billing_first_name' => $firstname,
        'billing_last_name' => $lastname,
        'billing_phone' => $telephone,
        'birthday' => $birthday,
        'gender' => $gender,
        'postcode' => $postcode,
        'billing_address_1' => $address_1,
        'input_latitude_1' => $input_latitude_1,
        'input_longitude_1' => $input_longitude_1,
        'billing_email' => $email,
        'billing_postcode' => $postcode,
      ];

      foreach ($user_infors as $user_infor => $value) {
        update_user_meta($user_id, $user_infor, $value);
      }

      wp_set_current_user($user_id);
      wp_set_auth_cookie($user_id);

      echo "<script>alert('Register successfully');</script>";
      return;
    }
  }
}
add_action('init', 'handle_user_registration');


function add_custom_billing_fields_to_woocommerce($fields)
{
  $fields['billing']['fields']['birthday'] = array(
    'label'       => 'Date of birth',
    'description' => 'Customer date of birth.',
    'type'        => 'date',
    'show'        => true
  );

  $fields['billing']['fields']['gender'] = array(
    'label'       => 'Gender',
    'description' => 'Customer gender.',
    'type'        => 'select',
    'options'     => array(
      'male'   => 'Male',
      'female' => 'Female',
      'other'  => 'Other'
    ),
    'show'        => true
  );

  $fields['billing']['fields']['input_latitude_1'] = array(
    'label'       => 'Latitude',
    'description' => 'Latitude of billing address.',
    'type'        => 'text',
    'show'        => true
  );

  $fields['billing']['fields']['input_longitude_1'] = array(
    'label'       => 'Longitude',
    'description' => 'Longitude of billing address.',
    'type'        => 'text',
    'show'        => true
  );

  return $fields;
}
add_filter('woocommerce_customer_meta_fields', 'add_custom_billing_fields_to_woocommerce');

function save_custom_fields_on_edit_account($user_id)
{
  if (isset($_POST['birthday'])) {
    update_user_meta($user_id, 'birthday', sanitize_text_field($_POST['birthday']));
  }

  if (isset($_POST['gender'])) {
    update_user_meta($user_id, 'gender', sanitize_text_field($_POST['gender']));
  }

  if (isset($_POST['postcode'])) {
    update_user_meta($user_id, 'postcode', sanitize_text_field($_POST['postcode']));
  }

  if (isset($_POST['input_latitude_1'])) {
    update_user_meta($user_id, 'input_latitude_1', sanitize_text_field($_POST['input_latitude_1']));
  }

  if (isset($_POST['input_longitude_1'])) {
    update_user_meta($user_id, 'input_longitude_1', sanitize_text_field($_POST['input_longitude_1']));
  }
}
add_action('woocommerce_save_account_details', 'save_custom_fields_on_edit_account');


function save_update_address()
{
  if (isset($_POST['update_billing_address'])) {
    if (!is_user_logged_in()) {
      return;
    }

    $current_user_id = get_current_user_id();

    update_user_meta($current_user_id, 'billing_first_name', sanitize_text_field($_POST['billing_first_name']));
    update_user_meta($current_user_id, 'billing_last_name', sanitize_text_field($_POST['billing_last_name']));
    update_user_meta($current_user_id, 'billing_address_1', sanitize_text_field($_POST['billing_address_1']));
    update_user_meta($current_user_id, 'billing_postcode', sanitize_text_field($_POST['billing_postcode']));
    update_user_meta($current_user_id, 'input_latitude_1', sanitize_text_field($_POST['input_latitude_1']));
    update_user_meta($current_user_id, 'input_longitude_1', sanitize_text_field($_POST['input_longitude_1']));

    wp_redirect('/my-account/edit-address/');
    exit;
  }
}
add_action('init', 'save_update_address');


function format_date_DdMY($date_string)
{
  $timestamp = strtotime($date_string);
  return date('D, d M Y', $timestamp);
}
