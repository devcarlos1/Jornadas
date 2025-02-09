<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'type', 'speaker_id', 'start_time', 'end_time','max_attendees'];

    public function speaker()
    {
        return $this->belongsTo(speaker::class);
    }
}
