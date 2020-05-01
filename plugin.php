<?php
/**
 * Plugin Name: MO Files Reviewer
 * Plugin URI: https://github.com/gordonsh24/mo-files-reviewer
 * Description: The plugin adds WP CLI commands to review content of MO Files
 * Version: 0.0.1
 * Author: Jakub Bis
 */

if ( defined( 'WP_CLI' ) ) {

	\WP_CLI::add_command( 'mo-files-reviewer', function() {
		\WP_CLI::success( 'Hello world' );
	} );

}

