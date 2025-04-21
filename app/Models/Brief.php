<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class Brief extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'teacher_id',
        'deadline',
        'status',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Add validation before saving
        static::saving(function ($brief) {
            if ($brief->deadline < Carbon::now()) {
                throw ValidationException::withMessages([
                    'deadline' => 'The deadline must be a future date and time.'
                ]);
            }
        });
    }

    // A brief belongs to a teacher (user)
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // A brief has many criteria
    public function criteria()
    {
        return $this->hasMany(BriefCriteria::class);
    }

    // A brief has many submissions
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    // Status helpers
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isPublished()
    {
        return $this->status === 'published';
    }

    public function isArchived()
    {
        return $this->status === 'archived';
    }
    
    public function isExpired()
    {
        return $this->status === 'expired' || 
               ($this->isActive() && $this->deadline < Carbon::now());
    }
} 