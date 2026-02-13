<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'repair_id',
        'courier_name',
        'type',
        'photo_proof_path',
    ];

    public function repair()
    {
        return $this->belongsTo(Repair::class);
    }
}
