<?php

declare(strict_types=1);

namespace GrindCTRL\Theme\Support;

final class SEO
{
	public static function hook(): void
	{
		add_action('wp_head', [self::class, 'jsonld_breadcrumbs'], 99);
		if (class_exists('WooCommerce')) {
			add_action('wp_head', [self::class, 'jsonld_product'], 99);
		}
	}

	public static function jsonld_breadcrumbs(): void
	{
		if (function_exists('is_front_page') && is_front_page()) {
			return;
		}
		$items = [
			[
				'@type' => 'ListItem',
				'position' => 1,
				'name' => get_bloginfo('name'),
				'item' => home_url('/'),
			],
		];
		$graph = [
			'@context' => 'https://schema.org',
			'@type' => 'BreadcrumbList',
			'itemListElement' => $items,
		];
		echo '<script type="application/ld+json">' . wp_json_encode($graph) . '</script>';
	}

	public static function jsonld_product(): void
	{
		if (! function_exists('is_product') || ! is_product()) {
			return;
		}
		global $product;
		if (! $product instanceof \WC_Product) {
			return;
		}
		$graph = [
			'@context' => 'https://schema.org',
			'@type' => 'Product',
			'name' => $product->get_name(),
			'image' => array_values(array_filter($product->get_gallery_image_ids() ? array_map('wp_get_attachment_url', $product->get_gallery_image_ids()) : [wp_get_attachment_url($product->get_image_id())])),
			'description' => wp_strip_all_tags(get_the_excerpt($product->get_id())),
			'offers' => [
				'@type' => 'Offer',
				'priceCurrency' => get_woocommerce_currency(),
				'price' => wc_get_price_to_display($product),
				'availability' => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
				'url' => get_permalink($product->get_id()),
			],
		];
		echo '<script type="application/ld+json">' . wp_json_encode($graph) . '</script>';
	}
}