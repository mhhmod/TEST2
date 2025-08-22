<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @package GrindCTRL
 * @since 1.0.0
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary">
        <?php esc_html_e('Skip to content', 'grindctrl'); ?>
    </a>

    <header id="masthead" class="site-header header">
        <div class="container">
            <div class="header-content">
                
                <div class="site-branding logo">
                    <?php
                    $custom_logo_id = get_theme_mod('custom_logo');
                    if ($custom_logo_id) :
                        $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                        ?>
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" class="custom-logo-link">
                            <img src="<?php echo esc_url($logo[0]); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="custom-logo">
                        </a>
                    <?php else : ?>
                        <i class="fas fa-shopping-bag" aria-hidden="true"></i>
                        <span class="site-title">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                <?php bloginfo('name'); ?>
                            </a>
                        </span>
                    <?php endif; ?>
                </div>

                <nav id="site-navigation" class="main-navigation nav" aria-label="<?php esc_attr_e('Primary Menu', 'grindctrl'); ?>">
                    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                        <span class="menu-toggle-text"><?php esc_html_e('Menu', 'grindctrl'); ?></span>
                        <i class="fas fa-bars" aria-hidden="true"></i>
                    </button>
                    
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'menu_class'     => 'nav-menu',
                        'container'      => false,
                        'fallback_cb'    => 'grindctrl_default_menu',
                    ));
                    ?>

                    <?php if (class_exists('WooCommerce')) : ?>
                        <div class="header-cart cart-icon">
                            <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="cart-link" aria-label="<?php esc_attr_e('View cart', 'grindctrl'); ?>">
                                <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                                <span class="cart-count" id="cartCount">
                                    <?php echo esc_html(WC()->cart->get_cart_contents_count()); ?>
                                </span>
                            </a>
                        </div>
                    <?php endif; ?>
                </nav>
                
            </div>
        </div>
    </header>

    <div id="content" class="site-content">
