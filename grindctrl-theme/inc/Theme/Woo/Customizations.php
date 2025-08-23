<?php

declare(strict_types=1);

namespace GrindCTRL\Theme\Theme\Woo;

/**
 * WooCommerce tweaks and block compatibility.
 *
 * @since 3.0.0
 */
final class Customizations
{
	private static ?Customizations $instance = null;

	public static function get_instance(): Customizations
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function hook(): void
	{
		if (! class_exists('WooCommerce')) {
			return;
		}
		add_filter('woocommerce_enqueue_styles', '__return_empty_array', 99);
		add_action('after_setup_theme', [$this, 'declare_block_theme_support'], 20);
		add_action('init', [$this, 'register_image_sizes']);
		add_filter('woocommerce_single_product_carousel_options', [$this, 'tune_product_gallery'], 10, 1);
	}

	public function declare_block_theme_support(): void
	{
		add_theme_support('woocommerce-block-theme');
	}

	public function register_image_sizes(): void
	{
		add_image_size('grindctrl-product-card', 600, 600, true);
		add_image_size('grindctrl-product-hero', 1200, 1200, false);
	}

	/**
	 * Fine-tune product gallery (e.g., disable autoplay for a11y).
	 *
	 * @param array<string, mixed> $options Gallery options.
	 * @return array<string, mixed>
	 */
	public function tune_product_gallery(array $options): array
	{
		$options['autoplay'] = false;
		$options['animation'] = 'slide';
		return $options;
	}
}
