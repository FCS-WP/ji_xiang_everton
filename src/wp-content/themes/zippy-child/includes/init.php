<?php

foreach (glob(THEME_DIR . '-child' . "/includes/checkout/*.php") as $file_name) {
  require_once($file_name);
}
foreach (glob(THEME_DIR . '-child' . "/includes/products/*.php") as $file_name) {
  require_once($file_name);
}
foreach (glob(THEME_DIR . '-child' . "/includes/users/*.php") as $file_name) {
  require_once($file_name);
}
foreach (glob(THEME_DIR . '-child' . "/includes/woocommerce/*.php") as $file_name) {
  require_once($file_name);
}
foreach (glob(THEME_DIR . '-child' . "/includes/cart/*.php") as $file_name) {
  require_once($file_name);
}

foreach (glob(THEME_DIR . '-child' . "/includes/product_combo/*.php") as $file_name) {
  require_once($file_name);
}
foreach (glob(THEME_DIR . '-child' . "/includes/custom_role/*.php") as $file_name) {
  require_once($file_name);
}
