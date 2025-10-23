<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link http://pear.php.net/package/PHP_CodeSniffer_CakePHP
 * @since CakePHP CodeSniffer 0.1.11
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace PSR2R\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Check for any line starting with 2 spaces - which would indicate space indenting
 * Also check for "\t " - a tab followed by a space, which is a common similar mistake
 */
class TabAndSpaceSniff implements Sniff {

	/**
	 * A list of tokenizers this sniff supports.
	 *
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
		$tokens = $phpcsFile->getTokens();

		$line = $tokens[$stackPtr]['line'];
		// Only check whitespace at the start of lines (indentation)
		if ($stackPtr > 0 && $tokens[$stackPtr - 1]['line'] === $line) {
			return;
		}

		$content = $tokens[$stackPtr]['orig_content'] ?? $tokens[$stackPtr]['content'];

		// Check for space followed by tab (wrong order)
		if (str_contains($content, ' ' . "\t")) {
			$error = 'Space followed by tab found in indentation; use tabs only';
			$fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceBeforeTab');
			if ($fix) {
				// Replace space+tab patterns with just tabs
				$phpcsFile->fixer->replaceToken($stackPtr, str_replace(" \t", "\t", $content));
			}
		}

		// Check for tab followed by space (mixed indentation)
		if (str_contains($content, "\t ")) {
			$error = 'Tab followed by space found in indentation; use tabs only';
			$fix = $phpcsFile->addFixableError($error, $stackPtr, 'TabAndSpace');
			if ($fix) {
				// Remove spaces after tabs at start of line
				$phpcsFile->fixer->replaceToken($stackPtr, preg_replace('/^(\t+) +/', '$1', $content));
			}
		}
	}

}
