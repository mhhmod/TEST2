/**
 * Customizer Preview Script for GrindCTRL Theme
 *
 * @package GrindCTRL
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Site title and description
    wp.customize('blogname', function(value) {
        value.bind(function(to) {
            $('.site-title a').text(to);
        });
    });

    wp.customize('blogdescription', function(value) {
        value.bind(function(to) {
            $('.site-description').text(to);
        });
    });

    // Header text color
    wp.customize('header_textcolor', function(value) {
        value.bind(function(to) {
            if ('blank' === to) {
                $('.site-title, .site-description').css({
                    'clip': 'rect(1px, 1px, 1px, 1px)',
                    'position': 'absolute'
                });
            } else {
                $('.site-title, .site-description').css({
                    'clip': 'auto',
                    'position': 'relative'
                });
                $('.site-title a, .site-description').css({
                    'color': to
                });
            }
        });
    });

    // Background color
    wp.customize('background_color', function(value) {
        value.bind(function(to) {
            $('body').css('background-color', to);
        });
    });

    // Custom logo
    wp.customize('custom_logo', function(value) {
        value.bind(function(to) {
            if (to) {
                // Logo was added
                if (!$('.custom-logo-link').length) {
                    $('.site-branding').prepend('<a href="' + wp.customize.settings.url.home + '" class="custom-logo-link" rel="home"></a>');
                }
            } else {
                // Logo was removed
                $('.custom-logo-link').remove();
            }
        });
    });

    // Theme colors (if implemented)
    wp.customize('primary_color', function(value) {
        value.bind(function(to) {
            $('<style id="primary-color-style"></style>').appendTo('head');
            $('#primary-color-style').text(':root { --primary-color: ' + to + '; }');
        });
    });

    wp.customize('accent_color', function(value) {
        value.bind(function(to) {
            $('<style id="accent-color-style"></style>').appendTo('head');
            $('#accent-color-style').text(':root { --accent-color: ' + to + '; }');
        });
    });

    // Typography (if implemented)
    wp.customize('body_font', function(value) {
        value.bind(function(to) {
            $('body').css('font-family', to);
        });
    });

    wp.customize('heading_font', function(value) {
        value.bind(function(to) {
            $('h1, h2, h3, h4, h5, h6').css('font-family', to);
        });
    });

    // Layout options (if implemented)
    wp.customize('container_width', function(value) {
        value.bind(function(to) {
            $('<style id="container-width-style"></style>').appendTo('head');
            $('#container-width-style').text('.container { max-width: ' + to + 'px; }');
        });
    });

    // WooCommerce colors (if implemented)
    wp.customize('wc_primary_color', function(value) {
        value.bind(function(to) {
            $('<style id="wc-primary-color-style"></style>').appendTo('head');
            $('#wc-primary-color-style').text('.woocommerce .button, .woocommerce button.button { background-color: ' + to + '; }');
        });
    });

    // Footer customizations (if implemented)
    wp.customize('footer_text', function(value) {
        value.bind(function(to) {
            $('.footer-text').text(to);
        });
    });

    wp.customize('show_footer_widgets', function(value) {
        value.bind(function(to) {
            if (to) {
                $('.footer-widgets').show();
            } else {
                $('.footer-widgets').hide();
            }
        });
    });

    // Social media links (if implemented)
    var socialNetworks = ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube'];
    
    socialNetworks.forEach(function(network) {
        wp.customize('social_' + network, function(value) {
            value.bind(function(to) {
                var linkElement = $('.social-links a[href*="' + network + '"]');
                if (to && to.length > 0) {
                    if (linkElement.length) {
                        linkElement.attr('href', to);
                    } else {
                        $('.social-links').append('<a href="' + to + '" target="_blank" rel="noopener"><i class="fab fa-' + network + '"></i></a>');
                    }
                } else {
                    linkElement.remove();
                }
            });
        });
    });

    // Live preview for custom CSS (if implemented)
    wp.customize('custom_css', function(value) {
        value.bind(function(to) {
            $('#custom-css-preview').remove();
            if (to) {
                $('head').append('<style id="custom-css-preview">' + to + '</style>');
            }
        });
    });

})(jQuery);