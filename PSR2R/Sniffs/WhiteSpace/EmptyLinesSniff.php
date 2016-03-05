<?php
namespace PSR2R\Sniffs\WhiteSpace;

use PHP_CodeSniffer_File;
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
	 */
	public function register() {
		return [T_WHITESPACE];
	}

	/**
	 * @inheritDoc
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();
		if ($tokens[$stackPtr]['content'] === $phpcsFile->eolChar
			&& isset($tokens[($stackPtr + 1)]) === true
			&& $tokens[($stackPtr + 1)]['content'] === $phpcsFile->eolChar
			&& isset($tokens[($stackPtr + 2)]) === true
			&& $tokens[($stackPtr + 2)]['content'] === $phpcsFile->eolChar
			&& isset($tokens[($stackPtr + 3)]) === true
			&& $tokens[($stackPtr + 3)]['content'] === $phpcsFile->eolChar
		) {
			$error = '2 empty lines and more are not allowed';
			$fix = $phpcsFile->addFixableError($error, ($stackPtr + 3), 'EmptyLines');
			if ($fix) {
				$phpcsFile->fixer->replaceToken($stackPtr + 3, '');
			}
		}
	}

}
