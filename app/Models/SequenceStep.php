<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SequenceStep extends Model
{
    /** @use HasFactory<\Database\Factories\SequenceStepFactory> */
    use HasFactory;

    protected $fillable = [
        'sequence_id',
        'type', 
        'interval',
        'description',
        'template',
        'step_number'
    ];

    public function sequence()
    {
        return $this->belongsTo(Sequence::class);
    }

    public function notes()
    {
        return $this->hasMany(RealtorSequenceStepNote::class, 'step_id');
    }
}
