<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posyandu extends Model
{
    use HasFactory;

    protected $guarded = [];

    // public function pregnant()
    // {
    //     return $this->hasMany(Pregnant::class);
    // }

    public function person()
    {
        return $this->hasMany(Person::class);
    }
}
