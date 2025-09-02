<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\RapidXUser;

use App\Model\Applications;

class HeadApprovals extends Model
{
    protected $table = "head_approvals";
    protected $connection = "mysql";

    public function approver_details()
    {
        return $this->hasOne(RapidXUser::class, 'id', 'approver_id');
    }

    // public function head_approval_details()
    // {
    //     return $this->hasOne(Applications::class, 'id', 'application_id');
    // }
}
