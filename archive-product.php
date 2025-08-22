<?php
/**
 * Shop/Product Archive template
 *
 * @package GrindCTRL
 * @version 1.0.0
 */

get_header(); ?>

<main class="main shop-main">
    <div class="container">
        <div class="shop-header">
            <h1 class="page-title">
                <?php if (is_shop()) : ?>
                    <?php _e('Shop', 'grindctrl'); ?>
                <?php else : ?>
                    <?php woocommerce_page_title(); ?>
                <?php endif; ?>
            </h1>
            
            <?php if (is_product_category() || is_product_tag()) : ?>
                <div class="archive-description">
                    <?php echo wpautop(wp_kses_post(term_description())); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="shop-content">
            <?php if (woocommerce_product_loop()) : ?>
                
                <?php woocommerce_product_loop_start(); ?>

                <?php if (wc_get_loop_prop('is_shortcode')) : ?>
                    <?php woocommerce_product_subcategories(); ?>
                <?php endif; ?>

                <?php while (have_posts()) : ?>
                    <?php the_post(); ?>
                    <?php wc_get_template_part('content', 'product'); ?>
                <?php endwhile; ?>

                <?php woocommerce_product_loop_end(); ?>

                <?php woocommerce_pagination(); ?>

            <?php else : ?>
                
                <div class="no-products-found">
                    <h2><?php _e('No products found', 'grindctrl'); ?></h2>
                    <p><?php _e('It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'grindctrl'); ?></p>
                    <?php get_search_form(); ?>
                </div>

            <?php endif; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>