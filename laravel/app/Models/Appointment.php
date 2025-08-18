<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'phone',
        'service_id',
        'date',
        'time',
        'notes',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}