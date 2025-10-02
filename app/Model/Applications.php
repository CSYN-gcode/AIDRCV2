<?php

namespace App\Model;

use App\Model\AcdcsDocs;
use App\Model\RapidXUser;
use App\Model\EsignApprover;
//use App\Model\AffectedDocuments;
use App\Model\HeadApprovals;

use App\Model\QSValidations;

use App\Model\DccValidations;

use App\Model\RapidXDepartment;

use App\Model\ApplicationRevisions;
use Illuminate\Database\Eloquent\Model;


class Applications extends Model
{
    protected $table = "applications";
    protected $connection = "mysql";

    public function esign_approver_details(){
        return $this->hasMany(EsignApprover::class, 'application_id', 'id')->whereNull('deleted_at');
    }

    public function self_details()
    {
        return $this->hasOne(Applications::class, 'id', 'id');
    }

    public function originator_details()
    {
    	return $this->hasOne(RapidXUser::class, 'id', 'application_originator');
    }


    public function section_head_details()
    {
    	return $this->hasOne(RapidXUser::class, 'id', 'application_section_head');
    }

    public function prod_head_details()
    {
        return $this->hasOne(RapidXUser::class, 'id', 'application_prod_head');
    }

    public function qc_head_details()
    {
        return $this->hasOne(RapidXUser::class, 'id', 'application_qc_head');
    }

    public function eng_head_details()
    {
        return $this->hasOne(RapidXUser::class, 'id', 'application_eng_head');
    }

    public function qs_inspector_details()
    {
        return $this->hasOne(RapidXUser::class, 'id', 'qs_inspector');
    }

    public function affected_documents_details()
    {
        return $this->hasMany(AffectedDocuments::class, 'application_id', 'id');
    }

    public function department_details()
    {
        return $this->hasOne(RapidXDepartment::class, 'department_id', 'department');
    }

    public function dcc_validation_details()
    {
        return $this->hasMany(DccValidations::class, 'application_id', 'id');
    }

    public function head_approval_details()
    {
        return $this->hasMany(HeadApprovals::class, 'application_id', 'id');
    }

    public function qs_validation_details()
    {
        return $this->hasMany(QSValidations::class, 'application_id', 'id');
    }

    public function application_revision_details()
    {
        return $this->hasMany(ApplicationRevisions::class, 'application_id', 'id');
    }

    public function control_details()
    {
        return $this->hasOne(AcdcsDocs::class, 'doc_no', 'document_number');
    }
}
