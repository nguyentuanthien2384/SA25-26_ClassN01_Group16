<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $guarded = [''];
    
    protected $fillable = [
        'u_name',
        'u_email',
        'u_password',
        'u_phone',
        'u_address',
        'u_avatar',
    ];
    
    protected $hidden = [
        'u_password',
        'remember_token',
    ];

    const STATUS_PUBLIC = 1;
    const STATUS_PRIVATE = 0;
    
    protected $status =[
        1 =>[
            'name' => 'Public',
            'class' =>'label-success'
        ],
        0 =>[
            'name' => 'Private',
            'class' =>'label-default'
        ]
    ];
    
    // Override authentication column names
    public function getAuthIdentifierName()
    {
        return 'id';
    }
    
    public function getAuthPassword()
    {
        return $this->u_password;
    }
    
    // Relationships
    public function transactions()
    {
        return $this->hasMany(\App\Models\Models\Transaction::class, 'tr_user_id');
    }
    
    public function carts()
    {
        return $this->hasMany(\App\Models\Models\Cart::class, 'c_user_id');
    }
    
    public function wishlists()
    {
        return $this->hasMany(\App\Models\Models\Wishlist::class, 'w_user_id');
    }
    
    public function ratings()
    {
        return $this->hasMany(\App\Models\Models\Rating::class, 'r_user_id');
    }
}
