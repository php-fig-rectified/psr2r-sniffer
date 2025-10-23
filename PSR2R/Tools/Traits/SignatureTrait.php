<?php

namespace PSR2R\Tools\Traits;

use PHP_CodeSniffer\Files\File;

/**
 * Method signature functionality.
 */
trait SignatureTrait {

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $stackPtr
	 *
	 * @return array<array>
	 */
	protected function getMethodSignature(File $phpcsFile, int $stackPtr): array {
		$tokens = $phpcsFile->getTokens();

		$startIndex = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr + 1);
		$endIndex = $tokens[$startIndex]['parenthesis_closer'];

		$arguments = [];
		$i = $startIndex;
		while ($nextVariableIndex = $phpcsFile->findNext(T_VARIABLE, $i + 1, $endIndex)) {
			$typehintIndex = $defaultIndex = $default = null;
			$possibleTypeHint = $phpcsFile->findPrevious([T_ARRAY, T_CALLABLE], $nextVariableIndex - 1, $nextVariableIndex - 3);
			if ($possibleTypeHint) {
				$typehintIndex = $possibleTypeHint;
			}

			$possibleEqualIndex = $phpcsFile->findNext([T_EQUAL], $nextVariableIndex + 1, $nextVariableIndex + 3);
			if ($possibleEqualIndex) {
				$whitelist = [T_CONSTANT_ENCAPSED_STRING, T_TRUE, T_FALSE, T_NULL, T_OPEN_SHORT_ARRAY, T_LNUMBER, T_DNUMBER];
				$possibleDefaultValue = $phpcsFile->findNext($whitelist, $possibleEqualIndex + 1, $possibleEqualIndex + 3);
				if ($possibleDefaultValue) {
					$defaultIndex = $possibleDefaultValue;
					//$default = $tokens[$defaultIndex]['content'];
					if ($tokens[$defaultIndex]['code'] === T_CONSTANT_ENCAPSED_STRING) {
						$default = 'string';
					} elseif ($tokens[$defaultIndex]['code'] === T_OPEN_SHORT_ARRAY) {
						$default = 'array';
					} elseif ($tokens[$defaultIndex]['code'] === T_FALSE || $tokens[$defaultIndex]['code'] === T_TRUE) {
						$default = 'bool';
					} elseif ($tokens[$defaultIndex]['code'] === T_LNUMBER) {
						$default = 'int';
					} elseif ($tokens[$defaultIndex]['code'] === T_DNUMBER) {
						$default = 'float';
					} elseif ($tokens[$defaultIndex]['code'] === T_NULL) {
						$default = 'null';
					} else {
						//die('Invalid default type: ' . $default);
					}
				}
			}

			$arguments[] = [
				'variable' => $nextVariableIndex,
				'typehintIndex' => $typehintIndex,
				'defaultIndex' => $defaultIndex,
				'default' => $default,
			];

			$i = $nextVariableIndex;
		}

		return $arguments;
	}

}
