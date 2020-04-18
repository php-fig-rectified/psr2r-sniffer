<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PSR2R\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PSR2R\Tools\AbstractSniff;
use PSR2R\Tools\Traits\CommentingTrait;
use PSR2R\Tools\Traits\SignatureTrait;

/**
 * Makes sure doc block param/return types have the right order and do not duplicate.
 *
 * @author Mark Scherer
 * @license MIT
 */
class DocBlockTypeOrderSniff extends AbstractSniff {

	use CommentingTrait;
	use SignatureTrait;

	/**
	 * Highest/First element will be last in list of param or return tag.
	 *
	 * @var string[]
	 */
	protected $sortMap = [
		'void',
		'null',
		'false',
	];

	/**
	 * @var string[]
	 */
	protected $sortOrder;

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
	public function process(File $phpCsFile, $stackPointer) {
		$docBlockEndIndex = $this->findRelatedDocBlock($phpCsFile, $stackPointer);
		if (!$docBlockEndIndex) {
			return;
		}

		$tokens = $phpCsFile->getTokens();
		$docBlockStartIndex = $tokens[$docBlockEndIndex]['comment_opener'];

		$docBlockParams = $this->getDocBlockParams($tokens, $docBlockStartIndex, $docBlockEndIndex);

		$this->assertOrder($phpCsFile, $docBlockParams);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpCsFile
	 * @param array $docBlockParams
	 *
	 * @return void
	 */
	protected function assertOrder(File $phpCsFile, array $docBlockParams) {
		foreach ($docBlockParams as $docBlockParam) {
			if (strpos($docBlockParam['type'], '$') !== false) {
				continue;
			}

			$docBlockParamTypes = explode('|', $docBlockParam['type']);
			if (count($docBlockParamTypes) === 1) {
				continue;
			}

			$unique = array_unique($docBlockParamTypes);
			if (count($docBlockParamTypes) !== count($unique)) {
				$phpCsFile->addError('Duplicate type in `' . $docBlockParam['type'] . '`', $docBlockParam['index'], 'Duplicate');

				continue;
			}
			$expectedOrder = $this->getExpectedOrder($docBlockParamTypes);
			if ($expectedOrder === $docBlockParamTypes) {
				continue;
			}
			$expectedTypes = implode('|', $expectedOrder);

			$fix = $phpCsFile->addFixableError('Nullish/falsely value in `' . $docBlockParam['type'] . '` should be the last element, expected `' . $expectedTypes . '`', $docBlockParam['index'], 'WrongOrder');
			if (!$fix) {
				continue;
			}

			$phpCsFile->fixer->beginChangeset();

			$content = $expectedTypes . $docBlockParam['appendix'];
			$phpCsFile->fixer->replaceToken($docBlockParam['index'], $content);

			$phpCsFile->fixer->endChangeset();
		}
	}

	/**
	 * @uses DocBlockTypeOrderSniff::compare()
	 *
	 * @param string[] $elements
	 *
	 * @return string[]
	 */
	protected function getExpectedOrder(array $elements) {
		if (version_compare(PHP_VERSION, '7.0') < 0) {
			return $this->getExpectedOrderLegacy($elements);
		}

		$sortOrder = array_reverse($this->sortMap);
		$this->sortOrder = $sortOrder;

		usort($elements, [$this, 'compare']);

		return $elements;
	}

	/**
	 * For PHP 5 we need a custom fallback sort.
	 *
	 * @param string[] $elements
	 *
	 * @return string[]
	 */
	protected function getExpectedOrderLegacy(array $elements) {
		$sortOrder = array_reverse($this->sortMap);
		$sortOrder = array_flip($sortOrder);
		foreach ($elements as $element) {
			if (!isset($sortOrder[$element])) {
				$sortOrder[$element] = -1;
			}
		}

		$array = [];
		foreach ($elements as $element) {
			$array[$element] = $sortOrder[$element];
		}

		static::asort($array);

		$elements = array_keys($array);

		return $elements;
	}

	/**
	 * asort() but with a/b of same value to keep existing order.
	 *
	 * Required for PHP5, as the order can be inverse here.
	 *
	 * @param array $array
	 * @param int $sortFlags
	 *
	 * @return bool
	 */
	protected static function asort(array &$array, $sortFlags = SORT_REGULAR) {
		$index = 0;
		foreach ($array as &$item) {
			$item = [$index++, $item];
		}
		$result = uasort($array, function($a, $b) use($sortFlags) {
			if ($a[1] == $b[1]) {
				return $a[0] - $b[0];
			}
			$set = [-1 => $a[1], 1 => $b[1]];
			asort($set, $sortFlags);
			reset($set);

			return key($set);
		});
		foreach ($array as &$item) {
			$item = $item[1];
		}

		return $result;
	}

	/**
	 * @param string $a
	 * @param string $b
	 *
	 * @return int
	 */
	protected function compare($a, $b) {
		$sortOrder = $this->sortOrder;

		$aIndex = array_search($a, $sortOrder, true);
		$bIndex = array_search($b, $sortOrder, true);
		if ($aIndex === false) {
			return -1;
		}
		if ($bIndex === false) {
			return 1;
		}

		return $aIndex - $bIndex;
	}

	/**
	 * @param array $tokens
	 * @param int $docBlockStartIndex
	 * @param int $docBlockEndIndex
	 *
	 * @return array
	 */
	protected function getDocBlockParams(array $tokens, $docBlockStartIndex, $docBlockEndIndex) {
		$docBlockParams = [];
		for ($i = $docBlockStartIndex + 1; $i < $docBlockEndIndex; $i++) {
			if ($tokens[$i]['type'] !== 'T_DOC_COMMENT_TAG') {
				continue;
			}
			if (!in_array($tokens[$i]['content'], ['@param', '@return'], true)) {
				continue;
			}

			$classNameIndex = $i + 2;

			if ($tokens[$classNameIndex]['type'] !== 'T_DOC_COMMENT_STRING') {
				continue;
			}

			$content = $tokens[$classNameIndex]['content'];

			$appendix = '';
			$spacePos = strpos($content, ' ');
			if ($spacePos) {
				$appendix = substr($content, $spacePos);
				$content = substr($content, 0, $spacePos);
			}

			preg_match('/\$[^\s]+/', $appendix, $matches);
			$variable = $matches ? $matches[0] : '';

			$docBlockParams[] = [
				'index' => $classNameIndex,
				'type' => $content,
				'variable' => $variable,
				'appendix' => $appendix,
			];
		}

		return $docBlockParams;
	}

}
