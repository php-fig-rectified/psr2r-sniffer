<?php

namespace PSR2R\Sniffs\ControlStructures;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;

/**
 */
class UnneededElseSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return [T_ELSE, T_ELSEIF];
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int $stackPtr The position of the current token in the
	 *                                        stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$stackPtr]['code'] === T_ELSEIF && $this->isNotLastCondition($phpcsFile, $stackPtr)) {
			return;
		}

		$prevScopeEndIndex = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);

		$scopeStartIndex = $tokens[$prevScopeEndIndex]['scope_opener'];

		$prevParenthesisEndIndex = $phpcsFile->findPrevious(T_WHITESPACE, ($scopeStartIndex - 1), null, true);
		$parenthesisStartIndex = $tokens[$prevParenthesisEndIndex]['parenthesis_opener'];

		$prevConditionIndex = $phpcsFile->findPrevious(T_WHITESPACE, ($parenthesisStartIndex - 1), null, true);
		// We only do trivial fixes right now
		if ($tokens[$prevConditionIndex]['code'] !== T_IF) {
			return;
		}

		$prevScopeLastTokenIndex = $phpcsFile->findPrevious(T_WHITESPACE, ($prevScopeEndIndex - 1), null, true);
		if ($tokens[$prevScopeLastTokenIndex]['type'] !== 'T_SEMICOLON') {
			return;
		}

		$returnEarlyIndex = $phpcsFile->findPrevious([T_RETURN, T_CONTINUE, T_BREAK], ($prevScopeLastTokenIndex - 1), $scopeStartIndex + 1);
		if (!$returnEarlyIndex) {
			return;
		}

		for ($i = $returnEarlyIndex + 1; $i < $prevScopeLastTokenIndex; $i++) {
			if ($tokens[$i]['type'] === 'T_SEMICOLON') {
				return;
			}
		}

		//var_dump($tokens[$returnEarlyIndex]); die();

		$fix = $phpcsFile->addFixableError('Unneeded ' . $tokens[$stackPtr]['type'] . ' detected.', $stackPtr);
		if (!$fix) {
			return;
		}

		if ($tokens[$stackPtr]['code'] === T_ELSEIF) {
			$this->fixElseIfToIf($phpcsFile, $stackPtr);
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		$prevIndex = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
		$indentationLevel = $tokens[$prevIndex]['column'] - 1;
		$line = $tokens[$prevIndex]['line'];

		for ($i = $prevIndex + 1; $i < $stackPtr; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$phpcsFile->fixer->addNewline($prevIndex);

		$phpcsFile->fixer->replaceToken($stackPtr, '');

		$nextScopeStartIndex = $tokens[$stackPtr]['scope_opener'];
		$nextScopeEndIndex = $tokens[$stackPtr]['scope_closer'];

		for ($i = $stackPtr + 1; $i < $nextScopeStartIndex; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$prevEndIndex = $phpcsFile->findPrevious(T_WHITESPACE, ($nextScopeEndIndex - 1), null, true);

		$phpcsFile->fixer->replaceToken($nextScopeStartIndex, '');
		$phpcsFile->fixer->replaceToken($nextScopeEndIndex, '');

		for ($i = $prevEndIndex + 1; $i < $nextScopeEndIndex; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		// Fix indentation
		$currentLine = $line;
		for ($i = $nextScopeStartIndex + 1; $i < $prevEndIndex; $i++) {
			if ($tokens[$i]['line'] === $line || $tokens[$i]['type'] !== 'T_WHITESPACE') {
				continue;
			}
			$currentLine = $tokens[$i]['line'];
			$this->outdent($phpcsFile, $i);
		}

		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param $index
	 */
	protected function outdent(PHP_CodeSniffer_File $phpcsFile, $index) {
		$tokens = $phpcsFile->getTokens();
		$char = $this->getIndentationCharacter($tokens[$index]['content'], true);

		$phpcsFile->fixer->replaceToken($index, $this->strReplaceOnce($char, '', $tokens[$index]['content']));
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param $index
     */
	protected function indent(PHP_CodeSniffer_File $phpcsFile, $index) {
		$tokens = $phpcsFile->getTokens();

		$phpcsFile->fixer->replaceToken($index, $this->strReplaceOnce("\t", "\t\t", $tokens[$index]['content']));
	}

	protected function strReplaceOnce($search, $replace, $subject) {
		$pos = strpos($subject, $search);
		if ($pos === false) {
			return $subject;
		}

		return substr($subject, 0, $pos) . $replace . substr($subject, $pos + strlen($search));
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param $stackPtr
	 * @return bool
     */
	protected function isNotLastCondition(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$nextScopeEndIndex = $tokens[$stackPtr]['scope_closer'];

		$nextConditionStartIndex = $phpcsFile->findNext(T_WHITESPACE, ($nextScopeEndIndex - 1), null, true);

		if (in_array($tokens[$nextConditionStartIndex]['code'], [T_ELSEIF, T_ELSE])) {
			return true;
		}

		return false;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param $stackPtr
	 * @return void
     */
	protected function fixElseIfToIf(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$phpcsFile->fixer->beginChangeset();

		$prevIndex = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
		$indentationLevel = $tokens[$prevIndex]['column'] - 1;
		$indentationCharacter = $this->getIndentationCharacter($this->getIndentationWhitespace($phpcsFile, $prevIndex));

		$indentation = str_repeat($indentationCharacter, $indentationLevel);

		for ($i = $prevIndex + 1; $i < $stackPtr; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$phpcsFile->fixer->addNewline($prevIndex);

		$phpcsFile->fixer->replaceToken($stackPtr, $indentation . 'if');

		$phpcsFile->fixer->endChangeset();
	}

	protected function getIndentationCharacter($content, $correctLength = false) {
		if (strpos($content, "\n")) {
			$parts = explode("\n", $content);
			array_shift($parts);
		} else {
			$parts = (array)$content;
		}

		$char = "\t";
		$countTabs = $countSpaces = 0;
		foreach ($parts as $part) {
			$countTabs += substr_count($content, $char);
			$countSpaces += (int)(substr_count($content, ' ') / 4);
		}
		var_dump($content);

		if ($countSpaces > $countTabs) {
			$char = $correctLength ? '    ' : ' ';
		}

		return $char;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param $prevIndex
     */
	private function getIndentationWhitespace(PHP_CodeSniffer_File $phpcsFile, $prevIndex) {
		$tokens = $phpcsFile->getTokens();

		$line = $tokens[$prevIndex]['line'];
		$currentIndex = $prevIndex;
		$whitespace = '';
		while ($tokens[$currentIndex - 1]['line'] === $line) {
			$currentIndex--;
		}
		if ($tokens[$currentIndex]['type'] === 'T_WHITESPACE') {
			$whitespace = $tokens[$currentIndex]['content'];
		}

		return $whitespace;
	}

}
