<?php
/**
 * The main template file
 *
 * @package GrindCTRL
 * @version 1.0.0
 */

get_header(); ?>

<main class="main" id="home">
    <div class="container">
        <?php if (have_posts()) : ?>
            <div class="posts-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <?php the_post_thumbnail('grindctrl-featured'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="post-content">
                            <h2 class="post-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            
                            <div class="post-meta">
                                <span class="post-date"><?php echo get_the_date(); ?></span>
                                <span class="post-author"><?php _e('by', 'grindctrl'); ?> <?php the_author(); ?></span>
                            </div>
                            
                            <div class="post-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                                <?php _e('Read More', 'grindctrl'); ?>
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            
            <?php
            // Pagination
            the_posts_pagination(array(
                'prev_text' => __('Previous', 'grindctrl'),
                'next_text' => __('Next', 'grindctrl'),
            ));
            ?>
        <?php else : ?>
            <div class="no-posts">
                <h2><?php _e('Nothing Found', 'grindctrl'); ?></h2>
                <p><?php _e('It looks like nothing was found at this location.', 'grindctrl'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>