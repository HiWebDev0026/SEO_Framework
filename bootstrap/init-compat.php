<?php
/**
 * @package The_SEO_Framework
 * @subpackage The_SEO_Framework\Bootstrap
 */

namespace The_SEO_Framework;

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

// Disable Headway theme SEO.
\add_filter( 'headway_seo_disabled', '__return_true' );

if ( \tsf()->is_theme( 'genesis' ) )
	require \THE_SEO_FRAMEWORK_DIR_PATH_COMPAT . 'theme-genesis.php';

foreach (
	array_intersect_key(
		[
			'bbpress/bbpress.php'                      => 'bbpress',
			'buddypress/buddypress.php'                => 'buddypress',
			'easy-digital-downloads/easy-digital-downloads.php' => 'edd',
			'elementor/elementor.php'                  => 'elementor',
			'jetpack/jetpack.php'                      => 'jetpack',
			'polylang/polylang.php'                    => 'polylang',
			'sitepress-multilingual-cms/sitepress.php' => 'wpml',
			'ultimate-member/ultimate-member.php'      => 'ultimatemember',
			'wpforo/wpforo.php'                        => 'wpforo',
			'woocommerce/woocommerce.php'              => 'woocommerce',
		],
		array_flip( \tsf()->active_plugins() ),
	)
	as $_plugin
) {
	require \THE_SEO_FRAMEWORK_DIR_PATH_COMPAT . "plugin-$_plugin.php";
}
