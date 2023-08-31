<?php
/**
 * @package The_SEO_Framework\Classes\Front\Meta\Generator
 * @subpackage The_SEO_Framework\Meta
 */

namespace The_SEO_Framework\Meta\Generator;

\defined( 'THE_SEO_FRAMEWORK_PRESENT' ) or die;

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
 * Holds Twitter generators for meta tag output.
 *
 * @since 4.3.0
 * @access protected
 * @internal
 * @final Can't be extended.
 */
final class Twitter {

	/**
	 * @since 4.3.0
	 * @var callable[] GENERATORS A list of autoloaded meta callbacks.
	 */
	public const GENERATORS = [
		[ __CLASS__, 'generate_twitter' ],
	];

	/**
	 * @since 4.3.0
	 * @access protected
	 * @generator
	 */
	public static function generate_twitter() {
		/**
		 * @since 3.1.4
		 * @since 4.3.0 Deprecated
		 * @deprecated
		 * @param bool $use_twitter_card
		 */
		$use_twitter_card = \apply_filters_deprecated(
			'the_seo_framework_use_twitter_tags',
			[
				(bool) \tsf()->get_option( 'twitter_tags' ),
			],
			'4.3.0 of The SEO Framework',
			'the_seo_framework_meta_generators',
		);

		if ( $use_twitter_card ) {
			yield from static::generate_twitter_card();
			yield from static::generate_twitter_site();
			yield from static::generate_twitter_creator();
			yield from static::generate_twitter_title();
			yield from static::generate_twitter_description();
			yield from static::generate_twitter_image();
		}
	}

	/**
	 * @since 4.3.0
	 * @access protected
	 * @generator
	 */
	public static function generate_twitter_card() {

		$card = \tsf()->generate_twitter_card_type();

		if ( $card )
			yield [
				'attributes' => [
					'name'    => 'twitter:card',
					'content' => $card,
				],
			];
	}

	/**
	 * @since 4.3.0
	 * @access protected
	 * @generator
	 */
	public static function generate_twitter_site() {

		$site = \tsf()->get_option( 'twitter_site' );

		if ( \has_filter( 'the_seo_framework_twittersite_output' ) ) {
			/**
			 * @since 2.3.0
			 * @since 2.7.0 Added output within filter.
			 * @param string $site The Twitter site owner tag.
			 * @param int    $id   The current page or term ID.
			 */
			$site = (string) \apply_filters_deprecated(
				'the_seo_framework_twittersite_output',
				[
					$site,
					\The_SEO_Framework\Helper\Query::get_the_real_id(),
				],
				'4.3.0 of The SEO Framework',
				'the_seo_framework_meta_render_data',
			);
		}

		if ( $site )
			yield [
				'attributes' => [
					'name'    => 'twitter:site',
					'content' => $site,
				],
			];
	}
	/**
	 * @since 4.3.0
	 * @access protected
	 * @generator
	 */
	public static function generate_twitter_creator() {

		$tsf = \tsf();

		$creator =
			   $tsf->get_current_post_author_meta_item( 'twitter_page' )
			?: $tsf->get_option( 'twitter_creator' );

		if ( \has_filter( 'the_seo_framework_twittercreator_output' ) ) {
			/**
			 * @since 2.3.0
			 * @since 2.7.0 Added output within filter.
			 * @param string $creator The Twitter page creator.
			 * @param int    $id      The current page or term ID.
			 */
			$creator = (string) \apply_filters_deprecated(
				'the_seo_framework_twittercreator_output',
				[
					$creator,
					\The_SEO_Framework\Helper\Query::get_the_real_id(),
				],
				'4.3.0 of The SEO Framework',
				'the_seo_framework_meta_render_data',
			);
		}

		if ( $creator )
			yield [
				'attributes' => [
					'name'    => 'twitter:creator',
					'content' => $creator,
				],
			];
	}
	/**
	 * @since 4.3.0
	 * @access protected
	 * @generator
	 */
	public static function generate_twitter_title() {

		$title = \tsf()->get_twitter_title();

		if ( \has_filter( 'the_seo_framework_twittertitle_output' ) ) {
			/**
			 * @since 2.3.0
			 * @since 2.7.0 Added output within filter.
			 * @param string $title The generated Twitter title.
			 * @param int    $id    The current page or term ID.
			 */
			$title = (string) \apply_filters_deprecated(
				'the_seo_framework_twittertitle_output',
				[
					$title,
					\The_SEO_Framework\Helper\Query::get_the_real_id(),
				],
				'4.3.0 of The SEO Framework',
				'the_seo_framework_meta_render_data',
			);
		}

		if ( $title )
			yield [
				'attributes' => [
					'name'    => 'twitter:title',
					'content' => $title,
				],
			];
	}
	/**
	 * @since 4.3.0
	 * @access protected
	 * @generator
	 */
	public static function generate_twitter_description() {

		$description = \tsf()->get_twitter_description();

		if ( \has_filter( 'the_seo_framework_twitterdescription_output' ) ) {
			/**
			 * @since 2.3.0
			 * @since 2.7.0 Added output within filter.
			 * @param string $description The generated Twitter description.
			 * @param int    $id          The current page or term ID.
			 */
			$description = (string) \apply_filters_deprecated(
				'the_seo_framework_twitterdescription_output',
				[
					$description,
					\The_SEO_Framework\Helper\Query::get_the_real_id(),
				],
				'4.3.0 of The SEO Framework',
				'the_seo_framework_meta_render_data',
			);
		}

		if ( $description )
			yield [
				'attributes' => [
					'name'    => 'twitter:description',
					'content' => $description,
				],
			];
	}
	/**
	 * @since 4.3.0
	 * @access protected
	 * @generator
	 */
	public static function generate_twitter_image() {

		$tsf = \tsf();

		// We always grab one image.
		// However, !multi ensures we get the cached generator's first image.
		foreach ( $tsf->get_image_details_from_cache( ! $tsf->get_option( 'multi_og_image' ) ) as $image ) {
			yield [
				'attributes' => [
					'name'    => 'twitter:image',
					'content' => $image['url'],
				],
			];

			if ( $image['alt'] ) {
				yield [
					'attributes' => [
						'name'    => 'twitter:image:alt',
						'content' => $image['alt'],
					],
				];
			}

			// Only grab a single image. Twitter grabs the final (less favorable) image otherwise.
			break;
		}
	}
}