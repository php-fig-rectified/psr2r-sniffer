<?php

namespace PSR2R\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Always have an extra new line at the beginning and end of a classy body.
 *
 * @author Mark Scherer
 * @license MIT
 */
class EmptyEnclosingLineSniff implements Sniff {

	/**
	 * @inheritDoc
	 */
	public function register(): array {
		return [
			T_CLASS,
			T_INTERFACE,
			T_TRAIT,
		];
	}

	/**
	 * @inheritDoc
	 */
	public function process(File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		if (!isset($tokens[$stackPtr]['scope_opener'])) {
			$error = 'Possible parse error: %s missing opening or closing brace';
			$errorData = [strtolower($tokens[$stackPtr]['content'])];
			$phpcsFile->addWarning($error, $stackPtr, 'MissingBrace', $errorData);

			return;
		}

		$curlyBraceStartIndex = $tokens[$stackPtr]['scope_opener'];
		$curlyBraceEndIndex = $tokens[$stackPtr]['scope_closer'];

		$lastContentIndex = $phpcsFile->findPrevious(T_WHITESPACE, $curlyBraceEndIndex - 1, $stackPtr, true);
		$this->checkBeginning($phpcsFile, $stackPtr, $curlyBraceStartIndex, $curlyBraceEndIndex, $lastContentIndex);
		$this->checkEnd($phpcsFile, $stackPtr, $curlyBraceStartIndex, $curlyBraceEndIndex, $lastContentIndex);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $stackPtr
	 * @param int $curlyBraceStartIndex
	 * @param int $curlyBraceEndIndex
	 * @param int $lastContentIndex
	 *
	 * @return void
	 */
	protected function checkEnd(File $phpcsFile, int $stackPtr, int $curlyBraceStartIndex, int $curlyBraceEndIndex, int $lastContentIndex): void {
		$tokens = $phpcsFile->getTokens();

		if ($lastContentIndex === $curlyBraceStartIndex) {
			// Single new line for empty classes
			if ($tokens[$curlyBraceEndIndex]['line'] === $tokens[$curlyBraceStartIndex]['line'] + 1) {
				return;
			}

			$error = 'Closing brace of an empty %s must have a single new line between curly brackets';
			$errorData = [strtolower($tokens[$stackPtr]['content'])];
			$fix = $phpcsFile->addFixableError($error, $curlyBraceEndIndex, 'CloseBraceNewLine', $errorData);
			if ($fix === true) {
				if ($curlyBraceEndIndex - $curlyBraceStartIndex === 1) {
					$phpcsFile->fixer->addNewline($curlyBraceStartIndex);
				} else {
					$phpcsFile->fixer->replaceToken($curlyBraceStartIndex + 1, '');
					$phpcsFile->fixer->addNewline($curlyBraceStartIndex + 1);
				}
			}

			return;
		}

		$contentLine = $tokens[$lastContentIndex]['line'];
		$braceLine = $tokens[$curlyBraceEndIndex]['line'];

		if ($braceLine !== $contentLine + 2) {
			$phpcsFile->recordMetric($stackPtr, 'Class closing brace placement', 'lines');
			$error = 'Closing brace of a %s must have a new line between itself and the last content.';
			$errorData = [strtolower($tokens[$stackPtr]['content'])];
			$fix = $phpcsFile->addFixableError($error, $curlyBraceEndIndex, 'CloseBraceNewLine2', $errorData);
			if ($fix === true) {
				if ($braceLine < $contentLine + 2) {
					$phpcsFile->fixer->addNewlineBefore($curlyBraceEndIndex);
				} else {
					$phpcsFile->fixer->replaceToken($curlyBraceEndIndex - 1, '');
				}
			}
		}
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $stackPtr
	 * @param int $curlyBraceStartIndex
	 * @param int $curlyBraceEndIndex
	 * @param int $lastContentIndex
	 *
	 * @return void
	 */
	public function checkBeginning(File $phpcsFile, int $stackPtr, int $curlyBraceStartIndex, int $curlyBraceEndIndex, int $lastContentIndex): void {
		$tokens = $phpcsFile->getTokens();

		if ($lastContentIndex === $curlyBraceStartIndex) {
			// End part takes care of this already
			return;
		}

		$firstContentIndex = $phpcsFile->findNext(T_WHITESPACE, $curlyBraceStartIndex + 1, $lastContentIndex, true);

		$contentLine = $tokens[$firstContentIndex]['line'];
		$braceLine = $tokens[$curlyBraceStartIndex]['line'];

		if ($contentLine !== $braceLine + 2) {
			$phpcsFile->recordMetric($stackPtr, 'Class opening brace placement', 'lines');
			$error = 'Opening brace of a %s must have a new line between itself and the first content.';
			$errorData = [strtolower($tokens[$stackPtr]['content'])];
			$fix = $phpcsFile->addFixableError($error, $curlyBraceStartIndex, 'OpenBraceNewLine', $errorData);
			if ($fix === true) {
				$phpcsFile->fixer->beginChangeset();

				if ($contentLine < $braceLine + 2) {
					$phpcsFile->fixer->addNewline($curlyBraceStartIndex);
				} else {
					for ($i = $curlyBraceStartIndex + 1; $i < $firstContentIndex - 1; $i++) {
						$phpcsFile->fixer->replaceToken($i, '');
					}
				}

				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
