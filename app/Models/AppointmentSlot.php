<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentSlot extends Model
{
    protected $guarded = [];
      public function doctor()
      {
          return $this->belongsTo(Doctor::class);
      }

}
