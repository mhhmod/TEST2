<?php

declare(strict_types=1);

namespace GrindCTRL\Theme\Theme;

/**
 * Handles asset enqueue and optimizations.
 *
 * @since 3.0.0
 */
final class Assets
{
	private static ?Assets $instance = null;

	public static function get_instance(): Assets
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function hook(): void
	{
		add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets'], 20);
		add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor_assets']);
	}

	public function enqueue_frontend_assets(): void
	{
		$theme_dir = get_template_directory();
		$theme_uri = get_template_directory_uri();

		$frontend_css = '/assets/css/frontend.css';
		$frontend_js  = '/assets/js/theme.js';

		if (file_exists($theme_dir . $frontend_css)) {
			wp_enqueue_style('grindctrl-frontend', $theme_uri . $frontend_css, [], (string) filemtime($theme_dir . $frontend_css));
		}
		if (file_exists($theme_dir . $frontend_js)) {
			wp_enqueue_script('grindctrl-theme', $theme_uri . $frontend_js, [], (string) filemtime($theme_dir . $frontend_js), true);
		}

		// Defer non-critical scripts where possible
		add_filter('script_loader_tag', static function (string $tag, string $handle, string $src): string {
			$defer_handles = ['grindctrl-theme'];
			if (in_array($handle, $defer_handles, true)) {
				return sprintf('<script src="%s" id="%s-js" defer></script>', esc_url($src), esc_attr($handle));
			}
			return $tag;
		}, 10, 3);
	}

	public function enqueue_editor_assets(): void
	{
		$theme_dir = get_template_directory();
		$theme_uri = get_template_directory_uri();
		$editor_css = '/assets/css/editor.css';
		if (file_exists($theme_dir . $editor_css)) {
			wp_enqueue_style('grindctrl-editor', $theme_uri . $editor_css, [], (string) filemtime($theme_dir . $editor_css));
		}
	}
}
