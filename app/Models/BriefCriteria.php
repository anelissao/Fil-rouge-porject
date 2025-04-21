<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BriefCriteria extends Model
{
    use HasFactory;

    protected $table = 'brief_criteria';

    protected $fillable = [
        'brief_id',
        'title',
        'description',
        'order',
    ];

    // A criteria belongs to a brief
    public function brief()
    {
        return $this->belongsTo(Brief::class);
    }

    // A criteria has many tasks
    public function tasks()
    {
        return $this->hasMany(BriefTask::class, 'criteria_id');
    }
} 