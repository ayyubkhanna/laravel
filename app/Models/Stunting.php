<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stunting extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }
}
