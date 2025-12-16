<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'school_code',
        'address',
        'logo_path',
        'contact_email',
        'contact_phone',
        'is_active',
        'date_issue',
        'expire_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
