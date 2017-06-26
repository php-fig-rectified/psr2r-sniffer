<?php

namespace PSR2R\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PSR2R\Tools\AbstractSniff;

/**
 * Make sure all class names in doc blocks are FQCN (Fully Qualified Class Name).
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
		'@return', '@param', '@throws', '@var', '@method', '@property', '@yield', '@see',
	];

	/**
	 * @inheritDoc
	 */
	public function process(File $phpCsFile, $stackPointer) {
		$tokens = $phpCsFile->getTokens();

		if ($tokens[$stackPointer]['code'] === T_COMMENT) {
			$this->processInlineComments($phpCsFile, $stackPointer);
			return;
		}

		$this->processDocBlockComments($phpCsFile, $stackPointer);
	}

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
	 * @param \PHP_CodeSniffer\Files\File $phpCsFile
	 * @param int $stackPointer
	 *
	 * @return void
	 */
	protected function processInlineComments(File $phpCsFile, $stackPointer) {
		$tokens = $phpCsFile->getTokens();

		if (!preg_match('|^\/\* @var (.+) \$.+\*\/$|', $tokens[$stackPointer]['content'], $matches)) {
			return;
		}

		$content = $matches[1];

		$classNames = explode('|', $content);

		$result = $this->generateClassNameMap($phpCsFile, $stackPointer, $classNames);
		if (!$result) {
			return;
		}

		$message = [];
		foreach ($result as $className => $useStatement) {
			$message[] = $className . ' => ' . $useStatement;
		}

		$fix = $phpCsFile->addFixableError(implode(', ', $message), $stackPointer, 'InlineComment');
		if (!$fix) {
			return;
		}

		$classes = implode('|', $classNames);
		$content = preg_replace('|@var (.+) \$|', '@var ' . $classes . ' $', $tokens[$stackPointer]['content']);

		$phpCsFile->fixer->beginChangeset();

		$phpCsFile->fixer->replaceToken($stackPointer, $content);

		$phpCsFile->fixer->endChangeset();
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpCsFile
	 * @param int $classNameIndex
	 * @param array $classNames
	 *
	 * @return array
	 */
	protected function generateClassNameMap(File $phpCsFile, $classNameIndex, array &$classNames) {
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

			if (in_array($className, static::$whitelistedTypes, true)) {
				continue;
			}

			$useStatement = $this->findUseStatementForClassName($phpCsFile, $className);
			if (!$useStatement) {
				$phpCsFile->addError('Invalid class name "' . $className . '"', $classNameIndex, 'InvalidClassName');
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

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpCsFile
	 * @param string $className
	 *
	 * @return string|null
	 */
	protected function findInSameNameSpace(File $phpCsFile, $className) {
		if (!$this->hasNamespace($phpCsFile)) {
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
	 * @param int $stackPointer
	 *
	 * @return void
	 */
	protected function processDocBlockComments(File $phpCsFile, $stackPointer) {
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
			if (!in_array($tokens[$i]['content'], static::$whitelistedTags, true)) {
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
			if (count($classNames) > 1) {
				$this->assertUniqueParts($phpCsFile, $classNames, $i);
			}

			$this->fixClassNames($phpCsFile, $classNameIndex, $classNames, $tokens[$i]['content'], $appendix);
		}
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpCsFile
	 * @param array $classNames
	 * @param int $index
	 *
	 * @return void
	 */
	protected function assertUniqueParts(File $phpCsFile, array $classNames, $index) {
		$exists = [];
		foreach ($classNames as $className) {
			if (in_array($className, $exists, true)) {
				$phpCsFile->addError('Type `' . $className . '` used twice', $index, 'NonUnique');
				continue;
			}
			$exists[] = $className;
		}
	}

	/** @noinspection MoreThanThreeArgumentsInspection */

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpCsFile
	 * @param int $classNameIndex
	 * @param array $classNames
	 * @param string $docBlockType
	 * @param string $appendix
	 *
	 * @return void
	 */
	protected function fixClassNames(File $phpCsFile, $classNameIndex, array $classNames, $docBlockType, $appendix) {
		$result = [];
		foreach ($classNames as $key => $className) {
			if (strpos($className, '\\') !== false) {
				continue;
			}

			$suffix = '';
			if (substr($className, -2) === '[]') {
				$suffix = '[]';
				$className = substr($className, 0, -2);
			} elseif ($docBlockType === '@see' && preg_match('/^[a-z]+\:\:/i', $className, $matches)) {
				$pos = strpos($className, '::');
				$suffix = substr($className, $pos);
				$className = substr($className, 0, $pos);
			}

			if (in_array($className, static::$whitelistedTypes, true)) {
				continue;
			}

			$useStatement = $this->findUseStatementForClassName($phpCsFile, $className);
			if (!$useStatement) {
				if ($docBlockType === '@see' && strpos($suffix, '::') !== 0) {
					continue;
				}

				$phpCsFile->addError('Invalid class name "' . $className . '"', $classNameIndex, 'InvalidClassName');
				continue;
			}

			$classNames[$key] = $useStatement . ($suffix ?: '');
			$result[$className . ($suffix ?: '')] = $classNames[$key];
		}

		if (!$result) {
			return;
		}

		$message = [];
		foreach ($result as $className => $useStatement) {
			$message[] = $className . ' => ' . $useStatement;
		}

		$fix = $phpCsFile->addFixableError(implode(', ', $message), $classNameIndex, 'FixedClassName');
		if ($fix) {
			$newContent = implode('|', $classNames);

			$phpCsFile->fixer->replaceToken($classNameIndex, $newContent . $appendix);
		}
	}

}
