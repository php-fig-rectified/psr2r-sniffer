<?php

namespace PSR2R\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PSR2R\Tools\AbstractSniff;
use PSR2R\Tools\Traits\CommentingTrait;
use PSR2R\Tools\Traits\SignatureTrait;

/**
 * Makes sure doc block param types match the variable name of the method signature.
 *
 * @author Mark Scherer
 * @license MIT
 */
class DocBlockParamSniff extends AbstractSniff {

	use CommentingTrait;
	use SignatureTrait;

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

		$docBlockEndIndex = $this->findRelatedDocBlock($phpcsFile, $stackPointer);

		if (!$docBlockEndIndex) {
			return;
		}

		$docBlockStartIndex = $tokens[$docBlockEndIndex]['comment_opener'];

		if ($this->hasInheritDoc($phpcsFile, $docBlockStartIndex, $docBlockEndIndex)) {
			return;
		}

		$methodSignature = $this->getMethodSignature($phpcsFile, $stackPointer);
		if (!$methodSignature) {
			return;
		}

		$docBlockParams = [];
		for ($i = $docBlockStartIndex + 1; $i < $docBlockEndIndex; $i++) {
			if ($tokens[$i]['type'] !== 'T_DOC_COMMENT_TAG') {
				continue;
			}
			if ($tokens[$i]['content'] !== '@param') {
				continue;
			}

			$classNameIndex = $i + 2;

			if ($tokens[$classNameIndex]['type'] !== 'T_DOC_COMMENT_STRING') {
				$phpcsFile->addError('Missing type in param doc block', $i, 'MissingParamType');

				continue;
			}

			$content = $tokens[$classNameIndex]['content'];

			$appendix = '';
			$spacePos = strpos($content, ' ');
			if ($spacePos) {
				$appendix = substr($content, $spacePos);
				$content = substr($content, 0, $spacePos);
			}

			/** @noinspection NotOptimalRegularExpressionsInspection */
			preg_match('/\$[^\s]+/', $appendix, $matches);
			$variable = $matches ? $matches[0] : '';

			$docBlockParams[] = [
				'index' => $classNameIndex,
				'type' => $content,
				'variable' => $variable,
				'appendix' => $appendix,
			];
		}

		if (count($docBlockParams) !== count($methodSignature)) {
			$phpcsFile->addError('Doc Block params do not match method signature', $stackPointer, 'ParamTypeMismatch');

			return;
		}

		foreach ($docBlockParams as $docBlockParam) {
			$methodParam = array_shift($methodSignature);
			$variableName = $tokens[$methodParam['variable']]['content'];

			if ($docBlockParam['variable'] === $variableName) {
				continue;
			}
			// We let other sniffers take care of missing type for now
			if (strpos($docBlockParam['type'], '$') !== false) {
				continue;
			}

			$error = 'Doc Block param variable `' . $docBlockParam['variable'] . '` should be `' . $variableName . '`';
			// For now just report (buggy yet)
			$phpcsFile->addError($error, $docBlockParam['index'], 'VariableWrong');

			/*
			$fix = $phpcsFile->addFixableError($error, $docBlockParam['index'], 'VariableWrong');
			if ($fix) {
				if ($docBlockParam['variable']) {
					$appendix = str_replace($docBlockParam['variable'], '', $docBlockParam['appendix']);
					$appendix = preg_replace('/' . preg_quote($docBlockParam['variable'], '/') . '\b/', $variableName, $appendix);
				} else {
					$appendix = ' ' . $variableName . $docBlockParam['appendix'];
				}
				$content = $docBlockParam['type'] . $appendix;
				$phpcsFile->fixer->replaceToken($docBlockParam['index'], $content);
			}
			*/
		}
	}

	/**
	 * //TODO: Replace with SignatureTrait
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $stackPtr
*
	 * @return array
	 */
	private function getMethodSignature(File $phpcsFile, int $stackPtr): array {
		$tokens = $phpcsFile->getTokens();

		$startIndex = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr + 1);
		$endIndex = $tokens[$startIndex]['parenthesis_closer'];

		$arguments = [];
		$i = $startIndex;
		while ($nextVariableIndex = $phpcsFile->findNext(T_VARIABLE, $i + 1, $endIndex)) {
			$typehint = $default = null;
			$possibleTypeHint =
				$phpcsFile->findPrevious([T_ARRAY, T_CALLABLE], $nextVariableIndex - 1, $nextVariableIndex - 3);
			if ($possibleTypeHint) {
				$typehint = $possibleTypeHint;
			}
			if ($possibleTypeHint) {
				$typehint = $possibleTypeHint;
			}

			$possibleEqualIndex = $phpcsFile->findNext([T_EQUAL], $nextVariableIndex + 1, $nextVariableIndex + 2);
			if ($possibleEqualIndex) {
				$possibleDefaultValue =
					$phpcsFile->findNext(
						[T_STRING, T_TRUE, T_FALSE, T_NULL, T_ARRAY],
						$possibleEqualIndex + 1,
						$possibleEqualIndex + 2,
					);
				if ($possibleDefaultValue) {
					$default = $possibleDefaultValue;
				}
			}

			$arguments[] = [
				'variable' => $nextVariableIndex,
				'typehint' => $typehint,
				'default' => $default,
			];

			$i = $nextVariableIndex;
		}

		return $arguments;
	}

}
