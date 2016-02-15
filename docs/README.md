# Documentation
For details on PSR-2-R see [fig-rectified-standards](https://github.com/php-fig-rectified/fig-rectified-standards).

## Documentation on the sniffer itself
This uses and extends [squizlabs/PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/).

## Sniffers available
The following sniffers are bundles together with PSR-2-R already, but you can
also use them standalone/separately in any way you like.

### PSR-2-R
- PSR2R.Classes.BraceOnSameLineSniff (Always on the end of the same line)
- PSR2R.Classes.ValidClassBracketsSniff (Always on the end of the same line)
- PSR2R.WhiteSpace.TabIndentSniff (Use single tab instead of spaces)
- PSR2R.WhiteSpace.FunctionSpacingSniff (Newlines above and below each function/method)
- PSR2R.WhiteSpace.EmptyEnclosingLinesSniff (Newline at beginning and end of class)

### Additions
- PSR2R.Commenting.PhpdocParamsSniff
- PSR2R.Commenting.PhpdocPipeSniff
- PSR2R.Commenting.PhpdocReturnSelfSniff
- PSR2R.ControlStructures.NoInlineAssignmentSniff
- PSR2R.ControlStructures.ConditionalExpressionOrderSniff
- PSR2R.Methods.MethodArgumentDefaultValueSniff
- PSR2R.PHP.RemoveFunctionAliasSniff
- PSR2R.PHP.ShortCastSniff
- PSR2R.PHP.NoSpacesCastSniff
- PSR2R.PHP.NoIsNullSniff
- PSR2R.PHP.PreferCastOverFunctionSniff
- PSR2R.PHP.PhpSapiConstantSniff
- PSR2R.Whitespace.WhitespaceAfterReturnSniff

Most of the sniffers also provide auto-fixing using `-f` option where it is possible.

## Open Tasks
* It would be nice if some of these sniffers find their way into the contrib section of the original sniffer repo.
If anyone wants to contribute and add those there, that would be awesome.
* More tests
