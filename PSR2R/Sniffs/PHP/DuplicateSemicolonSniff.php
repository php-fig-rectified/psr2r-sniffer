<?php

namespace PSR2R\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Makes sure there is no duplicate semicolon.
 *
 * @author Mark Scherer
 * @license MIT
 */
class DuplicateSemicolonSniff implements Sniff {

	/**
	 * @inheritDoc
	 */
	public function register(): array {
		return [T_SEMICOLON];
	}

	/**
	 * @inheritDoc
	 */
	public function process(File $phpcsFile, int $stackPtr): void {
		$tokens = $phpcsFile->getTokens();

		$previousIndex = $phpcsFile->findPrevious(Tokens::$emptyTokens, $stackPtr - 1, null, true);
		if (!$previousIndex || $tokens[$previousIndex]['code'] !== T_SEMICOLON) {
			return;
		}

		$possibleForIndex = $phpcsFile->findPrevious(T_FOR, $previousIndex - 1);
		if ($possibleForIndex && $tokens[$possibleForIndex]['parenthesis_closer'] > $stackPtr) {
			return;
		}

		$error = 'Double semicolon found';
		$fix = $phpcsFile->addFixableError($error, $stackPtr, 'DoubleSemicolon');
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		$phpcsFile->fixer->replaceToken($stackPtr, '');
		for ($i = $stackPtr; $i > $previousIndex; --$i) {
			if ($tokens[$i]['code'] !== T_WHITESPACE) {
				continue;
			}

			$phpcsFile->fixer->replaceToken($i, '');
		}

		$phpcsFile->fixer->endChangeset();
	}

}
