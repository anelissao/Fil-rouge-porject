<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BriefTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'criteria_id',
        'description',
        'order',
    ];

    // A task belongs to a criteria
    public function criteria()
    {
        return $this->belongsTo(BriefCriteria::class, 'criteria_id');
    }

    // A task has many evaluation answers
    public function evaluationAnswers()
    {
        return $this->hasMany(EvaluationAnswer::class, 'task_id');
    }
} 