<?php

namespace PSR2R\Sniffs\PHP;

/**
 */
class NoIsNullSniff implements \PHP_CodeSniffer_Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(T_STRING);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
	 * @param integer $stackPtr The position of the current token
	 *    in the stack passed in $tokens.
	 * @return void
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$wrongTokens = [T_FUNCTION, T_OBJECT_OPERATOR, T_NEW, T_DOUBLE_COLON];

		$tokens = $phpcsFile->getTokens();

		$tokenContent = $tokens[$stackPtr]['content'];
		if (strtolower($tokenContent) !== 'is_null') {
			return;
		}

		$previous = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
		if (!$previous || in_array($tokens[$previous]['code'], $wrongTokens)) {
			return;
		}

		$openingBrace = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
		if (!$openingBrace || $tokens[$openingBrace]['type'] !== 'T_OPEN_PARENTHESIS') {
			return;
		}

		$closingBrace = $tokens[$openingBrace]['parenthesis_closer'];

		$error = $tokenContent .'() found, should be strict === null check.';

		//FIXME: make fixable
		if (true) {
			$phpcsFile->addError($error, $stackPtr);
			return;
		}

		$fix = $phpcsFile->addFixableError($error, $stackPtr);
		if ($fix) {
			//
		}
	}

	protected $startIndex;

	protected $endIndex;


	/**
	 * @param \Symfony\CS\Tokenizer\Tokens|Token[] $tokens
	 *
	 * @return void
	 */
	protected function fixContent(Tokens $tokens)
	{
		$wrongTokens = [T_FUNCTION, T_OBJECT_OPERATOR, T_NEW, T_DOUBLE_COLON];

		foreach ($tokens as $index => $token) {
			$tokenContent = strtolower($token->getContent());
			if ($tokenContent !== self::STRING_MATCH) {
				continue;
			}

			$prevIndex = $tokens->getPrevNonWhitespace($index);
			if (in_array($tokens[$prevIndex]->getId(), $wrongTokens, true)) {
				continue;
			}

			$nextIndex = $tokens->getNextMeaningfulToken($index);
			if ($tokens[$nextIndex]->getContent() !== '(') {
				continue;
			}

			$lastIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $nextIndex);

			$needsBrackets = false;
			if ($tokens[$prevIndex]->isCast() || $tokens[$prevIndex]->isGivenKind([T_IS_NOT_EQUAL, T_IS_EQUAL, T_IS_IDENTICAL, T_IS_NOT_IDENTICAL])) {
				$needsBrackets = true;
			}

			$endBraceIndex = $tokens->getNextTokenOfKind($nextIndex, [')']);

			$nextEndBraceIndex = $tokens->getNextMeaningfulToken($endBraceIndex);
			if ($tokens[$nextEndBraceIndex]->isGivenKind([T_IS_NOT_EQUAL, T_IS_EQUAL, T_IS_IDENTICAL, T_IS_NOT_IDENTICAL])) {
				$needsBrackets = true;
			}

			// Special fix: true/false === is_null() => !==/=== null
			if ($this->isFixableComparison($tokens, $prevIndex, $nextEndBraceIndex)) {
				$needsBrackets = false;
			}

			$negated = false;
			if ($tokens[$prevIndex]->getContent() === '!') {
				$negated = true;
			}

			$replacement = '';
			for ($i = $nextIndex + 1; $i < $lastIndex; ++$i) {
				if (!$tokens[$i]->isGivenKind([T_VARIABLE, T_OBJECT_OPERATOR, T_STRING, T_CONST, T_DOUBLE_COLON, T_CONSTANT_ENCAPSED_STRING, T_LNUMBER])) {
					continue 2;
				}

				$replacement .= $tokens[$i]->getContent();
			}

			if ($this->startIndex !== null) {
				$index = $this->startIndex;
				$this->endIndex = $lastIndex;
				$negated = $tokens[$this->startIndex]->getContent() === 'false' ? true : false;
				$needsBrackets = false;
			}

			if ($this->endIndex !== null) {
				$lastIndex = $this->endIndex;

				if ($this->startIndex !== null) {
					$token = $tokens[$this->startIndex];
				} else {
					$token = $tokens[$this->endIndex];
				}

				$negated = $token->getContent() === 'false' ? true : false;
				$needsBrackets = false;
			}

			$replacement .= ' ' . ($negated ? '!' : '=') . '== null';
			if ($needsBrackets) {
				$replacement = '(' . $replacement . ')';
			}

			$offset = 0;
			if ($negated && $this->startIndex === null && $this->endIndex === null) {
				$offset = -($index - $prevIndex);
			}

			$index += $offset;
			for ($i = $index; $i < $lastIndex; ++$i) {
				$tokens[$i]->clear();
			}
			$tokens[$lastIndex]->setContent($replacement);
		}
	}

	/**
	 * @param \Symfony\CS\Tokenizer\Tokens $tokens
	 * @param int $prevIndex
	 * @param int $nextEndBraceIndex
	 *
	 * @return bool
	 */
	protected function isFixableComparison($tokens, $prevIndex, $nextEndBraceIndex)
	{
		if ($tokens[$prevIndex]->isGivenKind([T_IS_NOT_IDENTICAL, T_IS_IDENTICAL])) {
			$prevPrevIndex = $tokens->getPrevMeaningfulToken($prevIndex);
			if ($tokens[$prevPrevIndex]->getContent() === 'true' || $tokens[$prevPrevIndex]->getContent() === 'false') {
				$this->startIndex = $prevPrevIndex;

				return true;
			}
		}

		if ($nextEndBraceIndex === null) {
			return false;
		}

		if ($tokens[$nextEndBraceIndex]->isGivenKind([T_IS_NOT_IDENTICAL, T_IS_IDENTICAL])) {
			$nextNextIndex = $tokens->getNextMeaningfulToken($nextEndBraceIndex);

			if ($tokens[$nextNextIndex]->getContent() === 'true' || $tokens[$nextNextIndex]->getContent() === 'false') {
				$this->endIndex = $nextNextIndex;

				return true;
			}
		}

		return false;
	}

}
