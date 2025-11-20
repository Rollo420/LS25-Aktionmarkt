# Implementation Plan for Laravel Aktionmarkt Enhancements

## 1. Preisberechnung mit Config-Tabelle
- [ ] Enhance PriceResolverService to use caching for Config retrieval (e.g., via Cache facade) for efficiency.
- [ ] Optionally, create a ConfigService for centralized config access.

## 2. Admin-Navigation Refactoring
- [ ] Remove the admin link from navigation.blade.php.
- [ ] Add an "Admin" dropdown in the navigation, visible only to admins, containing links to users, stocks, game-times, dividends, configs.
- [ ] Remove or redirect the admin route to dashboard or first admin section.

## 3. Fehlende Views erstellen (CRUD)
- [ ] Create resources/views/admin/config/edit.blade.php with form for editing config fields, including validation errors.
- [ ] Update index.blade.php to use a delete modal instead of inline confirm for better UX.
- [ ] Ensure delete uses @method('DELETE') in form.

## 4. Analyse fehlender Komponenten
- [ ] Add success message display in views (e.g., @if(session('success'))).
- [ ] Add validation error display in forms.
- [ ] Create a generic delete confirmation modal component.
- [ ] Ensure config values are validated (already in controller).

## Followup steps
- [ ] Test price calculation with cached configs.
- [ ] Verify admin dropdown visibility and links.
- [ ] Test CRUD operations with new views.
- [ ] Run Laravel Sail to check UI.
