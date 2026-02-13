<?php

namespace App\Models;

use App\Models\Equipment;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'title',
        'description',
        'location',
        'starts_at',
        'ends_at',
        'equipment_id',
        'result_status',
        'result_link',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
