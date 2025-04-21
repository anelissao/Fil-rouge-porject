<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'brief_id',
        'student_id',
        'content',
        'file_path',
        'submission_date',
        'status',
    ];

    protected $casts = [
        'submission_date' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Add validation before saving
        static::saving(function ($submission) {
            $brief = Brief::find($submission->brief_id);
            
            if ($brief && $submission->submission_date > $brief->deadline) {
                throw ValidationException::withMessages([
                    'submission_date' => 'Submission date cannot exceed the brief deadline.'
                ]);
            }
        });
    }

    // A submission belongs to a brief
    public function brief()
    {
        return $this->belongsTo(Brief::class);
    }

    // A submission belongs to a student (user)
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // A submission has many evaluations
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    // Status helpers
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isSubmitted()
    {
        return $this->status === 'submitted';
    }

    public function isEvaluated()
    {
        return $this->status === 'evaluated';
    }
} 