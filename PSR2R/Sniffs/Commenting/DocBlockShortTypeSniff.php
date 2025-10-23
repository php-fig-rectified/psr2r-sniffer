<?php

namespace PSR2R\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PSR2R\Tools\AbstractSniff;

/**
 * Use short types for boolean and integer in doc blocks.
 *
 * @author Mark Scherer
 * @license MIT
 */
class DocBlockShortTypeSniff extends AbstractSniff {

	/**
	 * @inheritDoc
	 */
	public function register(): array {
		return [
			T_CLASS,
			T_INTERFACE,
			T_TRAIT,
			T_FUNCTION,
			T_VARIABLE,
		];
	}

	/**
	 * @inheritDoc
	 */
	public function process(File $phpcsFile, int $stackPointer): void {
		$tokens = $phpcsFile->getTokens();

		$docBlockEndIndex = $this->findRelatedDocBlock($phpcsFile, $stackPointer);

		if (!$docBlockEndIndex) {
			return;
		}

		$docBlockStartIndex = $tokens[$docBlockEndIndex]['comment_opener'];

		for ($i = $docBlockStartIndex + 1; $i < $docBlockEndIndex; $i++) {
			if ($tokens[$i]['type'] !== 'T_DOC_COMMENT_TAG') {
				continue;
			}
			if (!in_array($tokens[$i]['content'], ['@return', '@param'], true)) {
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

			if (empty($content)) {
				continue;
			}

			$parts = explode('|', $content);
			$this->fixParts($phpcsFile, $classNameIndex, $parts, $appendix);
		}
	}

	/** @noinspection MoreThanThreeArgumentsInspection */

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $classNameIndex
	 * @param array $parts
	 * @param string $appendix
	 *
	 * @return void
	 */
	protected function fixParts(File $phpcsFile, int $classNameIndex, array $parts, string $appendix): void {
		$mapping = [
			'boolean' => 'bool',
			'integer' => 'int',
		];

		$result = [];
		foreach ($parts as $key => $part) {
			if (!isset($mapping[$part])) {
				continue;
			}

			$parts[$key] = $mapping[$part];
			$result[$part] = $mapping[$part];
		}

		if (!$result) {
			return;
		}

		$message = [];
		foreach ($result as $part => $useStatement) {
			$message[] = $part . ' => ' . $useStatement;
		}

		$fix = $phpcsFile->addFixableError(implode(', ', $message), $classNameIndex, 'ShortType');
		if (!$fix) {
			return;
		}

		$newContent = implode('|', $parts);
		$phpcsFile->fixer->replaceToken($classNameIndex, $newContent . $appendix);
	}

}
