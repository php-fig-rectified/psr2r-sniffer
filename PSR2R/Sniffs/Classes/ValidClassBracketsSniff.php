<?php
namespace PSR2R\Sniffs\Classes;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;

/**
 * Ensures curly brackets are on the same line as the Class declaration
 */
class ValidClassBracketsSniff implements PHP_CodeSniffer_Sniff {

	/**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
	public function register() {
		return [T_CLASS];
	}

	/**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$found = $phpcsFile->findNext(T_OPEN_CURLY_BRACKET, $stackPtr);
		if ($tokens[$found - 1]['code'] !== T_WHITESPACE) {
			$error = 'Expected 1 space after class declaration, found 0';
			$fix = $phpcsFile->addFixableError($error, $found - 1, 'InvalidSpacing', []);
			if ($fix === true && $phpcsFile->fixer->enabled === true) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addContent($found - 1, ' ');
				$phpcsFile->fixer->endChangeset();
			}

			return;
		}

		if ($tokens[$found - 1]['content'] !== ' ') {
			$error = 'Expected 1 space before curly opening bracket';
			$phpcsFile->addError($error, $found - 1, 'InvalidBracketPlacement', []);
		}

		if (strlen($tokens[$found - 1]['content']) > 1 || $tokens[$found - 2]['code'] === T_WHITESPACE) {
			$error = 'Expected 1 space after class declaration, found ' . strlen($tokens[$found - 1]['content']);
			$fix = $phpcsFile->addFixableError($error, $found - 1, 'InvalidSpacing', []);
			if ($fix === true && $phpcsFile->fixer->enabled === true) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken($found - 1, ' ');
				$phpcsFile->fixer->endChangeset();
			}
		}
	}
}
