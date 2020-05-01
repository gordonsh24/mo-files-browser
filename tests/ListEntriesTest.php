<?php

namespace MOFilesBrowser;

use PHPUnit\Framework\TestCase;

final class ListEntriesTest extends TestCase {

	const EXISTING_FILE = '/some/file.mo';

	/**
	 * @test
	 */
	public function itGetsAllEntries() {
		$arguments = new Arguments( [ self::EXISTING_FILE ], [] );
		$result    = ListEntries::getList( $arguments, $this->load() );

		$this->assertEquals( 5, $result['total'] );
		$this->assertCount( 5, $result['items'] );

		$this->assertArrayHasKey( 'id', $result['items'][0] );
		$this->assertEquals( 'String 1 text', $result['items'][0]['singular'] );
		$this->assertEquals( '', $result['items'][0]['plural'] );
		$this->assertEquals( 'String 1 text translation', $result['items'][0]['translations'] );

		$this->assertArrayHasKey( 'id', $result['items'][1] );
		$this->assertEquals( 'String 2 text', $result['items'][1]['singular'] );
		$this->assertEquals( 'String 2 text plural', $result['items'][1]['plural'] );
		$this->assertEquals(
			'String 2 text translation | String 2 text plural translation',
			$result['items'][1]['translations']
		);
	}

	/**
	 * @test
	 */
	public function itGetsEntriesWithLimit() {
		$arguments = new Arguments( [ self::EXISTING_FILE ], ['limit' => 3] );
		$result    = ListEntries::getList( $arguments, $this->load() );

		$this->assertEquals( 5, $result['total'] );
		$this->assertCount( 3, $result['items'] );

		$this->assertEquals( 'String 1 text', $result['items'][0]['singular'] );
		$this->assertEquals( 'String 2 text', $result['items'][1]['singular'] );
		$this->assertEquals( 'String 3 text', $result['items'][2]['singular'] );
	}

	/**
	 * @test
	 */
	public function itGetsEntriesWithLimitAndOffset() {
		$arguments = new Arguments( [ self::EXISTING_FILE ], ['limit' => 3, 'offset' => 2] );
		$result    = ListEntries::getList( $arguments, $this->load() );

		$this->assertEquals( 5, $result['total'] );
		$this->assertCount( 3, $result['items'] );

		$this->assertEquals( 'String 3 text', $result['items'][0]['singular'] );
		$this->assertEquals( 'String 4 text', $result['items'][1]['singular'] );
		$this->assertEquals( 'String 5 text', $result['items'][2]['singular'] );
	}

	/**
	 * @test
	 */
	public function itSearchesInSingular() {
		$arguments = new Arguments( [ self::EXISTING_FILE ], ['search' => 'ng 4 text'] );
		$result    = ListEntries::getList( $arguments, $this->load() );

		$this->assertEquals( 1, $result['total'] );
		$this->assertCount( 1, $result['items'] );

		$this->assertEquals( 'String 4 text', $result['items'][0]['singular'] );
	}

	/**
	 * @test
	 */
	public function itSearchesInPlural() {
		$arguments = new Arguments( [ self::EXISTING_FILE ], ['search' => 'ng 2 text plural'] );
		$result    = ListEntries::getList( $arguments, $this->load() );

		$this->assertEquals( 1, $result['total'] );
		$this->assertCount( 1, $result['items'] );

		$this->assertEquals( 'String 2 text', $result['items'][0]['singular'] );
	}

	/**
	 * @test
	 */
	public function itSearchesInTranslations() {
		$arguments = new Arguments( [ self::EXISTING_FILE ], ['search' => 'ing 2 text plural translat'] );
		$result    = ListEntries::getList( $arguments, $this->load() );

		$this->assertEquals( 1, $result['total'] );
		$this->assertCount( 1, $result['items'] );

		$this->assertEquals( 'String 2 text', $result['items'][0]['singular'] );
	}

	private function load(): callable {
		return function ( $filePath ) {
			if ( $filePath !== self::EXISTING_FILE ) {
				return [];
			}

			return [
				'String 1 text' => (object) [
					'singular'     => 'String 1 text',
					'plural'       => null,
					'translations' => [
						'String 1 text translation',
					],
				],
				'String 2 text' => (object) [
					'singular'     => 'String 2 text',
					'plural'       => 'String 2 text plural',
					'translations' => [
						0 => 'String 2 text translation',
						2 => 'String 2 text plural translation',
					],
				],
				'String 3 text' => (object) [
					'singular'     => 'String 3 text',
					'plural'       => null,
					'translations' => [
						'String 3 text translation',
					],
				],
				'String 4 text' => (object) [
					'singular'     => 'String 4 text',
					'plural'       => null,
					'translations' => [
						'String 4 text translation',
					],
				],
				'String 5 text' => (object) [
					'singular'     => 'String 5 text',
					'plural'       => 'String 5 text plural',
					'translations' => [
						0 => 'String 5 text translation',
						2 => 'String 5 text plural translation 1',
						3 => 'String 5 text plural translation 2',
					],
				],
			];
		};
	}
}