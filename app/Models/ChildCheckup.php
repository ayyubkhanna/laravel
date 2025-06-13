<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChildCheckup extends Model
{
    use HasFactory;

    protected $fillable = ['child_id', 'date', 'age', 'length_body', 'weight', 'stunting', 'imunisasi'];

    protected $casts = [
        'imunisasi' => 'array',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function stunting()
    {
        return $this->hasOne(Stunting::class);
    }
    
}
