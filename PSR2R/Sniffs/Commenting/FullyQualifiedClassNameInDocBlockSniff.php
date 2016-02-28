<?php

namespace PSR2R\Sniffs\Commenting;

use PHP_CodeSniffer_File;
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
	 * @inheritDoc
	 */
	public function register() {
		return [
			T_CLASS,
			T_INTERFACE,
			T_TRAIT,
			T_FUNCTION,
			T_VARIABLE,
		];
	}

	/**
	 * @inheritDoc
	 */
	public function process(PHP_CodeSniffer_File $phpCsFile, $stackPointer) {
		$tokens = $phpCsFile->getTokens();

		$docBlockEndIndex = $this->findRelatedDocBlock($phpCsFile, $stackPointer);

		if (!$docBlockEndIndex) {
			return;
		}

		$docBlockStartIndex = $tokens[$docBlockEndIndex]['comment_opener'];

		for ($i = $docBlockStartIndex + 1; $i < $docBlockEndIndex; $i++) {
			if ($tokens[$i]['type'] !== 'T_DOC_COMMENT_TAG') {
				continue;
			}
			if (!in_array($tokens[$i]['content'], ['@return', '@yield', '@param', '@throws', '@var', '@method', '@see'])) {
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

			if (empty($content)) {
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
	 * @param \PHP_CodeSniffer_File $phpCsFile
	 * @param array $classNames
	 * @param int $index
	 *
	 * @return void
	 */
	protected function assertUniqueParts(PHP_CodeSniffer_File $phpCsFile, array $classNames, $index) {
		$exists = [];
		foreach ($classNames as $className) {
			if (in_array($className, $exists, true)) {
				$phpCsFile->addError('Type `' . $className . '` used twice', $index);
				continue;
			}
			$exists[] = $className;
		}
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpCsFile
	 * @param int $classNameIndex
	 * @param array $classNames
	 * @param string $docBlockType
	 * @param string $appendix
	 *
	 * @return void
	 */
	protected function fixClassNames(PHP_CodeSniffer_File $phpCsFile, $classNameIndex, array $classNames, $docBlockType, $appendix) {
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

			if (in_array($className, self::$whitelistedTypes)) {
				continue;
			}

			$useStatement = $this->findUseStatementForClassName($phpCsFile, $className);
			if (!$useStatement) {
				if ($docBlockType === '@see' && strpos($suffix, '::') !== 0) {
					continue;
				}

				$phpCsFile->addError('Invalid class name "' . $className . '"', $classNameIndex);
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

		$fix = $phpCsFile->addFixableError(implode(', ', $message), $classNameIndex);
		if ($fix) {
			$newContent = implode('|', $classNames);

			$phpCsFile->fixer->replaceToken($classNameIndex, $newContent . $appendix);
		}
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpCsFile
	 * @param string $className
	 *
	 * @return string|null
	 */
	protected function findUseStatementForClassName(PHP_CodeSniffer_File $phpCsFile, $className) {
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
	 * @param \PHP_CodeSniffer_File $phpCsFile
	 * @param string $className
	 *
	 * @return string|null
	 */
	protected function findInSameNameSpace(PHP_CodeSniffer_File $phpCsFile, $className) {
		if (!$this->hasNamespace($phpCsFile)) {
			return null;
		}
		$currentNameSpace = $this->getNamespaceInfo($phpCsFile);

		$file = $phpCsFile->getFilename();
		$dir = dirname($file) . DIRECTORY_SEPARATOR;
		if (!file_exists($dir . $className . '.php')) {
			return null;
		}

		return '\\' . $currentNameSpace . '\\' . $className;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpCsFile
	 *
	 * @return array
	 */
	protected function parseUseStatements(PHP_CodeSniffer_File $phpCsFile) {
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
