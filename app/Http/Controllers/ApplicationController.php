<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use DataTables;

use App\Model\AcdcsDocs;
use App\Model\Applications;
use App\Model\AffectedDocuments;
use App\Model\RapidXUser;
use App\Model\AccessLevel;
use App\Model\DccValidations;
use App\Model\ApplicationRevisions;


// use TCPDF;
use setasign\Fpdi\Tcpdf\Fpdi;
use Illuminate\Support\Facades\Log;

use Mail;

class ApplicationController extends Controller{

    public function check_existing_aidrc_application(Request $request){
        session_start();
        $rapidx_user_id = $_SESSION['rapidx_user_id'];
        // return $application = Applications::/*where('application_originator', $rapidx_user_id)->*/where('document_number', $request->document_number)->where('document_revision_number', $request->revision_number)->where('logdel', 0)->whereNotIn('status', [9, 10, 11])->count();
        // $application = Applications::/*where('application_originator', $rapidx_user_id)->*/where('document_number', $request->document_number)->where('document_revision_number', $request->revision_number)->where('logdel', 0)->whereNotIn('status', [9, 10, 11])
        $application = Applications::/*where('application_originator', $rapidx_user_id)->*/where('document_number', $request->document_number)
                        ->where('document_revision_number', $request->revision_number)
                        ->where('logdel', 0)
                        ->whereNotIn('status', [6, 9])
                        ->count();
                        // ->toSql();

                        // return $application;

        if ($application > 0){ // with result
            return response()->json(['result' => 1]);
        }else{ // no result
            return response()->json(['result' => 2]);
        }
    }

