<?php
/**
 * @package The_SEO_Framework\Classes\Meta
 * @subpackage The_SEO_Framework\Meta\URI
 */

namespace The_SEO_Framework\Meta;

\defined( 'THE_SEO_FRAMEWORK_PRESENT' ) or die;

use function \The_SEO_Framework\{
	umemo,
	Utils\normalize_generation_args,
};

use \The_SEO_Framework\Data,
	\The_SEO_Framework\Helper\Query;

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

/**
 * Holds getters for meta tag output.
 *
 * @since 4.3.0
 * @access protected
 * @internal Use tsf()->uri() instead.
 */
class URI {

	/**
	 * Gets the canonical URL for indexation.
	 *
	 * @since 4.3.0
	 *
	 * @return string $url If indexable, a canonical URL will appear.
	 */
	public static function get_indexable_canonical_url() {

		$custom_url = static::get_custom_canonical_url();

		if ( $custom_url )
			return $custom_url;

		if ( \has_filter( 'the_seo_framework_rel_canonical_output' ) ) {
			/**
			 * @since 2.6.5
			 * @since 4.3.0 Deprecated.
			 * @deprecated
			 * @param string $url The canonical URL. Must be escaped.
			 * @param int    $id  The current page or term ID.
			 */
			$url = (string) \apply_filters_deprecated(
				'the_seo_framework_rel_canonical_output',
				[
					'',
					\The_SEO_Framework\Helper\Query::get_the_real_id(), // Lacking import OK.
				],
				'4.3.0 of The SEO Framework',
				'the_seo_framework_meta_render_data',
			);

			if ( $url ) return $url;
		}

		if ( str_contains( Robots::get_meta(), 'noindex' ) )
			return '';

		return static::get_generated_url();
	}

	/**
	 * Returns the current canonical URL.
	 * Removes pagination if the URL isn't obtained via the query.
	 *
	 * @since 3.0.0
	 * @since 4.2.0 Now supports the `$args['pta']` index.
	 * @since 4.2.3 Now accepts arguments publicly.
	 * @since 4.3.0 1. No longer calls the query in the sitemap to remove pagination.
	 *              2. Moved to \The_SEO_Framework\Meta\URI.
	 *              3. Now always returns a sanitized URL.
	 *
	 * @param array|null $args The canonical URL arguments, leave null to autodetermine query : {
	 *    int    $id       The Post, Page or Term ID to generate the URL for.
	 *    string $taxonomy The Taxonomy.
	 *    string $pta      The Post Type Archive.
	 * }
	 * @return string The canonical URL, if any.
	 */
	public static function get_canonical_url( $args = null ) {
		return static::get_custom_canonical_url( $args )
			?: static::get_generated_url( $args );
	}

	/**
	 * Returns the custom canonical URL.
	 *
	 * @since 4.3.0
	 *
	 * @param array|null $args The query arguments. Accepts 'id', 'tax', and 'pta'.
	 *                         Leave null to autodetermine query.
	 * @return string The custom canonical URL, if any.
	 */
	public static function get_custom_canonical_url( $args = null ) {
		return isset( $args )
			? static::get_custom_canonical_url_from_args( $args )
			: static::get_custom_canonical_url_from_query();
	}

	/**
	 * Returns the generated canonical URL.
	 * Memoizes if $args is null.
	 *
	 * @since 4.3.0
	 *
	 * @param array|null $args The query arguments. Accepts 'id', 'tax', and 'pta'.
	 *                         Leave null to autodetermine and memoize query.
	 * @return string The custom canonical URL, if any.
	 */
	public static function get_generated_url( $args = null ) {
		return isset( $args )
			? static::get_generated_url_from_args( $args )
			: static::get_generated_url_from_query();
	}

