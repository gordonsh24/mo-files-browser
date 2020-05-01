<?php

namespace MOFilesBrowser;

use WPML\FP\Fns;
use WPML\FP\Logic;
use WPML\FP\Lst;
use WPML\FP\Obj;
use function WPML\FP\curryN;
use function WPML\FP\pipe;

final class ListEntries {

	public static function loadMOFile( string $fileName ): array {
		$mo = new \MO();
		$mo->import_from_file( $fileName );

		return $mo->entries;
	}

	public static function getList( Arguments $arguments, callable $loadEntries ) {
		$functions = [
			self::readFile( $loadEntries ),
			self::getSearchFns( $arguments->getSearch() ),
		];

		$allItems = pipe( ...$functions )( $arguments->getFilename() );

		return [
			'items' => Lst::slice( $arguments->getOffset(), $arguments->getLimit(), $allItems ),
			'total' => count( $allItems ),
		];
	}

	private static function readFile( callable $loadEntries ): callable {
		return function ( string $filePath ) use ( $loadEntries ): array {
			// $implodeTranslations :: [string] -> string
			$implodeTranslations = curryN( 2, 'implode' )( ' | ' );
			// $strTrimWidth :: string -> string
			$strTrimWidth = fn( $str ) => mb_strimwidth( $str, 0, 50, '...' );
			$strTrimWidth = Fns::identity();
			// $transformTranslations :: [string] -> string
			$transformTranslations = pipe( Fns::map( $strTrimWidth ), $implodeTranslations );

			// $mapEntry :: array -> array
			$mapEntry = function ( $entry ) use ( $strTrimWidth, $transformTranslations ) {
				return [
					'id'           => md5( $entry->singular . $entry->plural ),
					'singular'     => $strTrimWidth( $entry->singular ),
					'plural'       => $strTrimWidth( $entry->plural ),
					'translations' => $transformTranslations( $entry->translations ),
				];
			};

			return pipe( $loadEntries, Fns::map( $mapEntry ), 'array_values' )( $filePath );
		};
	}

	// getSearchFns :: string -> (string[string] -> string[string])
	private static function getSearchFns( string $search ): callable {
		if ( ! $search ) {
			return Fns::identity();
		}

		// $searchInField :: string -> string -> string[string] -> string[string]
		$searchInField = fn( $search, $fieldName, $entry ) => strpos( Obj::prop( $fieldName, $entry ),
				$search ) !== false;
		$searchInField = curryN( 3, $searchInField );
		$searchInField = $searchInField( $search );

		return Fns::filter( Logic::anyPass( [
			$searchInField( 'singular' ),
			$searchInField( 'plural' ),
			$searchInField( 'translations' ),
		] ) );
	}
}