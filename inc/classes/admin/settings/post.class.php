<?php
/**
 * @package The_SEO_Framework\Classes\Admin\Settings\Post
 * @subpackage The_SEO_Framework\Admin\Edit\Post
 */

namespace The_SEO_Framework\Admin\Settings;

\defined( 'THE_SEO_FRAMEWORK_PRESENT' ) or die;

use function \The_SEO_Framework\is_headless;

use \The_SEO_Framework\{
	Admin,
	Data,
};

use \The_SEO_Framework\Helper\Query;

/**
 * The SEO Framework plugin
 * Copyright (C) 2019 - 2023 Sybre Waaijer, CyberWire B.V. (https://cyberwire.nl/)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 3 as published
 * by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Prepares the Post Settings meta box interface.
 *
 * TODO carry over what we implemented in TSFEM, and make that the standard.
 *
 * @since 4.0.0
 * @since 4.3.0 1. Renamed from `PostSettings` to `Post`.
 *              2. Moved to `\The_SEO_Framework\Admin\Settings`.
 * @access private
 * @internal
 * @final Can't be extended.
 */
final class Post {

	/**
	 * Registers the meta box for the Post edit screens.
	 *
	 * @since 4.0.0
	 * @since 4.0.5 Now registers custom postbox classes.
	 * @since 4.2.8 No longer uses the post type label for the meta box title.
	 * @since 4.3.0 1. No longer uses the $post_type for the screen-parameter in add_meta_box.
	 *              2. No longer generates a dynamic title for the Homepage with settings-helper.
	 *                 This because Gutenberg is inconsistent with metabox display and escapes HTML incorrectly.
	 *              3. Now registers homepage warnings in the primary tabs.
	 *              4. Now adds postbox class to non-posts as well.
	 * @access private
	 */
	public static function _prepare_meta_box() {

		$box_id = 'tsf-inpost-box';

		\add_meta_box(
			$box_id,
			\esc_html__( 'SEO Settings', 'autodescription' ),
			[ static::class, '_meta_box' ],
			null, // We used to forward hook $post_type, which redundantly forces WP to regenerate the current screen type.
			/**
			 * @since 2.9.0
			 * @param string $context Accepts 'normal', 'side', and 'advanced'.
			 */
			(string) \apply_filters( 'the_seo_framework_metabox_context', 'normal' ),
			/**
			 * @since 2.6.0
			 * @param string $default Accepts 'high', 'default', 'low'
			 *                        Defaults to high, this box is seen right below the post/page edit screen.
			 */
			(string) \apply_filters( 'the_seo_framework_metabox_priority', 'high' )
		);

		$screen_id = \get_current_screen()->id;

		\add_filter( "postbox_classes_{$screen_id}_{$box_id}", [ static::class, '_add_postbox_class' ] );

		if ( ! is_headless( 'settings' ) && Query::is_static_frontpage( Query::get_the_real_id() ) ) {
			\add_action( 'the_seo_framework_pre_page_inpost_general_tab', [ static::class, '_homepage_warning' ] );
			\add_action( 'the_seo_framework_pre_page_inpost_visibility_tab', [ static::class, '_homepage_warning' ] );
			\add_action( 'the_seo_framework_pre_page_inpost_social_tab', [ static::class, '_homepage_warning' ] );
		}
	}

	/**
	 * Setting nav tab wrappers.
	 * Outputs Tabs and settings content.
	 *
	 * @since 4.0.0
	 * @since 4.3.0 Removed third parameter: $use_tabs.
	 * @access private
	 *
	 * @param string $id   The nav-tab ID.
	 * @param array  $tabs The tab content {
	 *    string tab ID => array : {
	 *       string   name     : Tab name.
	 *       callable callback : Output function.
	 *       string   dashicon : The dashicon to use.
	 *       mixed    args     : Optional callback function args.
	 *    }
	 * }
	 */
	public static function _flex_nav_tab_wrapper( $id, $tabs = [] ) {

		$vars = get_defined_vars();

		Admin\Template::output_view( 'edit/wrap-nav', $id, $tabs );
		Admin\Template::output_view( 'edit/wrap-content', $id, $tabs );
	}

	/**
	 * Outputs the meta box.
	 *
	 * @since 4.0.0
	 */
	public static function _meta_box() {

		\wp_nonce_field( Data\Admin\Post::$nonce_action, Data\Admin\Post::$nonce_name );

		/**
		 * @since 2.9.0
		 */
		\do_action( 'the_seo_framework_pre_page_inpost_box' );

		\tsf()->is_gutenberg_page()
			and Admin\Template::output_view( 'edit/seo-settings-singular-gutenberg-data' );

		Admin\Template::output_view( 'edit/seo-settings-singular', 'main' );

		/**
		 * @since 2.9.0
		 */
		\do_action( 'the_seo_framework_pro_page_inpost_box' );
	}

	/**
	 * Adds a Gutenberg/Block-editor box class.
	 *
	 * @since 4.0.5
	 * @access private
	 *
	 * @param array $classes The registered postbox classes.
	 * @return array
	 */
	public static function _add_postbox_class( $classes = [] ) {

		if ( \tsf()->is_gutenberg_page() )
			$classes[] = 'tsf-is-block-editor';

		return $classes;
	}

	/**
	 * Outputs the Homepage SEO settings warning.
	 *
	 * @since 4.3.0
	 */
	public static function _homepage_warning() {
		Admin\Template::output_view( 'edit/seo-settings-singular-homepage-warning' );
	}

	/**
	 * Outputs the Post SEO box general tab.
	 *
	 * @since 4.0.0
	 */
	public static function _general_tab() {
		/**
		 * @since 2.9.0
		 */
		\do_action( 'the_seo_framework_pre_page_inpost_general_tab' );
		Admin\Template::output_view( 'edit/seo-settings-singular', 'general' );
		/**
		 * @since 2.9.0
		 */
		\do_action( 'the_seo_framework_pro_page_inpost_general_tab' );
	}

	/**
	 * Outputs the Post SEO box visibility tab.
	 *
	 * @since 4.0.0
	 */
	public static function _visibility_tab() {
		/**
		 * @since 2.9.0
		 */
		\do_action( 'the_seo_framework_pre_page_inpost_visibility_tab' );
		Admin\Template::output_view( 'edit/seo-settings-singular', 'visibility' );
		/**
		 * @since 2.9.0
		 */
		\do_action( 'the_seo_framework_pro_page_inpost_visibility_tab' );
	}

	/**
	 * Outputs the Post SEO box social tab.
	 *
	 * @since 4.0.0
	 */
	public static function _social_tab() {
		/**
		 * @since 2.9.0
		 */
		\do_action( 'the_seo_framework_pre_page_inpost_social_tab' );
		Admin\Template::output_view( 'edit/seo-settings-singular', 'social' );
		/**
		 * @since 2.9.0
		 */
		\do_action( 'the_seo_framework_pro_page_inpost_social_tab' );
	}
}