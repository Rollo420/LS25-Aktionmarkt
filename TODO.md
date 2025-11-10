# TODO: Replace Dummy Data in DashboardController with Real Calculations

## Tasks
- [x] Add private method `calculatePortfolioPerformance($months)` to compute portfolio performance over given months based on GameTime.
- [x] Add private method `calculateRiskMetrics()` to compute cash balance, investment ratio, and beta (beta hardcoded).
- [x] Add private method `calculateDividendChart()` to compute expected monthly dividends based on held stocks and dividend frequency.
- [x] Add private method `calculatePurchasingPower()` to compute annual dividends sum and quantity of example stock (e.g., Apple) that can be bought.
- [x] Replace dummy data blocks in `index()` method with calls to these new methods.
- [x] Ensure calculations are efficient to avoid timeouts (optimize queries, use eager loading if needed).
- [ ] Test dashboard loading after changes.
