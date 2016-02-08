<?php

namespace PSR2R\Sniffs\PHP;

/**
 */
class PreferCastOverFunctionSniff implements \PHP_CodeSniffer_Sniff {

	/**
	 * @var array
	 */
	protected static $matching = [
		'strval' => 'string',
		'intval' => 'int',
		'floatval' => 'float',
		'boolval' => 'bool',
	];

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
		$key = strtolower($tokenContent);
		if (!isset(self::$matching[$key])) {
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

		$error = $tokenContent .'() found, should be ' . self::$matching[$key] . ' cast.';

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

    /**
     * @param \SplFileInfo $file
     * @param string $content
     *
     * @return string
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $this->fixContent($tokens);

        return $tokens->generateCode();
    }

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
            if (empty($tokenContent) || !isset(self::$matching[$tokenContent])) {
                continue;
            }

            $prevIndex = $tokens->getPrevNonWhitespace($index);
            if (in_array($tokens[$prevIndex]->getId(), $wrongTokens, true)) {
                continue;
            }

            $openingBrace = $tokens->getNextMeaningfulToken($index);
            if ($tokens[$openingBrace]->getContent() !== '(') {
                continue;
            }

            $closingBrace = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openingBrace);

            // Skip for non-trivial cases
            for ($i = $openingBrace + 1; $i < $closingBrace; ++$i) {
                if ($tokens[$i]->equals(',')) {
                    continue 2;
                }
            }

            $cast = '(' . self::$matching[$tokenContent] . ')';
            $tokens[$index]->setContent($cast);
            $tokens[$openingBrace]->setContent('');
            $tokens[$closingBrace]->setContent('');
        }
    }

    /**
     * Must run before any cast modifications
     *
     * @return int
     */
    public function getPriority()
    {
        return 10;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return FixerInterface::NONE_LEVEL;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Always use simple casts instead of method invocation.';
    }

}
