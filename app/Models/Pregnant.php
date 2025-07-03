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
        return $this->belongsTo(Person::class, 'peopleId');
    }

    public function child()
    {
        return $this->hasMany(Child::class, 'motherId');
    }

    public function prenatalCheckups()
    {
        return $this->hasMany(PrenetalCheckup::class, 'pregnantId');
    }

    public function pregnantInformation()
    {
        return $this->hasOne(PregnantInformation::class, 'pregnantId');
    }
}
