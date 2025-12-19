//wp-content/themes/twentytwentyfour/functions.php
function add_admin_link() {
    echo '<a href="' . ADMIN_URL . '" target="_blank">Админка CRUD</a>';
}
add_action('wp_footer', 'add_admin_link');
