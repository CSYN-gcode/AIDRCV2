<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ExternalApplication extends Model
{
    public function user_details(){
        return $this->hasOne(RapidXUser::class, 'id', 'created_by');
    }
}
