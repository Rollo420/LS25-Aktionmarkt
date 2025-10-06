<?php

namespace App\Models;

use Parental\HasParent;
use App\Models\Stock\Transaction;

class DepositTransaction extends Transaction
{
    use HasParent;
    // ...spezifische Logik für DepositTransaction...
}
