# PSR-2-R Sniffer
[![Build Status](https://api.travis-ci.org/php-fig-rectified/psr2r-sniffer.svg)](https://travis-ci.org/php-fig-rectified/psr2r-sniffer)
[![Minimum PHP Version](http://img.shields.io/badge/php-%3E%3D%205.4-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/fig-r/psr2r-sniffer/license.svg)](https://packagist.org/packages/fig-r/psr2r-sniffer)
[![Total Downloads](https://poser.pugx.org/fig-r/psr2r-sniffer/d/total.svg)](https://packagist.org/packages/fig-r/psr2r-sniffer)
[![Coding Standards](https://img.shields.io/badge/cs-PSR--2--R-yellow.svg)](https://github.com/php-fig-rectified/fig-rectified-standards)

For details on PSR-2-R see [fig-rectified-standards](https://github.com/php-fig-rectified/fig-rectified-standards).

Documentation @ [/docs/](docs).

## PHP-CS Sniffs

This uses [squizlabs/PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/).
It can detect all issues and fix some of them automatically and is ideal for CI integration
(travis, jenkins, circlci etc).

### Usage
#### How to use for your project
Most likely you are using composer. As such, put it into the require-dev dependencies:
```
composer require --dev fig-r/psr2r-sniffer
```

You can then use it like this:
```
// Sniffs only
vendor/bin/phpcs --standard=/path/to/ruleset.xml /path/to/your/files

// Sniffs and fixes
vendor/bin/phpcbf --standard=/path/to/ruleset.xml /path/to/your/files
```
To use PSR-2-R by default replace `/path/to/ruleset.xml` above with `vendor/fig-r/psr2r-sniffer/PSR2R/ruleset.xml`.
If you don't want to append this all the time, make a small wrapper script that internally calls phpcs/phpcbf this way.

#### Example
So, if you want to run the sniffer over your root `src` folder, run:
```
vendor/bin/phpcs --standard=vendor/fig-r/psr2r-sniffer/PSR2R/ruleset.xml src/
```

#### Useful commands
Verbose output with `-v` is always useful.

If you want a list of all sniffs in this standard, use `-e`:
```
vendor/bin/phpcs --standard=/path/to/ruleset.xml -e
```
It will them all grouped by their standard name.

To just run a single sniff, use `--sniffs=...` and a comma separated list of sniffs, .e.g.:
```
vendor/bin/phpcs --standard=/path/to/ruleset.xml --sniffs=PSR2R.Files.EndFileNewline
```

Usually, if you run it over your complete repository, you would want to exclude dirs like `vendor`:
```
vendor/bin/phpcs --standard=/path/to/ruleset.xml --ignore=vendor/ ./
```

#### Windows usage
For Win OS you should be using `\` as separator:
```
vendor\bin\phpcs --standard=vendor\fig-r\psr2r-sniffer\PSR2R\ruleset.xml ./
```

#### Hook it into your IDE for live-correction
You can easily make a "watcher" for your IDE, so any file you work on, will be auto-corrected when (auto-)saving.
For PHPStorm, for example, make sure you switch `Show Console` to `never` to not be disturbed by it all the time.

### Writing new sniffs
You can contribute by adding new sniffs as per PSR-2-R standard.
Please also add tests.

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

#### Tokenizing Tool
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

#### Running own sniffs on this project
There is a convenience script to run all sniffs for this repository:
```
sh phpcs.sh
```
If you want to fix the fixable errors, use
```
sh phpcs.sh -f
```
