<?php

namespace PSR2R\Sniffs\WhiteSpace;

use PHP_CodeSniffer_Sniff;

/**
 * Ensures no whitespaces and one whitespace is placed around each comma.
 *
 * @author Mark Scherer
 * @license MIT
 */
class CommaSpacingSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * @inheritDoc
	 */
	public function register() {
		return [T_COMMA];
	}

	/**
	 * @inheritDoc
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

		if ($tokens[$next]['code'] !== T_WHITESPACE && ($next !== $stackPtr + 2)) {
			// Last character in a line is ok.
			if ($tokens[$next]['line'] === $tokens[$stackPtr]['line']) {
				$error = 'Missing space after comma';
				$fix = $phpcsFile->addFixableError($error, $next);
				if ($fix) {
					$phpcsFile->fixer->addContent($stackPtr, ' ');
				}
			}
		}

		$previous = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);

		if ($tokens[$previous]['code'] !== T_WHITESPACE && ($previous !== $stackPtr - 1)) {
			if ($tokens[$previous]['code'] === T_COMMA) {
				return;
			}

			$error = 'Space before comma, expected none, though';
			$fix = $phpcsFile->addFixableError($error, $previous);
			if ($fix) {
				$phpcsFile->fixer->replaceToken($previous + 1, '');
			}
		}
	}

}
