<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    protected $primaryKey = 'application_id';
    public $timestamps = false;
    protected $fillable = [
        'job_id',
        'user_id',
        'cover_letter',
        'resume_file',
        'applied_at',
        'status',
    ];

    /**
     * An application belongs to a user (employee)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * An application belongs to a job
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }
}
