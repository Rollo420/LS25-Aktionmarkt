<?php

namespace App\Models;

use Parental\HasParent;
use App\Models\Stock\Transaction;

class BuyTransaction extends Transaction
{
    use HasParent;
    // ...spezifische Logik für BuyTransaction...
}
