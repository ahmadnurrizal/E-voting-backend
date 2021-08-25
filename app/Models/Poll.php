<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'deadline',
        'status',
        'image_path',
        'number_voter'
    ];

    public function pollOptions()
    {
        return $this->hasMany(PollOption::class); // poll has many pollOptions
    }

    public function voters()
    {
        return $this->hasMany(Voter::class); // poll has many Voters
    }

    public function user()
    {
        return $this->belongsTo(User::class); // Get the user that owns the post.
    }
}
