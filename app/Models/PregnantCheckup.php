<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PregnantCheckup extends Model
{
    use HasFactory;

    protected $guaded = [];

    public function pregnant()
    {
        return $this->belongsTo(Pregnant::class);
    }
}
