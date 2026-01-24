<?php

namespace PSR2R\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PSR2R\Tools\AbstractSniff;

/**
 * Doc Blocks that return $this should be declared as such.
 *
 * @author Mark Scherer
 * @license MIT
 */
class DocBlockReturnSelfSniff extends AbstractSniff {

	/**
	 * @inheritDoc
	 */
	public function register(): array {
		return [
			T_FUNCTION,
		];
	}

	/**
	 * @inheritDoc
	 */
	public function process(File $phpcsFile, int $stackPointer): void {
		$tokens = $phpcsFile->getTokens();

		// Skip static methods - they cannot return $this
		if ($this->isStaticMethod($phpcsFile, $stackPointer)) {
			return;
		}

		$docBlockEndIndex = $this->findRelatedDocBlock($phpcsFile, $stackPointer);

		if (!$docBlockEndIndex) {
			return;
		}

		$docBlockStartIndex = $tokens[$docBlockEndIndex]['comment_opener'];

		for ($i = $docBlockStartIndex + 1; $i < $docBlockEndIndex; $i++) {
			if ($tokens[$i]['type'] !== 'T_DOC_COMMENT_TAG') {
				continue;
			}
			if ($tokens[$i]['content'] !== '@return') {
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

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $stackPointer
	 *
	 * @return bool
	 */
	protected function isStaticMethod(File $phpcsFile, int $stackPointer): bool {
		$methodProperties = $phpcsFile->getMethodProperties($stackPointer);

		return $methodProperties['is_static'];
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $classNameIndex
	 * @param array<string> $parts
	 * @param string $appendix
	 *
	 * @return void
	 */
	protected function fixParts(File $phpcsFile, int $classNameIndex, array $parts, string $appendix): void {
		$result = [];
		foreach ($parts as $key => $part) {
			if ($part !== 'self') {
				continue;
			}

			$parts[$key] = '$this';
			$result[$part] = '$this';
		}

		if (!$result) {
			return;
		}

		$message = [];
		foreach ($result as $part => $useStatement) {
			$message[] = $part . ' => ' . $useStatement;
		}

		$fix = $phpcsFile->addFixableError(implode(', ', $message), $classNameIndex, 'ReturnSelf');
		if (!$fix) {
			return;
		}

		$newContent = implode('|', $parts);
		$phpcsFile->fixer->replaceToken($classNameIndex, $newContent . $appendix);
	}

}
