# TODO: Admin Stock Creation Page

## Tasks
- [x] Create AdminMiddleware for admin access control
- [x] Add create() and store() methods to AdminController
- [x] Add route /admin/stock/create with AdminMiddleware
- [x] Create view resources/views/admin/stock/create.blade.php with form
- [x] Update admin.blade.php to include link to stock creation
- [x] Test the functionality

## Details
- Form fields: name, firma, sektor, land, description, net_income, dividend_frequency, start_price
- Use existing isAdministrator() method for auth
- Create initial price entry for current game time
- Use Tailwind CSS and @csrf
