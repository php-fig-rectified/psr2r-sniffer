<?php

namespace PSR2R\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PSR2R\Tools\AbstractSniff;

/**
 * Verifies that only whitelisted `@...` tags are being used.
 *
 * @author Mark Scherer
 * @license MIT
 */
class DocBlockTagTypesSniff extends AbstractSniff {

	/**
	 * @var array
	 */
	protected static array $whitelistedTags = [
		'@api',
		'@author',
		'@copyright',
		'@deprecated',
		'@example',
		'@extends',
		'@filesource',
		'@ignore',
		'@inheritDoc',
		'@internal',
		'@license',
		'@link',
		'@method',
		'@mixin',
		'@override',
		'@param',
		'@property',
		'@property-read',
		'@property-write',
		'@return',
		'@see',
		'@since',
		'@source',
		'@throws',
		'@todo',
		'@triggers',
		'@uses',
		'@var',
		'@version',
		'@todo',
		// PHPUnit 9
		'@after',
		'@afterClass',
		'@backupGlobals',
		'@backupStaticAttributes',
		'@before',
		'@beforeClass',
		'@codeCoverageIgnore',
		'@codeCoverageIgnoreStart',
		'@codeCoverageIgnoreEnd',
		'@covers',
		'@coversDefaultClass',
		'@coversNothing',
		'@dataProvider',
		'@depends',
		'@doesNotPerformAssertions',
		'@group',
		'@large',
		'@medium',
		'@preserveGlobalState',
		'@requires',
		'@runTestsInSeparateProcesses',
		'@runInSeparateProcess',
		'@small',
		'@test',
		'@testdox',
		'@testWith',
		'@ticket',
		'@uses',
		'@coversNothing',
		// PHPMD
		'@SuppressWarnings(PHPMD)',
		// PhpStorm
		'@noinspection',
		// Stan
		'@phpstan-param',
		'@phpstan-return',
		'@phpstan-var',
		'@psalm-param',
		'@psalm-return',
		'@psalm-var',
	];

	/**
	 * @var array
	 */
	protected static array $blacklistedTags = [
		'@package',
		'@subpackage',
		'@global',
		'@category',
		'@static',
		'@void',
		'@overwrite',
	];

	/**
	 * @var array
	 */
	protected static array $mapping = [
		'@type' => '@var',
		'@overwrite' => '@override',
		'@inheritdoc' => '@inheritDoc',
		'@TODO' => '@todo',
	];

	/**
	 * Comma separated whitelist of further tags you can include in your ruleset.xml as
	 *
	 * <properties>
	 *     <property name="whitelist" value="@foo,@bar"/>
	 * </properties>
	 *
	 * @var string
	 */
	public string $whitelist = '';

	/**
	 * @inheritDoc
	 */
	public function register(): array {
		return [T_CLASS, T_FUNCTION];
	}

	/**
	 * @inheritDoc
	 */
	public function process(File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$docBlockEndIndex = $this->findRelatedDocBlock($phpcsFile, $stackPtr);
		if (!$docBlockEndIndex) {
			return;
		}

		$docBlockStartIndex = $tokens[$docBlockEndIndex]['comment_opener'];

		$this->prepareWhitelist();

		for ($i = $docBlockStartIndex + 1; $i < $docBlockEndIndex; $i++) {
			if ($tokens[$i]['type'] !== 'T_DOC_COMMENT_TAG') {
				continue;
			}
			$content = $tokens[$i]['content'];
			if (in_array($content, static::$whitelistedTags, true)) {
				continue;
			}

			$error = 'Unexpected tag type `' . $tokens[$i]['content'] . '` in doc block';
			if (!array_key_exists($content, static::$mapping) && !in_array($content, static::$blacklistedTags, true)) {
				$phpcsFile->addWarning($error, $i, 'Unknown');

				continue;
			}

			$mappingTag =
				isset(static::$mapping[$content]) ? static::$mapping[$tokens[$i]['content']] : null;
			if ($mappingTag) {
				$error .= ', expected `' . $mappingTag . '`';
			}

			$prevAsterix = $phpcsFile->findPrevious(T_DOC_COMMENT_STAR, $i - 1, $docBlockStartIndex);
			$nextAsterix =
				$phpcsFile->findNext([T_DOC_COMMENT_STAR, T_DOC_COMMENT_CLOSE_TAG], $i + 1, $docBlockEndIndex + 1);
			if (!$prevAsterix || !$nextAsterix) {
				$phpcsFile->addError($error, $i, 'Invalid');

				continue;
			}

			$phpcsFile->addFixableError($error, $i, 'Invalid');
			if ($phpcsFile->fixer->enabled) {
				if ($mappingTag) {
					$phpcsFile->fixer->replaceToken($i, $mappingTag);

					continue;
				}

				$phpcsFile->fixer->beginChangeset();
				for ($j = $prevAsterix; $j < $nextAsterix; $j++) {
					$phpcsFile->fixer->replaceToken($j, '');
				}
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

	/**
	 * @return void
	 */
	protected function prepareWhitelist(): void {
		if (!empty($this->whitelist)) {
			$whitelist = explode(',', $this->whitelist);
			foreach ($whitelist as $tag) {
				if (!in_array($tag, static::$whitelistedTags, true)) {
					static::$whitelistedTags[] = $tag;
				}
			}
		}
		$this->whitelist = '';
	}

}
