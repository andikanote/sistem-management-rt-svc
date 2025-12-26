<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [];

// Relasi: Tagihan milik satu warga
    public function user() {
        return $this->belongsTo(User::class);
    }
}
