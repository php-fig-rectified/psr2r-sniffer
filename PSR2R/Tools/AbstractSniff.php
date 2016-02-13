<?php

namespace PSR2R\Tools;

use PHP_CodeSniffer_File;

abstract class AbstractSniff implements \PHP_CodeSniffer_Sniff {


	/**
	 * Checks if the given token scope contains a single or multiple token codes/types.
	 *
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param string|array $tokens
	 * @param int $start
	 * @param int $end
	 * @return bool
	 */
	protected function contains(\PHP_CodeSniffer_File $phpcsFile, $tokens, $start, $end) {
		$whitelistedCodes = $whitelistedTypes = [];
		foreach ((array)$tokens as $token) {
			if (is_int($token)) {
				$whitelistedCodes[] = $token;
			} else {
				$whitelistedTypes[] = $token;
			}
		}

		$tokens = $phpcsFile->getTokens();
		for ($i = $start; $i <= $end; $i++) {
			if ($tokens[$i]['type'] === 'T_OPEN_PARENTHESIS') {
				$i = $tokens[$i]['parenthesis_closer'];
				continue;
			}
			if (in_array($tokens[$i]['code'], $whitelistedCodes, true)) {
				return true;
			}
			if (in_array($tokens[$i]['type'], $whitelistedTypes, true)) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Checks if the given token scope requires brackets when used standalone.
	 *
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param $openingBraceIndex
	 * @param $closingBraceIndex
	 * @return bool
	 */
	protected function needsBrackets(\PHP_CodeSniffer_File $phpcsFile, $openingBraceIndex, $closingBraceIndex) {
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
	 * @param
	 */

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $index
	 * @param int $count
	 * @return void
	 */
	protected function outdent(PHP_CodeSniffer_File $phpcsFile, $index, $count = 1) {
		$tokens = $phpcsFile->getTokens();
		$char = $this->getIndentationCharacter($tokens[$index]['content'], true);

		$phpcsFile->fixer->replaceToken($index, $this->strReplaceOnce($char, '', $tokens[$index]['content']));
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $index
	 * @param int $count
	 * @return void
	 */
	protected function indent(PHP_CodeSniffer_File $phpcsFile, $index, $count = 1) {
		$tokens = $phpcsFile->getTokens();

		$phpcsFile->fixer->replaceToken($index, $this->strReplaceOnce("\t", "\t\t", $tokens[$index]['content']));
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
		var_dump($content);

		if ($countSpaces > $countTabs) {
			$char = $correctLength ? '    ' : ' ';
		}

		return $char;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $prevIndex
	 * @return string
	 */
	protected function getIndentationWhitespace(PHP_CodeSniffer_File $phpcsFile, $prevIndex) {
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
