<?php

namespace PSR2R\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PSR2R\Tools\AbstractSniff;

class EmptyLinesSniff extends AbstractSniff {

	/**
	 * A list of tokenizers this sniff supports.
	 *
	 * @var array
	 */
	public $supportedTokenizers = [
		'PHP',
		'JS',
		'CSS',
	];

	/**
	 * @inheritDoc
	 * @return array
	 */
	public function register() {
		return [T_WHITESPACE];
	}

	/**
	 * @inheritDoc
	 * @return void
	 */
	public function process(File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();
		if (isset($tokens[$stackPtr + 1]) === true && isset($tokens[$stackPtr + 2]) === true &&
			$tokens[$stackPtr]['content'] === $phpcsFile->eolChar
			&& $tokens[$stackPtr + 1]['content'] === $phpcsFile->eolChar
			&& $tokens[$stackPtr + 2]['content'] === $phpcsFile->eolChar
		) {
			$error = '2 empty lines and more are not allowed';
			$fix = $phpcsFile->addFixableError($error, $stackPtr + 3, 'EmptyLines');
			if ($fix) {
				$phpcsFile->fixer->replaceToken($stackPtr + 2, '');
			}
		}
	}

}
