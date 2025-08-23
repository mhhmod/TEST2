<?php
/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * @package GrindCTRL
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header('shop'); ?>

<main id="primary" class="site-main">
    <div class="container">
        
        <?php while (have_posts()) : the_post(); ?>

            <div class="product-section" id="product-<?php the_ID(); ?>">
                
                <?php wc_get_template_part('content', 'single-product'); ?>

            </div>

        <?php endwhile; // end of the loop. ?>
        
    </div>
</main>

<?php
do_action('woocommerce_output_related_products_args');
get_footer('shop');
?>
