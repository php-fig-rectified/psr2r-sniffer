<?php

namespace Test;

use Some\Base\Exception;
use Psr\Log\LoggerInterface;
use Some\Namespace\ClassName;
use Some\Namespace\CustomException;
use Another\SerializableInterface;
use Some\CountableInterface;

// Test 1: new statement with FQCN
class NewStatement {
	public function test(): void {
		$obj = new ClassName();
	}
}

// Test 2: static call with FQCN
class StaticCall {
	public function test(): void {
		ClassName::staticMethod();
	}
}

// Test 3: instanceof with FQCN
class InstanceOfCheck {
	public function test(): void {
		$obj = new \stdClass();
		if ($obj instanceof ClassName) {
			// do something
		}
	}
}

// Test 4: catch with FQCN
class CatchBlock {
	public function test(): void {
		try {
			// something
		} catch (CustomException $e) {
			// handle
		}
	}
}

// Test 5: parameter type hint with FQCN
class ParameterTypeHint {
	public function test(ClassName $param): void {
	}
}

// Test 6: return type hint with FQCN
class ReturnTypeHint {
	public function test(): ClassName {
		return new ClassName();
	}
}

// Test 7: nullable parameter type with FQCN
class NullableParameter {
	public function test(?ClassName $param): void {
	}
}

// Test 8: nullable return type with FQCN
class NullableReturn {
	public function test(): ?ClassName {
		return null;
	}
}

// Test 9: Anonymous class implementing interface with FQCN
class AnonymousClassImplements {
	protected function test(): void {
		$logger = new class implements LoggerInterface {
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
		$obj = new class extends Exception {
		};
	}
}

// Anonymous class implementing multiple interfaces with FQCN
class AnonymousClassMultipleInterfaces {
	protected function test(): void {
		$obj = new class implements LoggerInterface, SerializableInterface {
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
class RegularClassImplements implements CountableInterface {
	public function count(): int {
		return 0;
	}
}

// Regular class extending class with FQCN
class RegularClassExtends extends Exception {
}
