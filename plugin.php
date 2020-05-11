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
	\MOFilesBrowser\ListCommand::defineCommand();
	\MOFilesBrowser\UpdateCommand::defineCommand();
}

