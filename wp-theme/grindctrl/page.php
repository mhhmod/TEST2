<?php if (!defined('ABSPATH')) { exit; }
get_header();
?>
<main class="main">
    <div class="container">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <article <?php post_class(); ?>>
                <h1 class="product-title"><?php the_title(); ?></h1>
                <div class="product-subtitle">&nbsp;</div>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; endif; ?>
    </div>
</main>
<?php get_footer(); ?>

