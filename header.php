<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>
    
    <?php if (is_front_page() || is_shop()) : ?>
    <meta name="description" content="Premium cotton luxury cropped black t-shirt. Minimal design, maximum comfort. Fast checkout with cart support.">
    <meta name="keywords" content="black t-shirt, luxury, cropped, premium cotton, fashion">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php bloginfo('name'); ?> | Premium Fashion">
    <meta property="og:description" content="Premium cotton luxury fashion. Minimal design, maximum comfort.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo home_url(); ?>">
    <?php endif; ?>
    
    <?php wp_head(); ?>
    
    <!-- Configuration for JavaScript -->
    <script>
    window.CONFIG = {
        WEBHOOK_URL: '<?php echo esc_js(get_option('grindctrl_webhook_url', '')); ?>',
        AJAX_URL: '<?php echo admin_url('admin-ajax.php'); ?>',
        NONCE: '<?php echo wp_create_nonce('grindctrl_nonce'); ?>'
    };
    </script>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-shopping-bag"></i>
                    <span><a href="<?php echo home_url(); ?>" style="color: inherit; text-decoration: none;"><?php bloginfo('name'); ?></a></span>
                </div>
                <nav class="nav">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_class' => 'nav-menu',
                        'container' => false,
                        'fallback_cb' => 'grindctrl_fallback_menu',
                    ));
                    ?>
                    
                    <?php if (class_exists('WooCommerce')) : ?>
                        <?php grindctrl_cart_link(); ?>
                    <?php else : ?>
                        <div class="cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count" id="cartCount">0</span>
                        </div>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

<?php
/**
 * Fallback menu when no menu is set
 */
function grindctrl_fallback_menu() {
    echo '<a href="' . home_url() . '#home" class="nav-link">Home</a>';
    if (class_exists('WooCommerce')) {
        echo '<a href="' . get_permalink(wc_get_page_id('shop')) . '" class="nav-link">Shop</a>';
        echo '<a href="' . get_permalink(wc_get_page_id('cart')) . '" class="nav-link">Cart</a>';
        echo '<a href="' . get_permalink(wc_get_page_id('myaccount')) . '" class="nav-link">Account</a>';
    } else {
        echo '<a href="#product" class="nav-link">Product</a>';
    }
    echo '<a href="#contact" class="nav-link">Contact</a>';
}
?>