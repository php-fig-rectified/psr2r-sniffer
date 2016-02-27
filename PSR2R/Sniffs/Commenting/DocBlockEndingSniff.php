<?php

namespace PSR2R\Sniffs\Commenting;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;
use PHP_CodeSniffer_Tokens;

/**
 * Makes sure ending docblocks have a single asterix.
 *
 * @author Mark Scherer
 * @license MIT
 */
class DocBlockEndingSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * @inheritDoc
	 */
	public function register() {
		return [T_DOC_COMMENT];
	}

	/**
	 * @inheritDoc
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		// We are only interested in function/class/interface doc block comments.
		$nextToken = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);
		$ignore = [
			T_CLASS,
			T_INTERFACE,
			T_FUNCTION,
			T_PUBLIC,
			T_PRIVATE,
			T_PROTECTED,
			T_STATIC,
			T_ABSTRACT,
			];

		if (in_array($tokens[$nextToken]['code'], $ignore) === false) {
			// Could be a file comment.
			$prevToken = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1), null, true);
			if ($tokens[$prevToken]['code'] !== T_OPEN_TAG) {
				return;
			}
		}

		// We only want to get the first comment in a block. If there is
		// a comment on the line before this one, return.
		$docComment = $phpcsFile->findPrevious(T_DOC_COMMENT, ($stackPtr - 1));
		if ($docComment !== false) {
			if ($tokens[$docComment]['line'] === ($tokens[$stackPtr]['line'] - 1)) {
				return;
			}
		}

		$comments = [$stackPtr];
		$currentComment = $stackPtr;
		$lastComment = $stackPtr;
		while (($currentComment = $phpcsFile->findNext(T_DOC_COMMENT, ($currentComment + 1))) !== false) {
			if ($tokens[$lastComment]['line'] === ($tokens[$currentComment]['line'] - 1)) {
				$comments[] = $currentComment;
				$lastComment = $currentComment;
			} else {
				break;
			}
		}

		// The $comments array now contains pointers to each token in the comment block.
		$requiredColumn = strpos($tokens[$stackPtr]['content'], '*');
		$requiredColumn += $tokens[$stackPtr]['column'];

		foreach ($comments as $commentPointer) {
			// Check the spacing after each asterisk.
			$content = $tokens[$commentPointer]['content'];
			$firstChar = substr($content, 0, 1);
			$lastChar = substr($content, -1);
			if ($firstChar === '/' || $lastChar !== '/') {
				continue;
			}

			$count = substr_count($content, '*');
			if ($count < 2) {
				continue;
			}

			$error = 'Expected 1 asterisk on closing line; %s found';
			$data = [$count];
			$fix = $phpcsFile->addFixableError($error, $commentPointer, 'SpaceBeforeTag', $data);
			if ($fix === true && $phpcsFile->fixer->enabled === true) {
				$pos = strpos($content, '*');
				$content = substr($content, 0, $pos + 1) . substr($content, $pos + $count);
				$phpcsFile->fixer->replaceToken($commentPointer, $content);
			}
		}
	}

}