	/**
	 * Returns the custom canonical URL, based on query.
	 *
	 * @since 4.3.0
	 *
	 * @return string The custom canonical URL, if any.
	 */
	public static function get_custom_canonical_url_from_query() {

		if ( Query::is_singular() ) {
			$url = Data\Plugin\Post::get_post_meta_item( '_genesis_canonical_uri' );
		} elseif ( Query::is_editable_term() ) {
			$url = Data\Plugin\Term::get_term_meta_item( 'canonical' ) ?: '';
		} elseif ( \is_post_type_archive() ) {
			$url = Data\Plugin\PTA::get_post_type_archive_meta_item( 'canonical' );
		}

		if ( empty( $url ) ) return '';

		if ( Meta\URI\Utils::url_matches_blog_domain( $url ) )
			$url = Meta\URI\Utils::set_preferred_url_scheme( $url );

		return \sanitize_url( $url, [ 'https', 'http' ] );
	}

	/**
	 * Returns the custom canonical URL, based on arguments.
	 *
	 * @since 4.3.0
	 *
	 * @param null|array $args The canonical URL arguments, leave null to autodetermine query : {
	 *    int    $id       The Post, Page or Term ID to generate the URL for.
	 *    string $taxonomy The taxonomy.
	 * }
	 * @return string The custom canonical URL, if any.
	 */
	public static function get_custom_canonical_url_from_args( $args ) {

		normalize_generation_args( $args );

		if ( $args['tax'] ) {
			$url = Data\Plugin\Term::get_term_meta_item( 'canonical', $args['id'] );
		} elseif ( $args['pta'] ) {
			$url = Data\Plugin\PTA::get_post_type_archive_meta_item( 'canonical', $args['pta'] );
		} elseif ( $args['id'] ) {
			$url = Data\Plugin\Post::get_post_meta_item( '_genesis_canonical_uri', $args['id'] );
		}

		if ( empty( $url ) ) return '';

		if ( Meta\URI\Utils::url_matches_blog_domain( $url ) )
			$url = Meta\URI\Utils::set_preferred_url_scheme( $url );

		return \sanitize_url( $url, [ 'https', 'http' ] );
	}

	/**
	 * Gets generated permalink, based on query.
	 *
	 * @since 4.3.0
	 *
	 * @return string The generated canonical URL.
	 */
	public static function get_generated_url_from_query() {

		if ( Query::is_real_front_page() ) {
			$url = static::get_front_page_url();
		} elseif ( Query::is_singular() ) {
			$url = static::get_singular_url();
		} elseif ( Query::is_archive() ) {
			if ( Query::is_editable_term() ) {
				$url = static::get_term_url();
			} elseif ( \is_post_type_archive() ) {
				$url = static::get_post_type_archive_url();
			} elseif ( Query::is_author() ) {
				$url = static::get_author_url();
			} elseif ( \is_date() ) {
				$url = static::get_date_url();
			}
		} elseif ( Query::is_search() ) {
			$url = static::get_search_url();
		}

		return $url ?? '' ?: '';
	}

	/**
	 * Gets generated permalink, based on args.
	 *
	 * @since 4.3.0
	 *
	 * @param array|null $args The query arguments. Accepts 'id', 'tax', and 'pta'.
	 *                         Leave null to autodetermine query.
	 * @return string The generated URL.
	 */
	public static function get_generated_url_from_args( $args ) {

		normalize_generation_args( $args );

		if ( $args['tax'] ) {
			$url = static::get_bare_term_url( $args['id'], $args['tax'] );
		} elseif ( $args['pta'] ) {
			$url = static::get_bare_post_type_archive_url( $args['pta'] );
		} elseif ( Query::is_real_front_page_by_id( $args['id'] ) ) {
			$url = static::get_bare_front_page_url();
		} elseif ( $args['id'] ) {
			$url = static::get_bare_singular_url( $args['id'] );
		}

		return $url ?? '' ?: '';
	}

	/**
	 * Returns home URL.
	 * Automatically adds pagination if the ID matches the query.
	 *
	 * @since 4.3.0
	 *
	 * @return string The home URL.
	 */
	public static function get_front_page_url() {

		$url = URI\Utils::slash_front_page_url( Data\Blog::get_front_page_url() );

		if ( empty( $url ) ) return '';

		$page = max( Query::paged(), Query::page() );

		if ( $page > 1 )
			$url = URI\Utils::add_pagination_to_url( $url, $page, true );

		return \sanitize_url(
			URI\Utils::set_preferred_url_scheme( $url ),
			[ 'https', 'http' ]
		);
	}

