<?php

namespace MOFilesBrowser;

final class Arguments {

	private string $filename;

	private int    $limit;
	private int    $offset;
	private string $search;

	public function __construct( array $args, array $assocArray ) {
		[ $this->filename ] = $args;

		$this->limit  = $assocArray['limit'] ?? 20;
		$this->offset = $assocArray['offset'] ?? 0;
		$this->search = $assocArray['search'] ?? '';
	}

	public function getFilename(): string {
		return $this->filename;
	}

	public function getLimit(): int {
		return $this->limit;
	}

	public function getOffset(): int {
		return $this->offset;
	}

	public function getSearch(): string {
		return $this->search;
	}
}