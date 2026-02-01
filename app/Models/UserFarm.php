<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserFarm extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'farm_id',
    ];

    /**
     * Get the user that owns the farm.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the farm that the user owns.
     */
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}
