# PSR2R Code Sniffer


The PSR2R standard contains 148 sniffs

Generic (20 sniffs)
-------------------
- Generic.Arrays.DisallowLongArraySyntax
- Generic.CodeAnalysis.ForLoopShouldBeWhileLoop
- Generic.CodeAnalysis.ForLoopWithTestFunctionCall
- Generic.CodeAnalysis.JumbledIncrementer
- Generic.CodeAnalysis.UnconditionalIfStatement
- Generic.CodeAnalysis.UnnecessaryFinalModifier
- Generic.ControlStructures.InlineControlStructure
- Generic.Files.ByteOrderMark
- Generic.Files.LineEndings
- Generic.Files.LineLength
- Generic.Formatting.DisallowMultipleStatements
- Generic.Formatting.NoSpaceAfterCast
- Generic.Functions.FunctionCallArgumentSpacing
- Generic.NamingConventions.UpperCaseConstantName
- Generic.PHP.DeprecatedFunctions
- Generic.PHP.DisallowShortOpenTag
- Generic.PHP.ForbiddenFunctions
- Generic.PHP.LowerCaseConstant
- Generic.PHP.LowerCaseKeyword
- Generic.PHP.NoSilencedErrors

PEAR (4 sniffs)
---------------
- PEAR.Classes.ClassDeclaration
- PEAR.ControlStructures.ControlSignature
- PEAR.Functions.ValidDefaultValue
- PEAR.NamingConventions.ValidClassName

PSR1 (3 sniffs)
---------------
- PSR1.Classes.ClassDeclaration
- PSR1.Files.SideEffects
- PSR1.Methods.CamelCapsMethodName

PSR2 (11 sniffs)
----------------
- PSR2.Classes.ClassDeclaration
- PSR2.Classes.PropertyDeclaration
- PSR2.ControlStructures.ControlStructureSpacing
- PSR2.ControlStructures.ElseIfDeclaration
- PSR2.Files.ClosingTag
- PSR2.Files.EndFileNewline
- PSR2.Methods.FunctionCallSignature
- PSR2.Methods.FunctionClosingBrace
- PSR2.Methods.MethodDeclaration
- PSR2.Namespaces.NamespaceDeclaration
- PSR2.Namespaces.UseDeclaration

PSR2R (40 sniffs)
-----------------
- PSR2R.Classes.BraceOnSameLine
- PSR2R.Classes.ClassCreateInstance
- PSR2R.Classes.InterfaceName
- PSR2R.Classes.MethodMultilineArguments
- PSR2R.Classes.PropertyDeclaration
- PSR2R.Classes.SelfAccessor
- PSR2R.Classes.TraitName
- PSR2R.Commenting.DocBlockEnding
- PSR2R.Commenting.DocBlockParam
- PSR2R.Commenting.DocBlockParamArray
- PSR2R.Commenting.DocBlockParamNoOp
- PSR2R.Commenting.DocBlockShortType
- PSR2R.Commenting.DocBlockTagTypes
- PSR2R.Commenting.DocBlockVarWithoutName
- PSR2R.Commenting.DocComment
- PSR2R.Commenting.NoControlStructureEndComment
- PSR2R.ControlStructures.ElseIfDeclaration
- PSR2R.ControlStructures.SwitchDeclaration
- PSR2R.ControlStructures.TernarySpacing
- PSR2R.ControlStructures.UnneededElse
- PSR2R.Files.ClosingTag
- PSR2R.Files.EndFileNewline
- PSR2R.Namespaces.NamespaceDeclaration
- PSR2R.Namespaces.NoInlineFullyQualifiedClassName
- PSR2R.Namespaces.UseDeclaration
- PSR2R.PHP.DuplicateSemicolon
- PSR2R.PHP.ListComma
- PSR2R.PHP.NoShortOpenTag
- PSR2R.PHP.PreferStaticOverSelf
- PSR2R.PHP.SingleQuote
- PSR2R.WhiteSpace.ArraySpacing
- PSR2R.WhiteSpace.CastSpacing
- PSR2R.WhiteSpace.DocBlockAlignment
- PSR2R.WhiteSpace.LanguageConstructSpacing
- PSR2R.WhiteSpace.MethodSpacing
- PSR2R.WhiteSpace.ObjectAttributeSpacing
- PSR2R.WhiteSpace.OperatorSpacing
- PSR2R.WhiteSpace.TabAndSpace
- PSR2R.WhiteSpace.TabIndent
- PSR2R.WhiteSpace.UnaryOperatorSpacing

