<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'issue_description',
        'service_center',
        'cost',
        'status',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function courierLogs()
    {
        return $this->hasMany(CourierLog::class);
    }
}
