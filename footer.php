<?php
/**
 * The template for displaying the footer
 *
 * @package GrindCTRL
 * @version 1.0.0
 */
?>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <div class="logo">
                    <i class="fas fa-shopping-bag"></i>
                    <span><?php bloginfo('name'); ?></span>
                </div>
                <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('All rights reserved.', 'grindctrl'); ?></p>
            </div>
            
            <div class="footer-links">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'footer',
                    'menu_class' => 'footer-menu',
                    'container' => false,
                    'fallback_cb' => 'grindctrl_footer_fallback_menu',
                ));
                ?>
            </div>
        </div>
    </div>
</footer>

<!-- Notification System -->
<div class="notification" id="notification" style="display: none;">
    <div class="notification-content">
        <i class="notification-icon"></i>
        <span class="notification-message"></span>
        <button class="notification-close" onclick="hideNotification()">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<?php wp_footer(); ?>

<?php
// Footer fallback menu function
function grindctrl_footer_fallback_menu() {
    echo '<div class="footer-menu">';
    echo '<a href="#">' . __('Privacy Policy', 'grindctrl') . '</a>';
    echo '<a href="#">' . __('Terms of Service', 'grindctrl') . '</a>';
    echo '<a href="#">' . __('Returns', 'grindctrl') . '</a>';
    echo '<a href="#">' . __('Contact', 'grindctrl') . '</a>';
    echo '</div>';
}
?>

</body>
</html>