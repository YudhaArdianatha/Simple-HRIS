<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];

    public function attendance()
    {
        return $this->hasMany(attendance::class);
    }

    public function leave()
    {
        return $this->hasMany(leave::class);
    }
}
