<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class);
    }

    public function checkup ()
    {
        return $this->hasMany(ChildCheckup::class);
    }

    public function stunting ()
    {
        return $this->hasOne(Stunting::class);
    }
}
