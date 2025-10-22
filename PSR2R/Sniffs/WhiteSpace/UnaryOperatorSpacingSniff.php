<?php

namespace PSR2R\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * No whitespace should be between unary operator and variable. Also account for ~, @ and & operator.
 *
 * @author Mark Scherer
 * @license MIT
 */
class UnaryOperatorSpacingSniff implements Sniff {

	/**
	 * @inheritDoc
	 */
	public function register(): array {
		return [T_INC, T_DEC, T_MINUS, T_PLUS, T_NONE, T_ASPERAND, T_BITWISE_AND];
	}

	/**
	 * @inheritDoc
	 */
	public function process(File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$stackPtr]['code'] === T_PLUS) {
			// Skip for now, another sniff is reverting it
			return;
		}

		if ($tokens[$stackPtr]['code'] === T_ASPERAND || $tokens[$stackPtr]['code'] === T_NONE) {
			$nextIndex = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);

			if ($nextIndex - $stackPtr === 1) {
				return;
			}

			$fix = $phpcsFile->addFixableError('No whitespace should be between ' . $tokens[$stackPtr]['content']
				. ' operator and variable.', $stackPtr, 'NoWhitespace');
			if ($fix) {
				$phpcsFile->fixer->replaceToken($stackPtr + 1, '');
			}

			return;
		}

		if ($tokens[$stackPtr]['code'] === T_DEC || $tokens[$stackPtr]['code'] === T_INC) {
			$this->checkBefore($phpcsFile, $stackPtr);
			$this->checkAfter($phpcsFile, $stackPtr);

			return;
		}

		// Find the last syntax item to determine if this is an unary operator.
		$lastSyntaxItem = $phpcsFile->findPrevious(
			[T_WHITESPACE],
			$stackPtr - 1,
			$tokens[$stackPtr]['column'] * -1,
			true,
			null,
			true,
		);
		$operatorSuffixAllowed = in_array(
			$tokens[$lastSyntaxItem]['code'],
			[
				T_LNUMBER,
				T_DNUMBER,
				T_CLOSE_PARENTHESIS,
				T_CLOSE_CURLY_BRACKET,
				T_CLOSE_SQUARE_BRACKET,
				T_CLOSE_SHORT_ARRAY,
				T_VARIABLE,
				T_STRING,
			],
			false,
		);

		if ($operatorSuffixAllowed !== false
			|| $tokens[$stackPtr + 1]['code'] !== T_WHITESPACE
		) {
			return;
		}

		$error = 'A unary operator statement must not be followed by a space';
		$fix = $phpcsFile->addFixableError($error, $stackPtr, 'WrongSpace');
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->replaceToken($stackPtr + 1, '');
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $stackPtr
	 *
	 * @return void
	 */
	protected function checkBefore(File $phpcsFile, int $stackPtr): void {
		$tokens = $phpcsFile->getTokens();

		$prevIndex = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
		if ($tokens[$prevIndex]['code'] !== T_VARIABLE) {
			return;
		}

		if ($stackPtr - $prevIndex === 1) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'No whitespace should be between variable and incrementer.',
			$stackPtr,
			'WhitespaceIncrementer',
		);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->replaceToken($stackPtr - 1, '');
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $stackPtr
	 *
	 * @return void
	 */
	protected function checkAfter(File $phpcsFile, int $stackPtr): void {
		$tokens = $phpcsFile->getTokens();

		$nextIndex = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
		if ($tokens[$nextIndex]['code'] !== T_VARIABLE) {
			return;
		}

		if ($nextIndex - $stackPtr === 1) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'No whitespace should be between incrementer and variable.',
			$stackPtr,
			'WhitespaceVariable',
		);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->replaceToken($stackPtr + 1, '');
	}

}
