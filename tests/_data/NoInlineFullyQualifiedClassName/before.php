<?php

namespace Test;

// Anonymous class implementing interface with FQCN
class AnonymousClassImplements {
	protected function test(): void {
		$logger = new class implements \Psr\Log\LoggerInterface {
			public function emergency($message, array $context = []): void {}
			public function alert($message, array $context = []): void {}
			public function critical($message, array $context = []): void {}
			public function error($message, array $context = []): void {}
			public function warning($message, array $context = []): void {}
			public function notice($message, array $context = []): void {}
			public function info($message, array $context = []): void {}
			public function debug($message, array $context = []): void {}
			public function log($level, $message, array $context = []): void {}
		};
	}
}

// Anonymous class extending class with FQCN
class AnonymousClassExtends {
	protected function test(): void {
		$obj = new class extends \Some\Base\Exception {
		};
	}
}

// Anonymous class implementing multiple interfaces with FQCN
class AnonymousClassMultipleInterfaces {
	protected function test(): void {
		$obj = new class implements \Psr\Log\LoggerInterface, \Another\SerializableInterface {
			public function emergency($message, array $context = []): void {}
			public function alert($message, array $context = []): void {}
			public function critical($message, array $context = []): void {}
			public function error($message, array $context = []): void {}
			public function warning($message, array $context = []): void {}
			public function notice($message, array $context = []): void {}
			public function info($message, array $context = []): void {}
			public function debug($message, array $context = []): void {}
			public function log($level, $message, array $context = []): void {}
			public function serialize(): string { return ''; }
			public function unserialize($data): void {}
		};
	}
}

// Regular class implementing interface with FQCN
class RegularClassImplements implements \Some\CountableInterface {
	public function count(): int {
		return 0;
	}
}

// Regular class extending class with FQCN
class RegularClassExtends extends \Some\Base\Exception {
}
