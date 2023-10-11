<?php
/**
 * @package The_SEO_Framework
 * @subpackage The_SEO_Framework\Bootstrap
 */

namespace The_SEO_Framework;

\defined( 'THE_SEO_FRAMEWORK_PRESENT' ) or die;

use \The_SEO_Framework\Helper\{
	Headers,
	Query,
};

/**
 * The SEO Framework plugin
 * Copyright (C) 2023 Sybre Waaijer, CyberWire B.V. (https://cyberwire.nl/)
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

// Remove canonical header tag from WP
\remove_action( 'wp_head', 'rel_canonical' );

// Remove shortlink.
\remove_action( 'wp_head', 'wp_shortlink_wp_head' );

// Remove adjacent rel tags.
\remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );

// Earlier removal of the generator tag. Doesn't require filter.
\remove_action( 'wp_head', 'wp_generator' );

// Prepares sitemap or stylesheet output.
if ( Sitemap\Utils::may_output_optimized_sitemap() ) {
	\add_action( 'parse_request', [ Sitemap\Registry::class, '_init' ], 15 );
	\add_filter( 'wp_sitemaps_enabled', '__return_false' );
} else {
	// Augment Core sitemaps. Can't hook into `wp_sitemaps_init` as we're augmenting the providers before that.
	// It's not a bridge, don't treat it like one: clean me up?
	\add_filter( 'wp_sitemaps_add_provider', [ Sitemap\WP\Filter::class, '_filter_add_provider' ], 9, 2 );
	\add_filter( 'wp_sitemaps_max_urls', [ Sitemap\WP\Filter::class, '_filter_max_urls' ], 9 );
	// We miss the proper hooks. https://github.com/sybrew/the-seo-framework/issues/610#issuecomment-1300191500
	\add_filter( 'wp_sitemaps_posts_query_args', [ Sitemap\WP\Filter::class, '_trick_filter_doing_sitemap' ], 11 );
}

// Initialize 301 redirects.
\add_action( 'template_redirect', [ \tsf(), '_init_custom_field_redirect' ] );

// Prepares requisite robots headers to avoid low-quality content penalties.
\add_action( 'do_robots', [ Headers::class, 'output_robots_noindex_headers' ] );
\add_action( 'the_seo_framework_sitemap_header', [ Headers::class, 'output_robots_noindex_headers' ] );

// Output meta tags.
\add_action( 'wp_head', [ Front\Meta\Head::class, 'print_wrap_and_tags' ], 1 );

if ( Data\Plugin::get_option( 'alter_archive_query' ) ) {
	switch ( Data\Plugin::get_option( 'alter_archive_query_type' ) ) {
		case 'post_query':
			\add_filter( 'the_posts', [ Query\Exclusion::class, '_alter_archive_query_post' ], 10, 2 );
			break;

		case 'in_query':
		default:
			\add_action( 'pre_get_posts', [ Query\Exclusion::class, '_alter_archive_query_in' ], 9999, 1 );
	}
}

if ( Data\Plugin::get_option( 'alter_search_query' ) ) {
	switch ( Data\Plugin::get_option( 'alter_search_query_type' ) ) {
		case 'post_query':
			\add_filter( 'the_posts', [ Query\Exclusion::class, '_alter_search_query_post' ], 10, 2 );
			break;

		case 'in_query':
		default:
			\add_action( 'pre_get_posts', [ Query\Exclusion::class, '_alter_search_query_in' ], 9999, 1 );
	}
}

if ( ! Data\Plugin::get_option( 'index_the_feed' ) )
	\add_action( 'template_redirect', [ Front\Feed::class, 'output_robots_noindex_headers_on_feed' ] );

// Modify the feed.
if (
		Data\Plugin::get_option( 'excerpt_the_feed' )
	|| Data\Plugin::get_option( 'source_the_feed' )
) {
	// Alter the content feed.
	\add_filter( 'the_content_feed', [ Front\Feed::class, 'modify_the_content_feed' ], 10, 2 );

	// Only add the feed link to the excerpt if we're only building excerpts.
	if ( \get_option( 'rss_use_excerpt' ) )
		\add_filter( 'the_excerpt_rss', [ Front\Feed::class, 'modify_the_content_feed' ], 10, 1 );
}

/**
 * @since 2.9.3
 * @param bool $overwrite_titles Whether to enable title overwriting.
 */
if ( \apply_filters( 'the_seo_framework_overwrite_titles', true ) ) {
	// Removes all pre_get_document_title filters.
	\remove_all_filters( 'pre_get_document_title', false );

	// New WordPress 4.4.0 filter. Hurray! It's also much faster :)
	\add_filter( 'pre_get_document_title', [ \tsf(), 'get_document_title' ], 10 );

	/**
	 * @since 2.4.1
	 * @param bool $overwrite_titles Whether to enable legacy title overwriting.
	 *
	 * TODO remove this block? -- it's been 7 years...
	 * <https://make.wordpress.org/core/2015/10/20/document-title-in-4-4/>
	 */
	if ( \apply_filters( 'the_seo_framework_manipulate_title', true ) ) {
		\remove_all_filters( 'wp_title', false );
		// Override WordPress Title
		\add_filter( 'wp_title', [ \tsf(), 'get_wp_title' ], 9 );
		// Override WooThemes Title TODO move this to wc compat file.
		\add_filter( 'woo_title', [ \tsf(), 'get_document_title' ], 99 );
	}
}

/**
 * @since 4.1.4
 * @param bool $kill_core_robots Whether you lack sympathy for rocks tricked to think.
 */
if ( \apply_filters( 'the_seo_framework_kill_core_robots', true ) ) {
	\remove_filter( 'wp_robots', 'wp_robots_max_image_preview_large' );
	// Reconsider readding this to "supported" queries only?
	\remove_filter( 'wp_robots', 'wp_robots_noindex_search' );
}

if ( Data\Plugin::get_option( 'og_tags' ) ) { // independent from filter at use_og_tags--let that be deciding later.
	// Disable Jetpack's Open Graph tags. But Sybre, compat files? Yes.
	\add_filter( 'jetpack_enable_open_graph', '__return_false' );
}

if ( Data\Plugin::get_option( 'twitter_tags' ) ) { // independent from filter at use_twitter_tags--let that be deciding later.
	// Disable Jetpack's Twitter Card tags. But Sybre, compat files? Maybe.
	\add_filter( 'jetpack_disable_twitter_cards', '__return_true' );
	// Future, maybe. See <https://github.com/Automattic/jetpack/issues/13146#issuecomment-516841698>
	// \add_filter( 'jetpack_enable_twitter_cards', '__return_false' );
}

if ( ! Data\Plugin::get_option( 'oembed_scripts' ) ) {
	/**
	 * Only hide the scripts, don't permeably purge them. This should be enough.
	 *
	 * This will still allow embedding within WordPress Multisite via WP-REST's proxy, since WP won't look for a script.
	 * We'd need to empty 'oembed_response_data' in that case... However, thanks to a bug in WP, this 'works' anyway.
	 * The bug: WP_oEmbed_Controller::get_proxy_item_permissions_check() always returns \WP_Error.
	 */
	\remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
}
/**
 * WordPress also filters this at priority '10', but it's registered before this runs.
 * Careful, WordPress can switch blogs when this filter runs. So, run this always,
 * and assess options (uncached!) therein.
 */
\add_filter( 'oembed_response_data', [ \tsf(), '_alter_oembed_response_data' ], 10, 2 );