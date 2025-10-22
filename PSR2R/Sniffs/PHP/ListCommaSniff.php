<?php

namespace PSR2R\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
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
	public function register(): array {
		return [T_LIST];
	}

	/**
	 * @inheritDoc
	 */
	public function process(File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$openIndex = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true);
		$closeIndex = $tokens[$openIndex]['parenthesis_closer'];

		$markIndex = null;
		$prevIndex = $phpcsFile->findPrevious(Tokens::$emptyTokens, $closeIndex - 1, null, true);
		while ($tokens[$prevIndex]['code'] === T_COMMA) {
			$markIndex = $prevIndex;
			$prevIndex = $phpcsFile->findPrevious(Tokens::$emptyTokens, $prevIndex - 1, null, true);
		}
		if ($markIndex === null) {
			return;
		}

		$fix = $phpcsFile->addFixableError('Superfluous commas in list', $markIndex, 'ExtraCommaList');
		if (!$fix) {
			return;
		}

		$this->clearRange(
			$phpcsFile,
			$markIndex,
			$closeIndex - 1,
		);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $startIndex
	 * @param int $endIndex
	 *
	 * @return void
	 */
	protected function clearRange(File $phpcsFile, int $startIndex, int $endIndex): void {
		for ($i = $startIndex; $i <= $endIndex; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}
	}

}
