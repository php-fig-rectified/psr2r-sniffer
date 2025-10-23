<?php
/**
 * PSR2_Sniffs_WhiteSpace_ControlStructureSpacingSniff.
 *
 * @author Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 *
 * @link http://pear.php.net/package/PHP_CodeSniffer
 */

namespace PSR2R\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * PSR2_Sniffs_WhiteSpace_ControlStructureSpacingSniff.
 *
 * Checks that control structures have the correct spacing around brackets.
 *
 * @author Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 *
 * @version Release: @package_version@
 *
 * @link http://pear.php.net/package/PHP_CodeSniffer
 */
class ControlStructureSpacingSniff implements Sniff {

	/**
	 * How many spaces should follow the opening bracket.
	 *
	 */
	public int $requiredSpacesAfterOpen = 0;

	/**
	 * @inheritDoc
	 */
	public function register(): array {
		return [
			T_IF,
			T_WHILE,
			T_FOREACH,
			T_FOR,
			T_SWITCH,
			T_DO,
			T_ELSE,
			T_ELSEIF,
			T_TRY,
			T_CATCH,
		];
	}

	/**
	 * How many spaces should precede the closing bracket.
	 *
	 */
	public int $requiredSpacesBeforeClose = 0;

	/**
	 * @inheritDoc
	 */
	public function process(File $phpcsFile, int $stackPtr): void {
		$this->requiredSpacesAfterOpen = (int)$this->requiredSpacesAfterOpen;
		$this->requiredSpacesBeforeClose = (int)$this->requiredSpacesBeforeClose;
		$tokens = $phpcsFile->getTokens();

		// Handle else/elseif brace positioning
		if ($tokens[$stackPtr]['code'] === T_ELSE || $tokens[$stackPtr]['code'] === T_ELSEIF) {
			$this->processElseSpacing($phpcsFile, $stackPtr);
		}

		if (isset($tokens[$stackPtr]['parenthesis_opener']) === false
			|| isset($tokens[$stackPtr]['parenthesis_closer']) === false
		) {
			return;
		}

		$parenOpener = $tokens[$stackPtr]['parenthesis_opener'];
		$parenCloser = $tokens[$stackPtr]['parenthesis_closer'];
		$spaceAfterOpen = 0;
		if ($tokens[$parenOpener + 1]['code'] === T_WHITESPACE) {
			if (strpos($tokens[$parenOpener + 1]['content'], $phpcsFile->eolChar) !== false) {
				$spaceAfterOpen = 'newline';
			} else {
				$spaceAfterOpen = strlen($tokens[$parenOpener + 1]['content']);
			}
		}

		$phpcsFile->recordMetric($stackPtr, 'Spaces after control structure open parenthesis', $spaceAfterOpen);

		if (($spaceAfterOpen !== $this->requiredSpacesAfterOpen) &&
			($tokens[$parenOpener]['line'] === $tokens[$parenCloser]['line'])
		) {
			$error = 'Expected %s spaces after opening bracket; %s found';
			$data = [
				$this->requiredSpacesAfterOpen,
				$spaceAfterOpen,
			];
			$fix = $phpcsFile->addFixableError($error, $parenOpener + 1, 'SpacingAfterOpenBrace', $data);
			if ($fix === true) {
				$padding = str_repeat(' ', $this->requiredSpacesAfterOpen);
				if ($spaceAfterOpen === 0) {
					$phpcsFile->fixer->addContent($parenOpener, $padding);
				} elseif ($spaceAfterOpen === 'newline') {
					$phpcsFile->fixer->replaceToken($parenOpener + 1, '');
				} else {
					$phpcsFile->fixer->replaceToken($parenOpener + 1, $padding);
				}
			}
		}

		if ($tokens[$parenOpener]['line'] !== $tokens[$parenCloser]['line']) {
			return;
		}

		$spaceBeforeClose = 0;
		if ($tokens[$parenCloser - 1]['code'] === T_WHITESPACE) {
			$spaceBeforeClose = strlen(ltrim($tokens[$parenCloser - 1]['content'], $phpcsFile->eolChar));
		}

		$phpcsFile->recordMetric($stackPtr, 'Spaces before control structure close parenthesis', (string)$spaceBeforeClose);
		if ($spaceBeforeClose === $this->requiredSpacesBeforeClose) {
			return;
		}

		$error = 'Expected %s spaces before closing bracket; %s found';
		$data = [
			$this->requiredSpacesBeforeClose,
			$spaceBeforeClose,
		];
		$fix = $phpcsFile->addFixableError($error, $parenCloser - 1, 'SpaceBeforeCloseBrace', $data);
		if ($fix !== true) {
			return;
		}

		$padding = str_repeat(' ', $this->requiredSpacesBeforeClose);
		if ($spaceBeforeClose === 0) {
			$phpcsFile->fixer->addContentBefore($parenCloser, $padding);
		} else {
			$phpcsFile->fixer->replaceToken($parenCloser - 1, $padding);
		}
	}

