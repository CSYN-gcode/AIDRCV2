<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AcdcsPendingDocuments extends Model
{
    protected $table = "tbl_documents";
    protected $connection = "mysql_rapid";
}
