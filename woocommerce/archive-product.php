<?php
/**
 * The Template for displaying product archives, including the main shop page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * @package GrindCTRL
 * @version 7.0.1
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header('shop');

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action('woocommerce_before_main_content');
?>

<main id="primary" class="site-main shop-main" role="main">
    <div class="container">
        
        <!-- Page Header -->
        <header class="woocommerce-products-header page-header" role="banner">
            <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
                <h1 class="woocommerce-products-header__title page-title">
                    <?php woocommerce_page_title(); ?>
                </h1>
            <?php endif; ?>

            <?php
            /**
             * Hook: woocommerce_archive_description.
             *
             * @hooked woocommerce_taxonomy_archive_description - 10
             * @hooked woocommerce_product_archive_description - 10
             */
            do_action('woocommerce_archive_description');
            ?>

            <!-- Shop Controls -->
            <div class="shop-controls" role="navigation" aria-label="<?php esc_attr_e('Shop controls', 'grindctrl'); ?>">
                
                <!-- View Toggle -->
                <div class="view-toggle" role="group" aria-label="<?php esc_attr_e('View options', 'grindctrl'); ?>">
                    <button class="view-btn grid-view active" 
                            aria-pressed="true"
                            aria-label="<?php esc_attr_e('Grid view', 'grindctrl'); ?>"
                            data-view="grid">
                        <i class="fas fa-th" aria-hidden="true"></i>
                    </button>
                    <button class="view-btn list-view" 
                            aria-pressed="false"
                            aria-label="<?php esc_attr_e('List view', 'grindctrl'); ?>"
                            data-view="list">
                        <i class="fas fa-list" aria-hidden="true"></i>
                    </button>
                </div>

                <!-- Results Count -->
                <div class="results-count" aria-live="polite">
                    <?php
                    $total = wc_get_loop_prop('total');
                    $per_page = wc_get_loop_prop('per_page');
                    $current = wc_get_loop_prop('current_page');
                    
                    $first = ($per_page * $current) - $per_page + 1;
                    $last = min($total, $per_page * $current);
                    
                    if ($total <= $per_page || -1 === $per_page) {
                        printf(
                            _n('Showing the single result', 'Showing all %d results', $total, 'grindctrl'),
                            number_format_i18n($total)
                        );
                    } else {
                        printf(
                            _x('Showing %1$d–%2$d of %3$d results', '%1$d = first, %2$d = last, %3$d = total', 'grindctrl'),
                            number_format_i18n($first),
                            number_format_i18n($last),
                            number_format_i18n($total)
                        );
                    }
                    ?>
                </div>
            </div>
        </header>

        <?php if (woocommerce_product_loop()) : ?>

            <div class="shop-content">
                
                <!-- Sidebar Filters -->
                <aside class="shop-sidebar" role="complementary" aria-label="<?php esc_attr_e('Product filters', 'grindctrl'); ?>">
                    
                    <!-- Filter Toggle (Mobile) -->
                    <button class="filter-toggle" 
                            aria-expanded="false" 
                            aria-controls="shop-filters"
                            aria-label="<?php esc_attr_e('Toggle filters', 'grindctrl'); ?>">
                        <i class="fas fa-filter" aria-hidden="true"></i>
                        <span><?php esc_html_e('Filters', 'grindctrl'); ?></span>
                    </button>

                    <div id="shop-filters" class="shop-filters">
                        
                        <!-- Active Filters -->
                        <div class="active-filters" id="active-filters">
                            <h3><?php esc_html_e('Active Filters', 'grindctrl'); ?></h3>
                            <div class="filter-tags" role="list" aria-label="<?php esc_attr_e('Active filters', 'grindctrl'); ?>">
                                <!-- Active filter tags will be populated by JavaScript -->
                            </div>
                            <button type="button" class="clear-all-filters" style="display: none;">
                                <?php esc_html_e('Clear All', 'grindctrl'); ?>
                            </button>
                        </div>

                        <!-- Price Filter -->
                        <div class="filter-widget price-filter">
                            <h3 class="widget-title">
                                <button class="filter-toggle-btn" 
                                        aria-expanded="true" 
                                        aria-controls="price-filter-content">
                                    <?php esc_html_e('Price', 'grindctrl'); ?>
                                    <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                </button>
                            </h3>
                            <div id="price-filter-content" class="filter-content">
                                <?php
                                $min_price = isset($_GET['min_price']) ? esc_attr($_GET['min_price']) : '';
                                $max_price = isset($_GET['max_price']) ? esc_attr($_GET['max_price']) : '';
                                ?>
                                <div class="price-range-inputs">
                                    <div class="price-input-group">
                                        <label for="min_price"><?php esc_html_e('Min', 'grindctrl'); ?></label>
                                        <input type="number" 
                                               id="min_price" 
                                               name="min_price" 
                                               value="<?php echo esc_attr($min_price); ?>" 
                                               min="0" 
                                               placeholder="0" />
                                    </div>
                                    <div class="price-input-group">
                                        <label for="max_price"><?php esc_html_e('Max', 'grindctrl'); ?></label>
                                        <input type="number" 
                                               id="max_price" 
                                               name="max_price" 
                                               value="<?php echo esc_attr($max_price); ?>" 
                                               min="0" 
                                               placeholder="1000" />
                                    </div>
                                </div>
                                <button type="button" class="apply-price-filter btn btn-secondary">
                                    <?php esc_html_e('Apply', 'grindctrl'); ?>
                                </button>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <?php if (get_terms('product_cat')) : ?>
                            <div class="filter-widget category-filter">
                                <h3 class="widget-title">
                                    <button class="filter-toggle-btn" 
                                            aria-expanded="true" 
                                            aria-controls="category-filter-content">
                                        <?php esc_html_e('Categories', 'grindctrl'); ?>
                                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                    </button>
                                </h3>
                                <div id="category-filter-content" class="filter-content">
                                    <ul class="product-categories">
                                        <?php
                                        $selected_cats = isset($_GET['product_cat']) ? explode(',', sanitize_text_field($_GET['product_cat'])) : array();
                                        $categories = get_terms(array(
                                            'taxonomy' => 'product_cat',
                                            'hide_empty' => true,
                                            'parent' => 0,
                                        ));
                                        
                                        foreach ($categories as $category) :
                                            $checked = in_array($category->slug, $selected_cats) ? 'checked' : '';
                                        ?>
                                            <li>
                                                <label class="filter-checkbox">
                                                    <input type="checkbox" 
                                                           name="product_cat" 
                                                           value="<?php echo esc_attr($category->slug); ?>"
                                                           <?php echo $checked; ?> />
                                                    <span class="checkmark"></span>
                                                    <span class="label-text">
                                                        <?php echo esc_html($category->name); ?>
                                                        <span class="count">(<?php echo esc_html($category->count); ?>)</span>
                                                    </span>
                                                </label>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Size Filter (if applicable) -->
                        <?php 
                        $size_attribute = wc_get_attribute(wc_attribute_taxonomy_id_by_name('size'));
                        if ($size_attribute) :
                            $size_terms = get_terms(array(
                                'taxonomy' => 'pa_size',
                                'hide_empty' => true,
                            ));
                            
                            if (!empty($size_terms)) :
                        ?>
                                <div class="filter-widget size-filter">
                                    <h3 class="widget-title">
                                        <button class="filter-toggle-btn" 
                                                aria-expanded="true" 
                                                aria-controls="size-filter-content">
                                            <?php esc_html_e('Size', 'grindctrl'); ?>
                                            <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                        </button>
                                    </h3>
                                    <div id="size-filter-content" class="filter-content">
                                        <div class="size-options">
                                            <?php
                                            $selected_sizes = isset($_GET['filter_size']) ? explode(',', sanitize_text_field($_GET['filter_size'])) : array();
                                            foreach ($size_terms as $size) :
                                                $checked = in_array($size->slug, $selected_sizes) ? 'checked' : '';
                                            ?>
                                                <label class="size-option">
                                                    <input type="checkbox" 
                                                           name="filter_size" 
                                                           value="<?php echo esc_attr($size->slug); ?>"
                                                           <?php echo $checked; ?> />
                                                    <span class="size-label"><?php echo esc_html($size->name); ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Rating Filter -->
                        <div class="filter-widget rating-filter">
                            <h3 class="widget-title">
                                <button class="filter-toggle-btn" 
                                        aria-expanded="true" 
                                        aria-controls="rating-filter-content">
                                    <?php esc_html_e('Rating', 'grindctrl'); ?>
                                    <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                </button>
                            </h3>
                            <div id="rating-filter-content" class="filter-content">
                                <?php
                                $selected_rating = isset($_GET['rating_filter']) ? absint($_GET['rating_filter']) : 0;
                                for ($i = 5; $i >= 1; $i--) :
                                ?>
                                    <label class="rating-option">
                                        <input type="radio" 
                                               name="rating_filter" 
                                               value="<?php echo esc_attr($i); ?>"
                                               <?php checked($selected_rating, $i); ?> />
                                        <div class="stars" aria-label="<?php printf(esc_attr__('%d stars and up', 'grindctrl'), $i); ?>">
                                            <?php for ($j = 1; $j <= 5; $j++) : ?>
                                                <i class="<?php echo $j <= $i ? 'fas' : 'far'; ?> fa-star" aria-hidden="true"></i>
                                            <?php endfor; ?>
                                            <span class="rating-text"><?php esc_html_e('& Up', 'grindctrl'); ?></span>
                                        </div>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>

                    </div>
                </aside>

                <!-- Products Grid -->
                <div class="products-area" role="main" aria-label="<?php esc_attr_e('Product list', 'grindctrl'); ?>">
                    
                    <!-- Shop Toolbar -->
                    <div class="shop-toolbar">
                        <?php
                        /**
                         * Hook: woocommerce_before_shop_loop.
                         *
                         * @hooked woocommerce_output_all_notices - 10
                         * @hooked woocommerce_result_count - 20
                         * @hooked woocommerce_catalog_ordering - 30
                         */
                        do_action('woocommerce_before_shop_loop');
                        ?>
                    </div>

                    <!-- Products List -->
                    <div class="products-grid" data-view="grid" data-columns="3">
                        <?php woocommerce_product_loop_start(); ?>

                        <?php if (wc_get_loop_prop('is_shortcode')) : ?>
                            <?php
                            $columns = absint(wc_get_loop_prop('columns'));
                            wc_set_loop_prop('columns', $columns);
                            ?>
                        <?php endif; ?>

                        <?php while (have_posts()) : ?>
                            <?php
                            the_post();

                            /**
                             * Hook: woocommerce_shop_loop.
                             */
                            do_action('woocommerce_shop_loop');

                            wc_get_template_part('content', 'product');
                            ?>
                        <?php endwhile; ?>

                        <?php woocommerce_product_loop_end(); ?>
                    </div>

                    <?php
                    /**
                     * Hook: woocommerce_after_shop_loop.
                     *
                     * @hooked woocommerce_pagination - 10
                     */
                    do_action('woocommerce_after_shop_loop');
                    ?>

                </div>

            </div>

        <?php else : ?>

            <!-- No Products Found -->
            <div class="no-products-found" role="alert">
                <div class="no-products-content">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <h2><?php esc_html_e('No products found', 'grindctrl'); ?></h2>
                    <p><?php esc_html_e('Sorry, no products were found matching your criteria. Try adjusting your filters or search terms.', 'grindctrl'); ?></p>
                    
                    <div class="no-products-actions">
                        <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="btn btn-primary">
                            <?php esc_html_e('View All Products', 'grindctrl'); ?>
                        </a>
                        <button type="button" class="btn btn-secondary clear-all-filters">
                            <?php esc_html_e('Clear Filters', 'grindctrl'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <?php
            /**
             * Hook: woocommerce_no_products_found.
             *
             * @hooked wc_no_products_found - 10
             */
            do_action('woocommerce_no_products_found');
            ?>

        <?php endif; ?>
        
    </div>
</main>

<?php
/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action('woocommerce_after_main_content');

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
// do_action('woocommerce_sidebar'); // Disabled as we have custom sidebar

get_footer('shop');
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Shop functionality
    const viewToggle = document.querySelectorAll('.view-btn');
    const productsGrid = document.querySelector('.products-grid');
    const filterToggle = document.querySelector('.filter-toggle');
    const shopFilters = document.querySelector('#shop-filters');
    const filterToggleBtns = document.querySelectorAll('.filter-toggle-btn');
    const priceFilter = document.querySelector('.apply-price-filter');
    const clearAllFilters = document.querySelectorAll('.clear-all-filters');
    
    // View toggle functionality
    viewToggle.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            
            // Update button states
            viewToggle.forEach(b => {
                b.classList.remove('active');
                b.setAttribute('aria-pressed', 'false');
            });
            this.classList.add('active');
            this.setAttribute('aria-pressed', 'true');
            
            // Update products grid
            if (productsGrid) {
                productsGrid.setAttribute('data-view', view);
                productsGrid.classList.toggle('list-view', view === 'list');
                productsGrid.classList.toggle('grid-view', view === 'grid');
            }
            
            // Save preference
            localStorage.setItem('shop_view', view);
        });
    });
    
    // Restore saved view preference
    const savedView = localStorage.getItem('shop_view');
    if (savedView) {
        const savedViewBtn = document.querySelector(`[data-view="${savedView}"]`);
        if (savedViewBtn) {
            savedViewBtn.click();
        }
    }
    
    // Mobile filter toggle
    if (filterToggle && shopFilters) {
        filterToggle.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
            shopFilters.classList.toggle('active', !isExpanded);
            
            if (!isExpanded) {
                // Focus first filter when opened
                const firstFilter = shopFilters.querySelector('input, button');
                if (firstFilter) {
                    firstFilter.focus();
                }
            }
        });
        
        // Close filters on outside click (mobile)
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768 && 
                !filterToggle.contains(e.target) && 
                !shopFilters.contains(e.target)) {
                filterToggle.setAttribute('aria-expanded', 'false');
                shopFilters.classList.remove('active');
            }
        });
    }
    
    // Filter section toggle
    filterToggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            const content = document.getElementById(this.getAttribute('aria-controls'));
            const icon = this.querySelector('i');
            
            this.setAttribute('aria-expanded', !isExpanded);
            content.style.display = isExpanded ? 'none' : 'block';
            
            if (icon) {
                icon.classList.toggle('fa-chevron-down', isExpanded);
                icon.classList.toggle('fa-chevron-up', !isExpanded);
            }
        });
    });
    
    // Price filter
    if (priceFilter) {
        priceFilter.addEventListener('click', function() {
            const minPrice = document.getElementById('min_price').value;
            const maxPrice = document.getElementById('max_price').value;
            
            const url = new URL(window.location);
            
            if (minPrice) {
                url.searchParams.set('min_price', minPrice);
            } else {
                url.searchParams.delete('min_price');
            }
            
            if (maxPrice) {
                url.searchParams.set('max_price', maxPrice);
            } else {
                url.searchParams.delete('max_price');
            }
            
            window.location.href = url.toString();
        });
    }
    
    // Filter change handlers
    const filterInputs = document.querySelectorAll('.shop-filters input[type="checkbox"], .shop-filters input[type="radio"]');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            updateFilters();
        });
    });
    
    // Clear all filters
    clearAllFilters.forEach(btn => {
        btn.addEventListener('click', function() {
            const url = new URL(window.location);
            
            // Remove all filter parameters
            const filterParams = ['min_price', 'max_price', 'product_cat', 'filter_size', 'rating_filter'];
            filterParams.forEach(param => {
                url.searchParams.delete(param);
            });
            
            window.location.href = url.toString();
        });
    });
    
    function updateFilters() {
        const url = new URL(window.location);
        
        // Collect all filter values
        const filters = {};
        
        // Category filters
        const categoryInputs = document.querySelectorAll('input[name="product_cat"]:checked');
        if (categoryInputs.length > 0) {
            filters.product_cat = Array.from(categoryInputs).map(input => input.value).join(',');
        }
        
        // Size filters
        const sizeInputs = document.querySelectorAll('input[name="filter_size"]:checked');
        if (sizeInputs.length > 0) {
            filters.filter_size = Array.from(sizeInputs).map(input => input.value).join(',');
        }
        
        // Rating filter
        const ratingInput = document.querySelector('input[name="rating_filter"]:checked');
        if (ratingInput) {
            filters.rating_filter = ratingInput.value;
        }
        
        // Update URL parameters
        Object.keys(filters).forEach(key => {
            if (filters[key]) {
                url.searchParams.set(key, filters[key]);
            } else {
                url.searchParams.delete(key);
            }
        });
        
        // Apply filters with a small delay to allow for multiple quick changes
        clearTimeout(window.filterTimeout);
        window.filterTimeout = setTimeout(() => {
            window.location.href = url.toString();
        }, 500);
    }
    
    // Update active filters display
    function updateActiveFiltersDisplay() {
        const activeFilters = document.querySelector('.filter-tags');
        const clearAllBtn = document.querySelector('.clear-all-filters');
        const url = new URL(window.location);
        
        if (!activeFilters) return;
        
        activeFilters.innerHTML = '';
        let hasFilters = false;
        
        // Check for active filters
        const filterParams = {
            'min_price': 'Min Price',
            'max_price': 'Max Price',
            'product_cat': 'Category',
            'filter_size': 'Size',
            'rating_filter': 'Rating'
        };
        
        Object.entries(filterParams).forEach(([param, label]) => {
            const value = url.searchParams.get(param);
            if (value) {
                hasFilters = true;
                const tag = document.createElement('span');
                tag.className = 'filter-tag';
                tag.innerHTML = `
                    ${label}: ${value}
                    <button class="remove-filter" data-filter="${param}" aria-label="Remove ${label} filter">×</button>
                `;
                activeFilters.appendChild(tag);
            }
        });
        
        // Show/hide clear all button
        if (clearAllBtn) {
            clearAllBtn.style.display = hasFilters ? 'block' : 'none';
        }
        
        // Add event listeners to remove buttons
        const removeButtons = activeFilters.querySelectorAll('.remove-filter');
        removeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const filterParam = this.dataset.filter;
                url.searchParams.delete(filterParam);
                window.location.href = url.toString();
            });
        });
    }
    
    // Initialize active filters display
    updateActiveFiltersDisplay();
});
</script>
