# PSR-2-R Sniffer
For details on PSR-2-R see [fig-rectified-standards](https://github.com/php-fig-rectified/fig-rectified-standards).

Documentation @ [/docs/](docs).

## PHP-CS Sniffs

This uses [squizlabs/PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/).
It can detect all issues and fix some of them automatically and is ideal for CI integration
(travis, jenkins, circlci etc).


### How to use for your project
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
To use PSR-2-R by default replace `/path/to/ruleset.xml` above with `vendor/fig-r/psr-2-r/sniffs/PSR2R/ruleset.xml`.
If you don't want to append this all the time, make a small wrapper script that internally calls phpcs/phpcbf this way.

### Example
So, if you want to run the sniffer over your root `src` folder, run:
```
vendor/bin/phpcs --standard=vendor/fig-r/psr-2-r/sniffs/PSR2R/ruleset.xml src
```

### Useful commands
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
bash tokenize.sh /path/to/file
```

With more verbose output:
```
bash tokenize.sh /path/to/file -v
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
// T_WHITESPACE (858) line=100, column=1, length=4, level=1, conditions={"9":358}
// T_PROTECTED (859) line=100, column=5, length=9, level=1, conditions={"9":358}
// T_WHITESPACE (860) line=100, column=14, length=1, level=1, conditions={"9":358}
// T_STATIC (861) line=100, column=15, length=6, level=1, conditions={"9":358}
// T_WHITESPACE (862) line=100, column=21, length=1, level=1, conditions={"9":358}
// T_FUNCTION (863) line=100, column=22, length=8, parenthesis_opener=866, parenthesis_closer=868, parenthesis_owner=863, scope_condition=863, scope_opener=870, scope_closer=1002, level=1, conditions={"9":358}
// T_WHITESPACE (864) line=100, column=30, length=1, level=1, conditions={"9":358}
// T_STRING (865) line=100, column=31, length=16, level=1, conditions={"9":358}
// T_OPEN_PARENTHESIS (866) line=100, column=47, length=1, parenthesis_opener=866, parenthesis_owner=863, parenthesis_closer=868, level=1, conditions={"9":358}
// T_VARIABLE (867) line=100, column=48, length=8, nested_parenthesis={"866":868}, level=1, conditions={"9":358}
// T_CLOSE_PARENTHESIS (868) line=100, column=56, length=1, parenthesis_owner=863, parenthesis_opener=866, parenthesis_closer=868, level=1, conditions={"9":358}
// T_WHITESPACE (869) line=100, column=57, length=1, level=1, conditions={"9":358}
// T_OPEN_CURLY_BRACKET (870) line=100, column=58, length=1, bracket_opener=870, bracket_closer=1002, scope_condition=863, scope_opener=870, scope_closer=1002, level=1, conditions={"9":358}
// T_WHITESPACE (871) line=100, column=59, length=0, level=2, conditions={"9":358,"863":337}
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
