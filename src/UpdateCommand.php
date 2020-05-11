<?php

namespace MOFilesBrowser;


use WPML\FP\Fns;
use WPML\FP\Logic;
use WPML\FP\Obj;
use WPML\FP\Relation;
use function WPML\FP\curryN;
use function WPML\FP\pipe;

class UpdateCommand {

	public static function defineCommand() {
		\WP_CLI::add_command( 'mo update', new self() );
	}

	/**
	 * Update a translation of a single entry in MO file
	 *
	 * <input>
	 * : Path to input file
	 *
	 * <output>
	 * : Path to output file
	 *
	 * <entry>
	 * : Name of entry which should be updated
	 *
	 * <translation>
	 * : Value of new translation
	 *
	 * [--translation-index=<translation-index>]
	 * : If an entry has more than 1 translation ( plural forms), then we have to specify which one should be updated.
	 *  By default it's 0.
	 */
	public function __invoke( array $args, array $assocArgs ) {
		[ $inputFile, $outputFile, $entryValue, $newTranslation ] = $args;
		$translationIndex = (int) ( $assocArgs['translation-index'] ?? 0 );

		$updateTranslation = curryN( 3, function ( $newTranslation, $index, $entry ) {
			$translations           = $entry->translations;
			$translations[ $index ] = $newTranslation;
			$entry->translations    = $translations;

			return $entry;
		} );

		$predicate = Relation::propEq( 'singular', $entryValue );

		$updateEntry = Logic::ifElse(
			$predicate,
			$updateTranslation( $newTranslation, $translationIndex ),
			Fns::identity()
		);

		$hasUpdatedAny = Fns::filter( $predicate );

		$buildNewFile = curryN( 3, function ( $createFile, $updateEntry, $entries ) {
			$output = $createFile();
			Fns::forEach( [ $output, 'add_entry' ], Fns::map( $updateEntry, $entries ) );

			return $output;
		} );

		$entries = ListEntries::loadMOFile( $inputFile );
		if ( ! $hasUpdatedAny( $entries ) ) {
			\WP_CLI::error( 'Could not found any matching strings' );
		}

		$output = $buildNewFile( fn() => new \MO(), $updateEntry, $entries );
		$output->export_to_file( $outputFile );


		\WP_CLI::success( 'OK' );
	}
}