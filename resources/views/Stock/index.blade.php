<div>
    <div class="stock-tabelle">
        <h2>Stock Tabelle:</h2>
        <ul>
            @foreach ($stocks as $stock)
            <li>
                <p>ID: {{ $stock->id }}</p>
                <ul>
                    <li>
                        <p>Price ID: {{ $stock->price_id }}</p>
                        <ul>
                            <li>
                                <p>Price: {{ $stock->price->name }} €</p>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <p>Product Type ID: {{ $stock->product_type_id }}</p>
                        <ul>
                            <li>
                                <p>Product Type: {{ $stock->productType->name }}</p>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <p>Transaction Infos:</p>
                        <ul>
                            @foreach ($stock->transactions as $transaction)

                            <li>
                                <p>Account ID: {{ $transaction->account_id }}</p>
                            </li>
                            <li>
                                <p>Transaktions ID's: {{ $transaction->id }}</p>
                            </li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
            </li>
            @endforeach
        </ul>
    </div>


</div>