<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\RapidXUser;

class SectionHeads extends Model
{
    //
    protected $fillable = ['rapidx_user_id'];

    protected $table = "section_heads";
    protected $connection = "mysql";

    public function rapidx_user_details()
    {
        return $this->hasOne(RapidXUser::class, 'id', 'rapidx_user_id');
    }
}
