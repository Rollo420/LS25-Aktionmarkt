# TODO: Fix User Locale Update Issue

## Changes Made
- [x] Added 'locale' to fillable attributes in User model
- [x] Implemented SetLocale middleware to set locale on every request based on authenticated user's preference
- [x] Registered SetLocale middleware for web routes in bootstrap/app.php
- [x] Removed locale setting from AppServiceProvider boot method (now handled by middleware)
- [x] Added missing English translations to resources/lang/en/messages.php
- [x] Replaced hardcoded German text in navigation.blade.php with translation keys

## Summary
The issue was that the locale wasn't being updated correctly because:
1. The 'locale' field wasn't in the User model's fillable attributes, preventing mass assignment
2. The locale was only set during application bootstrap in AppServiceProvider, not on every request
3. The SetLocale middleware existed but wasn't registered
4. Views had hardcoded German text instead of using translation keys
5. English language file was missing many translation keys

Now the SetLocale middleware runs on every web request and sets the locale based on the authenticated user's preference, defaulting to 'de' if not set or for unauthenticated users. All text in views uses translation keys for proper language switching.

## Follow-up Steps
- [ ] Test locale change functionality by updating user profile and verifying language changes immediately
- [ ] Verify middleware works for both authenticated and unauthenticated users
- [ ] Check that default locale 'de' is used when user has no locale set or when not authenticated
