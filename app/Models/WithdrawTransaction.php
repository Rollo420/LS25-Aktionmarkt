<?php

namespace App\Models;

use Parental\HasParent;
use App\Models\Stock\Transaction;

class WithdrawTransaction extends Transaction
{
    use HasParent;
    // ...spezifische Logik für WithdrawTransaction...
}
