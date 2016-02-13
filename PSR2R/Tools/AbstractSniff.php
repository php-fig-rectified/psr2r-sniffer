<?php

namespace PSR2R\Tools;

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

}
