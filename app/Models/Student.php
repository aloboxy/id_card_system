<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that should be appended to the model's array form.
     */
    protected $appends = ['qr_code_url', 'class_with_section'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'student_id',
        'first_name',
        'last_name',
        'middle_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'religion',
        'nationality',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'fingerprint_image_path',
        'profile_photo_path',
        'country',
        'class',
        'section',
        'roll_number',
        'admission_number',
        'admission_date',
        'father_name',
        'father_phone',
        'father_occupation',
        'mother_name',
        'mother_phone',
        'mother_occupation',
        'guardian_address',
        'id_card_template_id',
        'photo_path',
        'signature_path',
        'id_card_issue_date',
        'id_card_expiry_date',
        'is_active',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
        'id_card_issue_date' => 'date',
        'id_card_expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the student's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }

    /**
     * Get the student's age.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? Carbon::parse($this->date_of_birth)->age : null;
    }

    /**
     * Get the ID card template associated with the student.
     */
    public function idCardTemplate(): BelongsTo
    {
        return $this->belongsTo(IdCardTemplate::class);
    }

    /**
     * Get the school associated with the student.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the URL to the student's photo.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset('storage/'.$this->photo_path) : null;
    }

    /**
     * Get the URL to the student's signature.
     */
    public function getSignatureUrlAttribute(): ?string
    {
        return $this->signature_path ? asset('storage/'.$this->signature_path) : null;
    }

    /**
     * Check if the student's ID card is expired.
     */
    public function getIsIdCardExpiredAttribute(): bool
    {
        return $this->id_card_expiry_date
            ? now()->gt($this->id_card_expiry_date)
            : false;
    }

    /**
     * Scope a query to only include active students.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include students with expired ID cards.
     */
    public function scopeExpiredIdCards($query)
    {
        return $query->whereNotNull('id_card_expiry_date')
            ->where('id_card_expiry_date', '<', now());
    }

    /**
     * Get the student's class and section.
     */
    public function getClassWithSectionAttribute(): string
    {
        return $this->section
            ? "{$this->class} - {$this->section}"
            : $this->class;
    }

    /**
     * Generate QR code URL for the student.
     */
public function getQrCodeUrlAttribute(): string
{
    $school = $this->school;

    $qrData = "=== STUDENT ID CARD ===\n\n";

    if ($school) {
        $qrData .= "School: {$school->name}\n";
    }

    $qrData .= "Student ID: {$this->student_id}\n";
    $qrData .= "Name: {$this->first_name} {$this->last_name}\n";

    if ($this->middle_name) {
        $qrData .= "Middle Name: {$this->middle_name}\n";
    }

    $qrData .= "Class: {$this->class}\n";

    if ($this->section) {
        $qrData .= "Section: {$this->section}\n";
    }

    if ($this->admission_number) {
        $qrData .= "Admission No: {$this->admission_number}\n";
    }

    if ($this->date_of_birth) {
        $qrData .= "DOB: {$this->date_of_birth->format('Y-m-d')}\n";
    }

    if ($this->gender) {
        $qrData .= "Gender: {$this->gender}\n";
    }

    // ✅ FORCE UTF-8 ENCODING (THIS SOLVES THE ERROR)
    $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::encoding('UTF-8')
        ->size(200)
        ->format('svg')
        ->errorCorrection('H')
        ->generate($qrData);

    return 'data:image/svg+xml;base64,' . base64_encode($qrCode);
}

}
