<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $primaryKey = 'company_id';

    protected $fillable = [
        'user_id',
        'company_name',
        'company_details',
        'company_logo',
        'street',
        'city',
        'region',
        'zip_code',
    ];

    /**
     * A company belongs to a user (employer)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * A company has many jobs
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'company_id');
    }
}
