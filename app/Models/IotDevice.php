<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class IotDevice extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid', // Although it will be auto-generated, it's good to have it here
        'farm_id',
        'description',
        'status',
        'unit',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Register a creating event to auto-generate UUID
        static::creating(function ($model) {
            // Prevent overwriting if UUID is already set
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the farm that this device belongs to.
     */
    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farm_id');
    }

    /**
     * Get the sensor data for the IoT device.
     */
    public function sensorData()
    {
        return $this->hasMany(SensorData::class, 'uuid', 'uuid');
    }
}
