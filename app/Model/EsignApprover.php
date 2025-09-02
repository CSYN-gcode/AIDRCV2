<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EsignApprover extends Model{
    //
    public function user_details()
    {
        return $this->hasOne(RapidXUser::class, 'id', 'approver_id');
    }
}
