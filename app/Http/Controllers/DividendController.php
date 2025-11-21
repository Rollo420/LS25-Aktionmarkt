<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Stock\Stock;
use App\Models\Dividend;
use App\Models\GameTime;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;

class DividendController extends Controller
{
    // ===== DIVIDEND MANAGEMENT =====
    public function index()
    {
        $currentGameTime = GameTime::getCurrentGameTime();

        // Get dividends for current game time, grouped by stock (unique)
        $dividends = Dividend::where('game_time_id', $currentGameTime->id)
            ->with(['stock', 'gameTime'])
            ->get()
            ->unique('stock_id');

        return view('admin.dividends.index', compact('dividends', 'currentGameTime'));
    }

    public function show(Dividend $dividend)
    {
        return view('admin.dividends.show', compact('dividend'));
    }

    public function edit(Dividend $dividend)
    {
        $stocks = Stock::all();
        $gameTimes = GameTime::all();

        return view('admin.dividends.edit', compact('dividend', 'stocks', 'gameTimes'));
    }

    public function update(Request $request, Dividend $dividend)
    {
        $validated = $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'game_time_id' => 'required|exists:game_times,id',
            'amount_per_share' => 'required|numeric|min:0',
        ]);

        $dividend->update($validated);

        return redirect()->route('admin.dividends.index')
            ->with('success', __('Dividend aktualisiert'));
    }

    public function destroy(Dividend $dividend)
    {
        try {
            $dividend->delete();

            return redirect()->route('admin.dividends.index')
                ->with('success', __('Dividend wurde gelöscht'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Fehler beim Löschen: ') . $e->getMessage());
        }
    }
}
