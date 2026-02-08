<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Farm extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'farm_cat_id',
        'lat',
        'lng',
        'size',
        'farm_prefix',
    ];

    /**
     * Get the category that this farm belongs to.
     */
    public function farmCategory()
    {
        // Use the correct foreign key 'farm_cat_id'
        return $this->belongsTo(FarmCategory::class, 'farm_cat_id');
    }

    /**
     * The users that belong to the farm.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'farm_user');
    }
}
