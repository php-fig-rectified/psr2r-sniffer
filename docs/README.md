# Documentation
For details on PSR-2-R see [fig-rectified-standards](https://github.com/php-fig-rectified/fig-rectified-standards).

## Documentation on the sniffer itself
This uses and extends [squizlabs/PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/).

Runs on all OS, tested on Unix and Windows.

## Sniffers available
The following sniffers are bundles together with `PSR-2-R` already, but you can
also use them standalone/separately in any way you like.

**Currently PSR2R ships with over 100 sniffs.**

### PSR-2 Basics
- Inherited from included sniffer rulesets (> 10).

### PSR-2-R
- PSR2R.Classes.BraceOnSameLine (Always on the end of the same line)
- PSR2R.WhiteSpace.TabIndent (Use single tab instead of spaces)
- PSR2R.WhiteSpace.MethodSpacing (Newlines above and below each function/method)
- PSR2R.WhiteSpace.EmptyEnclosingLines (Newline at beginning and end of class)

### PSR-2-R Additions
- PSR2R.Commenting.DocComment
- PSR2R.Commenting.DocBlockShortType
- PSR2R.Commenting.DocBlockEnding
- PSR2R.Commenting.DocBlockPipe
- PSR2R.Commenting.DocBlockReturnType
- PSR2R.Commenting.DocBlockReturnSelf
- PSR2R.Commenting.FullyQualifiedClassNameInDocBlock
- PSR2R.ControlStructures.NoConditionalAssignment
- PSR2R.ControlStructures.ConditionalExpressionOrder
- PSR2R.Methods.MethodArgumentDefaultValue
- PSR2R.PHP.RemoveFunctionAlias
- PSR2R.PHP.ShortCast
- PSR2R.PHP.NoIsNull
- PSR2R.PHP.PreferCastOverFunction
- PSR2R.PHP.PhpSapiConstant
- PSR2R.Whitespace.CastSpacing
- PSR2R.Whitespace.WhitespaceAfterReturn
- PSR2R.Whitespace.ConcatenationSpacing
- PSR2R.Whitespace.CommaSpacing
- PSR2R.Whitespace.OperatorSpacing
- PSR2R.Whitespace.ObjectAttributeSpacing
- PSR2R.Whitespace.LanguageConstructSpacing
- ...
- And some more inherited ruleset sniffers (> 20).

Most of the sniffers also provide auto-fixing using `-f` option where it is possible.

## Open Tasks
* It would be nice if some of these sniffers find their way into the contrib section of the original sniffer repo.
If anyone wants to contribute and add those there, that would be awesome.
* More tests

## Using the original phpcs and phpcbf command tools
Of course you can also use the original cli commands:
```
// Sniffs only
vendor/bin/phpcs --standard=/path/to/ruleset.xml /path/to/your/files

// Sniffs and fixes
vendor/bin/phpcbf --standard=/path/to/ruleset.xml /path/to/your/files
```
To use PSR-2-R by default replace `/path/to/ruleset.xml` above with `vendor/fig-r/psr2r-sniffer/PSR2R/ruleset.xml`.
If you don't want to append this all the time, make a small wrapper script that internally calls phpcs/phpcbf this way.

### Example
So, if you want to run the sniffer over your root `src` folder, run:
```
vendor/bin/phpcs --standard=vendor/fig-r/psr2r-sniffer/PSR2R/ruleset.xml src/
```

## Writing sniffs
You can easily write new sniffers and run them isolated over test files.
It helps to use `-s` to see the names of the sniffers that reported issues.

For testing fixing, it can sometimes help to use `--suffix=fixed.php` etc to write to a different file.
This way you can check the output of this second file without the actual one being changed.

### Tokenizing Tool
It really helps to see what the code looks like for the sniffer.
So we can parse a PHP file into its tokens using the following tool:

```
bin/tokenize /path/to/file
```

With more verbose output:
```
bin/tokenize /path/to/file -v
```

For a file `MyClass.php` it will create a token file `MyClass.tokens.php` in the same folder.

Example output of a single line of PHP code:
```php
...
    protected static function _optionsToString($options) {
// T_WHITESPACE T_PROTECTED T_WHITESPACE T_STATIC T_WHITESPACE T_FUNCTION T_WHITESPACE T_STRING T_OPEN_PARENTHESIS T_VARIABLE T_CLOSE_PARENTHESIS T_WHITESPACE T_OPEN_CURLY_BRACKET T_WHITESPACE
...
```
Using the verbose option:
```php
...
    protected static function _optionsToString($options) {
// T_WHITESPACE (935) code=379, line=105, column=1, length=1, level=1, conditions={"9":358}, content=`\t`
// T_PROTECTED (936) code=348, line=105, column=2, length=9, level=1, conditions={"9":358}, content=`protected`
// T_WHITESPACE (937) code=379, line=105, column=11, length=1, level=1, conditions={"9":358}, content=` `
// T_STATIC (938) code=352, line=105, column=12, length=6, level=1, conditions={"9":358}, content=`static`
// T_WHITESPACE (939) code=379, line=105, column=18, length=1, level=1, conditions={"9":358}, content=` `
// T_FUNCTION (940) code=337, line=105, column=19, length=8, parenthesis_opener=943, parenthesis_closer=945, parenthesis_owner=940, scope_condition=940, scope_opener=947, scope_closer=1079, level=1, conditions={"9":358}, content=`function`
// T_WHITESPACE (941) code=379, line=105, column=27, length=1, level=1, conditions={"9":358}, content=` `
// T_STRING (942) code=310, line=105, column=28, length=16, level=1, conditions={"9":358}, content=`_optionsToString`
// T_OPEN_PARENTHESIS (943) code=PHPCS_T_OPEN_PARENTHESIS, line=105, column=44, length=1, parenthesis_opener=943, parenthesis_owner=940, parenthesis_closer=945, level=1, conditions={"9":358}, content=`(`
// T_VARIABLE (944) code=312, line=105, column=45, length=8, nested_parenthesis={"943":945}, level=1, conditions={"9":358}, content=`$options`
// T_CLOSE_PARENTHESIS (945) code=PHPCS_T_CLOSE_PARENTHESIS, line=105, column=53, length=1, parenthesis_owner=940, parenthesis_opener=943, parenthesis_closer=945, level=1, conditions={"9":358}, content=`)`
// T_WHITESPACE (946) code=379, line=105, column=54, length=1, level=1, conditions={"9":358}, content=` `
// T_OPEN_CURLY_BRACKET (947) code=PHPCS_T_OPEN_CURLY_BRACKET, line=105, column=55, length=1, bracket_opener=947, bracket_closer=1079, scope_condition=940, scope_opener=947, scope_closer=1079, level=1, conditions={"9":358}, content=`{`
// T_WHITESPACE (948) code=379, line=105, column=56, length=0, level=2, conditions={"9":358,"940":337}, content=`\n`
...
```

### Please also add tests.

To run the tests, use
```
// Download phpunit.phar
sh setup.sh

// Run all tests
php phpunit.phar
```

If you want to test a specific sniff, use
```
php phpunit.phar tests/Sniffs/FolderName/SnifferNameSniffTest.php
```

You can also test specific methods per Test file using `--filter=testNameOfMethodTest` etc.


### Running own sniffs on this project
There is a convenience script to run all sniffs for this repository:
```
sh phpcs.sh
```
If you want to fix the fixable errors, use
```
sh phpcs.sh -f
```
Make sure the root folder name is the same as the GitHub repository name (psr2r-sniffer) to exclude vendor as expected.
Once everything is green you can make a PR with your changes.
