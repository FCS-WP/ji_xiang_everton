<? phpadd_action('save_post', 'check_product_combo_stock_before_save_post', 10, 3);
function check_product_combo_stock_before_save_post($post_id, $post, $update)
{
    if ($post->post_type !== 'product') return;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id)) return;

    if (empty($_POST['acf']['field_6841549f8b056'])) return;

    $product_combo = $_POST['acf']['field_6841549f8b056'];

    foreach ($product_combo as $row) {
        $product_id  = intval($row['field_6841554b8b057']);
        $stock_level = intval($row['field_684155648b058']);

        $product = wc_get_product($product_id);
        if (!$product) continue;

        $actual_stock = $product->get_stock_quantity();

        if ($stock_level > $actual_stock) {
            $redirect_url = add_query_arg([
                'post' => $post_id,
                'action' => 'edit',
                'combo_stock_error' => sprintf(
                    'Error: The quantity entered for %s exceeds available stock (%d).',
                    $product->get_name(),
                    $actual_stock
                )
            ], admin_url('post.php'));


            wp_redirect($redirect_url);
            exit;
        }
    }
}
add_action('admin_enqueue_scripts', function () {
    $version = time();
    wp_enqueue_script('sweet-alert2-js', THEME_URL . '-child' . '/assets/lib/sweetalert/sweetalert2.all.min.js', [], $version, true);
});
add_action('admin_notices', function () {
    if (!empty($_GET['combo_stock_error'])) {
        $error_message = sanitize_text_field($_GET['combo_stock_error']);
?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: <?php echo json_encode($error_message); ?>
                });

                if (window.history.replaceState) {
                    const url = new URL(window.location);
                    url.searchParams.delete('combo_stock_error');
                    window.history.replaceState({}, document.title, url.toString());
                }
            });
        </script>
<?php
    }
});
