# TODO List for LS25-Aktionmarkt Project

## Completed Tasks
- [x] Fixed PHP syntax error in OrderController.php (removed blank line before <?php)
- [x] Set up Laravel Sail environment and migrated/seeded database
- [x] Created comprehensive OrderControllerTest with buy/sell functionality tests
- [x] Fixed GameTime seeding issues in tests

## Pending Tasks
- [ ] Debug why OrderController tests are failing (session messages not being set)
- [ ] Implement proper balance checking in OrderController for buy operations
- [ ] Fix the incomplete buy transaction creation in OrderController (line with $user->NaN)
- [ ] Add proper error handling for insufficient funds
- [ ] Test the UI functionality for buy/sell operations
- [ ] Implement transfer functionality in PaymentController
- [ ] Add proper validation for payment forms
- [ ] Test end-to-end buy/sell workflow through the UI

## Bugs to Fix
- [ ] OrderController has incomplete code: `$buyTransaction->user_id = $user->NaN` should be `$user->id`
- [ ] PaymentController payout method has syntax error: `NaNe->getMessage());`
- [ ] OrderController tests expect success/error messages but they are not being set properly

## Features to Implement
- [ ] Complete the buy/sell logic with proper balance updates
- [ ] Add transaction history display
- [ ] Implement dividend payments
- [ ] Add user portfolio view
- [ ] Implement admin panel for stock management
