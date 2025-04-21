<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'rating',
        'comment',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Add validation before saving
        static::saving(function ($feedback) {
            if ($feedback->rating < 1 || $feedback->rating > 5) {
                throw ValidationException::withMessages([
                    'rating' => 'The rating must be between 1 and 5.'
                ]);
            }
        });
    }

    // Feedback belongs to an evaluation
    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }
} 