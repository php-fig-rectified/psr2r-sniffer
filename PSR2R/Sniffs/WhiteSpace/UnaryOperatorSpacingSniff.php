<?php

namespace PSR2R\Sniffs\WhiteSpace;

use PHP_CodeSniffer_File;

/**
 * No whitespace should be between unary operator and variable. Also account for ~, @ and & operator.
 *
 * @author Mark Scherer
 * @license MIT
 */
class UnaryOperatorSpacingSniff implements \PHP_CodeSniffer_Sniff {

	/**
	 * @inheritDoc
	 */
	public function register() {
		return [T_INC, T_DEC, T_MINUS, T_PLUS, T_NONE, T_ASPERAND, T_BITWISE_AND];
	}

	/**
	 * @inheritDoc
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$stackPtr]['code'] === T_PLUS) {
			// Skip for now, another sniff is reverting it
			return;
		}

		if ($tokens[$stackPtr]['code'] === T_ASPERAND || $tokens[$stackPtr]['code'] === T_NONE) {
			$nextIndex = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

			if ($nextIndex - $stackPtr === 1) {
				return;
			}

			$fix = $phpcsFile->addFixableError('No whitespace should be between ' . $tokens[$stackPtr]['content'] . ' operator and variable.', $stackPtr);
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
			($stackPtr - 1),
			(($tokens[$stackPtr]['column']) * -1),
			true,
			null,
			true
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
			]
		);

		if ($operatorSuffixAllowed === false
			&& $tokens[($stackPtr + 1)]['code'] === T_WHITESPACE
		) {
			$error = 'A unary operator statement must not be followed by a space';
			$fix = $phpcsFile->addFixableError($error, $stackPtr, 'WrongSpace');
			if ($fix) {
				$phpcsFile->fixer->replaceToken(($stackPtr + 1), '');
			}
		}
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 * @return void
	 */
	protected function checkBefore(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$prevIndex = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
		if ($tokens[$prevIndex]['code'] === T_VARIABLE) {
			if ($stackPtr - $prevIndex === 1) {
				return;
			}

			$fix = $phpcsFile->addFixableError('No whitespace should be between variable and incrementor.', $stackPtr);
			if ($fix) {
				$phpcsFile->fixer->replaceToken($stackPtr - 1, '');
			}
		}
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr
	 * @return void
	 */
	protected function checkAfter(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$nextIndex = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
		if ($tokens[$nextIndex]['code'] === T_VARIABLE) {
			if ($nextIndex - $stackPtr === 1) {
				return;
			}

			$fix = $phpcsFile->addFixableError('No whitespace should be between incrementor and variable.', $stackPtr);
			if ($fix) {
				$phpcsFile->fixer->replaceToken($stackPtr + 1, '');
			}
		}
	}

}
