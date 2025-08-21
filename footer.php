    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="logo">
                        <i class="fas fa-shopping-bag"></i>
                        <span><?php bloginfo('name'); ?></span>
                    </div>
                    <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
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

    <!-- Order Success Modal -->
    <div class="modal" id="successModal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Order Confirmed!</h2>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <p>Thank you for your order! We've received your purchase and will begin processing it shortly.</p>
                <div class="order-details" id="orderDetails"></div>
                <p class="note">You will receive a confirmation email with tracking information once your order ships.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="closeModal()">Continue Shopping</button>
            </div>
        </div>
    </div>

    <?php wp_footer(); ?>

</body>
</html>

<?php
/**
 * Fallback footer menu
 */
function grindctrl_footer_fallback_menu() {
    if (class_exists('WooCommerce')) {
        echo '<a href="' . get_privacy_policy_url() . '">Privacy Policy</a>';
        echo '<a href="' . get_permalink(wc_get_page_id('terms')) . '">Terms of Service</a>';
        echo '<a href="' . get_permalink(wc_get_page_id('myaccount')) . '">My Account</a>';
    } else {
        echo '<a href="#privacy">Privacy Policy</a>';
        echo '<a href="#terms">Terms of Service</a>';
        echo '<a href="#returns">Returns</a>';
    }
    echo '<a href="#contact">Contact</a>';
}
?>