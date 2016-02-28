<?php

namespace PSR2R\Sniffs\Commenting;

use PHP_CodeSniffer_File;
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
	public function register() {
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
	public function process(PHP_CodeSniffer_File $phpCsFile, $stackPointer) {
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
			if (!in_array($tokens[$i]['content'], ['@return'])) {
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
			$this->fixParts($phpCsFile, $classNameIndex, $parts, $appendix);
		}
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpCsFile
	 * @param int $classNameIndex
	 * @param array $parts
	 * @param string $appendix
	 *
	 * @return void
	 */
	protected function fixParts(PHP_CodeSniffer_File $phpCsFile, $classNameIndex, array $parts, $appendix) {
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

		$fix = $phpCsFile->addFixableError(implode(', ', $message), $classNameIndex);
		if ($fix) {
			$newContent = implode('|', $parts);
			$phpCsFile->fixer->replaceToken($classNameIndex, $newContent . $appendix);
		}
	}

}
