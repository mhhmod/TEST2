<?php
/**
 * Checkout Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * @package GrindCTRL
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container">
        
        <header class="page-header">
            <h1 class="page-title"><?php esc_html_e('Checkout', 'grindctrl'); ?></h1>
        </header>

        <div class="checkout-content">
            <?php woocommerce_content(); ?>
        </div>
        
    </div>
</main>

<?php get_footer(); ?>
