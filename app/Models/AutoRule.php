<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AutoRule extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'iot_device_id',
        'actuator_id',
        'description',
        'activate_interval',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['iotDevice', 'actuator'];

    /**
     * Get the IoT device that owns the rule.
     */
    public function iotDevice()
    {
        return $this->belongsTo(IotDevice::class, 'iot_device_id');
    }

    /**
     * Get the actuator command that the rule executes.
     */
    public function actuator()
    {
        return $this->belongsTo(ActuatorCommand::class, 'actuator_id');
    }
}
