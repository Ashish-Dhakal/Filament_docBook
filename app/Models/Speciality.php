<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{
    protected $guarded = [];

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }
}
