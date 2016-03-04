<?php

namespace PSR2R\Sniffs\WhiteSpace;

use PHP_CodeSniffer_File;
use PSR2R\Tools\AbstractSniff;

/**
 * Checks that the method declaration has correct spacing.
 *
 * @author Mark Scherer
 * @license MIT
 */
class MethodSpacingSniff extends AbstractSniff {

	/**
	 * @inheritDoc
	 */
	public function register() {
		return [T_FUNCTION];
	}

	/**
	 * @inheritDoc
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
		if ($tokens[$braceStartIndex]['code'] !== T_OPEN_CURLY_BRACKET) {
			return;
		}

		if ($braceStartIndex - $parenthesisEndIndex === 2 && $tokens[$braceStartIndex - 1]['content'] === ' ') {
			return;
		}

		//TODO: beginning and end of method: no newlines
	}

}
