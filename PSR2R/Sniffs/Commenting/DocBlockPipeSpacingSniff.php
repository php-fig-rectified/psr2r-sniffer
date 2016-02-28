<?php

namespace PSR2R\Sniffs\Commenting;

use PHP_CodeSniffer_File;
use PSR2R\Tools\AbstractSniff;

/**
 * No spaces around pipes in doc block hints.
 *
 * @author Mark Scherer
 * @license MIT
 */
class DocBlockPipeSpacingSniff extends AbstractSniff {

	/**
	 * @inheritDoc
	 */
	public function register() {
		return [
			T_DOC_COMMENT_STRING,
		];
	}

	/**
	 * @inheritDoc
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$description = '';
		$content = $tokens[$stackPtr]['content'];

		$hint = $content;
		if (strpos($hint, ' ') !== false) {
			list($hint, $description) = explode(' ', $hint, 2);
		}

		if (strpos($hint, '|') === false) {
			return;
		}

		$pieces = explode('|', $hint);

		$hints = [];
		foreach ($pieces as $piece) {
			$hints[] = trim($piece);
		}

		$desc = trim($description);

		while (!empty($desc) && mb_substr($desc, 0, 1) === '|') {
			$desc = trim(mb_substr($desc, 1));

			$pos = mb_strpos($desc, ' ');
			if ($pos > 0) {
				$hints[] = trim(mb_substr($desc, 0, $pos));
				$desc = trim(mb_substr($desc, $pos));
			} else {
				$hints[] = $desc;
				$desc = '';
			}
		}

		if ($desc !== '') {
			$desc = ' ' . $desc;
		}

		$newContent = implode('|', $hints) . $desc;

		if ($newContent !== $content) {
			$fix = $phpcsFile->addFixableError('There should be no space around pipes in doc blocks.', $stackPtr);
			if ($fix) {
				$phpcsFile->fixer->replaceToken($stackPtr, $newContent);
			}
		}
	}

}
