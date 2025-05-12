# PSR2R Code Sniffer

The PSR2R standard contains 190 sniffs

Generic (23 sniffs)
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
- Generic.Formatting.SpaceAfterCast
- Generic.NamingConventions.UpperCaseConstantName
- Generic.PHP.DeprecatedFunctions
- Generic.PHP.DisallowAlternativePHPTags
- Generic.PHP.DisallowShortOpenTag
- Generic.PHP.ForbiddenFunctions
- Generic.PHP.LowerCaseConstant
- Generic.PHP.LowerCaseKeyword
- Generic.PHP.LowerCaseType
- Generic.PHP.NoSilencedErrors
- Generic.WhiteSpace.IncrementDecrementSpacing
- Generic.WhiteSpace.LanguageConstructSpacing
- Generic.WhiteSpace.ScopeIndent

PEAR (3 sniffs)
---------------
- PEAR.ControlStructures.ControlSignature
- PEAR.Functions.ValidDefaultValue
- PEAR.NamingConventions.ValidClassName

PhpCollective (39 sniffs)
-------------------------
- PhpCollective.Classes.ClassFileName
- PhpCollective.Classes.MethodArgumentDefaultValue
- PhpCollective.Classes.MethodDeclaration
- PhpCollective.Classes.MethodTypeHint
- PhpCollective.Classes.PropertyDefaultValue
- PhpCollective.Classes.Psr4
- PhpCollective.Classes.ReturnTypeHint
- PhpCollective.Classes.SelfAccessor
- PhpCollective.Commenting.Attributes
- PhpCollective.Commenting.DocBlockConst
- PhpCollective.Commenting.DocBlockConstructor
- PhpCollective.Commenting.DocBlockNoInlineAlignment
- PhpCollective.Commenting.DocBlockParamAllowDefaultValue
- PhpCollective.Commenting.DocBlockPipeSpacing
- PhpCollective.Commenting.DocBlockReturnNull
- PhpCollective.Commenting.DocBlockReturnNullableType
- PhpCollective.Commenting.DocBlockReturnTag
- PhpCollective.Commenting.DocBlockReturnVoid
- PhpCollective.Commenting.DocBlockStructure
- PhpCollective.Commenting.DocBlockTagOrder
- PhpCollective.Commenting.DocBlockTypeOrder
- PhpCollective.Commenting.DocBlockVar
- PhpCollective.Commenting.DocBlockVarNotJustNull
- PhpCollective.Commenting.FullyQualifiedClassNameInDocBlock
- PhpCollective.Commenting.InlineDocBlock
- PhpCollective.Commenting.TypeHint
- PhpCollective.ControlStructures.ConditionalExpressionOrder
- PhpCollective.ControlStructures.NoInlineAssignment
- PhpCollective.Formatting.ArrayDeclaration
- PhpCollective.PHP.NoIsNull
- PhpCollective.PHP.NotEqual
- PhpCollective.PHP.PhpSapiConstant
- PhpCollective.PHP.PreferCastOverFunction
- PhpCollective.PHP.RemoveFunctionAlias
- PhpCollective.PHP.ShortCast
- PhpCollective.WhiteSpace.CommaSpacing
- PhpCollective.WhiteSpace.ConcatenationSpacing
- PhpCollective.WhiteSpace.ImplicitCastSpacing
- PhpCollective.WhiteSpace.ObjectAttributeSpacing

PSR1 (3 sniffs)
---------------
- PSR1.Classes.ClassDeclaration
- PSR1.Files.SideEffects
- PSR1.Methods.CamelCapsMethodName

PSR2 (6 sniffs)
---------------
- PSR2.ControlStructures.ElseIfDeclaration
- PSR2.ControlStructures.SwitchDeclaration
- PSR2.Files.EndFileNewline
- PSR2.Methods.FunctionCallSignature
- PSR2.Namespaces.NamespaceDeclaration
- PSR2.Namespaces.UseDeclaration

PSR2R (43 sniffs)
-----------------
- PSR2R.Classes.BraceOnSameLine
- PSR2R.Classes.InterfaceName
- PSR2R.Classes.PropertyDeclaration
- PSR2R.Classes.TraitName
- PSR2R.Commenting.DocBlock
- PSR2R.Commenting.DocBlockEnding
- PSR2R.Commenting.DocBlockNoEmpty
- PSR2R.Commenting.DocBlockParam
- PSR2R.Commenting.DocBlockParamArray
- PSR2R.Commenting.DocBlockParamNoOp
- PSR2R.Commenting.DocBlockParamNotJustNull
- PSR2R.Commenting.DocBlockReturnSelf
- PSR2R.Commenting.DocBlockShortType
- PSR2R.Commenting.DocBlockTagTypes
- PSR2R.Commenting.DocBlockVarWithoutName
- PSR2R.Commenting.DocComment
- PSR2R.Commenting.NoControlStructureEndComment
- PSR2R.ControlStructures.ConditionalExpressionOrder
- PSR2R.ControlStructures.ControlStructureSpacing
- PSR2R.ControlStructures.ElseIfDeclaration
- PSR2R.ControlStructures.NoInlineAssignment
- PSR2R.ControlStructures.TernarySpacing
- PSR2R.ControlStructures.UnneededElse
- PSR2R.Files.ClosingTag
- PSR2R.Files.EndFileNewline
- PSR2R.Methods.MethodDeclaration
- PSR2R.Methods.MethodMultilineArguments
- PSR2R.Namespaces.NoInlineFullyQualifiedClassName
- PSR2R.PHP.DuplicateSemicolon
- PSR2R.PHP.ListComma
- PSR2R.PHP.NoShortOpenTag
- PSR2R.PHP.PreferStaticOverSelf
- PSR2R.PHP.SingleQuote
- PSR2R.WhiteSpace.ArraySpacing
- PSR2R.WhiteSpace.DocBlockAlignment
- PSR2R.WhiteSpace.EmptyEnclosingLine
- PSR2R.WhiteSpace.EmptyLines
- PSR2R.WhiteSpace.LanguageConstructSpacing
- PSR2R.WhiteSpace.MethodSpacing
- PSR2R.WhiteSpace.NamespaceSpacing
- PSR2R.WhiteSpace.TabAndSpace
- PSR2R.WhiteSpace.TabIndent
- PSR2R.WhiteSpace.UnaryOperatorSpacing

