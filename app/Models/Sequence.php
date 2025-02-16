<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sequence extends Model
{
    /** @use HasFactory<\Database\Factories\SequenceFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'target_audience',
    ];

    public function steps()
    {
        return $this->hasMany(SequenceStep::class)->orderBy('step_number');
    }
}
