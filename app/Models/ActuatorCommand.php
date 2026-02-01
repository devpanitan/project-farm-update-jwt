<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActuatorCommand extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'auto_rule_id',
        'actuator_prefix',
        'pin',
        'val',
    ];

    /**
     * Get the IoT device that this command belongs to.
     */
    public function iotDevice()
    {
        // Note: We are linking the local 'uuid' column to the 'uuid' column on the IotDevice model.
        return $this->belongsTo(IotDevice::class, 'uuid', 'uuid');
    }

    /**
     * Get the auto rule that this command is a part of.
     */
    public function autoRule()
    {
        return $this->belongsTo(AutoRule::class, 'auto_rule_id');
    }
}