	/**
	 * Returns home URL without query adjustments.
	 *
	 * @since 4.3.0
	 *
	 * @return string The home URL.
	 */
	public static function get_bare_front_page_url() {
		return umemo( __METHOD__ ) ?? umemo(
			__METHOD__,
			\sanitize_url(
				URI\Utils::slash_front_page_url(
					URI\Utils::set_preferred_url_scheme( Data\Blog::get_front_page_url() )
				),
				[ 'https', 'http' ]
			)
		);
	}

	/**
	 * Returns singular URL.
	 *
	 * @since 4.3.0
	 *
	 * @param int|null $post_id The page ID. Leave null to autodetermine.
	 * @return string The singular URL.
	 */
	public static function get_singular_url( $post_id = null ) {

		if ( isset( $post_id ) )
			return static::get_bare_singular_url( $post_id );

		$url = \get_permalink( Query::get_the_real_id() );

		if ( empty( $url ) ) return '';

		if ( Query::is_singular_archive() ) {
			// Singular archives, like blog pages and shop pages, use the pagination base with 'paged'.
			$paged = Query::paged();

			if ( $paged > 1 )
				$url = URI\Utils::add_pagination_to_url( $url, $paged, true );
		} else {
			$page = Query::page();

			if ( $page > 1 )
				$url = URI\Utils::add_pagination_to_url( $url, $page, false );
		}

		return \sanitize_url(
			URI\Utils::set_preferred_url_scheme( $url ),
			[ 'https', 'http' ]
		);
	}

	/**
	 * Returns singular canonical URL without query adjustments.
	 *
	 * @since 4.3.0
	 *
	 * @param int $post_id The post ID to get the URL from.
	 * @return string The singular canonical URL without complex optimizations.
	 */
	public static function get_bare_singular_url( $post_id ) {

		$url = \get_permalink( $post_id );

		if ( empty( $url ) ) return '';

		return \sanitize_url(
			URI\Utils::set_preferred_url_scheme( $url ),
			[ 'https', 'http' ]
		);
	}

	/**
	 * Returns taxonomical canonical URL.
	 * Automatically adds pagination if the ID matches the query.
	 *
	 * @since 4.3.0
	 *
	 * @param int|null $term_id  The term ID. Leave null to autodetermine.
	 * @param string   $taxonomy The taxonomy. Leave empty to autodetermine.
	 * @return string The taxonomical canonical URL, if any.
	 */
	public static function get_term_url( $term_id = null, $taxonomy = '' ) {

		if ( isset( $term_id ) )
			return static::get_bare_term_url( $term_id, $taxonomy );

		$url = \get_term_link( Query::get_the_real_id(), $taxonomy );

		if ( empty( $url ) || ! \is_string( $url ) )
			return '';

		return \sanitize_url(
			URI\Utils::set_preferred_url_scheme(
				URI\Utils::add_pagination_to_url( $url, Query::paged(), true )
			),
			[ 'https', 'http' ]
		);
	}

	/**
	 * Returns taxonomical canonical URL without query adjustments.
	 *
	 * @since 4.3.0
	 *
	 * @param int|null $term_id  The term ID.
	 * @param string   $taxonomy The taxonomy. Leave empty to autodetermine.
	 * @return string The taxonomical canonical URL, if any.
	 */
	public static function get_bare_term_url( $term_id = null, $taxonomy = '' ) {

		if ( empty( $term_id ) ) {
			$term_id  = Query::get_the_real_id();
			$taxonomy = Query::get_current_taxonomy();
		}

		$url = \get_term_link( $term_id, $taxonomy );

		if ( empty( $url ) || ! \is_string( $url ) )
			return '';

		return \sanitize_url(
			URI\Utils::set_preferred_url_scheme( $url ),
			[ 'https', 'http' ]
		);
	}

