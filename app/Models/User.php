<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'first_name',
        'last_name',
        'avatar',
        'bio',
        'google_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the user's avatar URL.
     *
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        // Default avatar based on the first letter of the username
        $firstLetter = strtoupper(substr($this->username, 0, 1));
        $colors = ['#1abc9c', '#3498db', '#9b59b6', '#f1c40f', '#e74c3c', '#34495e'];
        $colorIndex = ord($firstLetter) % count($colors);
        
        return "https://ui-avatars.com/api/?name={$firstLetter}&background=" . ltrim($colors[$colorIndex], '#') . "&color=ffffff&size=256";
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function isTeacher()
    {
        return $this->role === 'teacher';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Relationships for debriefing system
    
    // Teachers have many briefs
    public function briefs()
    {
        return $this->hasMany(Brief::class, 'teacher_id');
    }

    // Students have many submissions
    public function submissions()
    {
        return $this->hasMany(Submission::class, 'student_id');
    }

    // Students can be evaluators for many evaluations
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'evaluator_id');
    }
}
