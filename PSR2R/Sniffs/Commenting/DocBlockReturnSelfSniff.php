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
	public function process(File $phpCsFile, $stackPointer): void {
		$tokens = $phpCsFile->getTokens();
		if (($stackPointer > 1) && ($tokens[$stackPointer - 2]['code'] === T_STATIC)) {
			return; // Skip static function declarations
		}

		if ($tokens[$stackPointer]['code'] === T_FUNCTION && $this->isNonChainable($tokens, $stackPointer)) {
			return;
		}

		$docBlockEndIndex = $this->findRelatedDocBlock($phpCsFile, $stackPointer);

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

			if (strpos($content, '|') !== false) {
				return;
			}

			$parts = explode('|', $content);
			$this->fixParts($phpCsFile, $classNameIndex, $parts, $appendix);
		}
	}

	/**
	 * @param array<array<string, mixed>> $tokens
	 * @param int $stackPointer
	 *
	 * @return bool
	 */
	protected function isNonChainable(array $tokens, int $stackPointer): bool {
		if (empty($tokens[$stackPointer]['scope_opener'])) {
			return false;
		}

		$startIndex = $tokens[$stackPointer]['scope_opener'];
		$endIndex = $tokens[$stackPointer]['scope_closer'];
		$i = $startIndex + 1;
		while ($i < $endIndex) {
			if ($tokens[$i]['code'] === T_NEW) {
				return true;
			}
			$i++;
		}

		return false;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpCsFile
	 * @param int $classNameIndex
	 * @param array<string> $parts
	 * @param string $appendix
	 *
	 * @return void
	 */
	protected function fixParts(File $phpCsFile, int $classNameIndex, array $parts, string $appendix): void {
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

		$fix = $phpCsFile->addFixableError(implode(', ', $message), $classNameIndex, 'ReturnSelf');
		if (!$fix) {
			return;
		}

		$newContent = implode('|', $parts);
		$phpCsFile->fixer->replaceToken($classNameIndex, $newContent . $appendix);
	}

}
