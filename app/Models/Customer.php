<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_code',
        'name',
        'phone',
        'email',
        'birth_date',
        'gender',
        'address'
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
    }
}
