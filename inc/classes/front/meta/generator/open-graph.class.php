<?php
/**
 * @package The_SEO_Framework\Classes\Front\Front\Meta\Generator
 * @subpackage The_SEO_Framework\Meta
 */

namespace The_SEO_Framework\Front\Meta\Generator;

\defined( 'THE_SEO_FRAMEWORK_PRESENT' ) or die;

use \The_SEO_Framework\Data,
	\The_SEO_Framework\Meta;

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
 * Holds Open Graph generators for meta tag output.
 *
 * @since 4.3.0
 * @access private
 */
final class Open_Graph {

	/**
	 * @since 4.3.0
	 * @var callable[] GENERATORS A list of autoloaded meta callbacks.
	 */
	public const GENERATORS = [
		[ __CLASS__, 'generate_open_graph_type' ],
		[ __CLASS__, 'generate_open_graph_locale' ],
		[ __CLASS__, 'generate_open_graph_site_name' ],
		[ __CLASS__, 'generate_open_graph_title' ],
		[ __CLASS__, 'generate_open_graph_description' ],
		[ __CLASS__, 'generate_open_graph_url' ],
		[ __CLASS__, 'generate_open_graph_image' ],
		[ __CLASS__, 'generate_article_published_time' ],
		[ __CLASS__, 'generate_article_modified_time' ],
	];

	/**
	 * @since 4.3.0
	 * @access protected
	 * @generator
	 */
	public static function generate_open_graph_type() {

		$type = Meta\Open_Graph::get_type();

		if ( $type )
			yield [
				'attributes' => [
					'property' => 'og:type',
					'content'  => $type,
				],
			];
	}

	/**
	 * @since 4.3.0
	 * @access protected
	 * @generator
	 */
	public static function generate_open_graph_locale() {

		$locale = Meta\Open_Graph::get_locale();

		if ( \has_filter( 'the_seo_framework_ogdescription_output' ) ) {
			/**
			 * @since 2.3.0
			 * @since 2.7.0 Added output within filter.
			 * @since 4.3.0 Deprecated
			 * @deprecated
			 * @param string $locale The generated locale field.
			 * @param int    $id     The page or term ID.
			 */
			$locale = (string) \apply_filters_deprecated(
				'the_seo_framework_oglocale_output',
				[
					$locale,
					\The_SEO_Framework\Helper\Query::get_the_real_id(), // Lacking import OK.
				],
				'4.3.0 of The SEO Framework',
				'the_seo_framework_meta_render_data',
			);
		}

		if ( $locale )
			yield [
				'attributes' => [
					'property' => 'og:locale',
					'content'  => $locale,
				],
			];
	}

	/**
	 * @since 4.3.0
	 * @access protected
	 * @generator
	 */
	public static function generate_open_graph_site_name() {

		$sitename = Meta\Open_Graph::get_site_name();

		if ( \has_filter( 'the_seo_framework_ogsitename_output' ) ) {
			/**
			 * @since 2.3.0
			 * @since 2.7.0 Added output within filter.
			 * @since 4.3.0 Deprecated
			 * @deprecated
			 * @param string $locale The generated Open Graph site name.
			 * @param int    $id     The page or term ID.
			 */
			$sitename = (string) \apply_filters_deprecated(
				'the_seo_framework_ogsitename_output',
				[
					$sitename,
					\The_SEO_Framework\Helper\Query::get_the_real_id(), // Lacking import OK.
				],
				'4.3.0 of The SEO Framework',
				'the_seo_framework_meta_render_data',
			);
		}

		if ( $sitename )
			yield [
				'attributes' => [
					'property' => 'og:site_name',
					'content'  => $sitename,
				],
			];
	}

	/**
	 * @since 4.3.0
	 * @access protected
	 * @generator
	 */
	public static function generate_open_graph_title() {

		$title = Meta\Open_Graph::get_title();

		if ( \has_filter( 'the_seo_framework_ogtitle_output' ) ) {
			/**
			 * @since 2.3.0
			 * @since 2.7.0 Added output within filter.
			 * @since 4.3.0 Deprecated
			 * @deprecated
			 * @param string $title The generated Open Graph title.
			 * @param int    $id    The page or term ID.
			 */
			$title = (string) \apply_filters_deprecated(
				'the_seo_framework_ogtitle_output',
				[
					$title,
					\The_SEO_Framework\Helper\Query::get_the_real_id(), // Lacking import OK.
				],
				'4.3.0 of The SEO Framework',
				'the_seo_framework_meta_render_data',
			);
		}

		if ( $title )
			yield [
				'attributes' => [
					'property' => 'og:title',
					'content'  => $title,
				],
			];
	}

