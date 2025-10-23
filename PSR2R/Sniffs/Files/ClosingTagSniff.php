<?php
/**
 * PSR2_Sniffs_Files_ClosingTagsSniff.
 *
 * @author Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 *
 * @link http://pear.php.net/package/PHP_CodeSniffer
 */

namespace PSR2R\Sniffs\Files;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * PSR2_Sniffs_Files_LineEndingsSniff.
 *
 * Checks that the file does not end with a closing tag.
 *
 * @author Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 *
 * @version Release: @package_version@
 *
 * @link http://pear.php.net/package/PHP_CodeSniffer
 */
class ClosingTagSniff implements Sniff {

	/**
	 * @inheritDoc
	 */
	public function register(): array {
		return [T_OPEN_TAG];
	}

	/**
	 * @inheritDoc
	 */
	public function process(File $phpcsFile, int $stackPtr): void {
		$tokens = $phpcsFile->getTokens();

		// Make sure this file only contains PHP code.
		/** @noinspection ForeachInvariantsInspection */
		for ($i = 0; $i < $phpcsFile->numTokens; $i++) {
			if ($tokens[$i]['code'] === T_INLINE_HTML
				&& trim($tokens[$i]['content']) !== ''
			) {
				return;
			}
		}

		// Find the last non-empty token.
		for ($last = ($phpcsFile->numTokens - 1); $last > 0; $last--) {
			if (trim($tokens[$last]['content']) !== '') {
				break;
			}
		}

		if ($tokens[$last]['code'] === T_CLOSE_TAG) {
			$error = 'A closing tag is not permitted at the end of a PHP file';
			$fix = $phpcsFile->addFixableError($error, $last, 'NotAllowed');
			if ($fix === true) {
				$phpcsFile->fixer->replaceToken($last, '');
			}

			$phpcsFile->recordMetric($stackPtr, 'PHP closing tag at end of PHP-only file', 'yes');
		} else {
			$phpcsFile->recordMetric($stackPtr, 'PHP closing tag at end of PHP-only file', 'no');
		}
	}

}
