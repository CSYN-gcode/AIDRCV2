<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\RapidXUser;

class HeadApprovals extends Model
{
    protected $table = "head_approvals";
    protected $connection = "mysql";

    public function approver_details()
    {
        return $this->hasOne(RapidXUser::class, 'id', 'approver_id');
    }
}
