<?php
/**
 * @package The_SEO_Framework\Classes\Admin\View
 */

namespace The_SEO_Framework\Admin;

\defined( 'THE_SEO_FRAMEWORK_PRESENT' ) or die;

use function \The_SEO_Framework\memo;

/**
 * The SEO Framework plugin
 * Copyright (C) 2021 - 2023 Sybre Waaijer, CyberWire B.V. (https://cyberwire.nl/)
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
 * Holds API interfaces for screen views/templates.
 *
 * @since 4.3.0
 * @access protected
 * @internal
 * @final Can't be extended.
 */
final class Template {

	/**
	 * @since 4.3.0
	 * @var ?string $secret The include secret.
	 */
	private static $secret;

	/**
	 * Outputs a template.
	 *
	 * The secret is scoped to the instance so the static function cannot bypass it.
	 *
	 * @since 4.3.0
	 * @access private
	 *
	 * @param string $file         The relative view file name.
	 * @param array  ...$view_args The arguments to be supplied to the file.
	 */
	public static function output_view( $file, ...$view_args ) { // phpcs:ignore, VariableAnalysis.CodeAnalysis -- includes.

		// phpcs:ignore, VariableAnalysis.CodeAnalysis -- includes.
		$secret = static::$secret = uniqid( '', true );

		// This will crash on PHP 8+ if the view isn't resolved. That's good.
		include static::get_view_location( $file );
	}

	/**
	 * Gets view location. Forces a path on our Views folder.
	 *
	 * @since 3.1.0
	 * @since 4.3.0 Moved to `The_SEO_Framework\Admin\Template`.
	 * @access private
	 *
	 * @param string $file The file name.
	 * @return ?string The view location. Null on failure.
	 */
	public static function get_view_location( $file ) {

		static $realview;

		$realview ??= realpath( \THE_SEO_FRAMEWORK_DIR_PATH_VIEWS );
		$path       = realpath( "$realview/$file.php" );

		if ( $path && str_starts_with( $path, $realview ) )
			return $path;

		return null;
	}

	/**
	 * Verifies view secret.
	 *
	 * @since 4.1.1
	 * @since 4.3.0 Moved to `The_SEO_Framework\Admin\Template`.
	 * @access private
	 *
	 * @param string $value The value to match against secret.
	 * @return bool
	 */
	public static function verify_secret( $value ) {
		return isset( $value ) && static::$secret === $value;
	}
}