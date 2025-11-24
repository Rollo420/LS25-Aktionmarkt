<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ConfigRequest;

use App\Models\Config;
use Illuminate\Support\Facades\Cache;

class ConfigController extends Controller
{
    public function index()
    {
        $configs = Config::all();
        return view('admin.configs.index', compact('configs'));
    }

    public function create()
    {
        return view('admin.configs.create');
    }

    public function store(ConfigRequest $request)
    {
        $data = $request->validated();

        // Felder, die das "Config-Set" eindeutig machen sollen
        $numericKeys = [
            'volatility_range',
            'seasonal_effect_strength',
            'crash_probability_monthly',
            'crash_interval_months',
            'rally_probability_monthly',
            'rally_interval_months',
        ];

        // Nur numerische Felder für Unique-Check
        $uniqueData = collect($data)->only($numericKeys)->toArray();

        // Prüfen, ob Config mit denselben numerischen Werten existiert
        $existing = Config::where($uniqueData)->first();

        if ($existing) {
            $config = $existing;
        } else {
            $config = Config::create($data);
        }

        return redirect()->route('admin.configs.index')
            ->with('success', 'Config gespeichert (bestehend oder neu erstellt)');
    }

    public function update(ConfigRequest $request, Config $config)
    {
        $data = $request->validated();
        $config->update($data);

        Cache::forget('app.config.all');

        return redirect()->route('admin.configs.index')
            ->with('success', 'Config aktualisiert');
    }

    public function show(Config $config)
    {
        return view('admin.configs.show', compact('config'));
    }

    public function edit(Config $config)
    {
        return view('admin.configs.edit', compact('config'));
    }

    public function destroy(Config $config)
    {
        try {
            $configName = $config->name;
            $config->delete();

            Cache::forget('app.config.all');

            return redirect()->route('admin.configs.index')
                ->with('success', __('Config "') . $configName . __('" wurde gelöscht'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Fehler beim Löschen: ') . $e->getMessage());
        }
    }
}
