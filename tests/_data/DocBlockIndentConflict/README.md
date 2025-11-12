# Docblock Indentation Conflict Test

## Original Issue

This test documents the exact issue reported by the user:

```php
class SignedUrlGeneratorTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
    }
}
```

When running `phpcbf` to convert space indentation to tabs, the docblock was being "moved to the beginning of the line" - the content lines lost their indentation.

## Root Cause

Two sniffs were conflicting:
1. **TabIndentSniff** - Processes `T_DOC_COMMENT_WHITESPACE` at column 1
2. **DocBlockAlignmentSniff** - Also processes the same tokens

This caused "FAILED TO FIX" errors and negative error counts.

## Fix Applied

Modified `TabIndentSniff.php` to skip `T_DOC_COMMENT_WHITESPACE` tokens at column 1, letting `DocBlockAlignmentSniff` handle overall docblock positioning exclusively.

## Current Behavior

With the fix, phpcbf now completes successfully instead of failing. However:

✅ **Fixed:** No more conflicts between sniffs
✅ **Fixed:** Docblock opening `/**` gets tab indentation
✅ **Fixed:** Method and class lines get tab indentation
⚠️ **Remaining Issue:** Docblock content lines still have spaces instead of tabs

The output has:
```
\t/**
     * setUp method    <- Should be: \t * setUp method
     *                  <- Should be: \t *
     * @return void     <- Should be: \t * @return void
     */                <- Should be: \t */
```

## Next Steps

The `DocBlockAlignmentSniff` needs to be modified to ensure it uses tabs for indentation when aligning docblocks, not spaces. This is a separate fix needed in that sniff.
