<?php
/**
 * @package The_SEO_Framework\Suggestion
 * @subpackage The_SEO_Framework\Bootstrap\Install
 */

namespace The_SEO_Framework\Suggestion;

/**
 * The SEO Framework plugin
 * Copyright (C) 2018 - 2020 Sybre Waaijer, CyberWire (https://cyberwire.nl/)
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

defined( 'THE_SEO_FRAMEWORK_PRESENT' ) or die;

/**
 * This file holds functions for installing TSFEM.
 * This file will only be called ONCE on plugin install, or upgrade from pre-v3.0.6.
 *
 * @since 3.0.6
 * @since 3.2.4 Applied namspacing to this file. All method names have changed.
 * @access private
 */

_prepare();
/**
 * Prepares a suggestion notification to ALL applicable plugin users on upgrade;
 * For TSFEM, it's shown when:
 *    0. The upgrade happens when an applicable user is on the admin pages. (always true w/ default actions)
 *    1. The constant 'TSF_DISABLE_SUGGESTIONS' is not defined or false.
 *    2. The current dashboard is the main site's.
 *    3. TSFEM isn't already installed.
 *    4. PHP and WP requirements of TSFEM are met.
 *
 * This notice is automatically dismissed, and it can be ignored without reappearing.
 *
 * @since 3.0.6
 * @since 4.1.0 1. Now tests TSFEM 2.4.0 requirements.
 *              2. Removed the user capability requirement, and forwarded that to `_suggest_extension_manager()`.
 *              3. Can now run on the front-end without crashing.
 * @access private
 */
function _prepare() {

	//? 1
	if ( defined( 'TSF_DISABLE_SUGGESTIONS' ) && TSF_DISABLE_SUGGESTIONS ) return;
	//? 2
	if ( ! \is_main_site() ) return;
	//? 3a
	if ( defined( 'TSF_EXTENSION_MANAGER_VERSION' ) ) return;

	if ( ! function_exists( '\\get_plugins' ) )
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

	//? 3b
	if ( ! empty( \get_plugins()['the-seo-framework-extension-manager/the-seo-framework-extension-manager.php'] ) ) return;

	/** @source https://github.com/sybrew/The-SEO-Framework-Extension-Manager/blob/08db1ab7410874c47d8f05b15479ce923857c35e/bootstrap/envtest.php#L68-L77 */
	$requirements = [
		'php' => 50605,
		'wp'  => '4.9-dev',
	];

	// phpcs:disable, Generic.Formatting.MultipleStatementAlignment, WordPress.WhiteSpace.PrecisionAlignment
	//? PHP_VERSION_ID is definitely defined, but let's keep it homonymous with the envtest of TSFEM.
	   ! defined( 'PHP_VERSION_ID' ) || PHP_VERSION_ID < $requirements['php'] and $test = 1
	or version_compare( $GLOBALS['wp_version'], $requirements['wp'], '<' ) and $test = 2
	or $test = true;
	// phpcs:enable, Generic.Formatting.MultipleStatementAlignment, WordPress.WhiteSpace.PrecisionAlignment

	//? 4
	if ( true !== $test ) return;

	_suggest_extension_manager();
}

/**
 * Registers "look at TSFEM" notification to applicable plugin users on upgrade.
 *
 * @since 3.0.6
 * @since 4.1.0 Is now a persistent notice, that outputs at most 3 times, on any admin page, only for users that can install plugins.
 * @access private
 */
function _suggest_extension_manager() {

	$tsf = \the_seo_framework();

	$tsf->register_dismissible_persistent_notice(
		$tsf->convert_markdown(
			vsprintf(
				'<p>*Placeholder*</p>
				<p>[Extension Manager plugin](%s) | [Included extensions](%s) | [Pricing](%s)</p>',
				[
					'https://theseoframework.com/extension-manager/',
					'https://theseoframework.com/extensions/',
					'https://theseoframework.com/pricing/',
				]
			),
			[ 'a', 'em', 'strong' ],
			[ 'a_internal' => false ]
		),
		'suggest-extension-manager',
		[
			'type'   => 'info',
			'icon'   => false,
			'escape' => false,
		],
		[
			'screens'      => [],
			'excl_screens' => [ 'update-core', 'post', 'term', 'upload', 'media', 'plugin-editor', 'plugin-install', 'themes', 'widgets', 'user', 'nav-menus', 'theme-editor', 'profile', 'export', 'site-health', 'export-personal-data', 'erase-personal-data' ],
			'capability'   => 'install_plugins',
			'user'         => 0,
			'count'        => 3,
			'timeout'      => DAY_IN_SECONDS * 7,
		]
	);
}
