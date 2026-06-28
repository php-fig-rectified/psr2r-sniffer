<?php

namespace PSR2R\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PSR2R\Tools\AbstractSniff;

/**
 * Always use `static::` and "late static binding" over `self::` usage.
 *
 * @author Mark Scherer
 * @license MIT
 */
class PreferStaticOverSelfSniff extends AbstractSniff {

	/**
	 * @inheritDoc
	 */
	public function register(): array {
		return [T_DOUBLE_COLON];
	}

	/**
	 * @inheritDoc
	 */
	public function process(File $phpcsFile, int $stackPtr): void {
		$tokens = $phpcsFile->getTokens();

		$index = $phpcsFile->findPrevious(Tokens::$emptyTokens, $stackPtr - 1, null, true);
		if ($tokens[$index]['code'] !== T_SELF) {
			return;
		}
		if ($tokens[$index]['level'] < 2) {
			return;
		}
		if ($this->isInFinalClass($phpcsFile, $stackPtr)) {
			return;
		}

		$fix = $phpcsFile->addFixableError('Please use static:: instead of self::', $stackPtr, 'StaticVsSelf');
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->replaceToken($index, 'static');
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $stackPtr
	 * @return bool
	 */
	protected function isInFinalClass(File $phpcsFile, int $stackPtr): bool {
		$tokens = $phpcsFile->getTokens();
		$classPtr = $phpcsFile->findPrevious(T_CLASS, $stackPtr - 1);
		if ($classPtr === false || empty($tokens[$classPtr]['scope_closer']) || $tokens[$classPtr]['scope_closer'] < $stackPtr) {
			return false;
		}

		$previous = $phpcsFile->findPrevious(Tokens::$emptyTokens, $classPtr - 1, null, true);

		return $previous !== false && $tokens[$previous]['code'] === T_FINAL;
	}

}
