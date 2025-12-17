<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'staff_id',
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'phone',
        'designation',
        'department',
        'qualification',
        'joining_date',
        'profile_photo_path',
        'signature_path',
        'id_card_template_id',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'joining_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the staff's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }

    /**
     * Get the ID card template associated with the staff.
     */
    public function idCardTemplate(): BelongsTo
    {
        return $this->belongsTo(IdCardTemplate::class);
    }

    /**
     * Get the school associated with the staff.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the URL to the staff's photo.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->profile_photo_path ? asset('storage/'.$this->profile_photo_path) : null;
    }

    /**
     * Get the URL to the staff's signature.
     */
    public function getSignatureUrlAttribute(): ?string
    {
        return $this->signature_path ? asset('storage/'.$this->signature_path) : null;
    }

    /**
     * Scope a query to only include active staff.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
