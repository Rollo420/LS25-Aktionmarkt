<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Price extends Model
{
    use HasFactory;

    /**
     * Die Attribute, die massenweise zuweisbar sind.
     *
     * @var array
     */
    // keep date in fillable for backwards compatibility but prefer using game_time_id
    protected $fillable = ['id', 'created_at', 'updated_at', 'name', 'stock_id', 'date', 'game_time_id'];

    /**
     * Relationship: a price belongs to a GameTime
     */
    public function gameTime()
    {
        return $this->belongsTo(\App\Models\GameTime::class, 'game_time_id');
    }

    /**
     * Beziehung: Ein Preis gehört zu einer Aktie.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    } 
}
