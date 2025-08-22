<?php

declare(strict_types=1);

namespace GrindCTRL\Theme\Theme;

use GrindCTRL\Theme\Theme\Assets;
use GrindCTRL\Theme\Theme\Woo\Customizations;
use GrindCTRL\Theme\Support\Security;
use GrindCTRL\Theme\Support\SEO;
use GrindCTRL\Theme\Support\PWA;

/**
 * Main theme setup and supports.
 *
 * @since 3.0.0
 */
final class Setup
{
	private static ?Setup $instance = null;

	public static function get_instance(): Setup
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function boot(): void
	{
		$this->add_theme_supports();
		$this->register_block_patterns_category();
		Assets::get_instance()->hook();
		Customizations::get_instance()->hook();
		Security::csp_headers();
		SEO::hook();
		PWA::hook();
	}

	private function add_theme_supports(): void
	{
		add_theme_support('automatic-feed-links');
		add_theme_support('title-tag');
		add_theme_support('post-thumbnails');
		add_theme_support('editor-styles');
		add_editor_style('assets/css/editor.css');
		add_theme_support('wp-block-styles');
		add_theme_support('responsive-embeds');
		add_theme_support('align-wide');
		add_theme_support('custom-units');
		add_theme_support('custom-spacing');
		add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption','style','script']);
		add_theme_support('woocommerce');
		add_theme_support('woocommerce-block-theme');
	}

	private function register_block_patterns_category(): void
	{
		register_block_pattern_category('grindctrl', [
			'label' => __('GrindCTRL', 'grindctrl'),
		]);
	}
}
