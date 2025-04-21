<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'task_id',
        'response',
        'comment',
    ];

    protected $casts = [
        'response' => 'boolean',
    ];

    // An answer belongs to an evaluation
    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    // An answer belongs to a task
    public function task()
    {
        return $this->belongsTo(BriefTask::class, 'task_id');
    }
} 