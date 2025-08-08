<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Opd extends Model
{
    use HasFactory;

    protected $fillable = ['namaopd'];
    

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