    public function check_existing_aidrc_application_new(Request $request)
    {
        $application = Applications::/*where('application_originator', $rapidx_user_id)->*/where('document_name', $request->document_title)->where('document_type', 1)->where('logdel', 0)->count();

        if ($application > 0) {
            return response()->json(['result' => 1]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function load_acdcs_layout(Request $request)
    {
        session_start();

        $rapidx_user_id = $_SESSION['rapidx_user_id'];

        $rapidx_user_dcc = RapidXUser::whereIn('department_id', [21, 22, 23])->where('id', $rapidx_user_id)->first();

        if ($rapidx_user_dcc != null) {
            return response()->json(['result' => 1]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function load_acdcs_table(Request $request)
    {
        session_start();

        $rapidx_user_id = $_SESSION['rapidx_user_id'];

        $rapidx_user_dcc = AccessLevel::where('rapidx_id', $rapidx_user_id)->whereIn('access_level', [1, 2, 6])->orderBy('created_at', 'desc')->first();


        if ($rapidx_user_dcc != null){
            $applications = Applications::with(['qs_inspector_details', 'section_head_details', 'originator_details', 'affected_documents_details.approver_details', 'affected_documents_details' => function ($query2) {

                $query2->where('logdel', 0)->whereIn('approver_type', [2, 3, 4]);
            }, 'control_details']);
        }else{
            $applications = Applications::with(['department_details', 'control_details', 'qs_inspector_details', 'section_head_details', 'originator_details', 'affected_documents_details.approver_details', 'affected_documents_details' => function ($query) use ($rapidx_user_id) {

                $query->whereIn('approver_type', [2, 3, 4])->whereIn('document_status', [1, 2, 3, 4]);
                $query/*->where('affected_document_approver', $rapidx_user_id)*/->where('logdel', 0);
            }])->where(function ($originator) use ($request, $rapidx_user_id) {

                $originator->where('application_originator', $rapidx_user_id)->where('logdel', 0);
            })->orWhere(function ($section_head) use ($request, $rapidx_user_id) {

                $section_head->where('application_section_head', $rapidx_user_id)->where('logdel', 0);
            })->orWhere(function ($qs_inspector) use ($request, $rapidx_user_id) {

                $qs_inspector->where('application_qs_inspector', $rapidx_user_id)->where('logdel', 0);
            })->orWhereHas('affected_documents_details', function ($query) use ($rapidx_user_id) {
                $query->where('affected_document_approver', $rapidx_user_id)->where('logdel', 0);
            });
        }

        $applications = $applications->where('logdel', 0)->orderBy('aidrc_control_number', 'desc')->get();

        return DataTables::of($applications)
            ->addColumn('aidrc_control_num', function ($application) {

                $result = $application->aidrc_control_number;

                return $result;
            })
            ->addColumn('status', function ($application) {

                $result = '';

                switch ($application->status) {
                    case 1: {
                            //APPLICATION FILED

                            //$result = "<strong style='color: #17a2b8;'>FOR APPROVAL</strong>";


                            //NEW
                            $result = "<strong style='color: #fd7e14;'>FOR QS VALIDATION CHECK</strong>";


                            break;
                        }
                    case 2: {
                            //APPROVED BY SECTION HEAD (OPERATIONS)
                            // $result = "<strong style='color: #fd7e14;'>FOR QS VALIDATION CHECK</strong>";

                            //NEW
                            $result = "<strong style='color: #007bff;'>FOR CHECKPOINTS APPROVAL</strong>";

                            break;
                        }
                    case 3: {
                            //QS INSPECTION FILED
                            //$result = "<strong style='color: #007bff;'>FOR CHECKPOINTS APPROVAL</strong>";

                            $result = "<strong style='color: #17a2b8;'>FOR APPROVAL</strong>";

                            break;
                        }
                    case 4: {
                            //APPROVED BY ALL, FOR INITIAL DCC VALIDATION
                            $result = "<strong style='color: #6610f2;'>FOR DCC VALIDATION</strong>";

                            break;
                        }
                    case 5: {
                            //MINOR REVISIONS
                            $result = "<strong style='color: #20c997;'>FOR MINOR REVISIONS</strong>";

                            break;
                        }
                    case 6: {
                            //MINOR REVISIONS
                            $result = "<strong style='color: #6f42c1;'>FOR MAJOR REVISIONS, FOR FILING OF NEW APPLICATION</strong>";

                            break;
                        }
                    case 7: {

                            $search_revision_no = $application->document_revision_number;

                            if ($application->control_details != null) {
                                if ($application->control_details->rev_no == $search_revision_no) {
                                    $result = "<strong style='color: #28a745;'>DOCUMENT CONTROLLED</strong>";
                                } else {
                                    //DCC APPROVED, FOR CONTROL
                                    $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                                }
                            } else {
                                //DCC APPROVED, FOR CONTROL
                                $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                            }

                            break;
                        }
                    case 8: {
                            //DCC APPROVED, FOR CONTROL
                            $result = "DOCUMENT CONTROLLED";

                            break;
                        }
                    case 9: {
                            $result .= '<strong style="color: #dc3545;">APPLICATION DISAPPROVED BY SECTION HEAD</strong>';
                            break;
                        }
                    case 10: {
                            $result .= '<strong>APPLICATION CANCELLED</strong>';
                            break;
                        }
                    case 11: {
                            $result .= '<strong style="color: #e83e8c;">APPLICATION DISAPPROVED BY QS INSPECTOR</strong>';
                            $result .= '<br><strong>QS INSPECTOR REMARKS:</strong> ' . $application->qs_inspector_remarks;

                            break;
                        }
                    default: {
                            $result = "---";

                            break;
                        }
                }

                return $result;
            })
            ->addColumn('created_at', function ($application) {

                $result = $application->created_at;

                return $result;
            })
            ->addColumn('originator', function ($application) {

                $result = $application->originator_details->name;

                return $result;
            })
            ->addColumn('section_department', function ($application) {

                $result = $application->department_details->department_name;

                return $result;
            })
            ->addColumn('document_number', function ($application) {

                if ($application->document_number != null) {
                    $result = $application->document_number;
                } else {
                    $result = "---";
                }

                return $result;
            })
            ->addColumn('document_title', function ($application) {

                $result = $application->document_name;

                return $result;
            })
            ->addColumn('document_revision_number', function ($application) {

                $result = $application->document_revision_number;

                return $result;
            })
            ->addColumn('uploaded_file', function ($application) {

                $result = '<a href="http://192.168.3.188/aidrc_test/download_attached_document/' . $application->id . '" title="Click to download file">' . $application->original_filename . '</a>';

                return $result;
            })
            ->addColumn('application_approvers', function ($application) {

                $result = "";

                if ($application->status == 1) {
                    $result = '<span class="badge badge-warning">SECTION HEAD APPROVER: ' . $application->section_head_details->name . '</span>';
                } else if ($application->status == 9) {
                    $result = '<span class="badge badge-secondary">SECTION HEAD APPROVER: ' . $application->section_head_details->name . '</span>';
                } else if ($application->status == 10) {
                    $result = '<span class="badge badge-secondary">SECTION HEAD APPROVER: ' . $application->section_head_details->name . '</span>';
                } else {
                    $result = '<span class="badge badge-success">SECTION HEAD APPROVER: ' . $application->section_head_details->name . '</span>';
                }



                if ($application->for_group == 1) {
                    if ($application->status == 1) {
                        $result .= '<br><span class="badge badge-secondary">QS VALIDATION: ' . $application->qs_inspector_details->name . '</span>';
                    } else if ($application->status == 2) {
                        $result .= '<br><span class="badge badge-info">QS VALIDATION: ' . $application->qs_inspector_details->name . '</span>';
                    } else if ($application->status == 9) {
                        $result .= '<br><span class="badge badge-secondary">QS VALIDATION: ' . $application->qs_inspector_details->name . '</span>';
                    } else if ($application->status == 10) {
                        $result .= '<br><span class="badge badge-secondary">QS VALIDATION: ' . $application->qs_inspector_details->name . '</span>';
                    } else {
                        $result .= '<br><span class="badge badge-success">QS VALIDATION: ' . $application->qs_inspector_details->name . '</span>';
                    }
                }

                $affected_document = 0;

                for ($i = 0; $i < count($application->affected_documents_details); $i++) {
                    $approver_type = '';

                    if ($application->affected_documents_details[$i]->logdel == 0 && $application->affected_documents_details[$i]->document_status != 5) {
                        switch ($application->affected_documents_details[$i]->approver_type) {
                            case 1: {
                                    $approver_type = "AFFECTED DOCUMENT(S) APPROVAL: " . $application->affected_documents_details[$i]->approver_details->name;

                                    if ($affected_document < 1) {
                                        $result .= '<br><span class="badge badge-success">' . $approver_type . '</span>';
                                    }

                                    $affected_document++;

                                    break;
                                }
                            case 2: {
                                    $approver_type = "ENG'G VALIDATION APPROVAL: " . $application->affected_documents_details[$i]->approver_details->name;

                                    if ($application->affected_documents_details[$i]->document_status == 2 || $application->affected_documents_details[$i]->document_status == 6) {
                                        $result .= '<br><span class="badge badge-success">' . $approver_type . '</span>';
                                    } else {
                                        $result .= '<br><span class="badge badge-warning">' . $approver_type . '</span>';
                                    }



                                    break;
                                }
                            case 3: {
                                    $approver_type = "QC VALIDATION APPROVAL: " . $application->affected_documents_details[$i]->approver_details->name;

                                    if ($application->affected_documents_details[$i]->document_status == 2 || $application->affected_documents_details[$i]->document_status == 6) {
                                        $result .= '<br><span class="badge badge-success">' . $approver_type . '</span>';
                                    } else {
                                        $result .= '<br><span class="badge badge-warning">' . $approver_type . '</span>';
                                    }

                                    break;
                                }
                            case 4: {
                                    $approver_type = "ENG'G VALIDATION APPROVAL: " . $application->affected_documents_details[$i]->approver_details->name;

                                    if ($application->affected_documents_details[$i]->document_status == 2 || $application->affected_documents_details[$i]->document_status == 6) {
                                        $result .= '<br><span class="badge badge-success">' . $approver_type . '</span>';
                                    } else {
                                        $result .= '<br><span class="badge badge-warning">' . $approver_type . '</span>';
                                    }

                                    break;
                                }
                            default: {
                                    $approver_type = "---";

                                    $result .= '<br><span class="badge badge-success">' . $approver_type . '</span>';

                                    break;
                                }
                        }
                    }
                }


                return $result;
            })
            ->addColumn('action', function ($application) use ($rapidx_user_id, $rapidx_user_dcc) {

                $result = '';

                switch ($application->status) {
                    case 1: {
                            if ($application->application_qs_inspector == $rapidx_user_id) {
                                $result .= '<button type="button" class="btn btn-block btn-sm btn-success btn-qs-validations" data-toggle="modal" data-target="#modalQSValidation" application-id=' . $application->id . '><i class="fa fa-microscope"></i> QS Validation Check</button>';
                            }

                            if ($application->application_originator == $rapidx_user_id || $rapidx_user_dcc != null) {
                                $result .= '<button type="button" class="btn btn-block btn-sm btn-success btn-change-qs-inspector" data-toggle="modal" data-target="#modalChangeQsInspector" application-id=' . $application->id . '><i class="fa fa-retweet"></i> Change QS Inspector</button>';
                            }

                            break;
                        }
                    case 2: {
                            if ($application->affected_documents_details != null) {
                                for ($i = 0; $i < count($application->affected_documents_details); $i++) {
                                    if ($application->affected_documents_details[$i]->affected_document_approver == $rapidx_user_id) {
                                        if ($application->affected_documents_details[$i]->document_status == 1) {
                                            switch ($application->affected_documents_details[$i]->approver_type) {
                                                case 2: {
                                                        $result .= '<button type="button" class="btn btn-block btn-sm btn-success btn-approve-affected-operations-doc" data-toggle="modal" data-target="#modalApproveAffectedDocument" affected-doc-id=' . $application->affected_documents_details[$i]->id . '><i class="fa fa-check-circle"></i> Approve Eng. Validation (FMEA)</button>';

                                                        break;
                                                    }
                                                case 3: {
                                                        $result .= '<button type="button" class="btn btn-block btn-sm btn-success btn-approve-affected-operations-doc" data-toggle="modal" data-target="#modalApproveAffectedDocument" affected-doc-id=' . $application->affected_documents_details[$i]->id . '><i class="fa fa-check-circle"></i> Approve QC Validation</button>';

                                                        break;
                                                    }
                                                case 4: {
                                                        $result .= '<button type="button" class="btn btn-block btn-sm btn-success btn-approve-affected-operations-doc" data-toggle="modal" data-target="#modalApproveAffectedDocument" affected-doc-id=' . $application->affected_documents_details[$i]->id . '><i class="fa fa-check-circle"></i> Approve Eng. Validation (PPC)</button>';

                                                        break;
                                                    }
                                                default: {
                                                        break;
                                                    }
                                            }
                                        }
                                    }
                                }
                            }



                            break;
                        }
                    case 3: {

                            if ($application->application_section_head == $rapidx_user_id) {
                                $result .= '<button type="button" class="btn btn-block btn-sm btn-success btn-approve-application" data-toggle="modal" data-target="#modalApproveApplication" application-id=' . $application->id . '><i class="fa fa-check-circle"></i> Approve Application</button>';
                            }

                            if ($application->application_originator == $rapidx_user_id) {
                                $result .= '<button type="button" class="btn btn-block btn-sm btn-primary btn-edit-application" data-toggle="modal" data-target="#modalViewEditApplication" view-edit=2 edit-status=1 application-id=' . $application->id . '><i class="fa fa-edit"></i> Edit Application</button>';

                                $result .= '<button type="button" class="btn btn-block btn-sm btn-secondary btn-cancel-application" application-id=' . $application->id . '><i class="fa fa-ban"></i> Cancel Application</button>';
                            }

                            break;
                        }
                    case 4: {
                            if ($rapidx_user_dcc != null) {
                                $result .= '<button type="button" class="btn btn-block btn-sm btn-warning btn-dcc-validation" application-id=' . $application->id . ' data-toggle="modal" data-target="#modalDccValidation"><i class="fa fa-user-plus"></i> DCC Validation Check</button>';
                            }

                            break;
                        }
                    case 5: {
                            if ($application->application_originator == $rapidx_user_id) {
                                $result .= '<button type="button" class="btn btn-block btn-sm btn-success btn-minor-revision" data-toggle="modal" data-target="#modalMinorRevisions" application-id=' . $application->id . '><i class="fa fa-check"></i> Edit Minor Revisions</button>';
                            }

                            break;
                        }
                    case 6: {
                            /*if($application->application_originator == $rapidx_user_id)
                    {
                        $result .= '<button type="button" class="btn btn-block btn-sm btn-primary" application-id='.$application->id.'><i class="fa fa-check"></i> Major Revision Check</button>';
                    }*/

                            break;
                        }
                    case 7: {
                            $result .= '';
                            break;
                        }
                    case 8: {
                            $result .= '';
                            break;
                        }
                    case 9: {
                            $result .= '';
                            break;
                        }
                    case 10: {
                            $result .= '';
                            break;
                        }
                    case 11: {
                            if ($application->application_originator == $rapidx_user_id) {
                                $result .= '<button type="button" class="btn btn-block btn-sm btn-primary btn-edit-application" data-toggle="modal" data-target="#modalViewEditApplication" view-edit=2 edit-status=2 application-id=' . $application->id . '><i class="fa fa-edit"></i> Edit Application</button>';
                            }

                            break;
                        }
                    default: {
                            $result .= '';
                            break;
                        }
                }

                $result .= '<button type="button" class="btn btn-block btn-sm btn-info btn-view-application"  data-toggle="modal" data-target="#modalViewEditApplication" view-edit=1 edit-status=1 application-id=' . $application->id . '><i class="fa fa-eye"></i> View Application</button>';



                return $result;
            })
            ->rawColumns(['action', 'uploaded_file', 'application_approvers', 'status'])
            ->make(true);
    }

    public function load_acdcs_active_documents(Request $request)
    {
        $search_type = 0;

        if (isset($request->search_type)) {
            $search_type = $request->search_type;
        }


        $documents = AcdcsDocs::where('logdel', 0);


        if (isset($request->search_add_document)) {
            if (isset($request->document_search_type)) {
                if ($request->document_search_type == 1) {
                    $documents->where('doc_no', 'like', '%' . $request->search_add_document . '%');
                } else {
                    $documents->where('doc_title', 'like', '%' . $request->search_add_document . '%');
                }
            }
        }


        if (isset($request->search_affected_document)) {
            if (isset($request->document_search_type)) {
                if ($request->document_search_type == 1) {
                    $documents->where('doc_no', 'like', '%' . $request->search_affected_document . '%');
                } else {
                    $documents->where('doc_title', 'like', '%' . $request->search_affected_document . '%');
                }
            }
        }


        $documents->get();

        return DataTables::of($documents)
            ->addColumn('document_number', function ($document) {

                $result = $document->doc_no;

                return $result;
            })
            ->addColumn('document_title', function ($document) {

                $result = $document->doc_title;

                return $result;
            })
            ->addColumn('revision_number', function ($document) {

                $result = $document->rev_no;

                return $result;
            })

            //now we gonna add different actions here

            ->addColumn('action_global', function ($document) use ($search_type) {

                $result = '';

                switch ($search_type) {
                    case 1: {
                            $result = '<button type="button" class="btn btn-sm btn-info btn-global-affected-documents" active-doc-id=' . $document->pkid . ' title="Add Affected Document" data-toggle="modal" data-target="#modalGlobalAffectedDocumentDetails"><i class="fa fa-plus"></i></button>';

                            break;
                        }

                    case 2: {
                            $result = '<button type="button" class="btn btn-sm btn-info btn-global-approver-affected-documents" active-doc-id=' . $document->pkid . ' title="Add Affected Document" data-toggle="modal" data-target="#modalGlobalAffectedDocumentDetailsFromHead"><i class="fa fa-plus"></i></button>';

                            break;
                        }

                    case 3: {
                            $result = '';

                            break;
                        }

                    case 4: {
                            $result = '';

                            break;
                        }

                    default: {
                            $result = '';

                            break;
                        }
                }

                return $result;
            })

            ->addColumn('action_master_list', function ($document) {

                $result = '<button type="button" class="btn btn-sm btn-primary btn-add-master-list-details" active-doc-id=' . $document->pkid . ' title="Add Document Details"><i class="fa fa-plus"></i></button>';

                return $result;
            })

            //add document details
            ->addColumn('action_document_details', function ($document) {

                $result = '<button type="button" class="btn btn-sm btn-primary btn-add-document-details" active-doc-id=' . $document->pkid . ' title="Add Document Details"><i class="fa fa-plus"></i></button>';

                return $result;
            })


            //add document details
            ->addColumn('action_edit_document_details', function ($document) {

                $result = '<button type="button" class="btn btn-sm btn-primary btn-edit-document-details" active-doc-id=' . $document->pkid . ' title="Add Document Details"><i class="fa fa-plus"></i></button>';

                return $result;
            })

            //add affected documents
            ->addColumn('action_add_affected_document', function ($document) {

                $result = '<button type="button" class="btn btn-sm btn-info btn-add-affected-documents" active-doc-id=' . $document->pkid . ' title="Add Affected Document" data-toggle="modal" data-target="#modalAddaffectedDocumentDetails"><i class="fa fa-plus"></i></button>';

                return $result;
            })

            ->addColumn('action_qs_details', function ($document) {

                $result = '<button type="button" class="btn btn-sm btn-primary btn-add-qs-details" active-doc-id=' . $document->pkid . ' title="Add QS Details"><i class="fa fa-plus"></i></button>';

                return $result;
            })

            //add affected documents
            ->addColumn('action_sec_add_affected_document', function ($document) {

                $result = '<button type="button" class="btn btn-sm btn-info btn-sec-add-affected-documents" active-doc-id=' . $document->pkid . ' title="Add Affected Document" data-toggle="modal" data-target="#modalSecAddaffectedDocumentDetails"><i class="fa fa-plus"></i></button>';

                return $result;
            })

            ->addColumn('action_approver_affected_document', function ($document) {

                $result = '<button type="button" class="btn btn-sm btn-info btn-add-affected-documents-approver" active-doc-id=' . $document->pkid . ' title="Add Affected Document" data-toggle="modal" data-target="#modalApproverAffectedDocumentDetails"><i class="fa fa-plus"></i></button>';

                return $result;
            })

            ->addColumn('action_edit_affected_document', function ($document) {

                $result = '<button type="button" class="btn btn-sm btn-info btn-edit-affected-documents" active-doc-id=' . $document->pkid . ' title="Add Affected Document" data-toggle="modal" data-target="#modalApproverAffectedDocumentDetails"><i class="fa fa-plus"></i></button>';

                return $result;
            })


            ->rawColumns(['action_document_details', 'action_add_affected_document', 'action_qs_details', 'action_sec_add_affected_document', 'action_approver_affected_document', 'action_edit_document_details', 'action_global', 'action_master_list'])
            ->make(true);
    }

    public function load_acdcs_document_details(Request $request)
    {
        $document_details = AcdcsDocs::where('pkid', $request->active_doc_id)->get();

        if (count($document_details) > 0) {
            return response()->json(['result' => 1, 'document_details' => $document_details]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function load_previous_dcc_validations(Request $request)
    {
        $dcc_validations = DccValidations::with(['dcc_validator_details'])->where('application_id', $request->application_id)->where('logdel', 0)->orderBy('created_at', 'desc')->get();

        return DataTables::of($dcc_validations)
            ->addColumn('validation_datetime', function ($validation) {

                $result = $validation->created_at;

                return $result;
            })
            ->addColumn('validation_similar', function ($validation) {

                switch ($validation->checkpoint_similar) {
                    case 1: {
                            $result = "Compliant";
                            break;
                        }
                    case 2: {
                            $result = "Non-Compliant";
                            break;
                        }
                }

                return $result;
            })
            ->addColumn('validation_alignment', function ($validation) {

                switch ($validation->checkpoint_alignment) {
                    case 1: {
                            $result = "Compliant";
                            break;
                        }
                    case 2: {
                            $result = "Non-Compliant";
                            break;
                        }
                }

                return $result;
            })
            ->addColumn('validation_standard', function ($validation) {

                switch ($validation->checkpoint_standard) {
                    case 1: {
                            $result = "Compliant";
                            break;
                        }
                    case 2: {
                            $result = "Non-Compliant";
                            break;
                        }
                }

                return $result;
            })
            ->addColumn('validation_dcc_in_charge', function ($validation) {

                $result = $validation->dcc_validator_details->name;

                return $result;
            })
            ->addColumn('validation_remarks', function ($validation) {

                $result = $validation->dcc_remarks;

                return $result;
            })
            ->rawColumns(['validation_similar', 'validation_alignment', 'validation_standard'])
            ->make(true);
    }

    public function load_array_affected_documents(Request $request)
    {
        $array_affected_documents = [];

        if (isset($request->array_affected_documents)) {
            $array_affected_documents = $request->array_affected_documents;
        }

        return DataTables::of($array_affected_documents)
            ->addColumn('document_number', function ($document) {

                $result = $document['document_number'];

                return $result;
            })
            ->addColumn('document_title', function ($document) {

                $result = $document['document_title'];

                return $result;
            })
            ->addColumn('revision_number', function ($document) {

                $result = $document['document_revision_number'];

                return $result;
            })
            ->addColumn('revision_due_date', function ($document) {

                $result = $document['revision_due_date'];

                return $result;
            })

            ->addColumn('approver_pic', function ($document) {

                $result = $document['approver_pic_name'];

                return $result;
            })

            ->addColumn('action_remove_affected_document', function ($document) {

                $result = '<button type="button" class="btn btn-sm btn-danger btn-remove-from-affected-document-array" title="Remove from Affected Documents List" document-pkid=' . $document['document_pkid'] . '><i class="fa fa-times-circle"></i></button>';

                return $result;
            })

            ->addColumn('action_sec_remove_affected_document', function ($document) {

                $result = '<button type="button" class="btn btn-sm btn-danger btn-sec-remove-from-affected-document-array" title="Remove from Affected Documents List" document-pkid=' . $document['document_pkid'] . '><i class="fa fa-times-circle"></i></button>';

                return $result;
            })

            ->addColumn('action_approver_remove_affected_document', function ($document) {

                $result = '<button type="button" class="btn btn-sm btn-danger btn-approver-remove-from-affected-document-array" title="Remove from Affected Documents List" document-pkid=' . $document['document_pkid'] . '><i class="fa fa-times-circle"></i></button>';

                return $result;
            })

            ->rawColumns(['action_remove_affected_document', 'action_sec_remove_affected_document', 'action_approver_remove_affected_document'])
            ->make(true);
    }

    public function submit_add_application(Request $request)
    {
        session_start();

        date_default_timezone_set('Asia/Manila');

        $validator = '';

        switch ($request->add_for_group) {
            case 1: {
                    if ($request->add_doc_type == 1) //Operations, New Document
                    {
                        $validator = Validator::make($request->all(), [

                            'add_attachment' => 'required',
                            'add_doc_category' => 'required',
                            'add_doc_type' => 'required',
                            'add_for_group' => 'required',
                            'add_department' => 'required',
                            'add_doc_title' => 'required',
                            'add_application_approver' => 'required',
                            'add_qs_inspector' => 'required',

                        ]);
                    } else //Operations, Revised
                    {
                        $validator = Validator::make($request->all(), [

                            'add_attachment' => 'required',
                            'add_doc_category' => 'required',
                            'add_doc_type' => 'required',
                            'add_for_group' => 'required',
                            'add_department' => 'required',
                            'add_doc_no' => 'required',
                            'add_doc_title' => 'required',
                            'add_doc_rev_no' => 'required',
                            'add_application_approver' => 'required',
                            'add_qs_inspector' => 'required',

                        ]);
                    }

                    break;
                }
            case 2: {
                    if ($request->add_doc_type == 1) //Support Group, New Document
                    {
                        $validator = Validator::make($request->all(), [

                            'add_attachment' => 'required',
                            'add_doc_category' => 'required',
                            'add_doc_type' => 'required',
                            'add_for_group' => 'required',
                            'add_department' => 'required',
                            'add_doc_title' => 'required',
                            'add_application_approver' => 'required',

                        ]);
                    } else //Support, Revised
                    {
                        $validator = Validator::make($request->all(), [

                            'add_attachment' => 'required',
                            'add_doc_category' => 'required',
                            'add_doc_type' => 'required',
                            'add_for_group' => 'required',
                            'add_department' => 'required',
                            'add_doc_no' => 'required',
                            'add_doc_title' => 'required',
                            'add_doc_rev_no' => 'required',
                            'add_application_approver' => 'required',

                        ]);
                    }

                    break;
                }
            case 3: {
                    if ($request->add_doc_type == 1) //OPERATIONS - PPC Group, New Document
                    {
                        $validator = Validator::make($request->all(), [

                            'add_attachment' => 'required',
                            'add_doc_category' => 'required',
                            'add_doc_type' => 'required',
                            'add_for_group' => 'required',
                            'add_department' => 'required',
                            'add_doc_title' => 'required',
                            'add_application_approver' => 'required',

                        ]);
                    } else //OPERATIONS - PPC, Revised
                    {
                        $validator = Validator::make($request->all(), [

                            'add_attachment' => 'required',
                            'add_doc_category' => 'required',
                            'add_doc_type' => 'required',
                            'add_for_group' => 'required',
                            'add_department' => 'required',
                            'add_doc_no' => 'required',
                            'add_doc_title' => 'required',
                            'add_doc_rev_no' => 'required',
                            'add_application_approver' => 'required',

                        ]);
                    }

                    break;
                }
            default: {
                    $validator = Validator::make($request->all(), [

                        'add_attachment' => 'required',
                        'add_doc_category' => 'required',
                        'add_doc_type' => 'required',
                        'add_for_group' => 'required',
                        'add_department' => 'required',
                        'add_doc_no' => 'required',
                        'add_doc_title' => 'required',
                        'add_doc_rev_no' => 'required',
                        'add_application_approver' => 'required',
                        'add_qs_inspector' => 'required',

                    ]);

                    break;
                }
        }


        if ($validator->passes()) {
            $aidrc_control_number = '';
            $month = date('m');
            $year = date('y');
            $year2 = date('Y');
            $counter = 0;

            /*$applications = Applications::where('logdel',0)->whereYear('created_at', $year2)->count();

            if($applications > 0)
            {
                $counter = $applications + 1;
            }*/
            $applications = Applications::where('logdel', 0)->whereYear('created_at', $year2)->orderBy('created_at', 'desc')->first();


            if ($applications != null) {
                $control_number = $applications->aidrc_control_number;

                $number = explode('-', $control_number);

                $counter = intval($number[1]) + 1;
            } else {
                $counter = 1;
            }

            $aidrc_control_number = $month . $year  . "-" . str_pad($counter, 3, "0", STR_PAD_LEFT);

            //originator/creator
            $originator_id = $_SESSION['rapidx_user_id'];

            //attachment filename
            $generated_filename = "aidrc_attachment_" . date('YmdHis');
            $original_filename = $request->file('add_attachment')->getClientOriginalName();
            $file_extension = $request->file('add_attachment')->getClientOriginalExtension();
            $aidrc_filename = $generated_filename . "." . $file_extension;

            $qs_inspector = null;

            if (isset($request->add_qs_inspector)) {
                $qs_inspector = $request->add_qs_inspector;
            }

            try {
                Storage::putFileAs('public/file_attachments', $request->add_attachment, $aidrc_filename);

                $application_id = Applications::insertGetId([

                    'aidrc_control_number' => $aidrc_control_number,
                    'document_number' => $request->add_doc_no,
                    'document_name' => $request->add_doc_title,
                    'document_revision_number' => $request->add_doc_rev_no,
                    'document_category' => $request->add_doc_category,
                    'document_type' => $request->add_doc_type,
                    'for_group' => $request->add_for_group,
                    'department' => $request->add_department,
                    'application_originator' => $originator_id,
                    'application_section_head' => $request->add_application_approver,
                    'application_qs_inspector' => $qs_inspector,
                    'aidrc_filename' => $aidrc_filename,
                    'original_filename' => $original_filename,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'logdel' => 0

                ]);

                /* Storage::putFileAs('public/aidrc_attachments', $request->add_attachment, $aidrc_filename);*/

                return response()->json(['result' => 1, 'application_id' => $application_id, 'approver' => $request->add_application_approver]);
            } catch (\Exception $e) {
                DB::rollback();
                // throw $e;
                return response()->json(['result' => $e]);
            }
        } else {
            return response()->json(['result' => 0, 'error' => $validator->messages()]);
        }
    }

    public function submit_application_affected_documents(Request $request)
    {
        session_start();

        date_default_timezone_set('Asia/Manila');

        //originator/creator
        $originator_id = $_SESSION['rapidx_user_id'];

        $array_affected_document = [];

        if (isset($request->array_affected_document)) {
            $array_affected_document = $request->array_affected_document;
        }

        try {
            for ($i = 0; $i < count($array_affected_document); $i++) {
                AffectedDocuments::insert([

                    'application_id' => $request->application_id,
                    'document_acdcs_pkid' => $array_affected_document[$i]['document_pkid'],
                    'document_number' => $array_affected_document[$i]['document_number'],
                    'document_name' => $array_affected_document[$i]['document_title'],
                    'document_revision_number' => $array_affected_document[$i]['document_revision_number'],
                    'document_revision_due_date' => $array_affected_document[$i]['revision_due_date'],
                    'person_in_charge' => $originator_id,
                    'affected_document_approver' => $request->approver,
                    'approver_type' => 1,
                    'document_status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'logdel' => 0

                ]);
            }

            return response()->json(['result' => 1]);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }

    // public function load_application_details(Request $request)
    // {
    //     $application_details = Applications::with(['dcc_validation_details' => function ($query) {

    //         $query->where('logdel', 0)->orderBy('created_at', 'desc');
    //     }, 'dcc_validation_details.dcc_validator_details'])->where('id', $request->application_id)->where('logdel', 0)->get();

    //     if (count($application_details) > 0) {
    //         return response()->json(['result' => 1, 'application_details' => $application_details]);
    //     } else {
    //         return response()->json(['result' => 2]);
    //     }
    // }

    public function load_affected_documents_table(Request $request)
    {
        $array_edit = [];

        $view_edit = 0;

        if (isset($request->array_edit)) {
            $array_edit = $request->array_edit;
        }

        if (isset($request->view_edit)) {
            $view_edit = $request->view_edit;
        }

        $affected_documents = AffectedDocuments::with(['pic_details', 'approver_details'])->where('application_id', $request->application_id)->whereIn('document_status', [1, 2, 3, 4, 6])->where('logdel', 0)->get();

        return DataTables::of($affected_documents)
            ->addColumn('document_number', function ($document) use ($array_edit) {

                if (in_array($document->id, array_column($array_edit, 'affected_doc_id'))) {
                    $key = array_search($document->id, array_column($array_edit, 'affected_doc_id'));

                    if ($array_edit[$key]['affected_doc_status'] == 1) {
                        $result = '<span style="color: red;"><strong>' . $array_edit[$key]['affected_doc_no'] . '</strong></span>';
                    } else {
                        $result = '<span style="color: blue;"><strong>' . $array_edit[$key]['affected_doc_no'] . '</strong></span>';
                    }
                } else {
                    $result = $document->document_number;
                }

                return $result;
            })
            ->addColumn('document_title', function ($document) use ($array_edit) {

                if (in_array($document->id, array_column($array_edit, 'affected_doc_id'))) {
                    $key = array_search($document->id, array_column($array_edit, 'affected_doc_id'));

                    if ($array_edit[$key]['affected_doc_status'] == 1) {
                        $result = '<span style="color: red;"><strong>' . $array_edit[$key]['affected_doc_title'] . '</strong></span>';
                    } else {
                        $result = '<span style="color: blue;"><strong>' . $array_edit[$key]['affected_doc_title'] . '</strong></span>';
                    }
                } else {
                    $result = $document->document_name;
                }

                return $result;
            })
            ->addColumn('document_revision_number', function ($document) use ($array_edit) {

                if (in_array($document->id, array_column($array_edit, 'affected_doc_id'))) {
                    $key = array_search($document->id, array_column($array_edit, 'affected_doc_id'));

                    if ($array_edit[$key]['affected_doc_status'] == 1) {
                        $result = '<span style="color: red;"><strong>' . $array_edit[$key]['affected_doc_rev_no'] . '</strong></span>';
                    } else {
                        $result = '<span style="color: blue;"><strong>' . $array_edit[$key]['affected_doc_rev_no'] . '</strong></span>';
                    }
                } else {
                    $result = $document->document_revision_number;
                }

                return $result;
            })
            ->addColumn('document_revision_due_date', function ($document) use ($array_edit) {

                if (in_array($document->id, array_column($array_edit, 'affected_doc_id'))) {
                    $key = array_search($document->id, array_column($array_edit, 'affected_doc_id'));

                    if ($array_edit[$key]['affected_doc_status'] == 1) {
                        $result = '<span style="color: red;"><strong>' . $array_edit[$key]['affected_revision_date'] . '</strong></span>';
                    } else {
                        $result = '<span style="color: blue;"><strong>' . $array_edit[$key]['affected_revision_date'] . '</strong></span>';
                    }
                } else {
                    $result = $document->document_revision_due_date;
                }

                return $result;
            })
            ->addColumn('person_in_charge', function ($document) use ($array_edit) {

                if (in_array($document->id, array_column($array_edit, 'affected_doc_id'))) {
                    $key = array_search($document->id, array_column($array_edit, 'affected_doc_id'));

                    if ($array_edit[$key]['affected_doc_status'] == 1) {
                        $result = '<span style="color: red;"><strong>' . $array_edit[$key]['affected_pic_name'] . '</strong></span>';
                    } else {
                        $result = '<span style="color: blue;"><strong>' . $array_edit[$key]['affected_pic_name'] . '</strong></span>';
                    }
                } else {
                    if ($document->person_in_charge != null) {
                        $result = $document->pic_details->name;
                    } else {
                        $result = '---';
                    }
                }

                return $result;
            })
            ->addColumn('action_supervisor', function ($document) use ($array_edit) {

                if (in_array($document->id, array_column($array_edit, 'affected_doc_id'))) {
                    $key = array_search($document->id, array_column($array_edit, 'affected_doc_id'));

                    $result = '<button type="button" class="btn btn-sm btn-danger btn-sec-cancel-edit-document" affected-document-id=' . $array_edit[$key]['affected_doc_id'] . ' title="Cancel Document Edit"><i class="fa fa-times-circle"></i></button>';
                } else {
                    $result = '<button type="button" class="btn btn-sm btn-info btn-sec-edit-affected-document" affected-document-id=' . $document->id . ' title="Edit Revision Due Date / P.I.C" data-toggle="modal" data-target="#modalSecEditAffectedDocumentDetails"><i class="fa fa-edit"></i></button>';
                }

                return $result;
            })

            ->addColumn('supervisor_status', function ($document) use ($array_edit) {

                if (in_array($document->id, array_column($array_edit, 'affected_doc_id'))) {
                    $key = array_search($document->id, array_column($array_edit, 'affected_doc_id'));

                    if ($array_edit[$key]['affected_doc_status'] == 1) {
                        $result = '<span style="color: red;"><strong>FOR REMOVAL</strong></span>';
                    } else {
                        $result = '<span style="color: blue;"><strong>FOR EDIT</strong></span>';
                    }
                } else {
                    $result = "---";
                }

                return $result;
            })

            //from other functions
            ->addColumn('affected_document_type', function ($document) {

                $result = '';

                switch ($document->approver_type) {
                    case 1: {
                            $result = "From Application";
                            break;
                        }
                    case 2: {
                            $result = "FMEA";
                            break;
                        }
                    case 3: {
                            $result = "Control Plan";
                            break;
                        }
                    case 4: {
                            $result = "Pre-Production Checksheet";
                            break;
                        }
                    default: {
                            $result = "---";
                            break;
                        }
                }

                return $result;
            })
            ->addColumn('action_dcc', function ($document) {

                $result = "---";

                return $result;
            })

            ->addColumn('approver_details', function ($document) {

                $result = $document->approver_details->name;

                return $result;
            })

            ->addColumn('approver_datetime', function ($document) {

                if ($document->approval_datetime != null) {
                    $result = $document->approval_datetime;
                } else {
                    $result = "---";
                }

                return $result;
            })

            ->addColumn('document_remarks', function ($document) {

                if ($document->document_remarks != null) {
                    $result = $document->document_remarks;
                } else {
                    $result = "---";
                }

                return $result;
            })

            ->addColumn('action_view_edit', function ($document) use ($view_edit) {

                $result = '';

                if ($view_edit == 2) {
                    $result = '<button type="button" class="btn btn-sm btn-danger btn-remove-from-qs-validations-new" document-id=' . $document->id . ' title="Remove Document"><i class="fa fa-times-circle"></i></button>';
                } else {
                    $result = "";
                }

                return $result;
            })

            ->rawColumns(['action_supervisor', 'document_number', 'document_title', 'document_revision_number', 'document_revision_number', 'document_revision_due_date', 'person_in_charge', 'supervisor_status', 'action_view_edit'])
            ->make(true);
    }

    public function submit_section_head_approval(Request $request)
    {
        session_start();
        $section_head_id = $_SESSION['rapidx_user_id'];
        date_default_timezone_set('Asia/Manila');

        $status = 2;

        if ($request->sec_hidden_approval_type == "1") {
            if ($request->sec_for_group == "1") {
                $status = 4;
            } else {
                $status = 4;
            }
        } else {
            $status = 9;
        }

        try {
            Applications::where('id', $request->sec_hidden_application_id)->update([
                'section_head_approval_remarks' => $request->sec_remarks,
                'section_head_approval_datetime' => date('Y-m-d H:i:s'),
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return response()->json(['result' => 1]);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }

    public function submit_section_head_affected_documents(Request $request)
    {
        session_start();
        $section_head_id = $_SESSION['rapidx_user_id'];
        date_default_timezone_set('Asia/Manila');

        try {
            if (isset($request->array_edit_docs)) {
                $array_edit_docs = $request->array_edit_docs;

                for ($i = 0; $i < count($array_edit_docs); $i++) {
                    AffectedDocuments::where('id', $array_edit_docs[$i]['affected_doc_id'])->update([

                        'document_revision_due_date' => $array_edit_docs[$i]['affected_revision_date'],
                        'person_in_charge' => $array_edit_docs[$i]['affected_pic'],
                        'document_status' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'logdel' => $array_edit_docs[$i]['affected_doc_status'],

                    ]);
                }
            }

            if (isset($request->array_add_docs)) {
                $array_add_docs = $request->array_add_docs;

                for ($i = 0; $i < count($array_add_docs); $i++) {
                    AffectedDocuments::insert([


                        'application_id' => $request->application_id,
                        'affected_document_approver' => $section_head_id,
                        'document_acdcs_pkid' => $array_add_docs[$i]['document_pkid'],
                        'document_number' => $array_add_docs[$i]['document_number'],
                        'document_name' => $array_add_docs[$i]['document_title'],
                        'document_revision_number' => $array_add_docs[$i]['document_revision_number'],
                        'document_revision_due_date' => $array_add_docs[$i]['revision_due_date'],
                        'person_in_charge' => $array_add_docs[$i]['approver_pic_id'],
                        'approver_type' => 1,
                        'document_status' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'logdel' => 0,

                    ]);
                }
            }

            AffectedDocuments::where('application_id', $request->application_id)->where('logdel', 0)->update([

                'document_status' => 2,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return response()->json(['result' => 1]);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }

    public function submit_change_qs_inspector(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        $application = Applications::where('id', $request->change_qs_hidden_id)->where('logdel', 0)->get();

        if (count($application) > 0) {
            if ($request->change_qs_hidden_inspector != $request->change_qs_current_inspector) {
                if ($application[0]->status == 2) {
                    try {
                        Applications::where('id', $request->change_qs_hidden_id)->update([
                            'application_qs_inspector' => $request->change_qs_current_inspector,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                        return response()->json(['result' => 1, 'application_id' => $request->change_qs_hidden_id]);
                    } catch (\Exception $e) {
                        DB::rollback();
                        // throw $e;
                        return response()->json(['result' => $e]);
                    }
                } else {
                    return response()->json(['result' => 3]);
                }
            } else {
                return response()->json(['result' => 2]);
            }
        } else {
            return response()->json(['result' => 4]);
        }
    }

    public function load_qs_validation_checkpoints(Request $request)
    {
        $array_validation_checkpoints = [];

        if (isset($request->array_validation_checkpoints)) {
            $array_validation_checkpoints = $request->array_validation_checkpoints;
        }

        return DataTables::of($array_validation_checkpoints)
            ->addColumn('checkpoint', function ($document) {

                switch ($document['checkpoint_type']) {
                    case 2: {
                            $result = 'FMEA';
                            break;
                        }
                    case 3: {
                            $result = 'Control Plan';
                            break;
                        }
                    case 4: {
                            $result = 'Pre-Production Checksheet';
                            break;
                        }
                    default: {
                            $result = '---';
                            break;
                        }
                }

                return $result;
            })
            ->addColumn('document_number_rev', function ($document) {

                $result = '';

                if ($document['checkpoint_type'] == 4) {
                    $result = "Pre-Production Checksheet";
                } else {
                    $result = $document['document_number'] . " / rev " . $document['document_revision_number'];
                }

                return $result;
            })
            ->addColumn('checkpoint_remarks', function ($document) {

                $result = $document['document_remarks'];

                return $result;
            })
            ->addColumn('checkpoint_revision_due_date', function ($document) {

                $result = $document['document_revision_due_date'];

                return $result;
            })
            ->addColumn('checkpoint_pic', function ($document) {

                $pic = RapidXUser::where('id', $document['document_pic'])->get();
                $result = '';

                if (count($pic) > 0) {
                    $result = $pic[0]->name;
                } else {
                    $result = "---";
                }

                return $result;
            })
            ->addColumn('checkpoint_approver', function ($document) {

                $approver = RapidXUser::where('id', $document['document_approver'])->get();
                $result = '';

                if (count($approver) > 0) {
                    $result = $approver[0]->name;
                } else {
                    $result = "---";
                }

                return $result;
            })
            ->addColumn('action_qs_validation', function ($document) {

                $result = '<button type="button" class="btn btn-sm btn-danger btn-remove-from-qs-validations" title="Remove from Checkpoint from List" array-ctr=' . $document['array_ctr'] . '><i class="fa fa-times-circle"></i></button>';

                return $result;
            })
            ->rawColumns(['action_qs_validation'])
            ->make(true);
    }

    public function submit_qs_validations(Request $request)
    {
        session_start();
        $qs_validator = $_SESSION['rapidx_user_id'];
        date_default_timezone_set('Asia/Manila');

        $status = 3;

        if ($request->qs_hidden_status == 1) {
            $status = 2;
        } else if ($request->qs_hidden_status == 2) {
            $status = 3;
        } else {
            $status = 11;
        }

        $validator = Validator::make($request->all(), [

            'qs_remarks' => 'required',

        ]);

        if ($validator->passes()) {
            try {
                Applications::where('id', $request->qs_hidden_application_id)->update([
                    'qs_inspector_remarks' => $request->qs_remarks,
                    'qs_inspector_datetime' => date('Y-m-d H:i:s'),
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                return response()->json(['result' => 1]);
            } catch (\Exception $e) {
                DB::rollback();
                // throw $e;
                return response()->json(['result' => $e]);
            }
        } else {
            return response()->json(['result' => 0, 'error' => $validator->messages()]);
        }
    }

    public function submit_qs_validations_checkpoints(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        $array_qs_validations = [];

        if (isset($request->array_affected_documents)) {
            $array_qs_validations = $request->array_affected_documents;
        }

        try {
            for ($i = 0; $i < count($array_qs_validations); $i++) {
                AffectedDocuments::insert([

                    'application_id' => $request->application_id,
                    'document_acdcs_pkid' => $array_qs_validations[$i]['checkpoint_acdcs_pkid'],
                    'document_number' => $array_qs_validations[$i]['document_number'],
                    'document_name' => $array_qs_validations[$i]['document_title'],
                    'document_revision_number' => $array_qs_validations[$i]['document_revision_number'],
                    'document_revision_due_date' => $array_qs_validations[$i]['document_revision_due_date'],
                    'document_remarks' => $array_qs_validations[$i]['document_remarks'],
                    'person_in_charge' => $array_qs_validations[$i]['document_pic'],
                    'affected_document_approver' => $array_qs_validations[$i]['document_approver'],
                    'approver_type' => $array_qs_validations[$i]['checkpoint_type'],
                    'document_status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'logdel' => 0

                ]);
            }

            return response()->json(['result' => 1]);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }

    public function insert_qs_checkpoint(Request $request)
    {
        session_start();
        $qs_validator = $_SESSION['rapidx_user_id'];
        date_default_timezone_set('Asia/Manila');

        $document_pkid = null;

        if ($request->checkpoint_item['checkpoint_type'] != 4) {
            $document_pkid =  $request->checkpoint_item['checkpoint_acdcs_pkid'];
        }

        try {
            $document_id = AffectedDocuments::insertGetId([

                'application_id' => $request->checkpoint_item['application_id'],
                'document_acdcs_pkid' => $document_pkid,
                'document_number' => $request->checkpoint_item['document_number'],
                'document_name' => $request->checkpoint_item['document_title'],
                'document_revision_number' => $request->checkpoint_item['document_revision_number'],
                'document_revision_due_date' => $request->checkpoint_item['document_revision_due_date'],
                'document_remarks' => $request->checkpoint_item['document_remarks'],
                'person_in_charge' => $request->checkpoint_item['document_pic'],
                'affected_document_approver' => $request->checkpoint_item['document_approver'],
                'approver_type' =>  $request->checkpoint_item['checkpoint_type'],
                'document_status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'logdel' => 0

            ]);

            return response()->json(['result' => 1, 'document_id' => $document_id]);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }

    public function load_qs_validation_checkpoints_by_id(Request $request)
    {
        $documents = AffectedDocuments::with(['pic_details', 'approver_details'])->where('application_id', $request->application_id)->whereIn('approver_type', [2, 3, 4])->where('document_status', 1)->where('logdel', 0)->get();

        return DataTables::of($documents)
            ->addColumn('checkpoint', function ($document) {

                switch ($document->approver_type) {
                    case 2: {
                            $result = 'FMEA';
                            break;
                        }
                    case 3: {
                            $result = 'Control Plan';
                            break;
                        }
                    case 4: {
                            $result = 'Pre-Production Checksheet';
                            break;
                        }
                    default: {
                            $result = '---';
                            break;
                        }
                }

                return $result;
            })
            ->addColumn('document_number_rev', function ($document) {

                $result = '';

                if ($document->approver_type == 4) {
                    $result = "Pre-Production Checksheet";
                } else {
                    $result = $document->document_number . " / rev " . $document->document_revision_number;
                }

                return $result;
            })
            ->addColumn('checkpoint_remarks', function ($document) {

                $result = $document->document_remarks;

                return $result;
            })
            ->addColumn('checkpoint_revision_due_date', function ($document) {

                $result = $document->document_revision_due_date;

                return $result;
            })
            ->addColumn('checkpoint_pic', function ($document) {

                if ($document->person_in_charge != null) {
                    $result = $document->pic_details->name;
                } else {
                    $result = '';
                }


                return $result;
            })
            ->addColumn('checkpoint_approver', function ($document) {

                $result = $document->approver_details->name;

                return $result;
            })
            ->addColumn('action_qs_validation', function ($document) {

                $result = '<button type="button" class="btn btn-sm btn-danger btn-remove-from-qs-validations-new" title="Remove from Checkpoint from List" document-id=' . $document->id . '><i class="fa fa-times-circle"></i></button>';

                return $result;
            })
            ->rawColumns(['action_qs_validation'])
            ->make(true);
    }

    public function remove_qs_checkpoint(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        try {
            AffectedDocuments::where('id', $request->document_id)->update([

                'updated_at' => date('Y-m-d H:i:s'),
                'logdel' => 1
            ]);

            return response()->json(['result' => 1]);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }

    public function remove_qs_checkpoint_by_array(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        try {
            if (isset($request->array_checkpoint)) {
                for ($i = 0; $i < count($request->array_checkpoint); $i++) {
                    AffectedDocuments::where('id', $request->array_checkpoint[$i])->update([

                        'updated_at' => date('Y-m-d H:i:s'),
                        'logdel' => 1
                    ]);
                }
            }

            return response()->json(['result' => 1]);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }


    public function load_affected_document_details(Request $request){
        $document_details = AffectedDocuments::with(['application_details', 'control_details'])->where('id', $request->affected_doc_id)->where('logdel', 0)->get();

        if (count($document_details) > 0) {
            return response()->json(['result' => 1, 'document_details' => $document_details]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function approve_affected_document(Request $request){
        session_start();
        $qs_validator = $_SESSION['rapidx_user_id'];
        date_default_timezone_set('Asia/Manila');

        $validator = '';

        if ($request->app_aff_document_status == 6) {
            $validator = Validator::make($request->all(), [
                'app_aff_document_status' => 'required',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'app_aff_pic' => 'required',
                'app_aff_revision_due_date' => 'required',
            ]);
        }

        if ($validator->passes()) {
            $rev_no = 0;

            $revision_number = AffectedDocuments::where('id', $request->app_aff_doc_id)->pluck('document_revision_number')->first();

            if ($revision_number != null) {
                if ($request->app_aff_document_status == 6) {
                    if (is_numeric($revision_number)) {
                        if ($revision_number != 0) {
                            $rev_no = $revision_number - 1;
                        }
                    } else {
                        $rev_no = $revision_number;
                    }
                } else {
                    $rev_no = $revision_number;
                }
            }

            try {
                AffectedDocuments::where('id', $request->app_aff_doc_id)->update([
                    'approval_remarks' => $request->app_aff_doc_remarks,
                    'document_revision_number' => $rev_no,
                    'document_revision_due_date' => $request->app_aff_revision_due_date,
                    'person_in_charge' => $request->app_aff_pic,
                    'approval_datetime' => date('Y-m-d H:i:s'),
                    'document_status' => $request->app_aff_document_status,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);


                $affected_document_details = AffectedDocuments::where('id', $request->app_aff_doc_id)->where('logdel', 0)->get();

                return response()->json(['result' => 1, 'affected_document_details' => $affected_document_details]);
            } catch (\Exception $e) {
                DB::rollback();
                // throw $e;
                return response()->json(['result' => $e]);
            }
        } else {
            return response()->json(['result' => 0, 'error' => $validator->messages()]);
        }
    }

    public function check_application_affected_document_status(Request $request){
        $total_affected_documents = AffectedDocuments::where('application_id', $request->application_id)->whereIn('approver_type', [2, 3, 4])->whereIn('document_status', [1, 2, 3, 4])->where('logdel', 0)->count();

        $total_approved_affected_documents = AffectedDocuments::where('application_id', $request->application_id)->whereIn('approver_type', [2, 3, 4])->where('document_status', 2)->where('logdel', 0)->count();

        if ($total_affected_documents == $total_approved_affected_documents) {
            try {
                Applications::where('id', $request->application_id)->update([
                    'status' => 3,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                return response()->json(['result' => 1]);
            } catch (\Exception $e) {
                DB::rollback();
                // throw $e;
                return response()->json(['result' => $e]);
            }
        } else {
            try {
                Applications::where('id', $request->application_id)->update([
                    'status' => 2,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                return response()->json(['result' => 2]);
            } catch (\Exception $e) {
                DB::rollback();
                // throw $e;
                return response()->json(['result' => $e]);
            }
        }
    }

    public function submit_dcc_validation(Request $request){
        // return 'submitted';
        session_start();
        $dcc_validator = $_SESSION['rapidx_user_id'];
        date_default_timezone_set('Asia/Manila');

        try {
            DccValidations::insert([

                'application_id' => $request->dcc_hidden_application_id,
                'checkpoint_similar' => $request->dcc_checkpoint_similar,
                'checkpoint_alignment' => $request->dcc_checkpoint_alignment,
                'checkpoint_standard' => $request->dcc_checkpoint_standard,
                'dcc_in_charge' => $dcc_validator,
                'dcc_validation_date' => date('Y-m-d H:i:s'),
                'dcc_remarks' => $request->dcc_remarks,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'logdel' => 0

            ]);


            Applications::where('id', $request->dcc_hidden_application_id)->update([
                'status' => $request->dcc_hidden_status,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return response()->json(['result' => 1]);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }

    public function submit_minor_revisions(Request $request){
        session_start();
        $originator_id = $_SESSION['rapidx_user_id'];
        date_default_timezone_set('Asia/Manila');

        $application = Applications::with(['dcc_validation_details' => function ($query) {

            $query->where('logdel', 0)->orderBy('created_at', 'desc');
        }])->where('id', $request->minor_hidden_application_id)->where('logdel', 0)->get();

        if (count($application) > 0) {
            try {
                ApplicationRevisions::insert([

                    'application_id' => $application[0]->id,
                    'aidrc_attachment_filename' => $application[0]->aidrc_filename,
                    'original_filename' => $application[0]->original_filename,
                    'document_category' => $application[0]->document_category,
                    'document_number' => $application[0]->document_number,
                    'document_title' => $application[0]->document_name,
                    'document_revision_number' => $application[0]->document_revision_number,

                    'dcc_in_charge' => $application[0]->dcc_validation_details[0]->dcc_in_charge,
                    'dcc_validation_datetime' => $application[0]->dcc_validation_details[0]->dcc_validation_date,
                    'dcc_remarks' => $application[0]->dcc_validation_details[0]->dcc_remarks,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'logdel' => 0,

                ]);

                if (isset($request->minor_attachment)) {
                    $generated_filename = "aidrc_attachment_" . date('YmdHis');
                    $original_filename = $request->file('minor_attachment')->getClientOriginalName();
                    $file_extension = $request->file('minor_attachment')->getClientOriginalExtension();
                    $aidrc_filename = $generated_filename . "." . $file_extension;

                    Storage::putFileAs('public/file_attachments', $request->minor_attachment, $aidrc_filename);

                    //UPDATE
                    Applications::where('id', $request->minor_hidden_application_id)->update([

                        'aidrc_filename' => $aidrc_filename,
                        'original_filename' => $original_filename,
                        'document_category' => $request->minor_doc_category,
                        'document_number' => $request->minor_doc_no,
                        'document_name' => $request->minor_doc_title,
                        'document_revision_number' => $request->minor_doc_rev_no,
                        'status' => 4,

                    ]);
                } else {
                    //UPDATE
                    Applications::where('id', $request->minor_hidden_application_id)->update([

                        'document_category' => $request->minor_doc_category,
                        'document_number' => $request->minor_doc_no,
                        'document_name' => $request->minor_doc_title,
                        'document_revision_number' => $request->minor_doc_rev_no,
                        'status' => 4,

                    ]);
                }

                return response()->json(['result' => 1]);
            } catch (\Exception $e) {
                DB::rollback();
                // throw $e;
                return response()->json(['result' => $e]);
            }
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function download_attached_document(Request $request){
        $attachment = Applications::where('id', $request->application_id)->where('logdel', 0)->get();

        $newFilename = str_replace('modified_', '', $attachment[0]->aidrc_filename);
        $file =  storage_path()."/app/public/file_attachments/".$newFilename;
        // return $newFilename;
        // $file =  storage_path() . "/app/public/file_attachments/" . $attachment[0]->aidrc_filename;

        return Response::download($file, $attachment[0]->original_filename);
    }

    // public function download_attached_document123(Request $request){
    //     $attachment = Applications::with([
    //         'esign_approver_details',
    //         'esign_approver_details.user_details'
    //     ])
    //     ->where('id', $request->application_id)->where('logdel', 0)->get();

    //     $newFilename = str_replace('modified_', '', $attachment[0]->aidrc_filename);
    //     $file =  storage_path() . "/app/public/file_attachments/" . $newFilename;
    //     return $this->attachSignature(storage_path("app/public/file_attachments/{$newFilename}"), $attachment[0]->esign_approver_details);
    // }

    public function get_application_attachment(Request $request){
        $attachment = Applications::where('id', $request->application_id)->where('logdel', 0)->get();
        $newFilename = str_replace('modified_', '', $attachment[0]->aidrc_filename);
        $file =  storage_path()."/app/public/file_attachments/".$newFilename;
        // $test_file = '/var/www/aidrc_v2_test/storage/app/public/file_attachments/1025-1490_ aidrc_attachment_20251020185806.pdf';
        // return $file;
        if (!file_exists($file)){
            // abort(404, 'File not found.');
            return response()->json(['result' => 0, 'message' => 'File not found.']);
        }

        return response()->json(['result' => 1, 'file_name' => $newFilename,'file_path' => asset('/storage/app/public/file_attachments/'.$newFilename)]);
        // return response()->json(['result' => 1, 'file_path' => $file]);
    }

    // public function download_attached_document123(Request $request){
    //     $attachment = Applications::with([
    //         'esign_approver_details',
    //         'esign_approver_details.user_details'
    //     ])
    //     ->where('id', $request->application_id)->where('logdel', 0)->get();

    //     $newFilename = str_replace('modified_', '', $attachment[0]->aidrc_filename);
    //     $file =  storage_path() . "/app/public/file_attachments/" . $newFilename;
    //     return $this->attachSignature(storage_path("app/public/file_attachments/{$newFilename}"), $attachment[0]->esign_approver_details);
    // }

    public function download_attached_doc_excel(Request $request){
        $attachment = Applications::where('id', $request->application_id)->where('logdel', 0)->get();
        $file =  storage_path() . "/app/public/file_attachments/" . $attachment[0]->aidrc_excel_filename;

        return Response::download($file, $attachment[0]->excel_filename, [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'        => 'no-cache',
            'Expires'       => '0',
        ]);
    }

    public function send_mailer(Request $request){
        $application = Applications::with(['esign_approver_details.user_details', 'originator_details', 'affected_documents_details' => function ($query2) {
            $query2->where('logdel', 0);
        }, 'affected_documents_details.approver_details', 'department_details', 'dcc_validation_details' => function ($query) {
            $query->where('logdel', 0)->orderBy('created_at', 'desc');
        }, 'dcc_validation_details.dcc_validator_details'])->where('id', $request->application_id)->where('logdel', 0)->get();

        $data = ['application' => $application];
        if (count($application) > 0) {
            switch ($application[0]->status){
                case 1: {
                        $send_to = [$application[0]->originator_details->email];
                        // $send_cc = [$application[0]->section_head_details->email];

                        Mail::send('mail.aidrc_dcc_validation', $data, function ($message) use ($send_to) {
                            $message->to($send_to)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRCV2: DCC Validation');
                        });
                        break;
                    }
                case 2: {
                        $send_to = [$application[0]->originator_details->email];
                        $send_cc = [$application[0]->dcc_validation_details[0]->dcc_validator_details->email];

                        Mail::send('mail.aidrc_minor_revisions', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)
                                ->cc($send_cc)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRCV2: Application for Minor Revisions');
                        });
                        break;
                    }
                case 3: {
                        $send_to = [$application[0]->originator_details->email];
                        $send_cc = [$application[0]->dcc_validation_details[0]->dcc_validator_details->email];

                        Mail::send('mail.aidrc_major_revisions', $data, function ($message) use ($send_to) {
                            $message->to($send_to)
                                ->to($send_cc)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRCV2: Application for Major Revisions');
                        });

                        break;
                    }
                case 4: {
                        // Find the minimum approval_order among pending approvers
                        $pendingApprovers = collect($application[0]->esign_approver_details)
                                            ->filter(function ($item){
                                                return $item->status == 0 && is_null($item->deleted_at);
                                            });

                        // get the approver with the smallest approval_order
                        $currentApprover = $pendingApprovers->sortBy('approval_order')->first();

                        $send_to = [$currentApprover->user_details->email]; //current approver only
                        $send_cc = [$application[0]->originator_details->email];

                        Mail::send('mail.aidrc_new_application', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)
                                ->cc($send_cc)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRCV2: Application for Approval');
                        });
                        break;
                    }
                case 5: {
                        $send_to = [$application[0]->originator_details->email];
                        // $send_cc = [$application[0]->section_head_details->email];

                        Mail::send('mail.aidrc_disapproved', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)
                                // ->cc($send_cc)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRCV2: Application Disapproved');
                        });
                        break;
                    }
                case 6: {
                        $send_to = [$application[0]->originator_details->email];
                        // $send_cc = [$application[0]->section_head_details->email];

                        Mail::send('mail.aidrc_cancelled', $data, function ($message) use ($send_to) {
                            $message->to($send_to)
                                // ->cc($send_cc)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRCV2: Application Cancelled');
                        });

                        break;
                    }
                case 7: {
                        $send_to = [$application[0]->originator_details->email];
                        // $send_cc = [$application[0]->section_head_details->email, $application[0]->dcc_validation_details[0]->dcc_validator_details->email];

                        Mail::send('mail.aidrc_validated', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)
                                /*->to($send_cc)*/
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRCV2: Application Validated!, For Control');
                        });
                        break;
                    }
                case 8: {
                        $send_to = [$application[0]->originator_details->email];
                        // $send_cc = [$application[0]->section_head_details->email, $application[0]->dcc_validation_details[0]->dcc_validator_details->email];

                        Mail::send('mail.aidrc_validated', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)
                                /*->to($send_cc)*/
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRCV2: Application Validated!, For Control');
                        });
                        break;
                    }
                case 9: {
                        $result = "DOCUMENT CONTROLLED";
                        break;
                    }
                default: {
                        $result = "---";

                        break;
                    }
            }

            return response()->json(['result' => 1]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function send_manual_mailer(Request $request){

        $application = Applications::with(['esign_approver_details.user_details', 'originator_details', 'section_head_details', 'affected_documents_details' => function ($query2) {

            $query2->where('logdel', 0);
        }, 'affected_documents_details.approver_details', 'department_details', 'dcc_validation_details' => function ($query) {

            $query->where('logdel', 0)->orderBy('created_at', 'desc');
        }, 'dcc_validation_details.dcc_validator_details'])->where('id', $request->application_id)->where('logdel', 0)->get();

        $data = ['application' => $application];
        if (count($application) > 0) {
            switch ($application[0]->status) {
                case 1: {
                        $send_to = [$application[0]->originator_details->email];
                        // $send_cc = [$application[0]->section_head_details->email];

                        Mail::send('mail.aidrc_dcc_validation', $data, function ($message) use ($send_to) {
                            $message->to($send_to)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRCV2: DCC Validation');
                        });
                        break;
                    }
                case 2: {
                        $send_to = [$application[0]->originator_details->email];
                        $send_cc = [$application[0]->dcc_validation_details[0]->dcc_validator_details->email];

                        Mail::send('mail.aidrc_minor_revisions', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)
                                ->cc($send_cc)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRCV2: Application for Minor Revisions');
                        });
                        break;
                    }
                case 3: {
                        $send_to = [$application[0]->originator_details->email];
                        $send_cc = [$application[0]->dcc_validation_details[0]->dcc_validator_details->email];

                        Mail::send('mail.aidrc_major_revisions', $data, function ($message) use ($send_to) {
                            $message->to($send_to)
                                ->to($send_cc)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRCV2: Application for Major Revisions');
                        });

                        break;
                    }
                case 4: {
                        // Find the minimum approval_order among pending approvers
                        $pendingApprovers = collect($application->esign_approver_details)
                                            ->filter(function ($item){
                                                return $item->status == 0 && is_null($item->deleted_at);
                                            });

                        // get the approver with the smallest approval_order
                        $currentApprover = $pendingApprovers->sortBy('approval_order')->first();

                        $send_to = [$currentApprover->user_details->email]; //current approver only
                        $send_cc = [$application[0]->originator_details->email];

                        Mail::send('mail.aidrc_new_application', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)
                                ->cc($send_cc)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRCV2: Application for Approval');
                        });
                        break;
                    }
                case 5: {
                        $send_to = [$application[0]->originator_details->email];
                        // $send_cc = [$application[0]->section_head_details->email];

                        Mail::send('mail.aidrc_disapproved', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)
                                // ->cc($send_cc)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRCV2: Application Disapproved');
                        });
                        break;
                    }
                case 6: {
                        $send_to = [$application[0]->originator_details->email];
                        // $send_cc = [$application[0]->section_head_details->email];

                        Mail::send('mail.aidrc_cancelled', $data, function ($message) use ($send_to) {
                            $message->to($send_to)
                                // ->cc($send_cc)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRCV2: Application Cancelled');
                        });

                        break;
                    }
                case 7: {
                        $send_to = [$application[0]->originator_details->email];
                        // $send_cc = [$application[0]->section_head_details->email, $application[0]->dcc_validation_details[0]->dcc_validator_details->email];

                        Mail::send('mail.aidrc_validated', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)
                                /*->to($send_cc)*/
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRCV2: Application Validated!, For Control');
                        });
                        break;
                    }
                case 8: {
                        $send_to = [$application[0]->originator_details->email];
                        // $send_cc = [$application[0]->section_head_details->email, $application[0]->dcc_validation_details[0]->dcc_validator_details->email];

                        Mail::send('mail.aidrc_validated', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)
                                /*->to($send_cc)*/
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRCV2: Application Validated!, For Control');
                        });
                        break;
                    }
                case 9: {
                        $result = "DOCUMENT CONTROLLED";
                        break;
                    }
                default: {
                        $result = "---";

                        break;
                    }
            }

            return response()->json(['result' => 1]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function cancel_application(Request $request)
    {
        session_start();
        $originator_id = $_SESSION['rapidx_user_id'];
        date_default_timezone_set('Asia/Manila');

        try {
            Applications::where('id', $request->application_id)->update([
                'status' => 10,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return response()->json(['result' => 1]);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }

    public function submit_application_affected_documents_from_approver(Request $request)
    {
        session_start();

        date_default_timezone_set('Asia/Manila');

        //originator/creator
        $approver_id = $_SESSION['rapidx_user_id'];

        $array_affected_documents = [];

        if (isset($request->array_affected_documents)) {
            $array_affected_documents = $request->array_affected_documents;
        }

        try {
            for ($i = 0; $i < count($array_affected_documents); $i++) {
                AffectedDocuments::insert([

                    'application_id' => $request->application_id,
                    'document_acdcs_pkid' => $array_affected_documents[$i]['document_pkid'],
                    'document_number' => $array_affected_documents[$i]['document_number'],
                    'document_name' => $array_affected_documents[$i]['document_title'],
                    'document_revision_number' => $array_affected_documents[$i]['document_revision_number'],
                    'document_revision_due_date' => $array_affected_documents[$i]['revision_due_date'],
                    'person_in_charge' => $array_affected_documents[$i]['approver_pic_id'],
                    'affected_document_approver' => $approver_id,
                    'approver_type' => $array_affected_documents[$i]['approver_affected_type'],
                    'approval_datetime' => date('Y-m-d H:i:s'),
                    'document_status' => 2,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'logdel' => 0

                ]);
            }

            return response()->json(['result' => 1]);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }

    public function submit_global_affected_document(Request $request)
    {
        session_start();

        date_default_timezone_set('Asia/Manila');

        //originator/creator
        $originator_id = $_SESSION['rapidx_user_id'];

        try {
            $document_id = AffectedDocuments::insertGetId([

                'application_id' => $request->global_affected_application_id,
                'document_acdcs_pkid' => $request->global_affected_doc_pkid,
                'document_number' => $request->global_affected_doc_no,
                'document_name' => $request->global_affected_doc_title,
                'document_revision_number' => $request->global_affected_doc_rev_no,
                'document_revision_due_date' => $request->global_affected_doc_revision_datetime,
                'person_in_charge' => $originator_id,
                'affected_document_approver' => $request->global_affected_approver_id,
                'approver_type' => 1,
                'document_status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'logdel' => 0

            ]);

            return response()->json(['result' => 1, 'document_id' => $document_id]);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }

    public function submit_global_affected_document_from_head(Request $request)
    {
        session_start();

        date_default_timezone_set('Asia/Manila');


        try {
            $document_id = AffectedDocuments::insertGetId([

                'application_id' => $request->global_approver_affected_application_id,
                'document_acdcs_pkid' => $request->global_approver_affected_doc_pkid,
                'document_number' => $request->global_approver_affected_doc_no,
                'document_name' => $request->global_approver_affected_doc_title,
                'document_revision_number' => $request->global_approver_affected_doc_rev_no,
                'document_revision_due_date' => $request->global_approver_affected_doc_revision_datetime,
                'person_in_charge' => $request->global_approver_affected_pic,
                'affected_document_approver' => $request->global_approver_affected_approver_id,
                'approver_type' => 1,
                'document_status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'logdel' => 0

            ]);

            return response()->json(['result' => 1, 'document_id' => $document_id]);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }

    public function submit_edit_application(Request $request){
        session_start();
        date_default_timezone_set('Asia/Manila');
        //originator/creator
        $originator_id = $_SESSION['rapidx_user_id'];
        $qs_inspector = null;
        if (isset($request->edit_qs_inspector)) {
            $qs_inspector = $request->edit_qs_inspector;
        }

        try {
            if (isset($request->edit_attachment)) {
                $generated_filename = "aidrc_attachment_" . date('YmdHis');
                $original_filename = $request->file('edit_attachment')->getClientOriginalName();
                $file_extension = $request->file('edit_attachment')->getClientOriginalExtension();
                $aidrc_filename = $generated_filename . "." . $file_extension;

                Storage::putFileAs('public/file_attachments', $request->edit_attachment, $aidrc_filename);

                Applications::where('id', $request->edit_hidden_application_id)->update([
                    'aidrc_filename' => $aidrc_filename,
                    'original_filename' => $original_filename,
                ]);
            }

            Applications::where('id', $request->edit_hidden_application_id)->update([
                'document_number' => $request->edit_doc_no,
                'document_name' => $request->edit_doc_title,
                'document_revision_number' => $request->edit_doc_rev_no,
                'document_category' => $request->edit_doc_category,
                'document_type' => $request->edit_doc_type,
                'for_group' => $request->edit_for_group,
                'department' => $request->edit_department,
                'application_originator' => $originator_id,
                'application_section_head' => $request->edit_application_approver,
                'application_qs_inspector' => $qs_inspector,
                'status' => $request->edit_hidden_status,
                'updated_at' => date('Y-m-d H:i:s'),
                'logdel' => 0

            ]);

            /* Storage::putFileAs('public/aidrc_attachments', $request->add_attachment, $aidrc_filename);*/

            return response()->json(['result' => 1, 'application_id' => $request->edit_hidden_application_id]);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }

    function attachSignature($attachment, $approvers){
        // return $approvers;
        $pdf = new Fpdi('P', 'mm', 'A4'); // mm unit

        $pageCount = $pdf->setSourceFile($attachment);
        // return $pageCount;
        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($templateId);
            if($size['width'] > $size['height']){
                $orientation 	= 'L';
                /* A4 size is width 210 x height 297 mm */
                /* A3 size is width 297 x height 420 mm */
                /* I've used 280 to validate if A3 just to be safe. However, do not exceed 297mm */
                /* Check on landscape only since PMI does not use A3 on Portrait */
                if($size['width'] > 297){
                    $page_size 	= 'A3';
                }
            }
            // Add a new page and use the imported PDF as template
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            foreach($approvers as $approver) {
                if($approver->page_no == $i) {

                    // Set coordinates where the signature will be placed (e.g. x=120, y=250)
                    $exploded_ordinates = explode('|', $approver->coordinates);
                    $x = (float) $exploded_ordinates[0]  * $size['width']; // Convert to mm;
                    $y = (float) $exploded_ordinates[1] * $size['height']; // Convert to mm;

                    // Optional: resize signaturesignaturePath
                    $signatureWidth = 30;
                    $signatureHeight = 20;

                    // Path to your signature image (JPG or PNG)
                    $imagePath = '../RapidX_E-Signature/'.$approver->user_details->employee_number.'.png';
                    // Insert the image
                    $pdf->Image($imagePath, $x-10, $y+5, $signatureWidth, $signatureHeight, 'PNG');

                    // Log::info("PDF Page Size (mm): Width = {$size['width']}, Height = {$size['height']}");
                    // Log::info("Placing signature at: X = $x mm, Y = $y mm");
                }
            }
        }
        $pdf->Output($attachment, 'I'); // Stream file
    }
}