	/**
	 * @since 4.3.0
	 * @access protected
	 * @generator
	 */
	public static function generate_open_graph_description() {

		$description = Meta\Open_Graph::get_description();

		if ( \has_filter( 'the_seo_framework_ogdescription_output' ) ) {
			/**
			 * @since 2.3.0
			 * @since 2.7.0 Added output within filter.
			 * @since 4.3.0 Deprecated
			 * @deprecated
			 * @param string $description The generated Open Graph description.
			 * @param int    $id          The page or term ID.
			 */
			$description = (string) \apply_filters_deprecated(
				'the_seo_framework_ogdescription_output',
				[
					$description,
					\The_SEO_Framework\Helper\Query::get_the_real_id(), // Lacking import OK.
				],
				'4.3.0 of The SEO Framework',
				'the_seo_framework_meta_render_data',
			);
		}

		if ( $description )
			yield [
				'attributes' => [
					'property' => 'og:description',
					'content'  => $description,
				],
			];
	}

	/**
	 * @since 4.3.0
	 * @access protected
	 * @generator
	 */
	public static function generate_open_graph_url() {

		$url = Meta\Open_Graph::get_url();

		if ( \has_filter( 'the_seo_framework_ogurl_output' ) ) {
			/**
			 * @since 2.9.3
			 * @since 4.3.0 Deprecated
			 * @deprecated
			 * @param string $url The canonical/Open Graph URL. Must be escaped.
			 * @param int    $id  The page or term ID.
			 */
			$url = (string) \apply_filters_deprecated(
				'the_seo_framework_ogurl_output',
				[
					$url,
					\The_SEO_Framework\Helper\Query::get_the_real_id(), // Lacking import OK.
				],
				'4.3.0 of The SEO Framework',
				'the_seo_framework_meta_render_data',
			);
		}

		if ( $url )
			yield [
				'attributes' => [
					'property' => 'og:url',
					'content'  => $url,
				],
			];
	}

	/**
	 * @since 4.3.0
	 * @access protected
	 * @generator
	 */
	public static function generate_open_graph_image() {

		foreach ( Meta\Image::get_image_details(
			null,
			! Data\Plugin::get_option( 'multi_og_image' )
		) as $image ) {
			yield [
				'attributes' => [
					'property' => 'og:image',
					'content'  => $image['url'],
				],
			];

			if ( $image['height'] && $image['width'] ) {
				yield [
					'attributes' => [
						'property' => 'og:image:width',
						'content'  => $image['width'],
					],
				];
				yield [
					'attributes' => [
						'property' => 'og:image:height',
						'content'  => $image['height'],
					],
				];
			}

			if ( $image['alt'] ) {
				yield [
					'attributes' => [
						'property' => 'og:image:alt',
						'content'  => $image['alt'],
					],
				];
			}
		}
	}

	/**
	 * @since 4.3.0
	 * @access protected
	 * @generator
	 */
	public static function generate_article_published_time() {

		$time = Meta\Open_Graph::get_article_published_time();

		if ( \has_filter( 'the_seo_framework_publishedtime_output' ) ) {
			/**
			 * @since 2.3.0
			 * @since 2.7.0 Added output within filter.
			 * @since 2.9.3
			 * @since 4.3.0 Deprecated
			 * @param string $time The article published time.
			 * @param int    $id   The current page or term ID.
			 */
			$time = (string) \apply_filters_deprecated(
				'the_seo_framework_publishedtime_output',
				[
					$time,
					\The_SEO_Framework\Helper\Query::get_the_real_id(), // Lacking import OK.
				],
				'4.3.0 of The SEO Framework',
				'the_seo_framework_meta_render_data',
			);
		}

		if ( $time )
			yield [
				'attributes' => [
					'property' => 'article:published_time',
					'content'  => $time,
				],
			];
	}

	/**
	 * @since 4.3.0
	 * @access protected
	 * @generator
	 */
	public static function generate_article_modified_time() {

		$time = Meta\Open_Graph::get_article_modified_time();

		if ( \has_filter( 'the_seo_framework_modifiedtime_output' ) ) {
			/**
			 * @since 2.3.0
			 * @since 2.7.0 Added output within filter.
			 * @since 4.3.0 Deprecated
			 * @param string $time The article modified time.
			 * @param int    $id   The current page or term ID.
			 */
			$time = (string) \apply_filters_deprecated(
				'the_seo_framework_modifiedtime_output',
				[
					$time,
					\The_SEO_Framework\Helper\Query::get_the_real_id(), // Lacking import OK.
				],
				'4.3.0 of The SEO Framework',
				'the_seo_framework_meta_render_data',
			);
		}

		if ( $time )
			yield [
				'attributes' => [
					'property' => 'article:modified_time',
					'content'  => $time,
				],
			];
	}
}
