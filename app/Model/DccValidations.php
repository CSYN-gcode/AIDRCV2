<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Applications;
use App\Model\RapidXUser;

class DccValidations extends Model
{
    protected $table = "dcc_validations";
    protected $connection = "mysql";

    public function application_details()
    {
    	return $this->hasOne(Applications::class, 'id', 'application_id');
    }

    public function dcc_validator_details()
    {
    	return $this->hasOne(RapidXUser::class, 'id', 'dcc_in_charge');
    }
}
