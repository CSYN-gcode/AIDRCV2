<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\RapidXUser;
use App\Model\AffectedDocuments;

class AccessLevel extends Model
{
    protected $table = "access_level";
    protected $connection = "mysql";

    public function rapidx_user_details()
    {
    	return $this->hasOne(RapidXUser::class, 'id', 'rapidx_id');
    }

    public function affected_docs_details()
    {
    	return $this->hasMany(AffectedDocuments::class, 'application_id', 'id');
    }
}
