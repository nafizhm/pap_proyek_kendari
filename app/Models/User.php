<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'surname',
        'username',
        'password',
        'email',
        'status',
        'id_role',
        'id_marketing',
    ];

    public $timestamps = false;

    protected $hidden = ['password', 'remember_token'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role');
    }
}
