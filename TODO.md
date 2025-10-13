# Portfolio Chart Implementation Plan

## Tasks
- [x] Add private method `getHistoricalPortfolioValues($user, $months = 12)` to DashboardController
- [x] Update `createChartData($stocks)` in DashboardController to use historical data instead of static data
- [ ] Test the chart in the dashboard view

## Details
- The new method will calculate portfolio value for each of the last 12 months based on transactions and stock prices.
- For each month, aggregate holdings up to month-end, multiply by last price of the month, add bank balance.
- Now includes buy and sell transactions for accurate holdings calculation.
- Labels remain months, data becomes dynamic.