	/**
	 * Returns post type archive canonical URL.
	 *
	 * @since 4.3.0
	 *
	 * @param null|string $post_type The post type archive's post type.
	 *                               Leave null to autodetermine query and allow pagination.
	 * @return string The post type archive canonical URL, if any.
	 */
	public static function get_post_type_archive_url( $post_type = null ) {

		if ( isset( $post_type ) )
			return static::get_bare_post_type_archive_url( $post_type );

		$url = \get_post_type_archive_link( $post_type ?? Query::get_current_post_type() );

		if ( empty( $url ) ) return '';

		return \sanitize_url(
			URI\Utils::set_preferred_url_scheme(
				URI\Utils::add_pagination_to_url( $url, Query::paged(), true )
			),
			[ 'https', 'http' ]
		);
	}

	/**
	 * Returns post type archive canonical URL without query adjustments.
	 *
	 * @since 4.3.0
	 *
	 * @param null|string $post_type The post type archive's post type.
	 *                          Leave null to autodetermine query and allow pagination.
	 * @return string The post type archive canonical URL, if any.
	 */
	public static function get_bare_post_type_archive_url( $post_type = null ) {

		$url = \get_post_type_archive_link( $post_type ?? Query::get_current_post_type() );

		if ( empty( $url ) ) return '';

		return \sanitize_url(
			URI\Utils::set_preferred_url_scheme( $url ),
			[ 'https', 'http' ]
		);
	}

	/**
	 * Returns author canonical URL.
	 *
	 * @since 3.0.0
	 * @since 4.2.0 1. The first parameter is now optional.
	 *              2. When the $id isn't set, the URL won't get tested for pagination issues.
	 * @since 4.3.0 Moved to \The_SEO_Framework\Meta\URI.
	 *
	 * @param int|null $id The author ID. Leave null to autodetermine.
	 * @return string The author canonical URL, if any.
	 */
	public static function get_author_url( $id = null ) {

		if ( isset( $id ) )
			return static::get_bare_author_url( $id );

		$url = \get_author_posts_url( Query::get_the_real_id() );

		if ( empty( $url ) ) return '';

		return \sanitize_url(
			URI\Utils::set_preferred_url_scheme(
				URI\Utils::add_pagination_to_url( $url, Query::paged(), true )
			),
			[ 'https', 'http' ]
		);
	}

	/**
	 * Returns author canonical URL without query adjustments.
	 *
	 * @since 4.3.0
	 *
	 * @param int|null $id The author ID. Leave null to autodetermine.
	 * @return string The author canonical URL, if any.
	 */
	public static function get_bare_author_url( $id = null ) {

		$url = \get_author_posts_url( $id ?? Query::get_the_real_id() );

		if ( empty( $url ) ) return '';

		return \sanitize_url(
			URI\Utils::set_preferred_url_scheme( $url ),
			[ 'https', 'http' ]
		);
	}

	/**
	 * Returns date canonical URL.
	 * Automatically adds pagination if the date input matches the query.
	 *
	 * @since 3.0.0
	 *
	 * @param ?int $year  The year. Leave null to autodetermine entire query.
	 * @param ?int $month The month. If set, year must also be set.
	 * @param ?int $day   The day. If set, month and year must also be set.
	 * @return string The date canonical URL, if any.
	 */
	public static function get_date_url( $year = null, $month = null, $day = null ) {

		if ( isset( $year ) )
			return static::get_bare_date_url( $year, $month, $day );

		$year  = \get_query_var( 'year' );
		$month = \get_query_var( 'monthnum' );
		$day   = \get_query_var( 'day' );

		if ( $day ) {
			$url = \get_day_link( $year, $month, $day );
		} elseif ( $month ) {
			$url = \get_month_link( $year, $month );
		} else {
			$url = \get_year_link( $year );
		}

		if ( empty( $url ) ) return '';

		return \sanitize_url(
			URI\Utils::set_preferred_url_scheme(
				URI\Utils::add_pagination_to_url( $url, Query::paged(), true )
			),
			[ 'https', 'http' ]
		);
	}

