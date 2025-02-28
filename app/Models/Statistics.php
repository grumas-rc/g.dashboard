<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statistics extends Model
{
    use HasFactory;

    protected $primaryKey = 'statistic_id';
    public $timestamps = true;

    protected $fillable = [
        'nodes',
        'throughputs',
        'earned',
    ];
}
