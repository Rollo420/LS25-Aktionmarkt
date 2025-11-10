# TODO: Standardize Alert Messages in Blade Views

## Tasks
- [x] Update resources/views/Stock/store.blade.php
- [x] Update resources/views/depot/depotStockDetails.blade.php
- [x] Update resources/views/payment/index.blade.php
- [x] Update resources/views/components/payment-authorization-list.blade.php
- [x] Update resources/views/admin/stock/create.blade.php

## Standardized Alert Code
```
@foreach (['success', 'error', 'warning', 'info'] as $msg)
    @if(session($msg))
        <div class="mb-6 p-4 rounded-lg {{ $msg === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : ($msg === 'error' ? 'bg-red-50 text-red-800 border border-red-200' : ($msg === 'warning' ? 'bg-yellow-50 text-yellow-800 border border-yellow-200' : 'bg-blue-50 text-blue-800 border border-blue-200')) }} dark:bg-gray-800 dark:text-gray-100">
            {{ session($msg) }}
        </div>
    @endif
@endforeach
```

## Followup
- Test views to ensure alerts render correctly in different scenarios.
