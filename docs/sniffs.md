# PSR2R Code Sniffer


The PSR2R standard contains 123 sniffs

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
- Generic.Formatting.DisallowMultipleStatements
- Generic.Formatting.NoSpaceAfterCast
- Generic.NamingConventions.UpperCaseConstantName
- Generic.PHP.DeprecatedFunctions
- Generic.PHP.DisallowShortOpenTag
- Generic.PHP.ForbiddenFunctions
- Generic.PHP.LowerCaseConstant
- Generic.PHP.LowerCaseKeyword
- Generic.PHP.LowerCaseType
- Generic.PHP.NoSilencedErrors
- Generic.WhiteSpace.IncrementDecrementSpacing

PEAR (3 sniffs)
---------------
- PEAR.ControlStructures.ControlSignature
- PEAR.Functions.ValidDefaultValue
- PEAR.NamingConventions.ValidClassName

PSR1 (3 sniffs)
---------------
- PSR1.Classes.ClassDeclaration
- PSR1.Files.SideEffects
- PSR1.Methods.CamelCapsMethodName

PSR12 (5 sniffs)
----------------
- PSR12.Classes.ClassInstantiation
- PSR12.Functions.NullableTypeDeclaration
- PSR12.Keywords.ShortFormTypeKeywords
- PSR12.Namespaces.CompoundNamespaceDepth
- PSR12.Operators.OperatorSpacing

PSR2 (4 sniffs)
---------------
- PSR2.ControlStructures.ElseIfDeclaration
- PSR2.Files.EndFileNewline
- PSR2.Namespaces.NamespaceDeclaration
- PSR2.Namespaces.UseDeclaration

PSR2R (66 sniffs)
-----------------
- PSR2R.Classes.BraceOnSameLine
- PSR2R.Classes.ClassCreateInstance
- PSR2R.Classes.ClassFileName
- PSR2R.Classes.InterfaceName
- PSR2R.Classes.PropertyDeclaration
- PSR2R.Classes.SelfAccessor
- PSR2R.Classes.TraitName
- PSR2R.Commenting.DocBlock
- PSR2R.Commenting.DocBlockEnding
- PSR2R.Commenting.DocBlockParam
- PSR2R.Commenting.DocBlockParamAllowDefaultValue
- PSR2R.Commenting.DocBlockParamArray
- PSR2R.Commenting.DocBlockParamNoOp
- PSR2R.Commenting.DocBlockParamNotJustNull
- PSR2R.Commenting.DocBlockPipeSpacing
- PSR2R.Commenting.DocBlockReturnSelf
- PSR2R.Commenting.DocBlockReturnTag
- PSR2R.Commenting.DocBlockReturnVoid
- PSR2R.Commenting.DocBlockShortType
- PSR2R.Commenting.DocBlockTagTypes
- PSR2R.Commenting.DocBlockTypeOrder
- PSR2R.Commenting.DocBlockVar
- PSR2R.Commenting.DocBlockVarNotJustNull
- PSR2R.Commenting.DocBlockVarWithoutName
- PSR2R.Commenting.DocBlockVariableNullHintLast
- PSR2R.Commenting.DocComment
- PSR2R.Commenting.FullyQualifiedClassNameInDocBlock
- PSR2R.Commenting.InlineDocBlock
- PSR2R.Commenting.NoControlStructureEndComment
- PSR2R.ControlStructures.ConditionalExpressionOrder
- PSR2R.ControlStructures.ControlStructureSpacing
- PSR2R.ControlStructures.ElseIfDeclaration
- PSR2R.ControlStructures.NoInlineAssignment
- PSR2R.ControlStructures.SwitchDeclaration
- PSR2R.ControlStructures.TernarySpacing
- PSR2R.ControlStructures.UnneededElse
- PSR2R.Files.ClosingTag
- PSR2R.Files.EndFileNewline
- PSR2R.Methods.MethodDeclaration
- PSR2R.Methods.MethodMultilineArguments
- PSR2R.Namespaces.NoInlineFullyQualifiedClassName
- PSR2R.Namespaces.UnusedUseStatement
- PSR2R.Namespaces.UseInAlphabeticalOrder
- PSR2R.PHP.DuplicateSemicolon
- PSR2R.PHP.ListComma
- PSR2R.PHP.NoIsNull
- PSR2R.PHP.NoShortOpenTag
- PSR2R.PHP.PhpSapiConstant
- PSR2R.PHP.PreferCastOverFunction
- PSR2R.PHP.PreferStaticOverSelf
- PSR2R.PHP.RemoveFunctionAlias
- PSR2R.PHP.ShortCast
- PSR2R.PHP.SingleQuote
- PSR2R.WhiteSpace.ArraySpacing
- PSR2R.WhiteSpace.CommaSpacing
- PSR2R.WhiteSpace.ConcatenationSpacing
- PSR2R.WhiteSpace.DocBlockAlignment
- PSR2R.WhiteSpace.EmptyEnclosingLine
- PSR2R.WhiteSpace.EmptyLines
- PSR2R.WhiteSpace.LanguageConstructSpacing
- PSR2R.WhiteSpace.MethodSpacing
- PSR2R.WhiteSpace.ObjectAttributeSpacing
- PSR2R.WhiteSpace.OperatorSpacing
- PSR2R.WhiteSpace.TabAndSpace
- PSR2R.WhiteSpace.TabIndent
- PSR2R.WhiteSpace.UnaryOperatorSpacing

Squiz (21 sniffs)
-----------------
- Squiz.Arrays.ArrayBracketSpacing
- Squiz.Classes.LowercaseClassKeywords
- Squiz.Classes.ValidClassName
- Squiz.ControlStructures.ForEachLoopDeclaration
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
- Squiz.WhiteSpace.LanguageConstructSpacing
- Squiz.WhiteSpace.LogicalOperatorSpacing
- Squiz.WhiteSpace.MemberVarSpacing
- Squiz.WhiteSpace.ScopeKeywordSpacing
- Squiz.WhiteSpace.SemicolonSpacing
- Squiz.WhiteSpace.SuperfluousWhitespace

Zend (1 sniff)
---------------
- Zend.Files.ClosingTag
