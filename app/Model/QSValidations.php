<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Applications;
use App\Model\RapidXUser;

class QSValidations extends Model
{
    //
    protected $table = "qs_validations";
    protected $connection = "mysql";

    public function application_details()
    {
        return $this->hasOne(Applications::class, 'id', 'application_id');
    }

}
