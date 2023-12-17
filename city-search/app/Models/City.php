<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class City extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'city-search';
}
