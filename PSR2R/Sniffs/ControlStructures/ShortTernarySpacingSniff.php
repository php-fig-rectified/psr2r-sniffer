<?php
namespace PSR2R\Sniffs\ControlStructures;

use PHP_CodeSniffer_File;

/**
 * Asserts no whitespace between short ternary operator (?:), which was introduced in PHP 5.3.
 */
class ShortTernarySpacingSniff extends \PSR2R\Tools\AbstractSniff {

	/**
	 * @inheritDoc
	 */
	public function register() {
		return [T_INLINE_ELSE];
	}

	/**
	 * @inheritDoc
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$previous = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
		if ($tokens[$previous]['code'] !== T_INLINE_THEN) {
			return;
		}

		if ($previous === ($stackPtr - 1)) {
			return;
		}

		$error = 'There must be no space between ? and :';
		$fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceInlineElse');
		if ($fix) {
			$phpcsFile->fixer->replaceToken($previous + 1, '');
		}
	}

}
