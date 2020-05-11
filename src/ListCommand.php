<?php

namespace MOFilesBrowser;


class ListCommand {

	public static function defineCommand() {
		\WP_CLI::add_command( 'mo browse', new self());
	}

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
	public function __invoke(array $args, array $assocArgs) {
		[ 'items' => $items, 'total' => $total ] = ListEntries::getList(
			new Arguments( $args, $assocArgs ),
			[ ListEntries::class, 'loadMOFile' ]
		);

		\WP_CLI::line( sprintf( 'Total entities: %d', $total ) );
		if ( $total > count( $items ) ) {
			\WP_CLI::line( sprintf( 'Displayed entities: %d', count( $items ) ) );
		}

		\WP_CLI\Utils\format_items(
			'table',
			$items,
			[ 'singular', 'plural', 'translations' ]
		);
	}
}