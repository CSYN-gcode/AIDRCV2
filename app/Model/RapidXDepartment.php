<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RapidXDepartment extends Model
{
    protected $table = "departments";
    protected $connection = "mysql_rapidx";
}
