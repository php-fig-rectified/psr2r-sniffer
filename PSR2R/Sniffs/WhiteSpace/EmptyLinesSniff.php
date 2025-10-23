<?php

namespace PSR2R\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PSR2R\Tools\AbstractSniff;

/**
 * Removes multi newlines in favor of single newlines.
 */
class EmptyLinesSniff extends AbstractSniff {

	/**
	 * @var array
	 */
	public array $supportedTokenizers = [
		'PHP',
		'JS',
		'CSS',
	];

	/**
	 * @inheritDoc
	 */
	public function register(): array {
		return [T_WHITESPACE];
	}

	/**
	 * @inheritDoc
	 */
	public function process(File $phpcsFile, int $stackPtr): void {
		$this->assertMaximumOneEmptyLineBetweenContent($phpcsFile, $stackPtr);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $stackPtr
	 *
	 * @return void
	 */
	protected function assertMaximumOneEmptyLineBetweenContent(File $phpcsFile, int $stackPtr): void {
		$tokens = $phpcsFile->getTokens();
		if ($tokens[$stackPtr]['content'] !== $phpcsFile->eolChar
			|| !isset($tokens[($stackPtr + 1)])
			|| $tokens[($stackPtr + 1)]['content'] !== $phpcsFile->eolChar
			|| !isset($tokens[($stackPtr + 2)])
			|| $tokens[($stackPtr + 2)]['content'] !== $phpcsFile->eolChar
		) {
			return;
		}

		$error = 'Found more than a single empty line between content';
		$fix = $phpcsFile->addFixableError($error, ($stackPtr + 2), 'EmptyLines');
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->replaceToken($stackPtr + 2, '');
	}

}
