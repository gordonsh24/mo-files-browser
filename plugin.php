<?php
/**
 * Plugin Name: MO Files Reviewer
 * Plugin URI: https://github.com/gordonsh24/mo-files-reviewer
 * Description: The plugin adds WP CLI commands to review content of MO Files
 * Version: 0.0.1
 * Author: Jakub Bis
 */

require_once __DIR__ . '/vendor/autoload.php';

if ( defined( 'WP_CLI' ) ) {
	/**
	 * List content of selected MO file
	 *
	 * <file>
	 * : Path to MO File
	 *
	 *[--limit=<limit>]
	 * : It limits number of subscriptions
	 *
	 * ---
	 *
	 * [--offset=<offset>]
	 * : It starts from <offset> subscription
	 *
	 * [--search=<search>]
	 * : It searches inside all fields
	 */
	\WP_CLI::add_command( 'mo browse', function ( array $args, array $assocArgs ) {
		[ 'items' => $items, 'total' => $total ] = \MOFilesBrowser\ListEntries::getList(
			new \MOFilesBrowser\Arguments( $args, $assocArgs )
		);

		\WP_CLI::line( sprintf( 'Total entities: %d', $total ) );
		\WP_CLI\Utils\format_items(
			'table',
			$items,
			[ 'id', 'singular', 'plural', 'translations' ]
		);
	} );

}

