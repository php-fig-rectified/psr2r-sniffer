<?php

namespace PSR2R\Sniffs\Commenting;

use PHP_CodeSniffer_File;

/**
 * Ensures unnecessary comments, especially //end ... ones are removed.
 */
class NoControlStructureEndCommentSniff implements \PHP_CodeSniffer_Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return [T_COMMENT];
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token
	 *                                        in the stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$possibleCurlyBracket = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), 0, true);
		if ($possibleCurlyBracket === false || $tokens[$possibleCurlyBracket]['type'] !== 'T_CLOSE_CURLY_BRACKET') {
			return;
		}

		$content = $tokens[$stackPtr]['content'];
		if (strpos($content, '//end ') !== 0) {
			return;
		}

		$error = 'The unnecessary end comment must be removed';
		$fix = $phpcsFile->addFixableError($error, $stackPtr, 'Unnecessary');
		if ($fix === true) {
			$phpcsFile->fixer->replaceToken($stackPtr, preg_replace('/[^\s]/', '', $content));
		}
	}

}
