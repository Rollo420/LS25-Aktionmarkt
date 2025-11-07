<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Stock\Stock;
use App\Models\Stock\Price;
use App\Services\GameTimeService;
use Illuminate\Support\Facades\Redirect;

class AdminController extends Controller
{
    public function index()
    {
        $accounts = User::with('transactions')->get();
        //$username = account::find(1)->details;
        //$passwordHash = account::find(1)->password;

        $user = User::find(2);
        //if ($user->isAdministrator()) {
        //    // User is an administrator
        //    dd('User is an administrator');
        //} else {
        //    // User is not an administrator
        //    dd('User is not an administrator');
        //}

        return view('admin', ['accounts' => $accounts]);
    }

    public function create()
    {
        $sectors = ['Technology', 'Healthcare', 'Finance', 'Energy', 'Consumer Goods', 'Industrials', 'Materials', 'Utilities', 'Real Estate', 'Telecommunications'];
        $countries = ['Germany', 'USA', 'UK', 'France', 'Japan', 'China', 'India', 'Canada', 'Australia', 'Brazil'];
        $productTypes = \App\Models\ProductType::all();

        return view('admin.stock.create', compact('sectors', 'countries', 'productTypes'));
    }

    public function generateField(Request $request)
    {
        $field = $request->get('field');

        switch ($field) {
            case 'product_type_id':
                $productType = \App\Models\ProductType::inRandomOrder()->first();
                return response()->json([
                    'value' => $productType?->id ?? (\App\Models\ProductType::create(['name' => 'default'])->id),
                    'display' => $productType?->name ?? 'default'
                ]);

            case 'name':
                return response()->json(['value' => fake()->word()]);

            case 'firma':
                return response()->json(['value' => fake()->word()]);

            case 'sektor':
                $sectors = ['Technology', 'Healthcare', 'Finance', 'Energy', 'Consumer Goods', 'Industrials', 'Materials', 'Utilities', 'Real Estate', 'Telecommunications'];
                return response()->json(['value' => collect($sectors)->random()]);

            case 'land':
                $countries = ['Germany', 'USA', 'UK', 'France', 'Japan', 'China', 'India', 'Canada', 'Australia', 'Brazil'];
                return response()->json(['value' => collect($countries)->random()]);

            case 'description':
                return response()->json(['value' => fake()->text()]);

            case 'net_income':
                return response()->json(['value' => fake()->randomFloat(2, 1000, 1000000)]);

            case 'dividend_frequency':
                return response()->json(['value' => rand(0, 4)]);

            case 'start_price':
                return response()->json(['value' => fake()->numberBetween(10, 500)]);

            case 'dividend_amount':
                return response()->json(['value' => fake()->randomFloat(2, 0.5, 2.5)]);

            case 'next_dividend_date':
                return response()->json(['value' => now()->addMonths(rand(1, 12))->format('Y-m-d')]);

            default:
                return response()->json(['error' => 'Unknown field'], 400);
        }
    }

    public function store(Request $request, GameTimeService $gameTimeService)
    {
        $request->validate([
            'product_type_id' => 'nullable|exists:product_types,id',
            'name' => 'nullable|string|max:255',
            'firma' => 'nullable|string|max:255',
            'sektor' => 'nullable|string|max:255',
            'land' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'net_income' => 'nullable|numeric',
            'dividend_frequency' => 'nullable|integer|min:0|max:12',
            'start_price' => 'nullable|numeric|min:0',
            'dividend_amount' => 'nullable|numeric|min:0',
            'next_dividend_date' => 'nullable|date|after:now',
            'generate_missing' => 'nullable|boolean',
        ]);

        $generateMissing = $request->boolean('generate_missing', false);

        // Check if we need to generate missing data
        $missingFields = [];
        if (!$request->filled('product_type_id')) $missingFields[] = 'product_type_id';
        if (!$request->filled('name')) $missingFields[] = 'name';
        if (!$request->filled('firma')) $missingFields[] = 'firma';
        if (!$request->filled('sektor')) $missingFields[] = 'sektor';
        if (!$request->filled('land')) $missingFields[] = 'land';
        if (!$request->filled('description')) $missingFields[] = 'description';
        if (!$request->filled('net_income')) $missingFields[] = 'net_income';
        if (!$request->filled('dividend_frequency')) $missingFields[] = 'dividend_frequency';
        if (!$request->filled('start_price')) $missingFields[] = 'start_price';

        if (!empty($missingFields) && !$generateMissing) {
            return Redirect::back()
                ->withInput()
                ->with('warning', 'Einige Felder sind nicht ausgefüllt. Klicken Sie auf "Mit Factory-Daten generieren" um fehlende Daten automatisch zu erstellen.');
        }

        // Create the stock - use factory for missing fields
        $stockData = [];
        $stockData['product_type_id'] = $request->product_type_id ?: \App\Models\ProductType::inRandomOrder()->first()?->id ?? \App\Models\ProductType::create(['name' => 'default'])->id;
        $stockData['name'] = $request->name ?: fake()->word();
        $stockData['firma'] = $request->firma ?: fake()->word();
        $stockData['sektor'] = $request->sektor ?: collect(['Technology', 'Healthcare', 'Finance', 'Energy', 'Consumer Goods'])->random();
        $stockData['land'] = $request->land ?: collect(['Germany', 'USA', 'UK', 'France'])->random();
        $stockData['description'] = $request->description ?: fake()->text();
        $stockData['net_income'] = $request->net_income ?: fake()->randomFloat(2, 1000, 1000000);
        $stockData['dividend_frequency'] = $request->dividend_frequency ?? rand(0, 4);

        $stock = Stock::create($stockData);

        // Create initial price for current game time
        $currentGameTime = $gameTimeService->getOrCreate(now());
        $startPrice = $request->start_price ?: fake()->numberBetween(10, 500);

        Price::create([
            'stock_id' => $stock->id,
            'game_time_id' => $currentGameTime->id,
            'name' => $startPrice,
        ]);

        // Create initial dividend if frequency > 0 or if dividend_amount is provided
        if ($stock->dividend_frequency > 0 || $request->filled('dividend_amount')) {
            $dividendAmount = $request->dividend_amount ?: fake()->randomFloat(2, 0.5, 2.5);

            // Use provided date or current game time
            $dividendGameTime = $currentGameTime;
            if ($request->filled('next_dividend_date')) {
                $dividendGameTime = $gameTimeService->getOrCreate(\Carbon\Carbon::parse($request->next_dividend_date));
            }

            \App\Models\Dividend::create([
                'stock_id' => $stock->id,
                'game_time_id' => $dividendGameTime->id,
                'amount_per_share' => $dividendAmount,
            ]);
        }

        $message = $generateMissing ? 'Stock mit generierten Daten erfolgreich erstellt!' : 'Stock erfolgreich erstellt!';
        return Redirect::route('admin')->with('success', $message);
    }
}
