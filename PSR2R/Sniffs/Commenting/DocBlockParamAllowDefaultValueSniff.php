<?php

namespace PSR2R\Sniffs\Commenting;

use PSR2R\Tools\AbstractSniff;

/**
 * Makes sure doc block param types allow `|null`, `|array` etc, when those are used
 * as default values in the method signature.
 *
 * @author    Mark Scherer
 * @license   MIT
 */
class DocBlockParamAllowDefaultValueSniff extends AbstractSniff {

	/**
	 * @return array
	 */
	public function register() {
		return [
			T_FUNCTION,
		];
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpCsFile
	 * @param int $stackPointer
	 *
	 * @return void
	 */
	public function process(\PHP_CodeSniffer_File $phpCsFile, $stackPointer) {
		$tokens = $phpCsFile->getTokens();

		$docBlockEndIndex = $this->findRelatedDocBlock($phpCsFile, $stackPointer);

		if (!$docBlockEndIndex) {
			return;
		}

		$docBlockStartIndex = $tokens[$docBlockEndIndex]['comment_opener'];

		for ($i = $docBlockStartIndex + 1; $i < $docBlockEndIndex; $i++) {
			if ($tokens[$i]['type'] !== 'T_DOC_COMMENT_TAG') {
				continue;
			}
			if (!in_array($tokens[$i]['content'], ['@param'])) {
				continue;
			}

			$classNameIndex = $i + 2;

			if ($tokens[$classNameIndex]['type'] !== 'T_DOC_COMMENT_STRING') {
				continue;
			}

			$content = $tokens[$classNameIndex]['content'];

			$appendix = '';
			$spaceIndex = strpos($content, ' ');
			if ($spaceIndex) {
				$appendix = substr($content, $spaceIndex);
				$content = substr($content, 0, $spaceIndex);
			}
			if (empty($content) || strpos($content, '|') !== false) {
				continue;
			}

			if (!in_array($content, ['null', 'false', 'true'], true)) {
				continue;
			}

			$error = 'Possible doc block error: `' . $content . '` as only param type does not seem right. Makes this a no-op.';
			$phpCsFile->addWarning($error, $i);
		}
	}

}
