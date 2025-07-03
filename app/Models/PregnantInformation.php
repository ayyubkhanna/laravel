<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PregnantInformation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function pregnant()
    {
        return $this->belongsTo(Pregnant::class, 'pregnantId');
    }
}
