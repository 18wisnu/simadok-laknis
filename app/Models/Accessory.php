<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'name',
        'is_removable',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
