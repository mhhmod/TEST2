<?php
/**
 * The template for displaying all WooCommerce pages
 *
 * @package GrindCTRL
 * @since 1.0.0
 */

get_header(); ?>

<main id="primary" class="site-main woocommerce-main">
    <div class="container">
        
        <?php if (function_exists('woocommerce_breadcrumb')) : ?>
            <div class="woocommerce-breadcrumb-container">
                <?php woocommerce_breadcrumb(); ?>
            </div>
        <?php endif; ?>

        <div class="woocommerce-content">
            <?php woocommerce_content(); ?>
        </div>
        
    </div>
</main>

<?php get_footer(); ?>
