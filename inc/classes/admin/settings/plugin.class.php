<?php
/**
 * @package The_SEO_Framework\Classes\Admin\Settings\Plugin
 * @subpackage The_SEO_Framework\Admin\Settings
 */

namespace The_SEO_Framework\Admin\Settings;

\defined( 'THE_SEO_FRAMEWORK_PRESENT' ) or die;

use \The_SEO_Framework\Helper\Post_Types;
use \The_SEO_Framework\Admin;

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
 * Prepares the SEO Settings page interface.
 *
 * Note the use of "metabox" instead of "meta_box" throughout.
 *
 * @since 4.0.0
 * @since 4.3.0 1. Renamed from `SeoSettings` to `Plugin`.
 *              2. Moved to `\The_SEO_Framework\Admin\Settings`.
 * @access private
 */
final class Plugin {

	/**
	 * Registers meta boxes on the Site SEO Settings page.
	 *
	 * @since 4.0.0
	 * @access private
	 */
	public static function _register_seo_settings_meta_boxes() {

		/**
		 * Various meta box filters.
		 * Set any to false if you wish the meta box to be removed.
		 *
		 * @since 2.2.4
		 * @since 2.8.0 Added `the_seo_framework_general_metabox` filter.
		 * @since 4.2.0 Added `the_seo_framework_post_type_archive_metabox` filter.
		 */
		$general           = (bool) \apply_filters( 'the_seo_framework_general_metabox', true );
		$title             = (bool) \apply_filters( 'the_seo_framework_title_metabox', true );
		$description       = (bool) \apply_filters( 'the_seo_framework_description_metabox', true );
		$robots            = (bool) \apply_filters( 'the_seo_framework_robots_metabox', true );
		$home              = (bool) \apply_filters( 'the_seo_framework_home_metabox', true );
		$post_type_archive = (bool) \apply_filters( 'the_seo_framework_post_type_archive_metabox', true );
		$social            = (bool) \apply_filters( 'the_seo_framework_social_metabox', true );
		$schema            = (bool) \apply_filters( 'the_seo_framework_schema_metabox', true );
		$webmaster         = (bool) \apply_filters( 'the_seo_framework_webmaster_metabox', true );
		$sitemap           = (bool) \apply_filters( 'the_seo_framework_sitemap_metabox', true );
		$feed              = (bool) \apply_filters( 'the_seo_framework_feed_metabox', true );

		$settings_page_hook = \tsf()->seo_settings_page_hook;

		$class = static::class;

		// General Meta Box
		if ( $general )
			\add_meta_box(
				'autodescription-general-settings',
				\esc_html__( 'General Settings', 'autodescription' ),
				"$class::_general_metabox",
				$settings_page_hook,
				'main'
			);

		// Title Meta Box
		if ( $title )
			\add_meta_box(
				'autodescription-title-settings',
				\esc_html__( 'Title Settings', 'autodescription' ),
				"$class::_title_metabox",
				$settings_page_hook,
				'main'
			);

		// Description Meta Box
		if ( $description )
			\add_meta_box(
				'autodescription-description-settings',
				\esc_html__( 'Description Meta Settings', 'autodescription' ),
				"$class::_description_metabox",
				$settings_page_hook,
				'main'
			);

		// Homepage Meta Box
		if ( $home )
			\add_meta_box(
				'autodescription-homepage-settings',
				\esc_html__( 'Homepage Settings', 'autodescription' ),
				"$class::_homepage_metabox",
				$settings_page_hook,
				'main'
			);

		if ( $post_type_archive && Post_Types::get_public_post_type_archives() )
			\add_meta_box(
				'autodescription-post-type-archive-settings',
				\esc_html__( 'Post Type Archive Settings', 'autodescription' ),
				"$class::_post_type_archive_metabox",
				$settings_page_hook,
				'main'
			);

		// Social Meta Box
		if ( $social )
			\add_meta_box(
				'autodescription-social-settings',
				\esc_html__( 'Social Meta Settings', 'autodescription' ),
				"$class::_social_metabox",
				$settings_page_hook,
				'main'
			);

		// Schema Meta Box
		if ( $schema )
			\add_meta_box(
				'autodescription-schema-settings',
				\esc_html__( 'Schema.org Settings', 'autodescription' ),
				"$class::_schema_metabox",
				$settings_page_hook,
				'main'
			);

		// Robots Meta Box
		if ( $robots )
			\add_meta_box(
				'autodescription-robots-settings',
				\esc_html__( 'Robots Meta Settings', 'autodescription' ),
				"$class::_robots_metabox",
				$settings_page_hook,
				'main'
			);

		// Webmaster Meta Box
		if ( $webmaster )
			\add_meta_box(
				'autodescription-webmaster-settings',
				\esc_html__( 'Webmaster Meta Settings', 'autodescription' ),
				"$class::_webmaster_metabox",
				$settings_page_hook,
				'main'
			);

		// Sitemaps Meta Box
		if ( $sitemap )
			\add_meta_box(
				'autodescription-sitemap-settings',
				\esc_html__( 'Sitemap Settings', 'autodescription' ),
				"$class::_sitemaps_metabox",
				$settings_page_hook,
				'main'
			);

		// Feed Meta Box
		if ( $feed )
			\add_meta_box(
				'autodescription-feed-settings',
				\esc_html__( 'Feed Settings', 'autodescription' ),
				"$class::_feed_metabox",
				$settings_page_hook,
				'main'
			);
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
	 *       mixed    args     : Optional callback function args. These arguments
	 *                           will be extracted to variables in scope of the view.
	 *    }
	 * }
	 */
	public static function _nav_tab_wrapper( $id, $tabs = [] ) {
		Admin\Template::output_view( 'settings/wrap-nav', $id, $tabs );
		Admin\Template::output_view( 'settings/wrap-content', $id, $tabs );
	}

	/**
	 * Outputs SEO Settings page wrap.
	 *
	 * @since 4.0.0
	 * @access private
	 */
	public static function _output_wrap() {
		/**
		 * @since 3.0.0
		 */
		\do_action( 'the_seo_framework_pre_seo_settings' );
		Admin\Template::output_view( 'settings/wrap' );
		/**
		 * @since 3.0.0
		 */
		\do_action( 'the_seo_framework_pro_seo_settings' );
	}

	/**
	 * Outputs SEO Settings columns.
	 *
	 * @since 4.0.0
	 * @access private
	 */
	public static function _output_columns() {
		Admin\Template::output_view( 'settings/columns' );
	}

	/**
	 * Outputs General Settings meta box on the Site SEO Settings page.
	 *
	 * @since 4.0.0
	 * @access private
	 */
	public static function _general_metabox() {
		/**
		 * @since 2.8.0
		 */
		\do_action( 'the_seo_framework_general_metabox_before' );
		Admin\Template::output_view( 'settings/metaboxes/general', 'main' );
		/**
		 * @since 2.8.0
		 */
		\do_action( 'the_seo_framework_general_metabox_after' );
	}

	/**
	 * Outputs General Settings meta box general tab.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::general_metabox() : Callback for General Settings box.
	 */
	public static function _general_metabox_general_tab() {
		Admin\Template::output_view( 'settings/metaboxes/general', 'general' );
	}

	/**
	 * Outputs General Settings meta box layout tab.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::general_metabox() : Callback for General Settings box.
	 */
	public static function _general_metabox_layout_tab() {
		Admin\Template::output_view( 'settings/metaboxes/general', 'layout' );
	}

	/**
	 * Outputs General Settings meta box performance tab.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::general_metabox() : Callback for General Settings box.
	 */
	public static function _general_metabox_performance_tab() {
		Admin\Template::output_view( 'settings/metaboxes/general', 'performance' );
	}

	/**
	 * Outputs General Settings meta box canonical tab.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::general_metabox() : Callback for General Settings box.
	 */
	public static function _general_metabox_canonical_tab() {
		Admin\Template::output_view( 'settings/metaboxes/general', 'canonical' );
	}

	/**
	 * Outputs General Settings meta box timestamps tab.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::general_metabox() : Callback for General Settings box.
	 */
	public static function _general_metabox_timestamps_tab() {
		Admin\Template::output_view( 'settings/metaboxes/general', 'timestamps' );
	}

	/**
	 * Outputs General Settings meta box exclusions tab.
	 *
	 * @since 4.1.0
	 * @access private
	 * @see static::general_metabox() : Callback for General Settings box.
	 */
	public static function _general_metabox_exclusions_tab() {
		Admin\Template::output_view( 'settings/metaboxes/general', 'exclusions' );
	}

	/**
	 * Title meta box on the Site SEO Settings page.
	 *
	 * @since 4.0.0
	 * @access private
	 */
	public static function _title_metabox() {
		/**
		 * @since 2.5.0 or earlier.
		 */
		\do_action( 'the_seo_framework_title_metabox_before' );
		Admin\Template::output_view( 'settings/metaboxes/title', 'main' );
		/**
		 * @since 2.5.0 or earlier.
		 */
		\do_action( 'the_seo_framework_title_metabox_after' );
	}

	/**
	 * Title meta box general tab.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::title_metabox() : Callback for Title Settings box.
	 */
	public static function _title_metabox_general_tab() {
		Admin\Template::output_view( 'settings/metaboxes/title', 'general' );
	}

	/**
	 * Title meta box general tab.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::title_metabox() : Callback for Title Settings box.
	 *
	 * @param array $args The variables to pass to the meta box tab.
	 */
	public static function _title_metabox_additions_tab( $args ) {
		Admin\Template::output_view( 'settings/metaboxes/title', 'additions', $args );
	}

	/**
	 * Title meta box prefixes tab.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::title_metabox() : Callback for Title Settings box.
	 */
	public static function _title_metabox_prefixes_tab() {
		Admin\Template::output_view( 'settings/metaboxes/title', 'prefixes' );
	}

	/**
	 * Description meta box on the Site SEO Settings page.
	 *
	 * @since 4.0.0
	 * @access private
	 */
	public static function _description_metabox() {
		/**
		 * @since 2.5.0 or earlier.
		 */
		\do_action( 'the_seo_framework_description_metabox_before' );
		Admin\Template::output_view( 'settings/metaboxes/description', 'main' );
		/**
		 * @since 2.5.0 or earlier.
		 */
		\do_action( 'the_seo_framework_description_metabox_after' );
	}

	/**
	 * Robots meta box on the Site SEO Settings page.
	 *
	 * @since 4.0.0
	 * @access private
	 */
	public static function _robots_metabox() {
		/**
		 * @since 2.5.0 or earlier.
		 */
		\do_action( 'the_seo_framework_robots_metabox_before' );
		Admin\Template::output_view( 'settings/metaboxes/robots', 'main' );
		/**
		 * @since 2.5.0 or earlier.
		 */
		\do_action( 'the_seo_framework_robots_metabox_after' );
	}

	/**
	 * Robots Meta Box General Tab output.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::robots_metabox() Callback for Robots Settings box.
	 */
	public static function _robots_metabox_general_tab() {
		Admin\Template::output_view( 'settings/metaboxes/robots', 'general' );
	}

	/**
	 * Robots Meta Box "No-: Index/Follow/Archive" Tab output.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::robots_metabox() Callback for Robots Settings box.
	 *
	 * @param array $args The variables to pass to the meta box tab.
	 */
	public static function _robots_metabox_no_tab( $args ) {
		Admin\Template::output_view( 'settings/metaboxes/robots', 'no', $args );
	}

	/**
	 * Outputs the Homepage meta box on the Site SEO Settings page.
	 *
	 * @since 4.0.0
	 * @access private
	 */
	public static function _homepage_metabox() {
		/**
		 * @since 2.5.0 or earlier.
		 */
		\do_action( 'the_seo_framework_homepage_metabox_before' );
		Admin\Template::output_view( 'settings/metaboxes/homepage', 'main' );
		/**
		 * @since 2.5.0 or earlier.
		 */
		\do_action( 'the_seo_framework_homepage_metabox_after' );
	}

	/**
	 * Homepage meta box General Tab Output.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::homepage_metabox() Callback for Homepage Settings box.
	 */
	public static function _homepage_metabox_general_tab() {
		Admin\Template::output_view( 'settings/metaboxes/homepage', 'general' );
	}

	/**
	 * Homepage meta box Additions Tab Output.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::homepage_metabox() Callback for Homepage Settings box.
	 */
	public static function _homepage_metabox_additions_tab() {
		Admin\Template::output_view( 'settings/metaboxes/homepage', 'additions' );
	}

	/**
	 * Homepage meta box Robots Tab Output
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::homepage_metabox() Callback for Homepage Settings box.
	 */
	public static function _homepage_metabox_robots_tab() {
		Admin\Template::output_view( 'settings/metaboxes/homepage', 'robots' );
	}

	/**
	 * Homepage meta box Social Tab Output
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::homepage_metabox() Callback for Homepage Settings box.
	 */
	public static function _homepage_metabox_social_tab() {
		Admin\Template::output_view( 'settings/metaboxes/homepage', 'social' );
	}

	/**
	 * Post Type Archive meta box on the Site SEO Settings page.
	 *
	 * @since 4.2.0
	 * @access private
	 */
	public static function _post_type_archive_metabox() {
		/**
		 * @since 4.2.0
		 */
		\do_action( 'the_seo_framework_post_type_archive_before' );
		Admin\Template::output_view( 'settings/metaboxes/post-type-archive', 'main' );
		/**
		 * @since 4.2.0
		 */
		\do_action( 'the_seo_framework_post_type_archive_after' );
	}

	/**
	 * Social Meta Box General Tab output.
	 *
	 * @since 4.2.0
	 * @access private
	 *
	 * @param array $args The variables to pass to the meta box tab.
	 */
	public static function _post_type_archive_metabox_general_tab( $args ) {
		Admin\Template::output_view( 'settings/metaboxes/post-type-archive', 'general', $args );
	}

	/**
	 * Post Type Archive meta box on the Site SEO Settings page.
	 *
	 * @since 4.2.0
	 * @access private
	 *
	 * @param array $args The variables to pass to the meta box tab.
	 */
	public static function _post_type_archive_metabox_social_tab( $args ) {
		Admin\Template::output_view( 'settings/metaboxes/post-type-archive', 'social', $args );
	}

	/**
	 * Post Type Archive meta box on the Site SEO Settings page.
	 *
	 * @since 4.2.0
	 * @access private
	 *
	 * @param array $args The variables to pass to the meta box tab.
	 */
	public static function _post_type_archive_metabox_visibility_tab( $args ) {
		Admin\Template::output_view( 'settings/metaboxes/post-type-archive', 'visibility', $args );
	}

	/**
	 * Social meta box on the Site SEO Settings page.
	 *
	 * @since 4.0.0
	 * @access private
	 */
	public static function _social_metabox() {
		/**
		 * @since 2.5.0 or earlier.
		 */
		\do_action( 'the_seo_framework_social_metabox_before' );
		Admin\Template::output_view( 'settings/metaboxes/social', 'main' );
		/**
		 * @since 2.5.0 or earlier.
		 */
		\do_action( 'the_seo_framework_social_metabox_after' );
	}

	/**
	 * Social Meta Box General Tab output.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::social_metabox() Callback for Social Settings box.
	 */
	public static function _social_metabox_general_tab() {
		Admin\Template::output_view( 'settings/metaboxes/social', 'general' );
	}

	/**
	 * Social Meta Box Facebook Tab output.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::social_metabox() Callback for Social Settings box.
	 */
	public static function _social_metabox_facebook_tab() {
		Admin\Template::output_view( 'settings/metaboxes/social', 'facebook' );
	}

	/**
	 * Social Meta Box Twitter Tab output.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::social_metabox() Callback for Social Settings box.
	 */
	public static function _social_metabox_twitter_tab() {
		Admin\Template::output_view( 'settings/metaboxes/social', 'twitter' );
	}

	/**
	 * Social Meta Box oEmbed Tab output.
	 *
	 * @since 4.0.5
	 * @access private
	 * @see static::social_metabox() Callback for Social Settings box.
	 */
	public static function _social_metabox_oembed_tab() {
		Admin\Template::output_view( 'settings/metaboxes/social', 'oembed' );
	}

	/**
	 * Social Meta Box PostDates Tab output.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::social_metabox() Callback for Social Settings box.
	 */
	public static function _social_metabox_postdates_tab() {
		Admin\Template::output_view( 'settings/metaboxes/social', 'postdates' );
	}

	/**
	 * Webmaster meta box on the Site SEO Settings page.
	 *
	 * @since 4.0.0
	 * @access private
	 */
	public static function _webmaster_metabox() {
		/**
		 * @since 2.5.0 or earlier.
		 */
		\do_action( 'the_seo_framework_webmaster_metabox_before' );
		Admin\Template::output_view( 'settings/metaboxes/webmaster', 'main' );
		/**
		 * @since 2.5.0 or earlier.
		 */
		\do_action( 'the_seo_framework_webmaster_metabox_after' );
	}

	/**
	 * Sitemaps meta box on the Site SEO Settings page.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::sitemaps_metabox() Callback for Sitemaps Settings box.
	 */
	public static function _sitemaps_metabox() {
		/**
		 * @since 2.5.0 or earlier.
		 */
		\do_action( 'the_seo_framework_sitemaps_metabox_before' );
		Admin\Template::output_view( 'settings/metaboxes/sitemaps', 'main' );
		/**
		 * @since 2.5.0 or earlier.
		 */
		\do_action( 'the_seo_framework_sitemaps_metabox_after' );
	}

	/**
	 * Sitemaps Meta Box General Tab output.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::sitemaps_metabox() Callback for Sitemaps Settings box.
	 */
	public static function _sitemaps_metabox_general_tab() {
		Admin\Template::output_view( 'settings/metaboxes/sitemaps', 'general' );
	}

	/**
	 * Sitemaps Meta Box Robots Tab output.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::sitemaps_metabox() Callback for Sitemaps Settings box.
	 */
	public static function _sitemaps_metabox_robots_tab() {
		Admin\Template::output_view( 'settings/metaboxes/sitemaps', 'robots' );
	}

	/**
	 * Sitemaps Meta Box Metadata Tab output.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::sitemaps_metabox() Callback for Sitemaps Settings box.
	 */
	public static function _sitemaps_metabox_metadata_tab() {
		Admin\Template::output_view( 'settings/metaboxes/sitemaps', 'metadata' );
	}

	/**
	 * Sitemaps Meta Box Notify Tab output.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::sitemaps_metabox() Callback for Sitemaps Settings box.
	 */
	public static function _sitemaps_metabox_notify_tab() {
		Admin\Template::output_view( 'settings/metaboxes/sitemaps', 'notify' );
	}

	/**
	 * Sitemaps Meta Box Style Tab output.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::sitemaps_metabox() Callback for Sitemaps Settings box.
	 */
	public static function _sitemaps_metabox_style_tab() {
		Admin\Template::output_view( 'settings/metaboxes/sitemaps', 'style' );
	}

	/**
	 * Feed meta box on the Site SEO Settings page.
	 *
	 * @since 4.0.0
	 * @access private
	 */
	public static function _feed_metabox() {
		/**
		 * @since 2.5.2
		 */
		\do_action( 'the_seo_framework_feed_metabox_before' );
		Admin\Template::output_view( 'settings/metaboxes/feed', 'main' );
		/**
		 * @since 2.5.2
		 */
		\do_action( 'the_seo_framework_feed_metabox_after' );
	}

	/**
	 * Schema meta box on the Site SEO Settings page.
	 *
	 * @since 4.0.0
	 * @access private
	 */
	public static function _schema_metabox() {
		/**
		 * @since 2.6.0
		 */
		\do_action( 'the_seo_framework_schema_metabox_before' );
		Admin\Template::output_view( 'settings/metaboxes/schema', 'main' );
		/**
		 * @since 2.6.0
		 */
		\do_action( 'the_seo_framework_schema_metabox_after' );
	}

	/**
	 * Schema Meta Box Structure Tab output.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::schema_metabox() Callback for Schema.org Settings box.
	 */
	public static function _schema_metabox_structure_tab() {
		Admin\Template::output_view( 'settings/metaboxes/schema', 'structure' );
	}

	/**
	 * Schema Meta Box Presence Tab output.
	 *
	 * @since 4.0.0
	 * @access private
	 * @see static::schema_metabox() Callback for Schema.org Settings box.
	 */
	public static function _schema_metabox_presence_tab() {
		Admin\Template::output_view( 'settings/metaboxes/schema', 'presence' );
	}
}
