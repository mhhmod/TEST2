<?php if (!defined('ABSPATH')) { exit; }
get_header('shop');
?>
<main class="main">
    <div class="container">
        <?php woocommerce_content(); ?>
    </div>
</main>
<?php get_footer('shop'); ?>

