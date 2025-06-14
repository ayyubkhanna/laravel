<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function child()
    {
        return $this->hasOne(Person::class);
    }

    public function pregnant()
    {
        return $this->hasOne(Pregnant::class);
    }

    public function posyandu()
    {
        return $this->belongsTo(Person::class);
    }
}
