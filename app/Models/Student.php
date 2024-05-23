<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'sex',
        'birth_date',
        'active',
    ];

    protected $hidden = [
        'deleted_at'
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}
