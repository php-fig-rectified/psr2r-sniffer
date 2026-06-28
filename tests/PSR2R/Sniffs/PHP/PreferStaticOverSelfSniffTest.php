<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PSR2R\Test\PSR2R\Sniffs\PHP;

use PSR2R\Sniffs\PHP\PreferStaticOverSelfSniff;
use PSR2R\Test\TestCase;

class PreferStaticOverSelfSniffTest extends TestCase
{
    /**
     * @return void
     */
    public function testStaticOverSelfSniffer(): void
    {
        $this->assertSnifferFindsErrors(new PreferStaticOverSelfSniff(), 1);
    }

    /**
     * @return void
     */
    public function testStaticOverSelfFixer(): void
    {
        $this->assertSnifferCanFixErrors(new PreferStaticOverSelfSniff());
    }
}