	/**
	 * Returns date canonical URL without query adjustments.
	 *
	 * @since 4.3.0
	 *
	 * @param int  $year  The year.
	 * @param ?int $month The month.
	 * @param ?int $day   The day.
	 * @return string The date canonical URL, if any.
	 */
	public static function get_bare_date_url( $year, $month = null, $day = null ) {

		if ( $day ) {
			$url = \get_day_link( $year, $month, $day );
		} elseif ( $month ) {
			$url = \get_month_link( $year, $month );
		} else {
			$url = \get_year_link( $year );
		}

		if ( empty( $url ) ) return '';

		return \sanitize_url(
			URI\Utils::set_preferred_url_scheme( $url ),
			[ 'https', 'http' ]
		);
	}

	/**
	 * Returns search canonical URL.
	 * Automatically adds pagination if the input matches the query.
	 *
	 * @since 3.0.0
	 * @since 3.1.0 1. The first parameter now defaults to null.
	 *              2. The search term is now matched with the input query if not set,
	 *                 instead of it being empty.
	 * @since 4.3.0 Moved to \The_SEO_Framework\Meta\URI.
	 *
	 * @param string $search_query The search query. Mustn't be escaped.
	 *                             When left empty, the current query will be used.
	 * @return string The search canonical URL.
	 */
	public static function get_search_url( $search_query = null ) {

		if ( isset( $search_query ) )
			return static::get_bare_search_url( $search_query );

		$url = \get_search_link();

		if ( empty( $url ) ) return '';

		return \sanitize_url(
			URI\Utils::set_preferred_url_scheme(
				URI\Utils::add_pagination_to_url( $url, Query::paged(), true )
			),
			[ 'https', 'http' ]
		);
	}

	/**
	 * Returns search canonical URL without query adjustments.
	 *
	 * @since 4.3.0
	 *
	 * @param string $search_query The search query. Mustn't be escaped.
	 * @return string The date canonical URL, if any.
	 */
	public static function get_bare_search_url( $search_query ) {

		$url = \get_search_link( $search_query );

		if ( empty( $url ) ) return '';

		return \sanitize_url(
			URI\Utils::set_preferred_url_scheme( $url ),
			[ 'https', 'http' ]
		);
	}

	/**
	 * Generates Previous and Next links.
	 *
	 * @since 3.1.0
	 * @since 3.2.4 1. Now correctly removes the pagination base from singular URLs.
	 *              2. Now returns no URLs when a custom canonical URL is set.
	 * @since 4.1.0 Removed memoization.
	 * @since 4.1.2 1. Added back memoization.
	 *              2. Reduced needless canonical URL generation when it wouldn't be processed anyway.
	 * @since 4.3.0 1. Removed memoization thanks to optimization.
	 *              2. Moved to \The_SEO_Framework\Meta\URI.
	 *
	 * @return array Escaped site Pagination URLs: {
	 *    string 'prev'
	 *    string 'next'
	 * }
	 */
	public static function get_paged_urls() {

		$page     = max( Query::paged(), Query::page() );
		$numpages = Query::numpages();

		// If this page is not the last, create a next-URL.
		if ( ( $page + 1 ) <= $numpages ) {
			$url = URI\Utils::remove_pagination_from_url( static::get_generated_url() );

			if ( $url )
				$next = \sanitize_url(
					URI\Utils::add_pagination_to_url( $url, $page + 1 ),
					[ 'https', 'http' ]
				);
		}

		// If this page is not the first, create a prev-URL.
		if ( $page > 1 ) {
			$url ??= URI\Utils::remove_pagination_from_url( static::get_generated_url() );

			if ( $url )
				$prev = \sanitize_url(
					URI\Utils::add_pagination_to_url( $url, $page - 1 ),
					[ 'https', 'http' ]
				);
		}

		return [
			$prev ?? '',
			$next ?? '',
		];
	}

