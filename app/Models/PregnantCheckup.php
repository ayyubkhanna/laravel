<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PregnantCheckup extends Model
{
    use HasFactory;

    protected $fillable = [
        'pregnant_id',
        'date',
        'result',
        'notes',
        'medicine'
    ];

    public function pregnant()
    {
        return $this->belongsTo(Pregnant::class);
    }
}
