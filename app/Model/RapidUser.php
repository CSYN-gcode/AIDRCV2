<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RapidUser extends Model
{
     protected $table = "tbl_useraccounts";
    protected $connection = "mysql_rapid_users";
}
