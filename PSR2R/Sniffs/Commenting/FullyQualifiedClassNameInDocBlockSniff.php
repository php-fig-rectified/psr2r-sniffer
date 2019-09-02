<?php

namespace PSR2R\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PSR2R\Tools\AbstractSniff;

/**
 * Make sure all class names in doc bocks are FQCN.
 *
 * @author Mark Scherer
 * @license MIT
 */
class FullyQualifiedClassNameInDocBlockSniff extends AbstractSniff {

	/**
	 * @var array
	 */
	public static $whitelistedTypes = [
		'string', 'int', 'integer', 'float', 'bool', 'boolean', 'resource', 'null', 'void', 'callable',
		'array', 'mixed', 'object', 'false', 'true', 'self', 'static', '$this',
	];

	/**
	 * @var array
	 */
	public static $whitelistedTags = [
		'@return', '@param', '@throws', '@var', '@method', '@property', '@yield', '@see'
	];

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
			T_COMMENT,
		];
	}

	/**
	 * @inheritDoc
	 */
	public function process(File $phpCsFile, $stackPointer) {
		$docBlockEndIndex = $this->findRelatedDocBlock($phpCsFile, $stackPointer);

		if (!$docBlockEndIndex) {
			return;
		}

		$tokens = $phpCsFile->getTokens();

		$docBlockStartIndex = $tokens[$docBlockEndIndex]['comment_opener'];

		for ($i = $docBlockStartIndex + 1; $i < $docBlockEndIndex; $i++) {
			if ($tokens[$i]['type'] !== 'T_DOC_COMMENT_TAG') {
				continue;
			}
			if (!in_array($tokens[$i]['content'], static::$whitelistedTags)) {
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

			if (!$content) {
				continue;
			}

			$classNames = explode('|', $content);

			$strict = true;
			if ($tokens[$i]['content'] === '@see') {
				$strict = false;
			}
			$this->fixClassNames($phpCsFile, $classNameIndex, $classNames, $appendix, $strict);
		}
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpCsFile
	 * @param int $classNameIndex
	 * @param string[] $classNames
	 * @param string $appendix
	 * @param bool $strict
	 *
	 * @return void
	 */
	protected function fixClassNames(File $phpCsFile, $classNameIndex, array $classNames, $appendix, $strict = true) {
		$classNameMap = $this->generateClassNameMap($phpCsFile, $classNameIndex, $classNames, $strict);
		if (!$classNameMap) {
			return;
		}

		$message = [];
		foreach ($classNameMap as $className => $useStatement) {
			$message[] = $className . ' => ' . $useStatement;
		}

		$fix = $phpCsFile->addFixableError(implode(', ', $message), $classNameIndex, 'FQCN');
		if ($fix) {
			$newContent = implode('|', $classNames);

			$phpCsFile->fixer->replaceToken($classNameIndex, $newContent . $appendix);
		}
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpCsFile
	 * @param int $classNameIndex
	 * @param string[] $classNames
	 * @param bool $strict
	 *
	 * @return array
	 */
	protected function generateClassNameMap(File $phpCsFile, $classNameIndex, array &$classNames, $strict = true) {
		$result = [];

		foreach ($classNames as $key => $className) {
			if (strpos($className, '\\') !== false) {
				continue;
			}
			$arrayOfObject = false;
			if (substr($className, -2) === '[]') {
				$arrayOfObject = true;
				$className = substr($className, 0, -2);
			}
			if (in_array($className, static::$whitelistedTypes)) {
				continue;
			}
			$useStatement = $this->findUseStatementForClassName($phpCsFile, $className);
			if (!$useStatement) {
				if (!$strict) {
					return [];
				}
				$phpCsFile->addError('Invalid class name "' . $className . '"', $classNameIndex, 'ClassNameInvalid');
				continue;
			}
			$classNames[$key] = $useStatement . ($arrayOfObject ? '[]' : '');
			$result[$className . ($arrayOfObject ? '[]' : '')] = $classNames[$key];
		}

		return $result;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpCsFile
	 * @param string $className
	 *
	 * @return string|null
	 */
	protected function findUseStatementForClassName(File $phpCsFile, $className) {
		$useStatements = $this->parseUseStatements($phpCsFile);
		if (!isset($useStatements[$className])) {
			$useStatement = $this->findInSameNameSpace($phpCsFile, $className);
			if ($useStatement) {
				return $useStatement;
			}

			return null;
		}

		return $useStatements[$className];
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpCsFile
	 * @param string $className
	 *
	 * @return string|null
	 */
	protected function findInSameNameSpace(File $phpCsFile, $className) {
		$currentNameSpace = $this->getNamespace($phpCsFile);
		if (!$currentNameSpace) {
			return null;
		}
		$currentNameSpaceInfo = $this->getNamespaceInfo($phpCsFile);
		$currentNameSpace = $currentNameSpaceInfo['namespace'];

		$file = $phpCsFile->getFilename();
		$dir = dirname($file) . DIRECTORY_SEPARATOR;
		if (!file_exists($dir . $className . '.php')) {
			return null;
		}

		return '\\' . $currentNameSpace . '\\' . $className;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpCsFile
	 *
	 * @return string
	 */
	protected function getNamespace(File $phpCsFile) {
		$tokens = $phpCsFile->getTokens();

		$namespaceStart = null;
		foreach ($tokens as $id => $token) {
			if ($token['code'] !== T_NAMESPACE) {
				continue;
			}

			$namespaceStart = $id + 1;
			break;
		}
		if (!$namespaceStart) {
			return '';
		}

		$namespaceEnd = $phpCsFile->findNext(
			[
				T_NS_SEPARATOR,
				T_STRING,
				T_WHITESPACE,
			],
			$namespaceStart,
			null,
			true
		);

		$namespace = trim($phpCsFile->getTokensAsString(($namespaceStart), ($namespaceEnd - $namespaceStart)));

		return $namespace;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpCsFile
	 * @param int $stackPointer
	 *
	 * @return int|null Stackpointer value of docblock end tag, or null if cannot be found
	 */
	protected function findRelatedDocBlock(File $phpCsFile, $stackPointer) {
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
	 * @param \PHP_CodeSniffer\Files\File $phpCsFile
	 *
	 * @return array
	 */
	protected function parseUseStatements(File $phpCsFile) {
		$useStatements = [];
		$tokens = $phpCsFile->getTokens();

		foreach ($tokens as $id => $token) {
			if ($token['type'] !== 'T_USE') {
				continue;
			}

			$endIndex = $phpCsFile->findEndOfStatement($id);
			$useStatement = '';
			for ($i = $id + 2; $i < $endIndex; $i++) {
				$useStatement .= $tokens[$i]['content'];
			}

			$useStatement = trim($useStatement);

			if (strpos($useStatement, ' as ') !== false) {
				list($useStatement, $className) = explode(' as ', $useStatement);
			} else {
				$className = $useStatement;
				if (strpos($useStatement, '\\') !== false) {
					$lastSeparator = strrpos($useStatement, '\\');
					$className = substr($useStatement, $lastSeparator + 1);
				}
			}

			$useStatement = '\\' . ltrim($useStatement, '\\');

			$useStatements[$className] = $useStatement;
		}

		return $useStatements;
	}

}
