<?php

namespace PSR2R\Tools\Traits;

use PHP_CodeSniffer\Files\File;

/**
 * Common functionality around commenting.
 */
trait CommentingTrait {

	/**
	 * Looks for either `@inheritdoc` or `{@inheritdoc}`.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpCsFile
	 * @param int $docBlockStartIndex
	 * @param int $docBlockEndIndex
	 *
	 * @return bool
	 */
	protected function hasInheritDoc(File $phpCsFile, int $docBlockStartIndex, int $docBlockEndIndex): bool {
		$tokens = $phpCsFile->getTokens();

		for ($i = $docBlockStartIndex + 1; $i < $docBlockEndIndex; ++$i) {
			if (empty($tokens[$i]['content'])) {
				continue;
			}
			$content = strtolower($tokens[$i]['content']);
			if (strpos($content, '@inheritdoc') === false) {
				continue;
			}

			return true;
		}

		return false;
	}

	/**
	 * Allow \Foo\Bar[] or array<\Foo\Bar> to pass as array.
	 *
	 * @param array<string> $docBlockTypes
	 *
	 * @return bool
	 */
	protected function containsTypeArray(array $docBlockTypes): bool {
		foreach ($docBlockTypes as $docBlockType) {
			if (strpos($docBlockType, '[]') !== false || strpos($docBlockType, 'array<') === 0) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks for ...<...>.
	 *
	 * @param array<string> $docBlockTypes
	 *
	 * @return bool
	 */
	protected function containsIterableSyntax(array $docBlockTypes): bool {
		foreach ($docBlockTypes as $docBlockType) {
			if (strpos($docBlockType, '<') !== false) {
				return true;
			}
		}

		return false;
	}

}
