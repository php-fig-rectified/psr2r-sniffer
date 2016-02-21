<?php

namespace PSR2R\Sniffs\PHP;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Tokens;
use PSR2R\Tools\AbstractSniff;

/**
 * Remove trailing commas in list function calls.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Mark Scherer
 * @license MIT
 */
class ListCommaSniff extends AbstractSniff {

	/**
	 * @inheritDoc
	 */
	public function register() {
		return [T_LIST];
	}

	/**
	 * @inheritDoc
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$openIndex = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, $stackPtr + 1, null, true);
		$closeIndex = $tokens[$openIndex]['parenthesis_closer'];

		$markIndex = null;
		$prevIndex = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, $closeIndex - 1, null, true);
		while ($tokens[$prevIndex]['code'] === T_COMMA) {
			$markIndex = $prevIndex;
			$prevIndex = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, $prevIndex - 1, null, true);
		}
		if ($markIndex !== null) {
			$fix = $phpcsFile->addFixableError('Superflouos commas in list', $markIndex);
			if ($fix) {
				$this->clearRange(
					$phpcsFile,
					$markIndex,
					$closeIndex - 1
				);
			}
		}
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $startIndex
	 * @param int $endIndex
	 * @return void
	 */
	protected function clearRange(PHP_CodeSniffer_File $phpcsFile, $startIndex, $endIndex) {
		for ($i = $startIndex; $i <= $endIndex; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}
	}

}
