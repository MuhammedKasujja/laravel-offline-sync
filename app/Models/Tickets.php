<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\OfflineSync\Traits\GenerateModelIDTrait;

class Tickets extends Model
{
    use HasFactory, SoftDeletes, GenerateModelIDTrait;
}
