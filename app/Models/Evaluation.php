<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'evaluator_id',
        'overall_comment',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    // An evaluation belongs to a submission
    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    // An evaluation belongs to an evaluator (user)
    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    // An evaluation has many answers
    public function answers()
    {
        return $this->hasMany(EvaluationAnswer::class);
    }

    // An evaluation has one feedback
    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }

    // Status helpers
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }
} 