	/**
	 * Process else/elseif spacing and brace positioning.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $stackPtr Position of else/elseif token
	 *
	 * @return void
	 */
	protected function processElseSpacing(File $phpcsFile, int $stackPtr): void {
		$tokens = $phpcsFile->getTokens();

		// Find the previous non-whitespace token (should be closing brace)
		$prevNonWhitespace = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);

		if ($prevNonWhitespace === false || $tokens[$prevNonWhitespace]['code'] !== T_CLOSE_CURLY_BRACKET) {
			// No closing brace before else/elseif, skip
			return;
		}

		$closingBrace = $prevNonWhitespace;
		$elseToken = $stackPtr;
		$keyword = $tokens[$elseToken]['code'] === T_ELSE ? 'else' : 'elseif';

		// Check if closing brace and else/elseif are on the same line
		if ($tokens[$closingBrace]['line'] !== $tokens[$elseToken]['line']) {
			// They are on different lines - we need to fix this
			$error = sprintf('Expected "} %s" on the same line; found closing brace and %s on different lines', $keyword, $keyword);
			$fix = $phpcsFile->addFixableError($error, $elseToken, 'SpacingBetweenBraceAndKeyword');

			if ($fix) {
				// Fix: Remove all whitespace between closing brace and else/elseif, add single space
				$phpcsFile->fixer->beginChangeset();

				// Remove all tokens between closing brace and else/elseif
				for ($i = $closingBrace + 1; $i < $elseToken; $i++) {
					$phpcsFile->fixer->replaceToken($i, '');
				}

				// Add single space after closing brace
				$phpcsFile->fixer->addContent($closingBrace, ' ');

				$phpcsFile->fixer->endChangeset();
			}
		}

		// Check opening brace position
		$this->checkOpeningBrace($phpcsFile, $stackPtr);
	}

	/**
	 * Check and fix the opening brace position for else/elseif.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $stackPtr Position of else/elseif token
	 *
	 * @return void
	 */
	protected function checkOpeningBrace(File $phpcsFile, int $stackPtr): void {
		$tokens = $phpcsFile->getTokens();
		$elseToken = $stackPtr;
		$keyword = $tokens[$elseToken]['code'] === T_ELSE ? 'else' : 'elseif';

		// For elseif, we need to find the closing parenthesis first
		if ($tokens[$elseToken]['code'] === T_ELSEIF) {
			$openParen = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $elseToken + 1, null, false, null, true);
			if ($openParen === false || empty($tokens[$openParen]['parenthesis_closer'])) {
				return;
			}
			$closeParen = $tokens[$openParen]['parenthesis_closer'];
			$searchStart = $closeParen + 1;
		} else {
			// For else, search starts right after else keyword
			$searchStart = $elseToken + 1;
		}

		// Find the opening brace
		$openingBrace = $phpcsFile->findNext(T_OPEN_CURLY_BRACKET, $searchStart, null, false, null, true);
		if ($openingBrace === false) {
			return;
		}

		// Find the token right before the opening brace (should be closing paren for elseif, or else keyword)
		$prevNonWhitespace = $phpcsFile->findPrevious(T_WHITESPACE, $openingBrace - 1, null, true);
		if ($prevNonWhitespace === false) {
			return;
		}

		// Check if they are on the same line
		if ($tokens[$prevNonWhitespace]['line'] !== $tokens[$openingBrace]['line']) {
			// They are on different lines - we need to fix this
			$expectedFormat = $tokens[$elseToken]['code'] === T_ELSEIF ? '} elseif (...) {' : '} else {';
			$error = sprintf('Expected "%s" with opening brace on the same line; found opening brace on different line', $expectedFormat);
			$fix = $phpcsFile->addFixableError($error, $openingBrace, 'OpeningBraceOnDifferentLine');

			if ($fix) {
				// Fix: Remove all whitespace between previous token and opening brace, add single space
				$phpcsFile->fixer->beginChangeset();

				// Remove all tokens between previous token and opening brace
				for ($i = $prevNonWhitespace + 1; $i < $openingBrace; $i++) {
					$phpcsFile->fixer->replaceToken($i, '');
				}

				// Add single space after previous token
				$phpcsFile->fixer->addContent($prevNonWhitespace, ' ');

				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
