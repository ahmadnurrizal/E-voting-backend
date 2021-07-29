<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
  use HasFactory;

  protected $fillable = [
    'user_id',
    'poll_id',
    'poll_option_id'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function poll()
  {
    return $this->belongsTo(Poll::class);
  }

  public function pollOption()
  {
    return $this->belongsTo(PollOption::class);
  }
}
