<?php
/**
 * Generic_Sniffs_Files_EndFileNewlineSniff.
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
 * Generic_Sniffs_Files_EndFileNewlineSniff.
 *
 * Ensures the file ends with a newline character.
 *
 * @author Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 *
 * @link http://pear.php.net/package/PHP_CodeSniffer
 */
class EndFileNewlineSniff implements Sniff {

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
		if ($phpcsFile->findNext(T_INLINE_HTML, $stackPtr + 1) !== false) {
			return;
		}

		// Skip to the end of the file.
		$tokens = $phpcsFile->getTokens();
		$lastToken = ($phpcsFile->numTokens - 1);

		// Hard-coding the expected \n in this sniff as it is PSR-2 specific and
		// PSR-2 enforces the use of unix style newlines.
		if (!str_ends_with($tokens[$lastToken]['content'], "\n")) {
			$error = 'Expected 1 newline at end of file; 0 found';
			$fix = $phpcsFile->addFixableError($error, $lastToken, 'NoneFound');
			if ($fix === true) {
				$phpcsFile->fixer->addNewline($lastToken);
			}

			$phpcsFile->recordMetric($stackPtr, 'Number of newlines at EOF', '0');

			return;
		}

		// Go looking for the last non-empty line.
		$lastLine = $tokens[$lastToken]['line'];
		$lastCode = $lastToken;
		if ($tokens[$lastToken]['code'] === T_WHITESPACE) {
			$lastCode = $phpcsFile->findPrevious(T_WHITESPACE, $lastToken - 1, null, true);
		}

		$lastCodeLine = $tokens[$lastCode]['line'];
		$blankLines = ($lastLine - $lastCodeLine + 1);
		$phpcsFile->recordMetric($stackPtr, 'Number of newlines at EOF', (string)$blankLines);

		if ($blankLines > 1) {
			$error = 'Expected 1 blank line at end of file; %s found';
			$data = [$blankLines];
			$fix = $phpcsFile->addFixableError($error, $lastCode, 'TooMany', $data);

			if ($fix === true) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken($lastCode, rtrim($tokens[$lastCode]['content']));
				for ($i = ($lastCode + 1); $i < $lastToken; $i++) {
					$phpcsFile->fixer->replaceToken($i, '');
				}

				$phpcsFile->fixer->replaceToken($lastToken, $phpcsFile->eolChar);
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