	/**
	 * Returns the redirect URL, if any.
	 *
	 * @since 4.1.4
	 * @since 4.2.0 1. Now supports the `$args['pta']` index.
	 *              2. Now redirects post type archives.
	 * @since 4.3.0 1. Now expects an ID before getting a post meta item.
	 *              2. Moved to \The_SEO_Framework\Meta\URI.
	 *
	 * @param null|array $args The redirect URL arguments, leave null to autodetermine query : {
	 *    int    $id       The Post, Page or Term ID to generate the URL for.
	 *    string $taxonomy The taxonomy.
	 * }
	 * @return string The canonical URL if found, empty string otherwise.
	 */
	public static function get_redirect_url( $args = null ) {

		if ( null === $args ) {
			if ( Query::is_singular() ) {
				$url = Data\Plugin\Post::get_post_meta_item( 'redirect' );
			} elseif ( Query::is_editable_term() ) {
				$url = Data\Plugin\Term::get_term_meta_item( 'redirect' );
			} elseif ( \is_post_type_archive() ) {
				$url = Data\Plugin\PTA::get_post_type_archive_meta_item( 'redirect' );
			}
		} else {
			normalize_generation_args( $args );

			if ( $args['tax'] ) {
				$url = Data\Plugin\Term::get_term_meta_item( 'redirect', $args['id'] );
			} elseif ( $args['pta'] ) {
				$url = Data\Plugin\PTA::get_post_type_archive_meta_item( 'redirect', $args['pta'] );
			} elseif ( $args['id'] ) {
				$url = Data\Plugin\Post::get_post_meta_item( 'redirect', $args['id'] );
			}
		}

		if ( empty( $url ) ) return '';

		return \sanitize_url( $url, [ 'https', 'http' ] );
	}

	/**
	 * Generates shortlink URL.
	 *
	 * @since 4.3.0
	 *
	 * @return string|null Escaped site Shortlink URL.
	 */
	public static function get_shortlink_url() {

		if (
			   ! Data\Plugin::get_option( 'shortlink_tag' )
			|| Query::is_real_front_page() // var_dump() if we have an input, we need to remove test for "get_custom".
		) return '';

		return static::generate_shortlink_url();
	}

	/**
	 * Generates shortlink URL.
	 *
	 * @since 4.3.0
	 * @todo Append queries of other plugins for other pages as well?
	 *
	 * @return string|null Escaped site Shortlink URL.
	 */
	public static function generate_shortlink_url() {

		if ( Query::is_singular() ) {
			$query = [ 'p' => Query::get_the_real_id() ];
		} elseif ( Query::is_archive() ) {
			if ( Query::is_category() ) {
				$query = [ 'cat' => Query::get_the_real_id() ];
			} elseif ( Query::is_tag() ) {
				$slug = \get_queried_object()->slug ?? '';

				if ( $slug )
					$query = [ 'tag' => $slug ];
			} elseif ( Query::is_tag() || Query::is_tax() ) {
				// Generate shortlink for object type and slug.
				$object = \get_queried_object();

				$tax  = $object->taxonomy ?? '';
				$slug = $object->slug ?? '';

				if ( $tax && $slug )
					$query = [ $tax => $slug ];
			} elseif ( \is_date() && isset( $GLOBALS['wp_query']->query ) ) {
				// FIXME: Trac ticket: WP doesn't accept paged parameters w/ date parameters. It'll lead to the homepage.
				$_query = $GLOBALS['wp_query']->query;
				$_date  = [
					'y' => $_query['year'] ?? '',
					'm' => $_query['monthnum'] ?? '',
					'd' => $_query['day'] ?? '',
				];

				$query = [ 'm' => implode( '', $_date ) ];
			} elseif ( Query::is_author() ) {
				$query = [ 'author' => Query::get_the_real_id() ];
			}
		} elseif ( Query::is_search() ) {
			$query = [ 's' => \get_search_query( false ) ];
		}

		if ( empty( $query ) ) return '';

		$page  = Query::page();
		$paged = Query::paged();

		if ( $page > 1 ) {
			$query += [ 'page' => $page ];
		} elseif ( $paged > 1 ) {
			$query += [ 'paged' => $paged ];
		}

		$query       = http_build_query( $query );
		$extra_query = parse_url( static::get_generated_url( null ), \PHP_URL_QUERY );

		if ( $extra_query )
			$query .= "&$extra_query";

		return \sanitize_url(
			URI\Utils::append_query_to_url(
				static::get_bare_front_page_url(),
				$query
			),
			[ 'https', 'http' ]
		);
	}
}