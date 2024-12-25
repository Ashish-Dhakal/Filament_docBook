<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{
    protected $fillable = [ 'name' ,'slug'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }
}
