<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stunting extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function weighings()
    {
        return $this->belongsTo(Weighing::class, 'weightId');
    }
}
