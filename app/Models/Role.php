<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};

class Role extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['name', 'company_id'];

    /**
     * Get the company that owns the Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Get all of the responsibilities for the Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function responsibilities()
    {
        return $this->hasMany(Responsibility::class, 'role_id');
    }
}
