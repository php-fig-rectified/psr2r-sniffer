<?php
namespace PSR2R\Sniffs\Commenting;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Standards_AbstractScopeSniff;
use PSR2R\Tools\AbstractSniff;

/**
 * Verifies that only whitelisted `@...` tags are being used.
 *
 * @author Mark Scherer
 * @license MIT
 */
class DocBlockTagTypesSniff extends AbstractSniff {

	/**
	 * Comma separated whitelist of further tags you can include in your ruleset.xml as
	 *
	 * <properties>
	 *     <property name="whitelist" value="@foo,@bar"/>
	 * </properties>
	 *
	 * @var string
	 */
	public $whitelist = '';

	protected static $whitelistedTags = [
		'@api',
		'@author',
		'@copyright',
		'@deprecated',
		'@example',
		'@filesource',
		'@ignore',
		'@inheritDoc',
		'@internal',
		'@license',
		'@link',
		'@method',
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
		'@TODO',
		// PHPUnit
		'@covers',
		'@coversDefaultClass',
		'@expectedException',
		'@expectedExceptionCode',
		'@expectedExceptionMessage',
		'@expectedExceptionMessageRegExp',
		'@coversNothing',
		'@dataProvider',
		'@depends',
		'@group',
		'@uses',
		'@codeCoverageIgnore',
		'@codeCoverageIgnoreStart',
		'@codeCoverageIgnoreEnd',
		// PHPMD
		'@SuppressWarnings(PHPMD)'
	];

	protected static $blacklistedTags = [
		'@package',
		'@subpackage',
		'@global',
		'@category',
		'@static',
		'@void',
		'@overwrite'
	];

	protected static $mapping = [
		'@overwrite' => '@override',
		'@inheritdoc' => '@inheritDoc'
	];

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return [T_CLASS, T_FUNCTION];
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int $stackPtr The position of the current token in the stack passed in $tokens.
	 * @return void
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
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
			if (in_array($tokens[$i]['content'], self::$whitelistedTags)) {
				continue;
			}

			$error = 'Unexpected tag type `' . $tokens[$i]['content'] . '` in doc block';
			if (!in_array($tokens[$i]['content'], self::$blacklistedTags)) {
				$phpcsFile->addWarning($error, $i, 'Unknown');
				continue;
			}

			$mappingTag = isset(self::$mapping[$tokens[$i]['content']]) ? self::$mapping[$tokens[$i]['content']] : null;
			if ($mappingTag) {
				$error .= ', expected `' . $mappingTag . '`';
			}

			$prevAsterix = $phpcsFile->findPrevious(T_DOC_COMMENT_STAR, $i - 1, $docBlockStartIndex);
			$nextAsterix = $phpcsFile->findNext([T_DOC_COMMENT_STAR, T_DOC_COMMENT_CLOSE_TAG], $i + 1, $docBlockEndIndex + 1);
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
	protected function prepareWhitelist() {
		if (!empty($this->whitelist)) {
			$whitelist = explode(',', $this->whitelist);
			foreach ($whitelist as $tag) {
				if (!in_array($tag, self::$whitelistedTags)) {
					self::$whitelistedTags[] = $tag;
				}
			}
		}
		$this->whitelist = '';
	}

}
