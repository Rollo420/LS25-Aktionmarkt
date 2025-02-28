@extends('layouts.app')



<div>
    @section('title')

    <h1>All Account Infos</h1>
    @endsection

    @section('content')
    <div>
        <ul>
            @foreach ($accounts as $account)
            <li>
                <p class="text"> Username: {{ $account->username }}</p>
            </li>
            <ul>
                <li>
                    <p>
                        Account ID: {{ $account->id }}
                    </p>
                </li>
                <li>
                    <p>
                        Password ID: {{ $account->password_id }}
                    </p>
                </li>
                <li>
                    <p>
                        Password Hash: {{ $account->password->hash }}
                    </p>
                <li>
                    <p>
                        Mail: {{ $account->mail }}
                    </p>
                </li>
                <li>
                    <p>
                        Verified: {{ $account->is_verified }}
                    </p>
                </li>

                <li>
                    <p>Transactions:</p>
                    <ul>
                        @foreach ($account->transactions as $transaction)
                        <li>
                            <p>Transaction ID: {{ $transaction->id }}</p>
                        </li>
                        <ul>
                            <li>
                                <p> Account ID: {{ $transaction->account_id }}</p>
                            </li>
                        </ul>
                        <li>
                            <p>Stock ID: {{ $transaction->stock_id }}</p>
                        </li>
                        <li>
                            <p>Status (Buy/Sell): {{ $transaction->status }}</p>
                        </li>
                        <li>
                            <p>Quantity: {{ $transaction->quantity }}</p>
                        </li>
                        <br>
                        @endforeach
                    </ul>
                </li>

                <li>
                    <p>Stock:</p>
                    <ul>
                        @foreach ($account->transactions as $transaction)
                        <li>
                            <p> ID: {{ $transaction->stock->id }}</p>
                        </li>
                        <li>
                            <p>Price ID: {{ $transaction->stock->price_id }}</p>
                        </li>
                        <ul>
                            <li>
                                <p>Price Name: {{ $transaction->stock->price->name }} €</p>
                            </li>
                        </ul>
                </li>

                <li>
                    <p>Product Type ID: {{ $transaction->stock->product_type_id }}</p>
                    <ul>
                        <li>
                            <p>Product Type Name: {{ $transaction->stock->productType->name }}</p>
                        </li>
                    </ul>
                </li>

                @endforeach
            </ul>
            </li>
        </ul>

        <br>
        @endforeach
        </ul>
    </div>
    @endsection
</div>