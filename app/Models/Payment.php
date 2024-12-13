<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];

      public function user()
      {
          return $this->belongsTo(User::class);
      }
      public function appointment()
      {
          return $this->belongsTo(Appointment::class);
      }

      //relation with pattient
      public function patient()
      {
          return $this->belongsTo(Patient::class);
      }
}
