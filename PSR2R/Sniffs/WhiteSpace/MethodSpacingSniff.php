<?php

namespace PSR2R\Sniffs\WhiteSpace;

use PHP_CodeSniffer_File;
use PSR2R\Tools\AbstractSniff;

/**
 * Checks that the method declaration has correct spacing.
 */
class MethodSpacingSniff extends AbstractSniff {

	/**
	 * Registers the tokens that this sniff wants to listen for.
	 *
	 * @return int[]
	 */
	public function register() {
		return [T_FUNCTION];
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where the
	 *                                        token was found.
	 * @param int $stackPtr The position in the PHP_CodeSniffer
	 *                                        file's token stack where the token
	 *                                        was found.
	 *
	 * @return void|int Optionally returns a stack pointer. The sniff will not be
	 *                  called again on the current file until the returned stack
	 *                  pointer is reached. Return (count($tokens) + 1) to skip
	 *                  the rest of the file.
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$stringIndex = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
		if ($tokens[$stringIndex]['code'] !== T_STRING) {
			return;
		}

		$parenthesisIndex = $phpcsFile->findNext(T_WHITESPACE, ($stringIndex + 1), null, true);
		if ($tokens[$parenthesisIndex]['type'] !== 'T_OPEN_PARENTHESIS') {
			return;
		}

		if ($parenthesisIndex - $stringIndex !== 1) {
			$error = 'There should be no space between method name and opening parenthesis';
			$fix = $phpcsFile->addFixableError($error, $stackPtr, 'ContentBeforeOpen');
			if ($fix === true) {
				$phpcsFile->fixer->replaceToken($parenthesisIndex - 1, '');
			}
		}

		$parenthesisEndIndex = $tokens[$parenthesisIndex]['parenthesis_closer'];

		$braceStartIndex = $phpcsFile->findNext(T_WHITESPACE, ($parenthesisEndIndex + 1), null, true);
		if ($tokens[$braceStartIndex]['type'] !== 'T_OPEN_CURLY_BRACKET') {
			return;
		}

		if ($braceStartIndex - $parenthesisEndIndex === 2 && $tokens[$braceStartIndex - 1]['content'] === ' ') {
			return;
		}

		$error = 'There should be a single space between closing parenthesis and opening curly brace';
		$fix = $phpcsFile->addFixableError($error, $parenthesisEndIndex, 'ContentAfterOpen');
		if ($fix === true) {
			if ($braceStartIndex - $parenthesisEndIndex === 1) {
				$phpcsFile->fixer->addContent($parenthesisEndIndex, ' ');
			} else {
				$phpcsFile->fixer->replaceToken($braceStartIndex - 1, ' ');
			}
		}
	}

}
