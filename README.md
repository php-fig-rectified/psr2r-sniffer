# PSR-2-R Sniffer
[![CI](https://github.com/php-fig-rectified/psr2r-sniffer/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/php-fig-rectified/psr2r-sniffer/actions/workflows/ci.yml)
[![Latest Stable Version](https://poser.pugx.org/fig-r/psr2r-sniffer/v/stable.svg)](https://packagist.org/packages/fig-r/psr2r-sniffer)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.3-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/fig-r/psr2r-sniffer/license.svg)](https://packagist.org/packages/fig-r/psr2r-sniffer)
[![Total Downloads](https://poser.pugx.org/fig-r/psr2r-sniffer/d/total.svg)](https://packagist.org/packages/fig-r/psr2r-sniffer)
[![Coding Standards](https://img.shields.io/badge/cs-PSR--2--R-yellow.svg)](https://github.com/php-fig-rectified/fig-rectified-standards)

For details on PSR-2-R see [fig-rectified-standards](https://github.com/php-fig-rectified/fig-rectified-standards).

For PHP version compatibility see [version map](https://github.com/php-fig-rectified/psr2r-sniffer/wiki).

Documentation @ [/docs/](docs).

## Included sniffs
The following sniffers are bundles together as `PSR2R` already, but you can
also use them standalone/separately in any way you like.

**Currently PSR2R ships with over 180 sniffs.**

* [List of included sniffs](docs/sniffs.md)

Furthermore, the following ones are recommended to add:
- Spryker.Commenting.DocBlockThrows
- Spryker.Commenting.DocBlockTagGrouping
- SlevomatCodingStandard.Functions.DisallowEmptyFunction
- SlevomatCodingStandard.PHP.UselessParentheses

This library uses [squizlabs/PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/).
It can detect all issues and fix some of them automatically and is ideal for CI integration
(travis, jenkins, circleci etc).

### Usage

#### How to use for your project
Most likely you are using composer. As such, put it into the require-dev dependencies:
```
composer require --dev fig-r/psr2r-sniffer
```

Set up a `phpcs.xml` in the root of your library.
Define here the standard to use as well as the project paths, usually
```xml
<rule ref="vendor/fig-r/psr2r-sniffer/PSR2R/ruleset.xml"/>

<file>src/</file>
<file>tests/</file>
```

You can then use it like this:
```
// Sniffs only
vendor/bin/phpcs {path/file}

// Sniffs and fixes
vendor/bin/phpcbf {path/file}
```
The path is optional if you want to run it on a specific subset of your defined paths.
Otherwise, it defaults to the paths defined in your XML file.

### Customizing
You can always customize your `phpcs.xml` further, e.g.:

```xml
<?xml version="1.0"?>
<ruleset name="app">

	<arg value="nps"/>

	<file>src/</file>
	<file>tests/</file>

	<exclude-pattern>/config/Migrations/</exclude-pattern>

	<!-- Add main ruleset -->
	<rule ref="vendor/fig-r/psr2r-sniffer/PSR2R/ruleset.xml"/>

	<!-- Silence certain sniffs/rules -->
	<rule ref="PSR2.Methods.MethodDeclaration.Underscore">
		<severity>0</severity>
	</rule>
	<rule ref="PSR2.Classes.PropertyDeclaration.Underscore">
		<severity>0</severity>
	</rule>

	<!-- Define your own additional sniffs here -->

</ruleset>
```


#### Useful commands
Verbose output with `-v` is always useful. With `-vv` or even `-vvv` you get even more debug output.

If you want a list of all sniffs in this standard, use `-e`:
```
vendor/bin/phpcs -e
```
It will list them all grouped by their standard name and sniff type.

To just run a single sniff, use `--sniffs=...` and a comma separated list of sniffs, e.g.:
```
vendor/bin/phpcs --sniffs=PSR2R.Files.EndFileNewline
```

Usually, if you run it over your complete repository, you would want to exclude dirs like `tests/test_files/`:
```
vendor/bin/phpcs --ignore=/tests/test_files/
```

#### Windows usage
For Win OS you should be using `\` as separator:
```
vendor\bin\phpcs
```

#### Include it in your IDE via hotkey
E.g. for PHPStorm:
* Open Settings -> Tools -> External Tools
* Add a new tool named "cs-sniffer" and set Program to `$ProjectFileDir$/vendor/bin/phpcs`, Parameters to `--standard=$ProjectFileDir$/vendor/fig-r/psr2r-sniffer/PSR2R/ruleset.xml -p $FilePath$` and Working directoy to `$ProjectFileDir$`.
* Add a new tool named "cs-fixer" and set Program to `$ProjectFileDir$/vendor/bin/phpcbf`, Parameters to `--standard=$ProjectFileDir$/vendor/fig-r/psr2r-sniffer/PSR2R/ruleset.xml -v $FilePath$` and Working directoy to `$ProjectFileDir$`.
* Optionally uncheck "Open console" if you don't want to see any output here for the fixer.
* Now set up your hotkeys under Settings -> Keymap (search for cs-sniffer and cs-fixer). E.g. `Control + Comma` for sniffing, and `Control + Dot` for fixing.

##### Hook it into your IDE for live-correction
You can also easily make a "watcher" for your IDE, so any file you work on, will be auto-corrected when (auto-)saving.
But here you should better only whitelist certain sniffs that only add things and don't remove anything.

Note: For PHPStorm, for example, make sure you switch `Show Console` to `never` to not be disturbed by it all the time.

### Writing new sniffs
You can contribute by adding new sniffs as per PSR-2-R standard.

#### Tokenizing Tool
It really helps to see what the code looks like for the sniffer.
So we can parse a PHP file into its tokens using the following tool:

```
bin/tokenize /path/to/file
```
(If you run this from your application, it will have to be run as `vendor/bin/tokenize`)

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
