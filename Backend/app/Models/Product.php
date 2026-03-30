<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    public const TABLE = 'products';

    protected $fillable = [
        'user_id',
        'nome',
        'estoque',
        'custo_medio',
        'preco_venda',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
