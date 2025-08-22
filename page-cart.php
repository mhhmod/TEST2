<?php
/**
 * Cart Page Template
 *
 * @package GrindCTRL
 * @version 1.0.0
 */

get_header(); ?>

<main class="main cart-main">
    <div class="container">
        <div class="page-header">
            <h1 class="page-title"><?php _e('Shopping Cart', 'grindctrl'); ?></h1>
        </div>

        <?php while (have_posts()) : the_post(); ?>
            <div class="page-content">
                <?php the_content(); ?>
            </div>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>