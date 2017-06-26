<?php

namespace PSR2R\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PSR2R\Tools\AbstractSniff;

/**
 * No spaces around pipes in doc block hints.
 *
 * @author  Mark Scherer
 * @license MIT
 */
class DocBlockPipeSpacingSniff extends AbstractSniff {

	/**
	 * @inheritDoc
	 * @return void
	 */
	public function process(File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$content = $tokens[$stackPtr]['content'];
		if (substr_count($content, '|') !== 1) {
			return;
		}

		list($left, $right) = explode('|', $content);
		$newContent = trim($left) . '|' . trim($right);

		if ($newContent !== $content) {
			$fix = $phpcsFile->addFixableError('There should be no space around pipes in doc blocks.', $stackPtr,
				'PipeSpacing');
			if ($fix) {
				$phpcsFile->fixer->replaceToken($stackPtr, $newContent);
			}
		}
	}

	/**
	 * @inheritDoc
	 * @return array
	 */
	public function register() {
		return [
			T_DOC_COMMENT_STRING,
		];
	}

}
