<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weighing extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function child() 
    {
        return $this->belongsTo(Child::class, 'childId');
    }

    public function stunting() 
    {
        return $this->hasOne(Stunting::class, 'weightId');
    }
}
