<?php
/**
 * Generic_Sniffs_Methods_OpeningMethodBraceBsdAllmanSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
namespace PSR2R\Sniffs\Functions;

class OpeningFunctionBraceSniff implements \PHP_CodeSniffer_Sniff
{
    /**
     * Should this sniff check function braces?
     *
     * @var bool
     */
    public $checkFunctions = true;

    /**
     * Should this sniff check closure braces?
     *
     * @var bool
     */
    public $checkClosures = false;


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_FUNCTION,
                T_CLOSURE,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(\PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            return;
        }

        if (($tokens[$stackPtr]['code'] === T_FUNCTION
            && (bool) $this->checkFunctions === false)
            || ($tokens[$stackPtr]['code'] === T_CLOSURE
            && (bool) $this->checkClosures === false)
        ) {
            return;
        }

        $openingBrace = $tokens[$stackPtr]['scope_opener'];
        $closeBracket = $tokens[$stackPtr]['parenthesis_closer'];
        if ($tokens[$stackPtr]['code'] === T_CLOSURE) {
            $use = $phpcsFile->findNext(T_USE, ($closeBracket + 1), $tokens[$stackPtr]['scope_opener']);
            if ($use !== false) {
                $openBracket  = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($use + 1));
                $closeBracket = $tokens[$openBracket]['parenthesis_closer'];
            }
        }

        $functionLine = $tokens[$closeBracket]['line'];
        $braceLine    = $tokens[$openingBrace]['line'];

        $lineDifference = ($braceLine - $functionLine);

        if ($lineDifference !== 0) {
            $error = 'Opening brace should be on a same line';
            $fix   = $phpcsFile->addFixableError($error, $openingBrace, 'BraceOnNextLine');
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();

				$phpcsFile->fixer->replaceToken($closeBracket, $tokens[$closeBracket]['content'] . ' ');

				for ($i = $closeBracket + 1; $i < $openingBrace; $i++) {
					$phpcsFile->fixer->replaceToken($i, '');
				}

                $phpcsFile->fixer->endChangeset();
            }

            $phpcsFile->recordMetric($stackPtr, 'Function opening brace placement', 'same line');
        }

        $next = $phpcsFile->findNext(T_WHITESPACE, ($openingBrace + 1), null, true);
        if ($tokens[$next]['line'] === $tokens[$openingBrace]['line']) {
            if ($next === $tokens[$stackPtr]['scope_closer']) {
                // Ignore empty functions.
                return;
            }

            $error = 'Opening brace must be the last content on the line';
            $fix   = $phpcsFile->addFixableError($error, $openingBrace, 'ContentAfterBrace');
            if ($fix === true) {
                $phpcsFile->fixer->addNewline($openingBrace);
            }
        }

        // Only continue checking if the opening brace looks good.
        if ($lineDifference !== 1) {
            return;
        }

        // We need to actually find the first piece of content on this line,
        // as if this is a method with tokens before it (public, static etc)
        // or an if with an else before it, then we need to start the scope
        // checking from there, rather than the current token.
        $lineStart = $phpcsFile->findFirstOnLine(T_WHITESPACE, $stackPtr, true);

        // The opening brace is on the correct line, now it needs to be
        // checked to be correctly indented.
        $startColumn = $tokens[$lineStart]['column'];
        $braceIndent = $tokens[$openingBrace]['column'];

        if ($braceIndent !== $startColumn) {
            $expected = ($startColumn - 1);
            $found    = ($braceIndent - 1);

            $error = 'Opening brace indented incorrectly; expected %s spaces, found %s';
            $data  = array(
                      $expected,
                      $found,
                     );

            $fix = $phpcsFile->addFixableError($error, $openingBrace, 'BraceIndent', $data);
            if ($fix === true) {
                $indent = str_repeat(' ', $expected);
                if ($found === 0) {
                    $phpcsFile->fixer->addContentBefore($openingBrace, $indent);
                } else {
                    $phpcsFile->fixer->replaceToken(($openingBrace - 1), $indent);
                }
            }
        }//end if

        $phpcsFile->recordMetric($stackPtr, 'Function opening brace placement', 'new line');

    }//end process()


}
