<?php

namespace PSR2R\Sniffs\WhiteSpace;

use PHP_CodeSniffer_File;

/**
 * No whitespace should be at the beginning and end of an array.
 *
 * @author Mark Scherer
 * @license MIT
 */
class ArraySpacingSniff implements \PHP_CodeSniffer_Sniff {

	/**
	 * @inheritDoc
	 */
	public function register() {
		return [T_OPEN_SHORT_ARRAY];
	}

	/**
	 * @inheritDoc
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$endIndex = $tokens[$stackPtr]['bracket_closer'];
		$this->checkBeginning($phpcsFile, $stackPtr);
		$this->checkEnding($phpcsFile, $endIndex);
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 */
	protected function checkBeginning(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$nextIndex = $phpcsFile->findNext(\PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);
		if ($nextIndex - $stackPtr === 1) {
			return;
		}
		if ($tokens[$nextIndex]['line'] !== $tokens[$stackPtr]['line']) {
			return;
		}

		$fix = $phpcsFile->addFixableError('No whitespace after opening bracket', $stackPtr);
		if ($fix) {
			$phpcsFile->fixer->replaceToken($nextIndex - 1, '');
		}
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 */
	protected function checkEnding(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$previousIndex = $phpcsFile->findPrevious(\PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1), null, true);
		if ($stackPtr - $previousIndex === 1) {
			return;
		}
		if ($tokens[$previousIndex]['line'] !== $tokens[$stackPtr]['line']) {
			return;
		}

		$fix = $phpcsFile->addFixableError('No whitespace before closing bracket', $stackPtr);
		if ($fix) {
			$phpcsFile->fixer->replaceToken($previousIndex + 1, '');
		}
	}

}
