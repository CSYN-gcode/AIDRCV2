<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\RapidXUser;
use App\Model\Applications;
use App\Model\AcdcsDocs;


class AffectedDocuments extends Model
{
    protected $table = "affected_documents";
    protected $connection = "mysql";

     public function pic_details()
    {
    	return $this->hasOne(RapidXUser::class, 'id', 'person_in_charge');
    }

    public function application_details()
    {
    	return $this->hasOne(Applications::class, 'id', 'application_id');
    }

    public function control_details()
    {
        return $this->hasOne(AcdcsDocs::class, 'doc_no', 'document_number');
    }
}
