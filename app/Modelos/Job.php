<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'jobs';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
}
