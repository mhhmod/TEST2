<?php if (!defined('ABSPATH')) { exit; } ?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-shopping-bag"></i>
                    <span><?php bloginfo('name'); ?></span>
                </div>
                <nav class="nav">
                    <a href="<?php echo esc_url(home_url('/#home')); ?>" class="nav-link">Home</a>
                    <a href="<?php echo esc_url(home_url('/#product')); ?>" class="nav-link">Product</a>
                    <a href="<?php echo esc_url(home_url('/#contact')); ?>" class="nav-link">Contact</a>
                    <div class="cart-icon">
                        <a href="<?php echo esc_url(wc_get_cart_url()); ?>">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count" id="cartCount"><?php echo WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?></span>
                        </a>
                    </div>
                </nav>
            </div>
        </div>
    </header>

