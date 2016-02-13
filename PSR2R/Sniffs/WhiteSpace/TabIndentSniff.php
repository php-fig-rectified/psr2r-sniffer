<?php
namespace PSR2R\Sniffs\WhiteSpace;

/**
 * Check for any line starting with 4 spaces - which would indicate space indenting.
 */
class TabIndentSniff implements \PHP_CodeSniffer_Sniff {

	/**
	 * A list of tokenizers this sniff supports.
	 *
	 * @var array
	 */
	public $supportedTokenizers = [
		'PHP',
		'JS',
		'CSS'
	];

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return [T_WHITESPACE, T_DOC_COMMENT_OPEN_TAG];
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int $stackPtr  The position of the current token
	 *    in the stack passed in $tokens.
	 * @return void
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$stackPtr]['code'] !== T_WHITESPACE) {
			// Doc block
			for ($i = $stackPtr + 1; $i < $tokens[$stackPtr]['comment_closer']; $i++) {

				if ($tokens[$i]['code'] === 'PHPCS_T_DOC_COMMENT_WHITESPACE') {
					//FIXME
					//$this->fixTab($phpcsFile, $i, $tokens);
				}
			}
			return;
		}

		$line = $tokens[$stackPtr]['line'];
		if ($stackPtr > 0 && $tokens[($stackPtr - 1)]['line'] === $line) {
			return;
		}

		$this->fixTab($phpcsFile, $stackPtr, $tokens);
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPtr Stackpointer
	 * @param array $tokens Tokens
	 */
	protected function fixTab(\PHP_CodeSniffer_File $phpcsFile, $stackPtr, $tokens) {
		$content = $tokens[$stackPtr]['content'];
		$tabs = 0;
		while (strpos($content, '    ') === 0) {
			$content = substr($content, 4);
			$tabs++;
		}

		if ($tabs) {
			$error = ($tabs * 4) . ' spaces found, expected ' . $tabs . ' tabs';
			$fix = $phpcsFile->addFixableError($error, $stackPtr);
			if ($fix) {
				$phpcsFile->fixer->replaceToken($stackPtr, str_repeat("\t", $tabs) . $content);
			}
		}
	}

}
