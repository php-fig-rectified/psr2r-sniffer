<?php declare(strict_types = 1);

namespace PSR2R;

class FixMe {

	public const VALUE = 'value';

	public function value(): string {
		return self::VALUE;
	}

}

final class KeepMe {

	public const VALUE = 'value';

	public function value(): string {
		return self::VALUE;
	}

}
