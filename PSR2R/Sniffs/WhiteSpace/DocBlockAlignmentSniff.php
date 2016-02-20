<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://pear.php.net/package/PHP_CodeSniffer_CakePHP
 * @since         CakePHP CodeSniffer 1.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace PSR2R\Sniffs\WhiteSpace;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;
use PSR2R\Tools\AbstractSniff;

/**
 * Ensures doc block alignment with its code.
 */
class DocBlockAlignmentSniff extends AbstractSniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return [T_DOC_COMMENT_OPEN_TAG];
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int              $stackPtr  The position of the current token in the
	 *                                        stack passed in $tokens.
	 * @return void
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();
		$leftWall = [
			T_CLASS,
			T_NAMESPACE,
			T_INTERFACE,
			T_TRAIT,
			T_USE
		];
		$oneIndentation = [
			T_FUNCTION,
			T_VARIABLE,
			T_CONST
		];
		$allTokens = array_merge($leftWall, $oneIndentation);
		$isNotFlatFile = $phpcsFile->findNext(T_NAMESPACE, 0);
		$nextIndex = $phpcsFile->findNext($allTokens, $stackPtr + 1);

		$expectedColumn = $tokens[$nextIndex]['column'];
		$firstNonWhitespaceIndex = $this->findFirstNonWhitespaceInLine($phpcsFile, $nextIndex);
		if ($firstNonWhitespaceIndex) {
			$expectedColumn = $tokens[$firstNonWhitespaceIndex]['column'];
		}

		// We should allow for tabs and spaces
		$expectedColumnAdjusted = $expectedColumn;
		if ($expectedColumn === 2) {
			$expectedColumnAdjusted = 5;
		} elseif ($expectedColumn === 5) {
			$expectedColumnAdjusted = 2;
		}

		if ($nextIndex) {
			$isNotWalled = (in_array($tokens[$nextIndex]['code'], $leftWall) && $tokens[$stackPtr]['column'] !== 1);
			$isNotIndented = false;
			if ($isNotFlatFile) {
				$isNotIndented = (in_array($tokens[$nextIndex]['code'], $oneIndentation) && $tokens[$stackPtr]['column'] !== $expectedColumn && $tokens[$stackPtr]['column'] !== $expectedColumnAdjusted);
			}
			if ($isNotWalled || $isNotIndented) {
				$fix = $phpcsFile->addFixableError('Expected docblock to be aligned with code.', $stackPtr, 'NotAllowed');
				if ($fix) {
					$docBlockEndIndex = $tokens[$stackPtr]['comment_closer'];

					if ($isNotWalled) {
						$prevIndex = $stackPtr - 1;
						if ($tokens[$prevIndex]['code'] !== T_WHITESPACE) {
							return;
						}

						$phpcsFile->fixer->beginChangeset();

						$this->outdent($phpcsFile, $prevIndex);

						for ($i = $stackPtr; $i <= $docBlockEndIndex; $i++) {
							if (!$this->isGivenKind(T_DOC_COMMENT_WHITESPACE, $tokens[$i]) || $tokens[$i]['column'] !== 1) {
								continue;
							}
							$this->outdent($phpcsFile, $i);
						}
						$phpcsFile->fixer->endChangeset();
						return;
					}

					if ($isNotIndented) {
						// + means too much indentation (we need to outdend), - means not enough indentation (needs indenting)
						if ($tokens[$stackPtr]['column'] < $expectedColumnAdjusted) {
							$diff = $tokens[$stackPtr]['column'] - $expectedColumn;
						} else {
							$diff = ($tokens[$stackPtr]['column'] - $expectedColumnAdjusted) / 4;
						}

						$phpcsFile->fixer->beginChangeset();

						$prevIndex = $stackPtr - 1;
						if ($diff < 0 && $tokens[$prevIndex]['line'] !== $tokens[$stackPtr]['line']) {
							$phpcsFile->fixer->addContentBefore($stackPtr, str_repeat("\t", -$diff));
						} else {
							$this->outdent($phpcsFile, $prevIndex);
						}

						for ($i = $stackPtr; $i <= $docBlockEndIndex; $i++) {
							if (!$this->isGivenKind(T_DOC_COMMENT_WHITESPACE, $tokens[$i]) || $tokens[$i]['column'] !== 1) {
								continue;
							}
							if ($diff < 0) {
								$this->indent($phpcsFile, $i, -$diff);
							} else {
								$this->outdent($phpcsFile, $i, $diff);
							}
						}
						$phpcsFile->fixer->endChangeset();
					}
				}
			}
		}
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $index
	 * @return int|null
	 */
	protected function findFirstNonWhitespaceInLine(PHP_CodeSniffer_File $phpcsFile, $index) {
		$tokens = $phpcsFile->getTokens();

		$firstIndex = $index;
		while (!empty($tokens[$firstIndex - 1]) && $tokens[$firstIndex - 1]['line'] === $tokens[$index]['line']) {
			$firstIndex--;
		}

		return $phpcsFile->findNext(T_WHITESPACE, $firstIndex, $index, true);
	}

}
