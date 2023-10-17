<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PSR2R\Test\PSR2R\Sniffs\PHP;

use PSR2R\Test\TestCase;
use PSR2R\Sniffs\PHP\DuplicateSemicolonSniff;

class DuplicateSemicolonSniffTest extends TestCase
{
    /**
     * @return void
     */
    public function testDocBlockConstSniffer(): void
    {
        $this->assertSnifferFindsErrors(new DuplicateSemicolonSniff(), 2);
    }

    /**
     * @return void
     */
    public function testDocBlockConstFixer(): void
    {
        $this->assertSnifferCanFixErrors(new DuplicateSemicolonSniff());
    }
}
