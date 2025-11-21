<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Stock\Stock;
use App\Models\Stock\Price;
use App\Services\GameTimeService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use App\Models\GameTime;

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
        $configs = \App\Models\Config::all();

        return view('admin.stock.create', compact('sectors', 'countries', 'productTypes', 'configs'));
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
            'dividend_amount' => 'required|numeric|min:0',
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
        // Attach a default config if one exists to maintain previous behaviour when configs were used.
        $defaultConfig = \App\Models\Config::first();
        if ($defaultConfig) {
            $stock->configs()->attach($defaultConfig->id);
        }

        // Create initial price for current game time
        $currentGameTime = GameTime::getCurrentGameTime();
        $startPrice = $request->start_price ?: fake()->numberBetween(10, 500);

        Price::create([
            'stock_id' => $stock->id,
            'game_time_id' => $currentGameTime->id,
            'name' => $startPrice,
        ]);

        // Always create an initial dividend for the stock using the provided amount (or a fallback)
        $dividendAmount = $request->dividend_amount ?: fake()->randomFloat(2, 0.5, 2.5);

        // Use current game time if available, otherwise create a fallback GameTime for today
        $dividendGameTime = $currentGameTime ?? \App\Models\GameTime::latest('id')->first();
        if (!$dividendGameTime) {
            $dividendGameTime = \App\Models\GameTime::create(['name' => now()->format('Y-m-d')]);
        }

        \App\Models\Dividend::create([
            'stock_id' => $stock->id,
            'game_time_id' => $dividendGameTime->id,
            'amount_per_share' => $dividendAmount,
        ]);

        $message = $generateMissing ? 'Stock mit generierten Daten erfolgreich erstellt!' : 'Stock erfolgreich erstellt!';
        return Redirect::route('admin.stocks.index')->with('success', $message);
    }

    // ===== USER MANAGEMENT =====
    public function usersIndex()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function usersShow(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function usersEdit(User $user)
    {
        $userRole = $user->roles()->get()->first()->name;
        $roles = \App\Models\Role::all();
        return view('admin.users.edit', compact('user', 'roles', 'userRole'));
    }

    public function usersUpdate(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        // Handle role assignment separately (many-to-many relation)
        $roleIds = $request->input('role_ids', []);
        $request->validate(['role_ids' => 'nullable|array', 'role_ids.*' => 'exists:roles,id']);

        // If no roles selected, assign default user role
        if (empty($roleIds)) {
            $defaultRole = \App\Models\Role::where('name', 'default user')->first();
            if ($defaultRole) {
                $roleIds = [$defaultRole->id];
            }
        }

        // Update user attributes (excluding roles)
        $dataToUpdate = $validated;
        unset($dataToUpdate['role_ids']);

        $user->update($dataToUpdate);

        // Sync roles: replace existing roles with the selected ones
        $user->roles()->sync($roleIds);

        return redirect()->route('admin.users.index')
            ->with('success', __('User aktualisiert'));
    }

    public function usersDestroy(User $user)
    {
        try {
            $userName = $user->name;
            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', __('User "') . $userName . __('" wurde gelöscht'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Fehler beim Löschen: ') . $e->getMessage());
        }
    }

    // ===== STOCK MANAGEMENT =====
    public function stocksIndex()
    {
        $stocks = Stock::all();
        return view('admin.stocks.index', compact('stocks'));
    }

    public function stocksShow(Stock $stock)
    {
        return view('admin.stocks.show', compact('stock'));
    }

    public function stocksEdit(Stock $stock)
    {
        $sectors = ['Technology', 'Healthcare', 'Finance', 'Energy', 'Consumer Goods', 'Industrials', 'Materials', 'Utilities', 'Real Estate', 'Telecommunications'];
        $countries = ['Germany', 'USA', 'UK', 'France', 'Japan', 'China', 'India', 'Canada', 'Australia', 'Brazil'];
        $productTypes = \App\Models\ProductType::all();

        // Load available configs so admin can assign configs to a stock
        $configs = \App\Models\Config::all();
        $stock->load('configs');

        return view('admin.stocks.edit', compact('stock', 'sectors', 'countries', 'productTypes', 'configs'));
    }

    public function stocksUpdate(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'firma' => 'required|string|max:255',
            'sektor' => 'required|string',
            'land' => 'required|string',
            'description' => 'nullable|string',
            'product_type_id' => 'required|exists:product_types,id',
            'config_id' => 'nullable|integer|exists:configs,id',
        ]);

        // Separate config_id from stock attributes
        $configId = $validated['config_id'] ?? null;
        unset($validated['config_id']);

        // Update stock attributes first
        $stock->update($validated);

        // Sync single config (or detach all if null)
        try {
            if ($configId) {
                $stock->configs()->sync([$configId]);
            } else {
                $stock->configs()->sync([]);
            }
        } catch (\Exception $e) {
            // Log and return a friendly error
            Log::error('Failed to sync config for stock '. $stock->id .': '. $e->getMessage());
            return redirect()->route('admin.stocks.index')
                ->with('error', __('Fehler beim Anwenden der Konfiguration: ') . $e->getMessage());
        }

        return redirect()->route('admin.stocks.index')->with('success', __('Stock aktualisiert'));
    }

    public function stocksDestroy(Stock $stock)
    {
        try {
            $stockName = $stock->name;
            $stock->delete();

            return redirect()->route('admin.stocks.index')
                ->with('success', __('Stock "') . $stockName . __('" wurde gelöscht'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Fehler beim Löschen: ') . $e->getMessage());
        }
    }

    // ===== GAME TIME MANAGEMENT =====
    public function gameTimesIndex()
    {
        $gameTimes = GameTime::all();
        return view('admin.game-times.index', compact('gameTimes'));
    }

    public function gameTimesShow(GameTime $gameTime)
    {
        return view('admin.game-times.show', compact('gameTime'));
    }

    public function gameTimesEdit(GameTime $gameTime)
    {
        return view('admin.game-times.edit', compact('gameTime'));
    }

    public function gameTimesUpdate(Request $request, GameTime $gameTime)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $gameTime->update($validated);

        return redirect()->route('admin.game-times.index')
            ->with('success', __('GameTime aktualisiert'));
    }

    public function gameTimesDestroy(GameTime $gameTime)
    {
        try {
            $gameTimeName = $gameTime->name;
            $gameTime->delete();

            return redirect()->route('admin.game-times.index')
                ->with('success', __('GameTime "') . $gameTimeName . __('" wurde gelöscht'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Fehler beim Löschen: ') . $e->getMessage());
        }
    }

 }
