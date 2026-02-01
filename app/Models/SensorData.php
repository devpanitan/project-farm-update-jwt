<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SensorData extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'sensor_prefix',
        'val',
        'sensor_type_id',
    ];

    /**
     * Get the IoT device that this data belongs to.
     * We define the foreign key and owner key because we are using uuid instead of the standard id.
     */
    public function iotDevice()
    {
        return $this->belongsTo(IotDevice::class, 'uuid', 'uuid');
    }

    /**
     * Get the sensor type for this data.
     */
    public function sensorType()
    {
        return $this->belongsTo(SensorType::class, 'sensor_type_id');
    }
}
