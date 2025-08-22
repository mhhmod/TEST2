<?php
/**
 * WooCommerce template
 *
 * @package GrindCTRL
 * @version 1.0.0
 */

get_header(); ?>

<main class="main woocommerce-main">
    <div class="container">
        <?php grindctrl_woocommerce_messages(); ?>
        
        <?php if (is_shop() || is_product_category() || is_product_tag()) : ?>
            <div class="shop-header">
                <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
                <?php if (is_product_category() || is_product_tag()) : ?>
                    <div class="archive-description">
                        <?php echo wpautop(wp_kses_post(term_description())); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="woocommerce-content">
            <?php woocommerce_content(); ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>