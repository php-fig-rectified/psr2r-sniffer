<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PSR2R\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PSR2R\Tools\AbstractSniff;

/**
 * Ensures Doc Blocks for variables are not just type null, but always another type and optionally nullable on top.
 *
 * @author Mark Scherer
 * @license MIT
 */
class DocBlockVarNotJustNullSniff extends AbstractSniff {

	/**
	 * @inheritDoc
	 */
	public function register() {
		return [
			T_VARIABLE,
		];
	}

	/**
	 * @inheritDoc
	 */
	public function process(File $phpCsFile, $stackPointer) {
		$tokens = $phpCsFile->getTokens();

		$previousIndex = $phpCsFile->findPrevious(Tokens::$emptyTokens, $stackPointer - 1, null, true);
		if ($previousIndex && $tokens[$previousIndex]['code'] === T_STATIC) {
			$previousIndex = $phpCsFile->findPrevious(Tokens::$emptyTokens, $previousIndex - 1, null, true);
		}

		if (!$this->isGivenKind([T_PUBLIC, T_PROTECTED, T_PRIVATE], $tokens[$previousIndex])) {
			return;
		}

		$docBlockEndIndex = $this->findRelatedDocBlock($phpCsFile, $stackPointer);

		if (!$docBlockEndIndex) {
			return;
		}

		$docBlockStartIndex = $tokens[$docBlockEndIndex]['comment_opener'];

		$varIndex = null;
		for ($i = $docBlockStartIndex + 1; $i < $docBlockEndIndex; $i++) {
			if ($tokens[$i]['type'] !== 'T_DOC_COMMENT_TAG') {
				continue;
			}
			if (!in_array($tokens[$i]['content'], ['@var'], true)) {
				continue;
			}

			$varIndex = $i;
		}

		if (!$varIndex) {
			return;
		}

		$typeIndex = $varIndex + 2;

		$content = $tokens[$typeIndex]['content'];
		$spaceIndex = strpos($content, ' ');
		if ($spaceIndex) {
			$content = substr($content, 0, $spaceIndex);
		}

		if (empty($content)) {
			return;
		}

		if ($content !== 'null') {
			return;
		}

		$phpCsFile->addError('Doc Block type `' . $content . '` for annotation @var not enough.', $stackPointer, 'VarTypeIncorrect');
	}

}
