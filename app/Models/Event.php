<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'type', 'speaker_id', 'start_time', 'end_time', 'total_attendees', 'total_revenue','amount', 'max_attendees', 'photo'];

    public function speaker()
    {
        return $this->belongsTo(speaker::class);
    }
    public function registrations() {
        return $this->hasMany(Registration::class, 'event_id');
    }
}
