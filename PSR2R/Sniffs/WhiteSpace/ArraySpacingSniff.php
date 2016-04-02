<?php

namespace PSR2R\Sniffs\WhiteSpace;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Tokens;

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
	 * @return void
	 */
	protected function checkBeginning(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$nextIndex = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);
		if ($nextIndex - $stackPtr === 1) {
			return;
		}
		if ($tokens[$nextIndex]['line'] !== $tokens[$stackPtr]['line']) {
			return;
		}

		$fix = $phpcsFile->addFixableError('No whitespace after opening bracket', $stackPtr, 'InvalidAfter');
		if ($fix) {
			$phpcsFile->fixer->replaceToken($nextIndex - 1, '');
		}
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 * @return void
	 */
	protected function checkEnding(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$previousIndex = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
		if ($stackPtr - $previousIndex === 1) {
			return;
		}
		if ($tokens[$previousIndex]['line'] !== $tokens[$stackPtr]['line']) {
			return;
		}

		// Let another sniffer take care of invalid commas
		if ($tokens[$previousIndex]['code'] === T_COMMA) {
			return;
		}

		$fix = $phpcsFile->addFixableError('No whitespace before closing bracket', $stackPtr, 'InvalidBefore');
		if ($fix) {
			$phpcsFile->fixer->replaceToken($previousIndex + 1, '');
		}
	}

}
