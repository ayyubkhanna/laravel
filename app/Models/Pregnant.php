<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pregnant extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function person()
    {
        return $this->belongsTo(Person::class, 'people_id');
    }

    public function child()
    {
        return $this->hasMany(Child::class, 'motherId');
    }

    public function checkups()
    {
        return $this->hasMany(PregnantCheckup::class);
    }
}
