<?php

namespace MOFilesBrowser;

use WPML\FP\Fns;
use WPML\FP\Logic;
use WPML\FP\Lst;
use WPML\FP\Obj;
use function WPML\FP\curryN;
use function WPML\FP\pipe;

class ListEntries {

	public static function getList( Arguments $arguments ) {
		$functions = [
			self::readFile(),
			self::getSearchFns( $arguments->getSearch() ),
		];

		$allItems = pipe( ...$functions )( $arguments->getFilename() );

		return [
			'items'  => Lst::slice( $arguments->getOffset(), $arguments->getLimit(), $allItems ),
			'total' => count( $allItems ),
		];
	}

	private static function readFile(): callable {
		return function ( string $filePath ): array {
			$loadEntries = function ( $filePath ) {
				$mo = new \MO();
				$mo->import_from_file( $filePath );

				return $mo->entries;
			};

			// $implodeTranslations :: [string] -> string
			$implodeTranslations = curryN( 2, 'implode' )( '; ' );
			// $strTrimWidth :: string -> string
			$strTrimWidth = fn( $str ) => mb_strimwidth( $str, 0, 50, '...' );
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

			return pipe( $loadEntries, Fns::map( $mapEntry ) )( $filePath );
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