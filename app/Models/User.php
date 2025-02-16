<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

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
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'password',
        'avatar'
    ];

    protected $appends = ['formatted_full_name', 'assigned_tasks_count', 'avatar_url'];

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
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? Storage::url($this->avatar) : '/storage/avatars/default-avatar.png';
    }

    /**
     * Get the full name as a formatted attribute
     *
     * @return string
     */
    public function getFormattedFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAssignedTasksCountAttribute()
    {
        return count($this->assignedTasks);
    }

    public function assignedNotes()
    {
        return $this->hasMany(RealtorNote::class, 'assigned_to');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function incompleteAssignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to')->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function assignedOfficeNotes()
    {
        return $this->hasMany(OfficeNote::class, 'assigned_to');
    }

    public function officeVisits()
    {
        return $this->hasMany(OfficeVisit::class, 'user_id');
    }

    public function taskNotes()
    {
        return $this->hasMany(TaskNote::class, 'created_by');
    }

    public function communications()
    {
        return $this->hasMany(RealtorCommunicationLog::class, 'user_id');
    }
}
