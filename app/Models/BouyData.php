<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BouyData extends Model
{
    protected $table = 'bouy_datas';

    protected $fillable = [
        "transmit_time",
        "device_id",
        "fLat",
        "fLon",
        "entered_water",
        "left_water",
        "resurfaced",
        "scheduled",
        "remains_at_surface",
        "moved",
        "fH20Temp",
        "nTimeToNxtUpd",
        "fDepthMean",
        "nSOC",
        "low_battery",
        "nDiveCount",
        "fVelocity",
        "fDepthMax",
        "nDiveSeconds",
        "is_backup",
    ];
}
