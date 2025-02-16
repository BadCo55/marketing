<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealtorSequenceStepNote extends Model
{
    /** @use HasFactory<\Database\Factories\RealtorSequenceStepNoteFactory> */
    use HasFactory;

    protected $fillable = [
        'realtor_id',
        'sequence_id',
        'step_id',
        'status',
        'status_description',
        'next_update',
        'note',
        'created_by',
    ];

    public function realtor()
    {
        return $this->belongsTo(Realtor::class);
    }

    public function sequence()
    {
        return $this->belongsTo(Sequence::class);
    }

    public function step()
    {
        return $this->belongsTo(SequenceStep::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
