<?php

namespace MrAlirezaEb\BugloosTest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BLHeaderSchema extends Model
{
  use HasFactory;

  // Disable Laravel's mass assignment protection
  protected $guarded = [];
}