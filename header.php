<?php
/**
 * The header for our theme
 *
 * @package GrindCTRL
 * @version 1.0.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Header -->
<header class="header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <i class="fas fa-shopping-bag"></i>
                    <span><?php bloginfo('name'); ?></span>
                </a>
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
                    <div class="cart-icon">
                        <a href="<?php echo wc_get_cart_url(); ?>">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                        </a>
                    </div>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>

<?php
// Fallback menu function
function grindctrl_fallback_menu() {
    echo '<div class="nav-menu">';
    echo '<a href="' . esc_url(home_url('/')) . '" class="nav-link">' . __('Home', 'grindctrl') . '</a>';
    if (class_exists('WooCommerce')) {
        echo '<a href="' . esc_url(wc_get_page_permalink('shop')) . '" class="nav-link">' . __('Shop', 'grindctrl') . '</a>';
    }
    echo '<a href="' . esc_url(get_permalink(get_option('page_for_posts'))) . '" class="nav-link">' . __('Blog', 'grindctrl') . '</a>';
    echo '</div>';
}
?>