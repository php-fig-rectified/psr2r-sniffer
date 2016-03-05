<?php

namespace PSR2R\Sniffs\WhiteSpace;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Tokens;

/**
 * No whitespace should be between cast and variable. Also account for implicit casts (!).
 *
 * @author Mark Scherer
 * @license MIT
 */
class CastSpacingSniff implements \PHP_CodeSniffer_Sniff {

	/**
	 * @inheritDoc
	 */
	public function register() {
		return array_merge(PHP_CodeSniffer_Tokens::$castTokens, [T_BOOLEAN_NOT]);
	}

	/**
	 * @inheritDoc
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$nextIndex = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

		if ($nextIndex - $stackPtr === 1) {
			return;
		}

		// Skip for !! casts, other sniffers take care of that
		$prevIndex = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
		if ($tokens[$prevIndex]['code'] === T_BOOLEAN_NOT) {
			return;
		}

		$fix = $phpcsFile->addFixableError('No whitespace should be between cast and variable.', $stackPtr);
		if ($fix) {
			$phpcsFile->fixer->replaceToken($stackPtr + 1, '');
		}
	}

}
