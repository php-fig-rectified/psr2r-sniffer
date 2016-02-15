# Documentation
For details on PSR-2-R see [fig-rectified-standards](https://github.com/php-fig-rectified/fig-rectified-standards).

## Documentation on the sniffer itself
This uses and extends [squizlabs/PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/).

## Sniffers available
The following sniffers are bundles together with `PSR-2-R` already, but you can
also use them standalone/separately in any way you like.

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
- PSR2R.PHP.NoSpacesCast
- PSR2R.PHP.NoIsNull
- PSR2R.PHP.PreferCastOverFunction
- PSR2R.PHP.PhpSapiConstant
- PSR2R.Whitespace.WhitespaceAfterReturn
- And some more inherited ruleset sniffers (> 20).

Most of the sniffers also provide auto-fixing using `-f` option where it is possible.

## Open Tasks
* It would be nice if some of these sniffers find their way into the contrib section of the original sniffer repo.
If anyone wants to contribute and add those there, that would be awesome.
* More tests
