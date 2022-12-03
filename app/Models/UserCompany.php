<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserCompany extends Pivot
{
    use HasFactory;

    protected $table = "user_companies";
    public $incrementing = true;
}
