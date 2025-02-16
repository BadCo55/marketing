<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskNote extends Model
{
    /** @use HasFactory<\Database\Factories\TaskNoteFactory> */
    use HasFactory;

    protected $fillable = ['note', 'created_by', 'task_id'];

    protected $appends = ['formatted_created_at'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFormattedCreatedAtAttribute()
    {
        return Carbon::parse($this->created_at)
            ->timezone('America/New_York')
            ->format('m/d/y g:i a');
    }
}
