<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\RapidUser;

class AcdcsDocs extends Model
{
    protected $table = "tbl_active_docs";
    protected $connection = "mysql_rapid";

    public function controller_details()
    {
        return $this->hasOne(RapidUser::class, 'username', 'controlled_by');
    }
}
