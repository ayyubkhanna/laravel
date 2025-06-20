<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function pregnant()
    {
        return $this->belongsTo(Pregnant::class);
    }

    public function checkups ()
    {
        return $this->hasMany(ChildCheckup::class);
    }
}
