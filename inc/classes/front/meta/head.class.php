<?php
/**
 * @package The_SEO_Framework\Classes\Front\Meta
 * @subpackage The_SEO_Framework\Meta
 */

namespace The_SEO_Framework\Front\Meta;

\defined( 'THE_SEO_FRAMEWORK_PRESENT' ) or die;

use function \The_SEO_Framework\memo;

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
 * Outputs the front-end metadata output in WP Head.
 *
 * @since 4.3.0
 * @access protected
 * @internal
 * @final Can't be extended.
 */
final class Head {

	/**
	 * Prints the indicator wrap and meta tags.
	 * Adds various action hooks for outside the wrap.
	 *
	 * @since 4.3.0
	 */
	public static function print_wrap_and_tags() {

		/**
		 * @since 2.6.0
		 */
		\do_action( 'the_seo_framework_do_before_output' );

		/**
		 * The bootstrap timer keeps adding when metadata is strapping.
		 * This causes both timers to increase simultaneously.
		 * We catch the bootstrap here, and let the meta-timer take over.
		 */
		$bootstrap_timer = \The_SEO_Framework\_bootstrap_timer();
		/**
		 * Start the meta timer here. This also catches file inclusions,
		 * which is also caught by the _bootstrap_timer().
		 */
		$init_start = hrtime( true );

		static::print_plugin_indicator( 'before' );

		static::print_tags();

		static::print_plugin_indicator(
			'after',
			( hrtime( true ) - $init_start ) / 1e9,
			$bootstrap_timer
		);

		/**
		 * @since 2.6.0
		 */
		\do_action( 'the_seo_framework_do_after_output' );
	}

	/**
	 * Registers, generates, and prints the meta tags.
	 * Adds various action hooks for around the tags.
	 *
	 * @since 4.3.0
	 */
	public static function print_tags() {

		/**
		 * @since 4.2.0
		 */
		\do_action( 'the_seo_framework_before_meta_output' );

		// Limit processing and redundant tags on 404 and search.
		switch ( true ) {
			case \is_search():
				$generator_pools = [ 'Robots', 'URI', 'Open_Graph', 'Theme_Color', 'Webmasters' ];
				break;
			case \is_404():
				$generator_pools = [ 'Robots', 'Theme_Color', 'Webmasters' ];
				break;
			case \tsf()->is_query_exploited():
				// search and 404 cannot be exploited, hence they're tested earlier.
				$generator_pools = [ 'Advanced_Query_Protection', 'Robots', 'Theme_Color', 'Webmasters' ];
				break;
			default:
				$generator_pools = [
					'Robots',
					'URI',
					'Description',
					'Theme_Color',
					'Open_Graph',
					'Facebook',
					'Twitter',
					'Structured_Data',
					'Webmasters',
				];
		}

		/**
		 * @since 4.3.0
		 * @param string[] $generator_pools A list of tag pools requested for the current query.
		 *                                  The tag pool names correspond directly to the classes.
		 *                                  Do not register new pools, it'll cause a fatal error.
		 */
		$generator_pools = \apply_filters(
			'the_seo_framework_meta_generator_pools',
			$generator_pools
		);

		$tag_generators    = &Tags::tag_generators();
		$generators_spread = [];

		// Queue array_merge for improved performance.
		foreach ( $generator_pools as $pool )
			$generators_spread[] = ( '\The_SEO_Framework\Meta\Generator\\' . $pool )::GENERATORS;

		/**
		 * @since 4.3.0
		 * @param callable[] $tag_generators  A list of meta tag generator callbacks.
		 *                                    The generators may offload work to other generators.
		 * @param string[]   $generator_pools A list of tag pools requested for the current query.
		 *                                    The tag pool names correspond directly to the classes.
		 */
		$tag_generators = \apply_filters_ref_array(
			'the_seo_framework_meta_generators',
			[
				$tag_generators = array_merge( ...$generators_spread ),
				$generator_pools,
			]
		);

		Tags::fill_render_data_from_registered_generators();

		/**
		 * @since 4.3.0
		 * @param array[] $tags_render_data  The meta tags' render data : {
		 *    @param ?array  attributes A list of attributes by [ name => value ].
		 *    @param ?string tag        The tag name. Defaults to 'meta' if left empty.
		 *    @param ?string content    The tag's content. Leave null to not render content.
		 *    @param ?true   rendered   Do not write; tells whether the tag is rendered.
		 * }
		 * @param callable[] $tag_generators A list of meta tag generator callbacks.
		 *                                   The generators may offload work to other generators.
		 */
		$tags_render_data = \apply_filters_ref_array(
			'the_seo_framework_meta_render_data',
			[
				$tags_render_data = &Tags::tags_render_data(),
				$tag_generators,
			]
		);

		// Now output everything.
		Tags::render_tags();

		/**
		 * @since 4.2.0
		 */
		\do_action( 'the_seo_framework_after_meta_output' );
	}

	/**
	 * Returns the plugin hidden HTML indicators.
	 * Memoizes the filter outputs.
	 *
	 * @since 2.9.2
	 * @since 4.0.0 Added boot timers.
	 * @since 4.2.0 1. The annotation is translatable again (regressed in 4.0.0).
	 *              2. Is now a protected function.
	 * @access private
	 *
	 * @param string $where                 Determines the position of the indicator.
	 *                                      Accepts 'before' for before, anything else for after.
	 * @param float  $meta_timer            Total meta time in seconds.
	 * @param float  $bootstrap_timer       Total bootstrap time in seconds.
	 * @return string The SEO Framework's HTML plugin indicator.
	 */
	private static function print_plugin_indicator( $where = 'before', $meta_timer = 0, $bootstrap_timer = 0 ) {

		$cache = memo() ?? memo( [
			/**
			 * @since 2.0.0
			 * @param bool $run Whether to run and show the plugin indicator.
			 */
			'run'        => (bool) \apply_filters( 'the_seo_framework_indicator', true ),
			/**
			 * @since 2.4.0
			 * @param bool $show_timer Whether to show the generation time in the indicator.
			 */
			'show_timer' => (bool) \apply_filters( 'the_seo_framework_indicator_timing', true ),
			'annotation' => \esc_html( trim( vsprintf(
				/* translators: 1 = The SEO Framework, 2 = 'by Sybre Waaijer */
				\__( '%1$s %2$s', 'autodescription' ),
				[
					'The SEO Framework',
					/**
					 * @since 2.4.0
					 * @param bool $sybre Whether to show the author name in the indicator.
					 */
					\apply_filters( 'sybre_waaijer_<3', true ) // phpcs:ignore, WordPress.NamingConventions.ValidHookName -- Easter egg.
						? \__( 'by Sybre Waaijer', 'autodescription' )
						: '',
				]
			) ) ),
		] );

		if ( ! $cache['run'] ) return '';

		switch ( $where ) {
			case 'before':
				// phpcs:ignore, WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped earlier.
				echo "\n<!-- {$cache['annotation']} -->\n";
				break;
			case 'after':
				if ( $cache['show_timer'] && $meta_timer && $bootstrap_timer ) {
					$timers = sprintf(
						' | %s meta | %s boot',
						number_format( $meta_timer * 1e3, 2, null, '' ) . 'ms',
						number_format( $bootstrap_timer * 1e3, 2, null, '' ) . 'ms'
					);
				} else {
					$timers = '';
				}

				// phpcs:ignore, WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped earlier.
				echo "<!-- / {$cache['annotation']}{$timers} -->\n\n";
		}
	}
}