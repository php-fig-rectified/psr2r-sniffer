<?php

namespace PSR2R\Sniffs\WhiteSpace;

use PHP_CodeSniffer_Tokens;

/**
 * No whitespace should be between cast and variable. Also account for implicit casts.
 */
class NoSpaceAfterCastSniff implements \PHP_CodeSniffer_Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array_merge(PHP_CodeSniffer_Tokens::$castTokens, [T_BOOLEAN_NOT, T_NONE, T_INC, T_DEC, T_ASPERAND]);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
	 * @param int $stackPtr The position of the current token
	 *    in the stack passed in $tokens.
	 * @return void
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$nextIndex = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

		if ($nextIndex - $stackPtr === 1) {
			return;
		}

		$fix = $phpcsFile->addFixableError('No whitespace should be between cast and variable.', $stackPtr);
		if ($fix) {
			$phpcsFile->fixer->replaceToken($stackPtr + 1, '');
		}
	}

}