PSR12 (7 sniffs)
----------------
- PSR12.Classes.ClassInstantiation
- PSR12.Files.ImportStatement
- PSR12.Functions.NullableTypeDeclaration
- PSR12.Functions.ReturnTypeDeclaration
- PSR12.Keywords.ShortFormTypeKeywords
- PSR12.Namespaces.CompoundNamespaceDepth
- PSR12.Operators.OperatorSpacing

SlevomatCodingStandard (45 sniffs)
----------------------------------
- SlevomatCodingStandard.Arrays.DisallowImplicitArrayCreation
- SlevomatCodingStandard.Arrays.MultiLineArrayEndBracketPlacement
- SlevomatCodingStandard.Arrays.SingleLineArrayWhitespace
- SlevomatCodingStandard.Arrays.TrailingArrayComma
- SlevomatCodingStandard.Classes.ClassConstantVisibility
- SlevomatCodingStandard.Classes.ConstantSpacing
- SlevomatCodingStandard.Classes.ModernClassNameReference
- SlevomatCodingStandard.Classes.PropertySpacing
- SlevomatCodingStandard.Commenting.DeprecatedAnnotationDeclaration
- SlevomatCodingStandard.Commenting.DisallowOneLinePropertyDocComment
- SlevomatCodingStandard.Commenting.EmptyComment
- SlevomatCodingStandard.ControlStructures.AssignmentInCondition
- SlevomatCodingStandard.ControlStructures.DisallowContinueWithoutIntegerOperandInSwitch
- SlevomatCodingStandard.ControlStructures.DisallowYodaComparison
- SlevomatCodingStandard.ControlStructures.JumpStatementsSpacing
- SlevomatCodingStandard.ControlStructures.NewWithParentheses
- SlevomatCodingStandard.ControlStructures.RequireNullCoalesceOperator
- SlevomatCodingStandard.ControlStructures.RequireShortTernaryOperator
- SlevomatCodingStandard.Exceptions.DeadCatch
- SlevomatCodingStandard.Functions.ArrowFunctionDeclaration
- SlevomatCodingStandard.Functions.DisallowTrailingCommaInCall
- SlevomatCodingStandard.Functions.DisallowTrailingCommaInClosureUse
- SlevomatCodingStandard.Functions.DisallowTrailingCommaInDeclaration
- SlevomatCodingStandard.Functions.RequireTrailingCommaInCall
- SlevomatCodingStandard.Functions.RequireTrailingCommaInClosureUse
- SlevomatCodingStandard.Functions.RequireTrailingCommaInDeclaration
- SlevomatCodingStandard.Namespaces.AlphabeticallySortedUses
- SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly
- SlevomatCodingStandard.Namespaces.RequireOneNamespaceInFile
- SlevomatCodingStandard.Namespaces.UnusedUses
- SlevomatCodingStandard.Namespaces.UseDoesNotStartWithBackslash
- SlevomatCodingStandard.Namespaces.UseFromSameNamespace
- SlevomatCodingStandard.Namespaces.UselessAlias
- SlevomatCodingStandard.Namespaces.UseSpacing
- SlevomatCodingStandard.Operators.SpreadOperatorSpacing
- SlevomatCodingStandard.PHP.ShortList
- SlevomatCodingStandard.PHP.TypeCast
- SlevomatCodingStandard.PHP.UselessSemicolon
- SlevomatCodingStandard.TypeHints.DNFTypeHintFormat
- SlevomatCodingStandard.TypeHints.LongTypeHints
- SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue
- SlevomatCodingStandard.TypeHints.ParameterTypeHintSpacing
- SlevomatCodingStandard.TypeHints.ReturnTypeHintSpacing
- SlevomatCodingStandard.Variables.DuplicateAssignmentToVariable
- SlevomatCodingStandard.Whitespaces.DuplicateSpaces

Squiz (20 sniffs)
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
- Squiz.WhiteSpace.LogicalOperatorSpacing
- Squiz.WhiteSpace.MemberVarSpacing
- Squiz.WhiteSpace.ScopeKeywordSpacing
- Squiz.WhiteSpace.SemicolonSpacing
- Squiz.WhiteSpace.SuperfluousWhitespace

Zend (1 sniff)
--------------
- Zend.Files.ClosingTag
