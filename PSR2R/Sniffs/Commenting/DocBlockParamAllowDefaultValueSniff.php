<?php

namespace PSR2R\Sniffs\Commenting;

use PSR2R\Tools\AbstractSniff;

/**
 * Makes sure doc block param types allow `|null`, `|array` etc, when those are used
 * as default values in the method signature.
 *
 * @author Mark Scherer
 * @license MIT
 */
class DocBlockParamAllowDefaultValueSniff extends AbstractSniff {

	/**
	 * @inheritDoc
	 */
	public function register() {
		return [
			T_FUNCTION,
		];
	}

	/**
	 * @inheritDoc
	 */
	public function process(\PHP_CodeSniffer_File $phpCsFile, $stackPointer) {
		$tokens = $phpCsFile->getTokens();

		$docBlockEndIndex = $this->findRelatedDocBlock($phpCsFile, $stackPointer);

		if (!$docBlockEndIndex) {
			return;
		}

		$methodSignature = $this->getMethodSignature($phpCsFile, $stackPointer);
		if (!$methodSignature) {
			return;
		}
		//TODO: check count of signature vs doc block param count

		$docBlockStartIndex = $tokens[$docBlockEndIndex]['comment_opener'];

		$paramCount = 0;
		for ($i = $docBlockStartIndex + 1; $i < $docBlockEndIndex; $i++) {
			if ($tokens[$i]['type'] !== 'T_DOC_COMMENT_TAG') {
				continue;
			}
			if (!in_array($tokens[$i]['content'], ['@param'])) {
				continue;
			}

			if (empty($methodSignature[$paramCount])) {
				$phpCsFile->addError('Param type does not have a matching signature in method', $i);
				continue;
			}
			$methodSignatureValue = $methodSignature[$paramCount];
			$paramCount++;

			$classNameIndex = $i + 2;

			if ($tokens[$classNameIndex]['type'] !== 'T_DOC_COMMENT_STRING') {
				$phpCsFile->addError('Missing type in param doc block', $i);
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

			if (empty($methodSignatureValue['typehint']) && empty($methodSignatureValue['default'])) {
				continue;
			}

			$pieces = explode('|', $content);
			// We skip for mixed
			if (in_array('mixed', $pieces, true)) {
				continue;
			}

			if ($methodSignatureValue['typehint']) {
				$typeIndex = $methodSignatureValue['typehint'];
				$type = $tokens[$typeIndex]['content'];
				if (!in_array($type, $pieces)) {
					$error = 'Possible doc block error: `' . $content . '` seems to be missing type `' . $type . '`.';
					$fix = $phpCsFile->addFixableError($error, $classNameIndex);
					if ($fix) {
						$pieces[] = $type;
						$content = implode('|', $pieces);
						$phpCsFile->fixer->replaceToken($classNameIndex, $content . $appendix);
					}
				}
			}
			if ($methodSignatureValue['default']) {
				$typeIndex = $methodSignatureValue['default'];
				$type = $tokens[$typeIndex]['content'];
				if (!in_array($type, $pieces)) {
					$error = 'Possible doc block error: `' . $content . '` seems to be missing type `' . $type . '`.';
					$fix = $phpCsFile->addFixableError($error, $classNameIndex);
					if ($fix) {
						$pieces[] = $type;
						$content = implode('|', $pieces);
						$phpCsFile->fixer->replaceToken($classNameIndex, $content . $appendix);
					}
				}
			}
		}
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpCsFile
	 * @param int $stackPtr
	 * @return array
	 */
	private function getMethodSignature(\PHP_CodeSniffer_File $phpCsFile, $stackPtr) {
		$tokens = $phpCsFile->getTokens();

		$startIndex = $phpCsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr + 1);
		$endIndex = $tokens[$startIndex]['parenthesis_closer'];

		$arguments = [];
		$i = $startIndex;
		while ($nextVariableIndex = $phpCsFile->findNext(T_VARIABLE, $i + 1, $endIndex)) {
			$typehint = $default = null;
			$possibleTypeHint = $phpCsFile->findPrevious([T_ARRAY_HINT, T_CALLABLE], $nextVariableIndex - 1, $nextVariableIndex - 3);
			if ($possibleTypeHint) {
				$typehint = $possibleTypeHint;
			}
			if ($possibleTypeHint) {
				$typehint = $possibleTypeHint;
			}

			$possibleEqualIndex = $phpCsFile->findNext([T_EQUAL], $nextVariableIndex + 1, $nextVariableIndex + 2);
			if ($possibleEqualIndex) {
				$possibleDefaultValue = $phpCsFile->findNext([T_STRING, T_TRUE, T_FALSE, T_NULL, T_ARRAY], $possibleEqualIndex + 1, $possibleEqualIndex + 2);
				if ($possibleDefaultValue) {
					$default = $possibleDefaultValue;
				}
			}

			$arguments[] = [
				'variable' => $nextVariableIndex,
				'typehint' => $typehint,
				'default' => $default
			];

			$i = $nextVariableIndex;
		}

		return $arguments;
	}

}
