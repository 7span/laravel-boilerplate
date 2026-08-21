#### Translation Keys

**All `__()` keys must exist in language files.**
When a `__('some.key')` call is added in changed lines, check that the key is defined in `lang/en/` (and any other configured locale files in `lang/`). Flag any key that is not present. If you cannot read the lang files, note the key and ask the reviewer to confirm.
