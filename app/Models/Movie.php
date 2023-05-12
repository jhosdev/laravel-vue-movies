<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'date', 'status', 'imgPath', 'created_at', 'updated_at'];

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

}
