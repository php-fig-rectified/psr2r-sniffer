<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PSR2R\Test\PSR2R\Sniffs\Namespaces;

use PSR2R\Sniffs\Namespaces\NoInlineFullyQualifiedClassNameSniff;
use PSR2R\Test\TestCase;

class NoInlineFullyQualifiedClassNameSniffTest extends TestCase
{
	/**
	 * @return void
	 */
	public function testNoInlineFullyQualifiedClassNameSniffer(): void
	{
		$this->assertSnifferFindsErrors(new NoInlineFullyQualifiedClassNameSniff(), 11);
	}

	/**
	 * @return void
	 */
	public function testNoInlineFullyQualifiedClassNameFixer(): void
	{
		$this->assertSnifferCanFixErrors(new NoInlineFullyQualifiedClassNameSniff());
	}
}