SlevomatCodingStandard (6 sniffs)
---------------------------------
- SlevomatCodingStandard.Arrays.TrailingArrayComma
- SlevomatCodingStandard.Namespaces.AlphabeticallySortedUses
- SlevomatCodingStandard.Namespaces.UnusedUses
- SlevomatCodingStandard.Namespaces.UseFromSameNamespace
- SlevomatCodingStandard.TypeHints.ParameterTypeHintSpacing
- SlevomatCodingStandard.TypeHints.ReturnTypeHintSpacing

Spryker (40 sniffs)
-------------------
- Spryker.Classes.ClassFileName
- Spryker.Classes.MethodArgumentDefaultValue
- Spryker.Classes.MethodDeclaration
- Spryker.Commenting.DocBlock
- Spryker.Commenting.DocBlockNoInlineAlignment
- Spryker.Commenting.DocBlockParam
- Spryker.Commenting.DocBlockParamAllowDefaultValue
- Spryker.Commenting.DocBlockParamArray
- Spryker.Commenting.DocBlockParamNotJustNull
- Spryker.Commenting.DocBlockPipeSpacing
- Spryker.Commenting.DocBlockReturnSelf
- Spryker.Commenting.DocBlockReturnTag
- Spryker.Commenting.DocBlockReturnVoid
- Spryker.Commenting.DocBlockTagGrouping
- Spryker.Commenting.DocBlockTagOrder
- Spryker.Commenting.DocBlockThrows
- Spryker.Commenting.DocBlockVar
- Spryker.Commenting.FullyQualifiedClassNameInDocBlock
- Spryker.Commenting.InlineDocBlock
- Spryker.ControlStructures.ConditionalExpressionOrder
- Spryker.ControlStructures.ControlStructureSpacing
- Spryker.ControlStructures.NoInlineAssignment
- Spryker.Formatting.ArrayDeclaration
- Spryker.Namespaces.UseStatement
- Spryker.Namespaces.UseWithAliasing
- Spryker.Namespaces.UseWithLeadingBackslash
- Spryker.PHP.NoIsNull
- Spryker.PHP.PhpSapiConstant
- Spryker.PHP.PreferCastOverFunction
- Spryker.PHP.RemoveFunctionAlias
- Spryker.PHP.ShortCast
- Spryker.WhiteSpace.CommaSpacing
- Spryker.WhiteSpace.ConcatenationSpacing
- Spryker.WhiteSpace.EmptyLines
- Spryker.WhiteSpace.FunctionSpacing
- Spryker.WhiteSpace.ImplicitCastSpacing
- Spryker.WhiteSpace.MemberVarSpacing
- Spryker.WhiteSpace.MethodSpacing
- Spryker.WhiteSpace.ObjectAttributeSpacing
- Spryker.WhiteSpace.OperatorSpacing

Squiz (23 sniffs)
-----------------
- Squiz.Arrays.ArrayBracketSpacing
- Squiz.Classes.LowercaseClassKeywords
- Squiz.Classes.ValidClassName
- Squiz.ControlStructures.ControlSignature
- Squiz.ControlStructures.ForEachLoopDeclaration
- Squiz.ControlStructures.ForLoopDeclaration
- Squiz.ControlStructures.LowercaseDeclaration
- Squiz.Functions.FunctionDeclaration
- Squiz.Functions.FunctionDeclarationArgumentSpacing
- Squiz.Functions.LowercaseFunctionKeywords
- Squiz.Operators.ValidLogicalOperators
- Squiz.PHP.Eval
- Squiz.PHP.NonExecutableCode
- Squiz.Scope.MemberVarScope
- Squiz.Scope.MethodScope
- Squiz.Scope.StaticThisUsage
- Squiz.WhiteSpace.CastSpacing
- Squiz.WhiteSpace.ControlStructureSpacing
- Squiz.WhiteSpace.LogicalOperatorSpacing
- Squiz.WhiteSpace.ScopeClosingBrace
- Squiz.WhiteSpace.ScopeKeywordSpacing
- Squiz.WhiteSpace.SemicolonSpacing
- Squiz.WhiteSpace.SuperfluousWhitespace

Zend (1 sniff)
---------------
- Zend.Files.ClosingTag;