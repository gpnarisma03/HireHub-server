<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    protected $primaryKey = 'job_id';

    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'category_id',
        'job_title',
        'job_description',
        'job_qualifications',
        'job_responsibilities',
        'job_type',
        'job_vacancy',
        'payment_range',
        'status',
        'date_start',
        'posted_at',
    ];

    /**
     * Job belongs to a company
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Job belongs to a job category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'category_id');
    }

    /**
     * Job has many applications
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'job_id');
    }

    
}
