<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Job;

class JobCategory extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id';

    public $timestamps = false; // Assuming your table has no created_at/updated_at

    protected $fillable = [
        'category_name',
        'category_logo'
    ];

    /**
     * JobCategory has many jobs
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'category_id');
    }
}
