<?php
namespace PSR2R\Sniffs\ControlStructures;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Tokens;

/**
 * Asserts no whitespace between ternary operator (? and :) and surroundings.
 */
class TernarySpacingSniff extends \PSR2R\Tools\AbstractSniff {

	/**
	 * @inheritDoc
	 */
	public function register() {
		return [T_INLINE_THEN];
	}

	/**
	 * @inheritDoc
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$this->assertSpaceBefore($phpcsFile, $stackPtr);
		$this->checkAfter($phpcsFile, $stackPtr);
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 * @return void
	 */
	protected function checkAfter(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$inlineElseIndex = $phpcsFile->findNext(T_INLINE_ELSE, $stackPtr + 1);
		if (!$inlineElseIndex) {
			return;
		}

		// Skip for short ternary
		$prevIndex = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, $inlineElseIndex - 1, null, true);
		if ($prevIndex === $stackPtr) {
			return;
		}

		$this->assertSpaceAfter($phpcsFile, $stackPtr);

		$this->assertSpaceBefore($phpcsFile, $inlineElseIndex);
		$this->assertSpaceAfter($phpcsFile, $inlineElseIndex);
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 * @return void
	 */
	protected function assertSpaceBefore(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$previous = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
		if ($stackPtr - $previous > 1) {
			return;
		}

		$tokens = $phpcsFile->getTokens();
		$content = $tokens[$stackPtr]['content'];
		$error = 'There must be a single space before ternary operator part `' . $content . '`';
		$fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceBeforeInlineThen');
		if ($fix) {
			$phpcsFile->fixer->addContentBefore($stackPtr, ' ');
		}
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 * @return void
	 */
	protected function assertSpaceAfter(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$nextIndex = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
		if ($nextIndex - $stackPtr > 1) {
			return;
		}

		$tokens = $phpcsFile->getTokens();
		$content = $tokens[$stackPtr]['content'];
		$error = 'There must be a single space after ternary operator part `' . $content . '`';
		$fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterInlineThen');
		if ($fix) {
			$phpcsFile->fixer->addContent($stackPtr, ' ');
		}
	}

}
