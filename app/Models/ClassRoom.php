<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    protected $fillable = ['name', 'code'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

}
