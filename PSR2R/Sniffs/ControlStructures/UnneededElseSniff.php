<?php

namespace PSR2R\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PSR2R\Tools\AbstractSniff;

/**
 * Too much else is considered a code-smell and can often be resolved by returning early.
 *
 * @author Mark Scherer
 * @license MIT
 */
class UnneededElseSniff extends AbstractSniff {

	/**
	 * @inheritDoc
	 */
	public function register(): array {
		return [T_ELSE, T_ELSEIF];
	}

	/**
	 * @inheritDoc
	 */
	public function process(File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		// Check that ALL preceding branches in the if/elseif chain have early exits
		if (!$this->allPrecedingBranchesHaveEarlyExits($phpcsFile, $stackPtr)) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Unneeded ' . $tokens[$stackPtr]['type'] . ' detected.',
			$stackPtr,
			'UnneededElse',
		);
		if (!$fix) {
			return;
		}

		if ($tokens[$stackPtr]['code'] === T_ELSEIF) {
			$this->fixElseIfToIf($phpcsFile, $stackPtr);

			return;
		}

		if (empty($tokens[$stackPtr]['scope_opener']) || empty($tokens[$stackPtr]['scope_closer'])) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		$prevIndex = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
		$line = $tokens[$prevIndex]['line'];

		for ($i = $prevIndex + 1; $i < $stackPtr; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$phpcsFile->fixer->addNewline($prevIndex);

		$phpcsFile->fixer->replaceToken($stackPtr, '');

		$nextScopeStartIndex = $tokens[$stackPtr]['scope_opener'];
		$nextScopeEndIndex = $tokens[$stackPtr]['scope_closer'];

		for ($i = $stackPtr + 1; $i < $nextScopeStartIndex; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$prevEndIndex = $phpcsFile->findPrevious(T_WHITESPACE, $nextScopeEndIndex - 1, null, true);

		$phpcsFile->fixer->replaceToken($nextScopeStartIndex, '');
		$phpcsFile->fixer->replaceToken($nextScopeEndIndex, '');

		for ($i = $prevEndIndex + 1; $i < $nextScopeEndIndex; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		// Fix indentation
		for ($i = $nextScopeStartIndex + 1; $i < $prevEndIndex; $i++) {
			if ($tokens[$i]['line'] === $line || $tokens[$i]['type'] !== 'T_WHITESPACE') {
				continue;
			}
			$this->outdent($phpcsFile, $i);
		}

		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * Check if all preceding branches in the if/elseif chain have early exits.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $stackPtr
	 *
	 * @return bool
	 */
	protected function allPrecedingBranchesHaveEarlyExits(File $phpcsFile, int $stackPtr): bool {
		$tokens = $phpcsFile->getTokens();

		// Walk backwards through the if/elseif chain
		$currentPtr = $stackPtr;

		while (true) {
			$prevScopeEndIndex = $phpcsFile->findPrevious(T_WHITESPACE, $currentPtr - 1, null, true);
			if (!$prevScopeEndIndex || empty($tokens[$prevScopeEndIndex]['scope_opener'])) {
				return false;
			}

			$scopeStartIndex = $tokens[$prevScopeEndIndex]['scope_opener'];
			$prevScopeLastTokenIndex = $phpcsFile->findPrevious(T_WHITESPACE, $prevScopeEndIndex - 1, null, true);

			if ($tokens[$prevScopeLastTokenIndex]['type'] !== 'T_SEMICOLON') {
				return false;
			}

			// Check if this branch has an early exit
			$returnEarlyIndex = $phpcsFile->findPrevious(
				[T_RETURN, T_CONTINUE, T_BREAK],
				$prevScopeLastTokenIndex - 1,
				$scopeStartIndex + 1,
			);

			if (!$returnEarlyIndex) {
				return false;
			}

			// Make sure it's the last statement (no other semicolons after the early exit)
			for ($i = $returnEarlyIndex + 1; $i < $prevScopeLastTokenIndex; $i++) {
				if ($tokens[$i]['type'] === 'T_SEMICOLON') {
					return false;
				}
			}

			// Find the condition token (if/elseif)
			$prevParenthesisEndIndex = $phpcsFile->findPrevious(T_WHITESPACE, $scopeStartIndex - 1, null, true);
			if (!$prevParenthesisEndIndex || !array_key_exists('parenthesis_opener', $tokens[$prevParenthesisEndIndex])) {
				return false;
			}

			$parenthesisStartIndex = $tokens[$prevParenthesisEndIndex]['parenthesis_opener'];
			$prevConditionIndex = $phpcsFile->findPrevious(T_WHITESPACE, $parenthesisStartIndex - 1, null, true);

			if (!in_array($tokens[$prevConditionIndex]['code'], [T_IF, T_ELSEIF], true)) {
				return false;
			}

			// If we found an IF, we're done checking (this is the start of the chain)
			if ($tokens[$prevConditionIndex]['code'] === T_IF) {
				return true;
			}

			// Otherwise, it's an ELSEIF, continue checking the previous branch
			$currentPtr = $prevConditionIndex;
		}
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $stackPtr
*
	 * @return void
	 */
	protected function fixElseIfToIf(File $phpcsFile, int $stackPtr): void {
		$tokens = $phpcsFile->getTokens();

		$phpcsFile->fixer->beginChangeset();

		$prevIndex = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
		$indentation = $this->getIndentationWhitespace($phpcsFile, $prevIndex);

		for ($i = $prevIndex + 1; $i < $stackPtr; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$phpcsFile->fixer->addNewline($prevIndex);

		$phpcsFile->fixer->replaceToken($stackPtr, $indentation . 'if');

		$phpcsFile->fixer->endChangeset();
	}

}
