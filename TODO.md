# TODO: Buy/Sell Button Integration and Alerts

## Tasks
- [x] Improve buy_sell-buttons.blade.php design (add icons, better styling, animations)
- [x] Add alert loop to depotStockDetails.blade.php
- [ ] Test functionality after changes
- [ ] Compile SCSS if needed

## Dependent Files
- resources/views/components/buy_sell-buttons.blade.php
- resources/views/depot/depotStockDetails.blade.php
- resources/sass/_paymentAlert.scss (already included)

## Notes
- Keep existing project design (Tailwind CSS, SCSS alerts)
- Ensure OrderController sets session messages correctly
- Test buy/sell transactions work with alerts
