<?php

namespace PSR2R\Sniffs\PHP;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Tokens;

/**
 * Makes sure there is no duplicate semicolon.
 *
 * @author Mark Scherer
 * @license MIT
 */
class DuplicateSemicolonSniff implements \PHP_CodeSniffer_Sniff {

	/**
	 * @inheritDoc
	 */
	public function register() {
		return [T_SEMICOLON];
	}

	/**
	 * @inheritDoc
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$previousIndex = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1), null, true);
		if (!$previousIndex || $tokens[$previousIndex]['code'] !== T_SEMICOLON) {
			return;
		}

		$possibleForIndex = $phpcsFile->findPrevious(T_FOR, ($previousIndex - 1));
		if ($possibleForIndex && $tokens[$possibleForIndex]['parenthesis_closer'] > $stackPtr) {
			return;
		}

		$error = 'Double semicolon found';
		$fix = $phpcsFile->addFixableError($error, $stackPtr);
		if ($fix) {
			$phpcsFile->fixer->beginChangeset();

			$phpcsFile->fixer->replaceToken($stackPtr, '');
			for ($i = $stackPtr; $i > $previousIndex; --$i) {
				if ($tokens[$i]['code'] === T_WHITESPACE) {
					$phpcsFile->fixer->replaceToken($i, '');
				}
			}

			$phpcsFile->fixer->endChangeset();
		}
	}

}
