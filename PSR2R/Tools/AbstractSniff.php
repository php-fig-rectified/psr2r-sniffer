<?php

namespace PSR2R\Tools;

use PHP_CodeSniffer_File;

abstract class AbstractSniff implements \PHP_CodeSniffer_Sniff {

	/**
	 * Checks if the given token is of this token code/type.
	 *
	 * @param int|string $search
	 * @param array $token
	 * @return bool
	 */
	protected function isGivenKind($search, array $token) {
		$search = (array)$search;
		if (in_array($token['code'], $search, true)) {
			return true;
		}
		if (in_array($token['type'], $search, true)) {
			return true;
		}
		return false;
	}

	/**
	 * Checks if the given token scope contains a single or multiple token codes/types.
	 *
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param string|array $search
	 * @param int $start
	 * @param int $end
	 * @param bool $skipNested
	 * @return bool
	 */
	protected function contains(PHP_CodeSniffer_File $phpcsFile, $search, $start, $end, $skipNested = true) {
		$tokens = $phpcsFile->getTokens();

		for ($i = $start; $i <= $end; $i++) {
			if ($skipNested && $tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
				$i = $tokens[$i]['parenthesis_closer'];
				continue;
			}
			if ($skipNested && $tokens[$i]['code'] === T_OPEN_SHORT_ARRAY) {
				$i = $tokens[$i]['bracket_closer'];
				continue;
			}
			if ($skipNested && $tokens[$i]['code'] === T_OPEN_CURLY_BRACKET) {
				$i = $tokens[$i]['bracket_closer'];
				continue;
			}

			if ($this->isGivenKind($search, $tokens[$i])) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if the given token scope requires brackets when used standalone.
	 *
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $openingBraceIndex
	 * @param int $closingBraceIndex
	 * @return bool
	 */
	protected function needsBrackets(PHP_CodeSniffer_File $phpcsFile, $openingBraceIndex, $closingBraceIndex) {
		$tokens = $phpcsFile->getTokens();

		$whitelistedCodes = [
			T_LNUMBER,
			T_STRING,
			T_BOOL_CAST,
			T_STRING_CAST,
			T_INT_CAST,
			T_ARRAY_CAST,
			T_COMMENT,
			T_WHITESPACE,
			T_VARIABLE,
			T_DOUBLE_COLON,
			T_OBJECT_OPERATOR,
		];

		for ($i = $openingBraceIndex + 1; $i < $closingBraceIndex; $i++) {
			if ($tokens[$i]['type'] === 'T_OPEN_PARENTHESIS') {
				$i = $tokens[$i]['parenthesis_closer'];
				continue;
			}
			if (in_array($tokens[$i]['code'], $whitelistedCodes)) {
				continue;
			}

			return true;
		}

		return false;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpCsFile
	 * @param int $stackPointer
	 *
	 * @return int|null Stackpointer value of docblock end tag, or null if cannot be found
	 */
	protected function findRelatedDocBlock(PHP_CodeSniffer_File $phpCsFile, $stackPointer) {
		$tokens = $phpCsFile->getTokens();

		$line = $tokens[$stackPointer]['line'];
		$beginningOfLine = $stackPointer;
		while (!empty($tokens[$beginningOfLine - 1]) && $tokens[$beginningOfLine - 1]['line'] === $line) {
			$beginningOfLine--;
		}

		if (!empty($tokens[$beginningOfLine - 2]) && $tokens[$beginningOfLine - 2]['type'] === 'T_DOC_COMMENT_CLOSE_TAG') {
			return $beginningOfLine - 2;
		}

		return null;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $index
	 * @param int $count
	 * @return void
	 */
	protected function outdent(PHP_CodeSniffer_File $phpcsFile, $index, $count = 1) {
		$tokens = $phpcsFile->getTokens();
		$char = $this->getIndentationCharacter($tokens[$index]['content'], true);

		$content = $tokens[$index]['content'];
		for ($i = 0; $i < $count; $i++) {
			$content = $this->strReplaceOnce($char, '', $content);
		}
		$phpcsFile->fixer->replaceToken($index, $content);
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $index
	 * @param int $count
	 * @return void
	 */
	protected function indent(PHP_CodeSniffer_File $phpcsFile, $index, $count = 1) {
		$tokens = $phpcsFile->getTokens();
		$char = $this->getIndentationCharacter($tokens[$index]['content'], true);

		$content = str_repeat($char, $count) . $tokens[$index]['content'];
		$phpcsFile->fixer->replaceToken($index, $content);
	}

	/**
	 * @param string $search
	 * @param string $replace
	 * @param string $subject
	 * @return string
	 */
	protected function strReplaceOnce($search, $replace, $subject) {
		$pos = strpos($subject, $search);
		if ($pos === false) {
			return $subject;
		}

		return substr($subject, 0, $pos) . $replace . substr($subject, $pos + strlen($search));
	}

	/**
	 * Get level of indentation, 0 based.
	 *
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $index
	 * @return int
	 */
	protected function getIndentationLevel(PHP_CodeSniffer_File $phpcsFile, $index) {
		$tokens = $phpcsFile->getTokens();

		$whitespace = $this->getIndentationWhitespace($phpcsFile, $index);
		$char = $this->getIndentationCharacter($whitespace);

		$level = $tokens[$index]['column'] - 1;

		if ($char === "\t") {
			return $level;
		}

		return (int)($level / 4);
	}

	/**
	 * @param string $content
	 * @param bool $correctLength
	 * @return string
	 */
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

		if ($countSpaces > 3 && $countSpaces > $countTabs) {
			$char = $correctLength ? '    ' : ' ';
		}

		return $char;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $index
	 * @return string
	 */
	protected function getIndentationWhitespace(PHP_CodeSniffer_File $phpcsFile, $index) {
		$tokens = $phpcsFile->getTokens();

		$firstIndex = $this->getFirstTokenOfLine($tokens, $index);
		$whitespace = '';
		if ($tokens[$firstIndex]['type'] === 'T_WHITESPACE' || $tokens[$firstIndex]['type'] === 'T_DOC_COMMENT_WHITESPACE') {
			$whitespace = $tokens[$firstIndex]['content'];
		}

		return $whitespace;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $index
	 * @return int
	 */
	protected function getIndentationColumn(PHP_CodeSniffer_File $phpcsFile, $index) {
		$tokens = $phpcsFile->getTokens();

		$firstIndex = $this->getFirstTokenOfLine($tokens, $index);

		$nextIndex = $phpcsFile->findNext(T_WHITESPACE, ($firstIndex + 1), null, true);
		if ($tokens[$nextIndex]['line'] !== $tokens[$index]['line']) {
			return 0;
		}
		return $tokens[$nextIndex]['column'] - 1;
	}

	/**
	 * @param array $tokens
	 * @param int $index
	 * @return int
	 */
	protected function getFirstTokenOfLine(array $tokens, $index) {
		$line = $tokens[$index]['line'];

		$currentIndex = $index;
		while ($tokens[$currentIndex - 1]['line'] === $line) {
			$currentIndex--;
		}

		return $currentIndex;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpCsFile
	 * @return bool
	 */
	protected function hasNamespace(PHP_CodeSniffer_File $phpCsFile) {
		return $this->findNamespaceIndex($phpCsFile) !== null;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpCsFile
	 * @return int|null
	 */
	protected function findNamespaceIndex(PHP_CodeSniffer_File $phpCsFile) {
		$namespacePosition = $phpCsFile->findNext(T_NAMESPACE, 0);
		if (!$namespacePosition) {
			return null;
		}
		return $namespacePosition;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @return array
	 */
	protected function getNamespaceInfo(PHP_CodeSniffer_File $phpcsFile) {
		$startIndex = $this->findNamespaceIndex($phpcsFile);

		$endIndex = 0;
		if ($startIndex) {
			$endIndex = $phpcsFile->findNext(T_SEMICOLON, $startIndex + 1);
		}

		if (empty($startIndex) || empty($endIndex)) {
			return [];
		}

		return [
			'start' => $startIndex,
			'namespace' => $this->getNamespaceAsString($phpcsFile, $startIndex + 1, $endIndex - 1),
			'end' => $endIndex
		];
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpCsFile
	 * @param int $startIndex
	 * @param int $endIndex
	 *
	 * @return string
	 */
	protected function getNamespaceAsString(PHP_CodeSniffer_File $phpCsFile, $startIndex, $endIndex) {
		$tokens = $phpCsFile->getTokens();

		$namespace = '';
		for ($i = $startIndex; $i <= $endIndex; $i++) {
			$namespace .= $tokens[$i]['content'];
		}

		return trim($namespace);
	}

}
