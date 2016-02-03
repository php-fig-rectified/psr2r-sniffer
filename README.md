# PSR-2-R Sniffer
For details on PSR-2-R see [fig-rectified-standards](https://github.com/php-fig-rectified/fig-rectified-standards).

Documentation @ [/docs/](docs).

## PHP-CS Sniffs

This uses [squizlabs/PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/).
It can detect all issues and fix some of them automatically and is ideal for CI integration
(travis, jenkins, circlci etc).

### Configuration
You can use PSR-2-R by default.
For that replace `/path/to/ruleset.xml` in the usage section with `vendor/fig-r/psr-2-r/sniffs/PSR2R/ruleset.xml`

If you don't want to append this all the time, make a small wrapper script that internally calls phpcs/phpcbf this way.

### Usage
```
vendor/bin/phpcs --standard=/path/to/ruleset.xml // Sniffs only
vendor/bin/phpcbf --standard=/path/to/ruleset.xml // Sniffs and fixes
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
