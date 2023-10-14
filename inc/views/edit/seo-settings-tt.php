<?php
/**
 * @package The_SEO_Framework\Views\Edit
 * @subpackage The_SEO_Framework\Admin\Edit\Term
 */

namespace The_SEO_Framework;

\defined( 'THE_SEO_FRAMEWORK_PRESENT' ) and Admin\Template::verify_secret( $secret ) or die;

use const \The_SEO_Framework\ROBOTS_IGNORE_SETTINGS;

use \The_SEO_Framework\Interpreters\{
	HTML,
	Form,
};

// phpcs:disable, WordPress.WP.GlobalVariablesOverride -- This isn't the global scope.

/**
 * The SEO Framework plugin
 * Copyright (C) 2017 - 2023 Sybre Waaijer, CyberWire B.V. (https://cyberwire.nl/)
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

// See output_setting_fields et al.
[ $term, $taxonomy ] = $view_args;

// Fetch Term ID and taxonomy.
$term_id = $term->term_id;
$meta    = Data\Plugin\Term::get_term_meta( $term_id );

$title       = $meta['doctitle'];
$description = $meta['description'];
$canonical   = $meta['canonical'];
$noindex     = $meta['noindex'];
$nofollow    = $meta['nofollow'];
$noarchive   = $meta['noarchive'];
$redirect    = $meta['redirect'];

$social_image_url = $meta['social_image_url'];
$social_image_id  = $meta['social_image_id'];

$og_title       = $meta['og_title'];
$og_description = $meta['og_description'];
$tw_title       = $meta['tw_title'];
$tw_description = $meta['tw_description'];

$generator_args = [
	'id'  => $term_id,
	'tax' => $taxonomy,
];

$show_og = (bool) Data\Plugin::get_option( 'og_tags' );
$show_tw = (bool) Data\Plugin::get_option( 'twitter_tags' );

$image_placeholder = Meta\Image::get_first_generated_image_url( $generator_args, 'social' );

$canonical_placeholder = Meta\URI::get_generated_url( $generator_args );
$robots_defaults       = Meta\Robots::generate_meta(
	$generator_args,
	[ 'noindex', 'nofollow', 'noarchive' ],
	ROBOTS_IGNORE_SETTINGS
);

// TODO reintroduce the info blocks, and place the labels at the left, instead??
$robots_settings = [
	'noindex'   => [
		'id'        => 'autodescription-meta[noindex]',
		'name'      => 'autodescription-meta[noindex]',
		'force_on'  => 'index',
		'force_off' => 'noindex',
		'label'     => \__( 'Indexing', 'autodescription' ),
		'_default'  => empty( $robots_defaults['noindex'] ) ? 'index' : 'noindex',
		'_value'    => $noindex,
		'_info'     => [
			\__( 'This tells search engines not to show this term in their search results.', 'autodescription' ),
			'https://developers.google.com/search/docs/advanced/crawling/block-indexing',
		],
	],
	'nofollow'  => [
		'id'        => 'autodescription-meta[nofollow]',
		'name'      => 'autodescription-meta[nofollow]',
		'force_on'  => 'follow',
		'force_off' => 'nofollow',
		'label'     => \__( 'Link following', 'autodescription' ),
		'_default'  => empty( $robots_defaults['nofollow'] ) ? 'follow' : 'nofollow',
		'_value'    => $nofollow,
		'_info'     => [
			\__( 'This tells search engines not to follow links on this term.', 'autodescription' ),
			'https://developers.google.com/search/docs/advanced/guidelines/qualify-outbound-links',
		],
	],
	'noarchive' => [
		'id'        => 'autodescription-meta[noarchive]',
		'name'      => 'autodescription-meta[noarchive]',
		'force_on'  => 'archive',
		'force_off' => 'noarchive',
		'label'     => \__( 'Archiving', 'autodescription' ),
		'_default'  => empty( $robots_defaults['noarchive'] ) ? 'archive' : 'noarchive',
		'_value'    => $noarchive,
		'_info'     => [
			\__( 'This tells search engines not to save a cached copy of this term.', 'autodescription' ),
			'https://developers.google.com/search/docs/advanced/robots/robots_meta_tag#directives',
		],
	],
];

?>
<h2><?php \esc_html_e( 'General SEO Settings', 'autodescription' ); ?></h2>

<table class="form-table tsf-term-meta">
	<tbody>
		<?php
		if ( Data\Plugin::get_option( 'display_seo_bar_metabox' ) ) {
			?>
			<tr class=form-field>
				<th scope=row valign=top><?php \esc_html_e( 'Doing it Right', 'autodescription' ); ?></th>
				<td>
					<?php
					// phpcs:ignore, WordPress.Security.EscapeOutput -- generate_bar() escapes.
					echo Admin\SEOBar\Builder::generate_bar( $generator_args );
					?>
				</td>
			</tr>
			<?php
		}
		?>

		<tr class=form-field>
			<th scope=row valign=top>
				<label for="autodescription-meta[doctitle]">
					<strong><?php \esc_html_e( 'Meta Title', 'autodescription' ); ?></strong>
					<?php
					echo ' ';
					HTML::make_info(
						\__( 'The meta title can be used to determine the title used on search engine result pages.', 'autodescription' ),
						'https://developers.google.com/search/docs/advanced/appearance/title-link'
					);
					?>
				</label>
				<?php
				Data\Plugin::get_option( 'display_character_counter' )
					and Form::output_character_counter_wrap( 'autodescription-meta[doctitle]' );
				Data\Plugin::get_option( 'display_pixel_counter' )
					and Form::output_pixel_counter_wrap( 'autodescription-meta[doctitle]', 'title' );
				?>
			</th>
			<td>
				<div class=tsf-title-wrap>
					<input type=text name="autodescription-meta[doctitle]" id="autodescription-meta[doctitle]" value="<?= \tsf()->escape_text( \tsf()->sanitize_text( $title ) ) ?>" size=40 autocomplete=off data-form-type=other />
					<?php
					\tsf()->output_js_title_data(
						'autodescription-meta[doctitle]',
						[
							'state' => [
								'refTitleLocked'    => false,
								'defaultTitle'      => \tsf()->escape_text( Meta\Title::get_bare_generated_title( $generator_args ) ),
								'addAdditions'      => Meta\Title\Conditions::use_title_branding( $generator_args ),
								'useSocialTagline'  => Meta\Title\Conditions::use_title_branding( $generator_args, true ),
								'additionValue'     => \tsf()->escape_text( Meta\Title::get_addition() ),
								'additionPlacement' => 'left' === Meta\Title::get_addition_location() ? 'before' : 'after',
							],
						]
					);
					?>
				</div>
				<label for="autodescription-meta[title_no_blog_name]" class=tsf-term-checkbox-wrap>
					<input type=checkbox name="autodescription-meta[title_no_blog_name]" id="autodescription-meta[title_no_blog_name]" value=1 <?php \checked( Data\Plugin\Term::get_term_meta_item( 'title_no_blog_name' ) ); ?> />
					<?php
					\esc_html_e( 'Remove the site title?', 'autodescription' );
					echo ' ';
					HTML::make_info( \__( 'Use this when you want to rearrange the title parts manually.', 'autodescription' ) );
					?>
				</label>
			</td>
		</tr>

		<tr class=form-field>
			<th scope=row valign=top>
				<label for="autodescription-meta[description]">
					<strong><?php \esc_html_e( 'Meta Description', 'autodescription' ); ?></strong>
					<?php
					echo ' ';
					HTML::make_info(
						\__( 'The meta description can be used to determine the text used under the title on search engine results pages.', 'autodescription' ),
						'https://developers.google.com/search/docs/advanced/appearance/snippet'
					);
					?>
				</label>
				<?php
				Data\Plugin::get_option( 'display_character_counter' )
					and Form::output_character_counter_wrap( 'autodescription-meta[description]' );
				Data\Plugin::get_option( 'display_pixel_counter' )
					and Form::output_pixel_counter_wrap( 'autodescription-meta[description]', 'description' );
				?>
			</th>
			<td>
				<textarea name="autodescription-meta[description]" id="autodescription-meta[description]" rows=4 cols=50 class=large-text autocomplete=off><?= \tsf()->escape_text( \tsf()->sanitize_text( $description ) ) ?></textarea>
				<?php
				\tsf()->output_js_description_data(
					'autodescription-meta[description]',
					[
						'state' => [
							'defaultDescription' => \tsf()->escape_text(
								Meta\Description::get_generated_description( $generator_args )
							),
						],
					]
				);
				?>
			</td>
		</tr>
	</tbody>
</table>

<h2><?php \esc_html_e( 'Social SEO Settings', 'autodescription' ); ?></h2>
<?php

\tsf()->output_js_social_data(
	'autodescription_social_tt',
	[
		'og' => [
			'state' => [
				'defaultTitle' => \tsf()->escape_text( Meta\Open_Graph::get_generated_title( $generator_args ) ),
				'addAdditions' => Meta\Title\Conditions::use_title_branding( $generator_args, 'og' ),
				'defaultDesc'  => \tsf()->escape_text( Meta\Open_Graph::get_generated_description( $generator_args ) ),
			],
		],
		'tw' => [
			'state' => [
				'defaultTitle' => \tsf()->escape_text( Meta\Twitter::get_generated_title( $generator_args ) ),
				'addAdditions' => Meta\Title\Conditions::use_title_branding( $generator_args, 'twitter' ),
				'defaultDesc'  => \tsf()->escape_text( Meta\Twitter::get_generated_description( $generator_args ) ),
			],
		],
	]
);
?>

<table class="form-table tsf-term-meta">
	<tbody>
		<tr class=form-field <?= $show_og ? '' : 'style=display:none' ?>>
			<th scope=row valign=top>
				<label for="autodescription-meta[og_title]">
					<strong><?php \esc_html_e( 'Open Graph Title', 'autodescription' ); ?></strong>
				</label>
				<?php
				Data\Plugin::get_option( 'display_character_counter' )
					and Form::output_character_counter_wrap( 'autodescription-meta[og_title]' );
				?>
			</th>
			<td>
				<div id=tsf-og-title-wrap>
					<input name="autodescription-meta[og_title]" id="autodescription-meta[og_title]" type=text value="<?= \tsf()->escape_text( \tsf()->sanitize_text( $og_title ) ) ?>" size=40 autocomplete=off data-form-type=other data-tsf-social-group=autodescription_social_tt data-tsf-social-type=ogTitle />
				</div>
			</td>
		</tr>

		<tr class=form-field <?= $show_og ? '' : 'style=display:none' ?>>
			<th scope=row valign=top>
				<label for="autodescription-meta[og_description]">
					<strong><?php \esc_html_e( 'Open Graph Description', 'autodescription' ); ?></strong>
				</label>
				<?php
				Data\Plugin::get_option( 'display_character_counter' )
					and Form::output_character_counter_wrap( 'autodescription-meta[og_description]' );
				?>
			</th>
			<td>
				<textarea name="autodescription-meta[og_description]" id="autodescription-meta[og_description]" rows=4 cols=50 class=large-text autocomplete=off data-tsf-social-group=autodescription_social_tt data-tsf-social-type=ogDesc><?= \tsf()->escape_text( \tsf()->sanitize_text( $og_description ) ) ?></textarea>
			</td>
		</tr>

		<tr class=form-field <?= $show_tw ? '' : 'style=display:none' ?>>
			<th scope=row valign=top>
				<label for="autodescription-meta[tw_title]">
					<strong><?php \esc_html_e( 'Twitter Title', 'autodescription' ); ?></strong>
				</label>
				<?php
				Data\Plugin::get_option( 'display_character_counter' )
					and Form::output_character_counter_wrap( 'autodescription-meta[tw_title]' );
				?>
			</th>
			<td>
				<div id=tsf-tw-title-wrap>
					<input name="autodescription-meta[tw_title]" id="autodescription-meta[tw_title]" type=text value="<?= \tsf()->escape_text( \tsf()->sanitize_text( $tw_title ) ) ?>" size=40 autocomplete=off data-form-type=other data-tsf-social-group=autodescription_social_tt data-tsf-social-type=twTitle />
				</div>
			</td>
		</tr>

		<tr class=form-field <?= $show_tw ? '' : 'style=display:none' ?>>
			<th scope=row valign=top>
				<label for="autodescription-meta[tw_description]">
					<strong><?php \esc_html_e( 'Twitter Description', 'autodescription' ); ?></strong>
				</label>
				<?php
				Data\Plugin::get_option( 'display_character_counter' )
					and Form::output_character_counter_wrap( 'autodescription-meta[tw_description]' );
				?>
			</th>
			<td>
				<textarea name="autodescription-meta[tw_description]" id="autodescription-meta[tw_description]" rows=4 cols=50 class=large-text autocomplete=off data-tsf-social-group=autodescription_social_tt data-tsf-social-type=twDesc><?= \tsf()->escape_text( \tsf()->sanitize_text( $tw_description ) ) ?></textarea>
			</td>
		</tr>

		<tr class=form-field>
			<th scope=row valign=top>
				<label for=autodescription_meta_socialimage-url>
					<strong><?php \esc_html_e( 'Social Image URL', 'autodescription' ); ?></strong>
					<?php
					echo ' ';
					HTML::make_info(
						\__( "The social image URL can be used by search engines and social networks alike. It's best to use an image with a 1.91:1 aspect ratio that is at least 1200px wide for universal support.", 'autodescription' ),
						'https://developers.facebook.com/docs/sharing/best-practices#images'
					);
					?>
				</label>
			</th>
			<td>
				<input type=url name="autodescription-meta[social_image_url]" id=autodescription_meta_socialimage-url placeholder="<?= \esc_attr( $image_placeholder ) ?>" value="<?= \esc_attr( $social_image_url ) ?>" size=40 autocomplete=off />
				<input type=hidden name="autodescription-meta[social_image_id]" id=autodescription_meta_socialimage-id value="<?= \absint( $social_image_id ) ?>" disabled class=tsf-enable-media-if-js />
				<div class="hide-if-no-tsf-js tsf-term-button-wrap">
					<?php
					// phpcs:ignore, WordPress.Security.EscapeOutput -- Already escaped.
					echo Form::get_image_uploader_form( [ 'id' => 'autodescription_meta_socialimage' ] );
					?>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<h2><?php \esc_html_e( 'Visibility SEO Settings', 'autodescription' ); ?></h2>

<table class="form-table tsf-term-meta">
	<tbody>
		<tr class=form-field>
			<th scope=row valign=top>
				<label for="autodescription-meta[canonical]">
					<strong><?php \esc_html_e( 'Canonical URL', 'autodescription' ); ?></strong>
					<?php
					echo ' ';
					HTML::make_info(
						\__( 'This urges search engines to go to the outputted URL.', 'autodescription' ),
						'https://developers.google.com/search/docs/advanced/crawling/consolidate-duplicate-urls'
					);
					?>
				</label>
			</th>
			<td>
				<input type=url name="autodescription-meta[canonical]" id="autodescription-meta[canonical]" placeholder="<?= \esc_attr( $canonical_placeholder ) ?>" value="<?= \esc_attr( $canonical ) ?>" size=40 autocomplete=off />
			</td>
		</tr>

		<tr class=form-field>
			<th scope=row valign=top>
				<?php
				\esc_html_e( 'Robots Meta Settings', 'autodescription' );
				echo ' ';
				HTML::make_info(
					\__( 'These directives may urge robots not to display, follow links on, or create a cached copy of this term.', 'autodescription' ),
					'https://developers.google.com/search/docs/advanced/robots/robots_meta_tag#directives'
				);
				?>
				</th>
			<td>
				<?php
				/* translators: %s = default option value */
				$_default_i18n = \__( 'Default (%s)', 'autodescription' );

				foreach ( $robots_settings as $_s ) {
					// phpcs:disable, WordPress.Security.EscapeOutput -- make_single_select_form() escapes.
					echo Form::make_single_select_form( [
						'id'      => $_s['id'],
						'class'   => 'tsf-term-select-wrap',
						'name'    => $_s['name'],
						'label'   => $_s['label'],
						'options' => [
							0  => sprintf( $_default_i18n, $_s['_default'] ),
							-1 => $_s['force_on'],
							1  => $_s['force_off'],
						],
						'default' => $_s['_value'],
						'info'    => $_s['_info'],
						'data'    => [
							'defaultUnprotected' => $_s['_default'],
							'defaultI18n'        => $_default_i18n,
						],
					] );
					// phpcs:enable, WordPress.Security.EscapeOutput
				}
				?>
			</td>
		</tr>

		<tr class=form-field>
			<th scope=row valign=top>
				<label for="autodescription-meta[redirect]">
					<strong><?php \esc_html_e( '301 Redirect URL', 'autodescription' ); ?></strong>
					<?php
					echo ' ';
					HTML::make_info(
						\__( 'This will force visitors to go to another URL.', 'autodescription' ),
						'https://developers.google.com/search/docs/advanced/crawling/301-redirects'
					);
					?>
				</label>
			</th>
			<td>
				<input type=url name="autodescription-meta[redirect]" id="autodescription-meta[redirect]" value="<?= \esc_attr( $redirect ) ?>" size=40 autocomplete=off />
			</td>
		</tr>
	</tbody>
</table>
<?php
