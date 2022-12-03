<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['name', 'phone', 'age', 'photo', 'email', 'is_verified', 'verified_at', 'role_id', 'team_id'];

    
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
