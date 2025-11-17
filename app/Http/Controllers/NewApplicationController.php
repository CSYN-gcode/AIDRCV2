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
use App\Model\HeadApprovals;
use App\Model\DccValidations;
use App\Model\RapidXUser;
use App\Model\ApplicationRevisions;
use App\Model\AffectedDocuments;
use App\Model\EsignApprover;
use App\Model\PatchDataPdf;
use App\Model\ExternalApplication;

// use TCPDF;
use setasign\Fpdi\Tcpdf\Fpdi;
use Illuminate\Support\Facades\Log;

use Mail;
// use Imagick;
use Carbon\Carbon;

class NewApplicationController extends Controller{

    // DATATABLES START
    public function load_aidrc_applications_table(Request $request){
        session_start();
        $originator_id = $_SESSION['rapidx_user_id'];
        $rapidx_user_dcc = RapidXUser::whereIn('department_id', [21, 22, 23, 1])->where('id', $originator_id)->get();
        $applications = [];
        if (count($rapidx_user_dcc) > 0){
            $applications = Applications::with(['control_details' => function ($query2) {
                $query2->where('logdel', 0);
            }, 'department_details', 'self_details', 'head_approval_details' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])->where('logdel', 0)->orderBy('created_at', 'desc')->get();
        }else{
            $applications = Applications::with(['control_details' => function ($query2) {
                $query2->where('logdel', 0);
            }, 'department_details', 'self_details', 'head_approval_details' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }, 'esign_approver_details' => function ($query3) use ($originator_id) {
                $query3->whereNull('deleted_at');
            }])
            ->where('logdel', 0)
            ->where(function ($query) use ($originator_id) {
                $query->where('application_originator', $originator_id)
                    ->orWhereHas('esign_approver_details', function ($q) use ($originator_id) {
                        $q->where('approver_id', $originator_id)
                            ->whereNull('deleted_at');
                    });
            })
            // ->where('application_originator', $originator_id)
            ->orderBy('created_at', 'desc')->get();
            // orWhere('application_section_head', $originator_id)->get();
        }

        if(isset($request->check_section_department)) {
            $applications = $applications->whereIn('department', $request->section_department);
        }

        if(isset($request->check_originator)) {
            $applications = $applications->whereIn('application_originator', $request->originator);
        }

        if(isset($request->check_category)){
            $applications = $applications->whereIn('document_category', $request->category);
        }

        if(isset($request->check_app_status)){
            $applications = $applications->whereIn('status', $request->app_status);
        }

        $applications_final = collect($applications)->flatten(1);
        $arrayApplications = [];

        // LOGIC TO FILTER APPLICATIONS BASED ON STATUS & CONNECTION TO THE APPROVERS
        if(isset($request->check_status)){
            foreach ($applications_final as $application){
                // return $application;
                // if ($application->status == 1){ //DCC Validation
                //     $arrayApplications[] = $application->id;
                // }else
                // return $application;
                if($application->status == 4) { //FOR APPROVAL
                    // FOR APPROVAL – check if originator is current approver
                    // if($application->esign_approver_details){ //check esign_approver

                        // Find the minimum approval_order among pending approvers
                        $pendingApprovers = collect($application->esign_approver_details)
                                            ->filter(function ($item){
                                                return $item->status == 0 && is_null($item->deleted_at);
                                            });

                        $minOrder = $pendingApprovers->min('approval_order');

                        $isCurrentApprover = $pendingApprovers->contains(function ($approver) use ($originator_id, $minOrder) {
                            return $approver->approver_id == $originator_id && $approver->approval_order == $minOrder;
                        });

                        if ($isCurrentApprover){
                          $arrayApplications[] = $application->id;
                        }
                    // }
                }
            }
        }else{
            for($i = 0; $i < count($applications_final); $i++){
                $arrayApplications[] = $applications_final[$i]->id;
            }
        }

        // applications
        // $applications3 = DB::connection('mysql')->table('applications')
        //                     ->leftJoin('db_rapidx.users AS originator_name', 'applications.application_originator', '=', 'originator_name.id')
        //                     ->leftJoin('esign_approvers', 'applications.id', '=', 'esign_approvers.application_id')
        //                     ->leftJoin('db_rapidx.users AS approver_name', 'esign_approvers.approver_id', '=', 'approver_name.id')
        //                     ->leftJoin('db_rapidx.departments', 'applications.department', '=', 'departments.department_id')
        //                     ->select('*')
        //                     ->where('applications.logdel', 0)
        //                     ->whereIn('applications.id', $arrayApplications)
        //                     ->orderBy('applications.created_at', 'desc')
        //                     ->get();
        // return $applications3;

        $applications3 = Applications::with(['control_details' => function ($query2) {
            $query2->where('logdel', 0);
        },
        'esign_approver_details.user_details',
        'external_app_details',
        'department_details',
        ])->where('logdel', 0)->whereIn('id', $arrayApplications)->orderBy('created_at', 'desc')->get();

        return DataTables::of($applications3)
            ->addColumn('control_number', function ($application) {
                $result = $application->aidrc_control_number;
                return $result;
            })
            ->addColumn('application_datetime', function ($application) {
                $result = $application->created_at;
                $formatted = date("M j, Y g:i A", strtotime($application->created_at));
                return $formatted;
            })
            ->addColumn('originator', function ($application) {
                // $result = '';
                // $result = $application->originator_name;
                $result = $application->originator_details->name;
                return $result;
            })
            ->addColumn('section_dept', function ($application) {
                $result = $application->department_details->department_name;
                return $result;
            })
            ->addColumn('doc_no', function ($application) {
                if ($application->document_number != null) {
                    $result = $application->document_number;
                } else {
                    $result = "---";
                }
                return $result;
            })
            ->addColumn('new_doc_no', function ($application) {
                if ($application->new_doc_number != null) {
                    $result = $application->new_doc_number;
                }else{
                    $result = "---";
                }
                return $result;
            })
            ->addColumn('doc_title', function ($application) {
                $result = $application->document_name;
                return $result;
            })
            ->addColumn('rev_no', function ($application) {
                $result = $application->document_revision_number;
                return $result;
            })
            ->addColumn('uploaded_file', function ($application) {
                $result = '<a href="download_attached_document_new/' . $application->id . '" title="Click to download file" target="_blank">' . $application->original_filename . '</a>';
                return $result;
            })
            ->addColumn('uploaded_excel_file', function ($application) {
                $result = '<a href="download_attached_doc_excel/' . $application->id . '" title="Click to download file" target="_blank">' . $application->excel_filename . '</a>';
                return $result;
            })
            // ->addColumn('original_file_external', function ($application) {
            //     if($application->application_type == 2 && $application->external_app_details){
            //         $result = '<a href="download_attached_document_new/'.$application->id.'/orig_pdf" target="_blank">' . $application->external_app_details->orig_original_filename . '</a>';
            //     }else{
            //         $result = '---';
            //     }
            //     return $result;
            // })
            // ->addColumn('uploaded_file_external', function ($application) {
            //     if($application->application_type == 2 && $application->external_app_details){
            //         $result = '<a href="download_external_application/'.$application->external_app_details->id.'/external_pdf" target="_blank">' . $application->external_app_details->external_original_filename . '</a>';
            //     }else{
            //         $result = '---';
            //     }
            //     return $result;
            // })
            ->addColumn('status', function ($application) {
                $result = "";
                switch ($application->status) {
                    case 0: {
                        $result = "<strong style='color: #6610f2;'>FOR SUBMISSION</strong>";
                        break;
                    }
                    case 1: { //DCC Template Validation
                        $result = "<strong style='color:rgb(87, 37, 134);'>FOR DCC VALIDATION</strong>";
                        break;
                    }
                    case 2: { //Minor Revision
                        $result = "<strong style='color: #20c997;'>FOR MINOR REVISIONS</strong>";
                        break;
                    }
                    case 3: { //Major Revision
                        $result .= '<strong style="color: #dc3545;">FOR MAJOR REVISIONS</strong>';
                        break;
                    }
                    case 4: { //Application Approved, For Approval of Sections Heads
                        $result = "<strong style='color: #445626;'>DCC VALIDATED, FOR APPROVAL</strong>";
                        break;
                    }
                    case 5: { //Approval NG
                        $result = "<strong style='color: #dc3545;'>DISAPPROVED</strong>";
                        break;
                    }
                    case 6: { //Cancelled
                        $result = "<strong style='color:rgb(233, 181, 10);'>CANCELLED</strong>";
                        break;
                    }
                    case 7: { //Completed - Editted PDF
                        // Default value
                        $result = "<strong style='color:rgb(87, 37, 134);'>FOR DCC (FOR UPDATE)</strong>";

                        if ($application->application_type == 2 && optional($application->external_app_details)->status == 0) {
                            $result = "<strong style='color:#6610f2;'>FOR REUPLOAD (With YEC Approval)</strong>";
                        }

                        // if($application->application_type == 2){
                        //     $result = "<strong style='color:#6610f2;'>FOR REUPLOAD (With YEC Approval)</strong>";
                        // }else{
                        //     $result = "<strong style='color:rgb(87, 37, 134);'>FOR DCC (FOR UPDATE)</strong>";
                        // }
                        break;
                    }
                    case 8: { //Completed - FOR UPLOAD TO ACDCS
                        $result = "<strong style='color:rgb(87, 37, 134);'>FOR DCC (FOR ACDCS UPLOAD)</strong>";
                        break;
                    }
                    case 9: { //Completed
                        $result = "<strong style='color: #28a745;'>DOCUMENT CONTROLLED</strong><br>";
                        $formatted = date("M j, Y g:i A", strtotime($application->updated_at));
                        $result .= $formatted;
                        break;
                    }
                    default: {
                            $result = "---";
                            break;
                        }
                }
                return $result;
            })
            ->addColumn('application_approvers', function ($application) {
                $qs_staff = $application->qs_inspector_details['name'] != NULL?$application->qs_inspector_details['name']:"NULL";
                $prod_head = $application->prod_head_details['name'] != NULL?$application->prod_head_details['name']:"NULL";
                $eng_head = $application->eng_head_details['name'] != NULL?$application->eng_head_details['name']:"NULL";
                $qc_head = $application->qc_head_details['name'] != NULL?$application->qc_head_details['name']:"NULL";
                $section_head = $application->section_head_details['name'] != NULL?$application->section_head_details['name']:"NULL";

                $result = "";
                switch ($application->status){
                    case 0: {
                             $result .= '<br><span class="badge badge-secondary">ORIGINATOR</span>';
                             break;
                    }
                    case 1: { //DCC Template Validation
                            $result .= '<br><span class="badge badge-secondary">DCC STAFF</span>';
                            break;
                        }
                    case 2: { //Minor Revision
                            $result .= '<br><span class="badge badge-secondary">ORIGINATOR: MINOR REVISION</span>';
                            break;
                        }
                    case 3: { //Major Revision
                            $result .= '<br><span class="badge badge-secondary">ORIGINATOR: MAJOR REVISION</span>';
                            break;
                        }
                    case 4: { //Application Approved, For Approval of Sections Heads
                            if($application->esign_approver_details && count($application->esign_approver_details) > 0){
                                foreach ($application->esign_approver_details AS $esign_approver){
                                    $message = '';
                                    if($esign_approver->status == 1){
                                        $badge = 'badge-success';
                                        $message .= 'Date: ';
                                        $formatted = date("M j, Y g:i A", strtotime($esign_approver->updated_at));
                                        $message .= $formatted;
                                    }elseif($esign_approver->status == 2){
                                        $badge = 'badge-danger';
                                    }else{
                                        // Find the minimum approval_order among pending approvers
                                        $pendingApprovers = collect($application->esign_approver_details)
                                                        ->filter(function ($item){
                                                            return $item->status == 0 && is_null($item->deleted_at);
                                                        });

                                        // get the approver with the smallest approval_order
                                        $currentApprover = $pendingApprovers->sortBy('approval_order')->first();
                                        if($esign_approver->approval_order == $currentApprover->approval_order){
                                            $badge = 'badge-primary';
                                        }else{
                                            $badge = 'badge-secondary';
                                        }
                                    }

                                    $result .= '<br><span class="badge '.$badge.'">APPROVER #'. $esign_approver->approval_order. ': '.$esign_approver->user_details->name.'<br>'.$message.'</span>';
                                }
                            }else{
                                    $result .= '<br><span class="badge badge-secondary">No Approvers</span>';
                            }
                            break;
                        }
                    case 5: { //Disapproved
                            if($application->esign_approver_details && count($application->esign_approver_details) > 0){
                                foreach ($application->esign_approver_details AS $esign_approver) {
                                    $message = '';
                                    if($esign_approver->status == 1){
                                        $badge = 'badge-success';

                                        $message .= 'Date: ';
                                        $formatted = date("M j, Y g:i A", strtotime($esign_approver->updated_at));
                                        $message .= $formatted;
                                    }elseif($esign_approver->status == 2){
                                        $badge = 'badge-danger';

                                        $message .= 'Date: ';
                                        $formatted = date("M j, Y g:i A", strtotime($esign_approver->updated_at));
                                        $message .= $formatted;
                                    }else{
                                        $badge = 'badge-secondary';
                                    }

                                    $result .= '<br><span class="badge '.$badge.'">APPROVER #'. $esign_approver->approval_order. ': '.$esign_approver->user_details->name.'<br>'.$message.'</span>';
                                }
                            }else{
                                    $result .= '<br><span class="badge badge-secondary">No Approvers</span>';
                            }
                            break;
                        }
                    case 6: { //Cancelled
                            if($application->esign_approver_details && count($application->esign_approver_details) > 0){
                                foreach ($application->esign_approver_details AS $esign_approver) {
                                    $message = '';
                                    $message .= 'Date: ';
                                    $formatted = date("M j, Y g:i A", strtotime($esign_approver->updated_at));
                                    $message .= $formatted;
                                     $result .= '<br><span class="badge badge-warning">APPROVER #'. $esign_approver->approval_order. ': '.$esign_approver->user_details->name.'<br>'.$message.'</span>';
                                }
                            }else{
                                    $result .= '<br><span class="badge badge-secondary">No Approvers</span>';
                            }
                            break;
                        }
                    case 7: { //Completed - FOR Edit PDF
                            if($application->esign_approver_details && count($application->esign_approver_details) > 0){
                                foreach ($application->esign_approver_details AS $esign_approver) {
                                    $message = '';
                                    $message .= 'Date: ';
                                    $formatted = date("M j, Y g:i A", strtotime($esign_approver->updated_at));
                                    $message .= $formatted;

                                    $result .= '<span class="badge badge-success">APPROVER #'. $esign_approver->approval_order. ': '.$esign_approver->user_details->name.'<br>'.$message.'</span>';
                                }

                                if($application->application_type == 2){
                                    $result .= '<br><span class="badge badge-secondary">ORIGINATOR REUPLOAD</span>';
                                }
                            }else{
                                $result .= '<br><span class="badge badge-secondary">No Approvers</span>';
                            }
                            break;
                        }
                    case 8: { //Completed - FOR UPLOAD TO ACDCS
                            if($application->esign_approver_details && count($application->esign_approver_details) > 0){
                                foreach ($application->esign_approver_details AS $esign_approver) {
                                    $message = '';
                                    $message .= 'Date: ';
                                    $formatted = date("M j, Y g:i A", strtotime($esign_approver->updated_at));
                                    $message .= $formatted;

                                    $result .= '<span class="badge badge-success">APPROVER #'. $esign_approver->approval_order. ': ' .$esign_approver->user_details->name.'<br>'.$message.'</span>';
                                }
                            }else{
                                    $result .= '<br><span class="badge badge-secondary">No Approvers</span>';
                            }
                            break;
                        }
                    case 9: { //Completed
                            $result .= '<br><span class="badge badge-secondary">DOCUMENT CONTROLLED</span>';
                            break;
                        }
                    default: {
                            $result = "---";
                            break;
                        }
                }
                return $result;
            })
            ->addColumn('action', function ($application) use ($originator_id) {

                $result = "";
                switch ($application->status) {
                    case 0: {
                            $result .= '<button type="button" class="btn btn-sm btn-block btn-success btnSubmitNewApplication" application-id='.$application->id.'><i class="fa fa-check-circle"></i> Submit Application</button>';
                            $result .= '<button type="button" class="btn btn-sm btn-block btn-danger btnCancelNewApplication" application-id='.$application->id.'><i class="fa fa-times-circle"></i> Cancel Application</button>';
                        break;
                    }
                    case 1: { //DCC Template Validation
                            $user_details = RapidXUser::where('id', $originator_id)->where('user_stat', 1)->get();
                            if (count($user_details) > 0) {
                                if ($user_details[0]->department_id == 21 || $user_details[0]->department_id == 22 || $user_details[0]->department_id == 23 || $user_details[0]->id == 8 || $user_details[0]->id == '461') {
                                    $result .= '<button type="button" class="btn btn-sm btn-block btn-warning btn-dcc-validation" data-toggle="modal" data-target="#modalDccValidations" title="For Initial DCC Confirmation" application-id='.$application->id.'><i class="fa fa-chevron-right"></i> DCC Validation Check</button>';
                                }
                            }
                            break;
                        }
                    case 2: { //MINOR REVISION
                            $result = "";
                            break;
                        }
                    case 3: { //MAJOR REVISION
                            $result = "";
                            break;
                        }
                    case 4: { //Application Validated, For Approvals
                            $result = "";
                                // $approvers = $application->esign_approver_details;
                                if($application->esign_approver_details->count() > 0){
                                    $approver_list = EsignApprover::where('application_id', $application->id)->where('status', 0)->whereNull('deleted_at')->orderBy('approval_order', 'asc')->first();
                                    if($approver_list){
                                        if($approver_list->approver_id == $originator_id || $originator_id == '461'){ //if application is not approved yet
                                            $result .= '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" approving-as="2" application-id='.$application->id.' approval_order='.$approver_list->approval_order.' ><i class="fa fa-check-circle"></i> Review Application</button>';
                                        }else{
                                            $result .= "";
                                        }
                                    }
                                }else{
                                    $result .= "";
                                }
                            break;
                        }
                    // case 5: { //QAS Head Approval
                    //         $result = "";
                    //         if ($application->application_section_head === $originator_id || $originator_id == '461'){
                    //             $result = '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id='.$application->id.' approving-as="4"><i class="fa fa-check-circle"></i> QAS Review Application </button>';
                    //         }
                    //         break;
                    //     }
                    case 5: { //Disapproved
                            $result = "";
                            break;
                        }
                    case 6: { //Cancelled
                            $result = "";
                            break;
                        }
                    case 7: { //DOCUMENT FOR CONTROL - EDIT PDF
                            $result = "";

                            $user_details = RapidXUser::where('id', $originator_id)->where('user_stat', 1)->get();
                            if(count($user_details) > 0){
                                if($user_details[0]->department_id == 21 || $user_details[0]->department_id == 22 || $user_details[0]->department_id == 23 || $user_details[0]->id == 8 || $user_details[0]->id == '461'){
                                    $editPdfButton = '<button type="button" class="btn btn-sm btn-block btn-warning btn-edit-pdf" data-toggle="modal" data-target="#modalTest" application-id='.$application->id.' ><i class="fa fa-chevron-right"></i> Edit PDF</button>';
                                }else{
                                    $editPdfButton = '';
                                }
                            }

                            // ExternalApplication
                            if($application->application_type == 2){
                                if($application->external_app_details == '' || $application->external_app_details->status == 0){
                                    $result .= '<button type="button" class="btn btn-sm btn-block btn-success btn-external-app" data-toggle="modal" data-target="#modalAddExternalApplication" application-id='.$application->id.' control_no='.$application->aidrc_control_number.' ><i class="fa fa-edit"></i>Reupload Attachment</button>';
                                }else{
                                    $result .= $editPdfButton;
                                }
                            }else if($application->application_type == 1){
                                $result .= $editPdfButton;
                            }

                            // if($application->application_type == 1 || $application->external_app_details->status == 1){
                            //     if(count($user_details) > 0){
                            //         if($user_details[0]->department_id == 21 || $user_details[0]->department_id == 22 || $user_details[0]->department_id == 23 || $user_details[0]->id == 8 || $user_details[0]->id == '461'){
                            //             $result .= '<button type="button" class="btn btn-sm btn-block btn-warning btn-edit-pdf" data-toggle="modal" data-target="#modalTest" application-id='.$application->id.' ><i class="fa fa-chevron-right"></i> Edit PDF</button>';
                            //         }
                            //     }
                            // }
                            break;
                        }
                    case 8: { //DOCUMENT FOR CONTROL - UPLOAD TO ACDCS
                            $result = "";

                            // $user_details = RapidXUser::where('id', $originator_id)->where('user_stat', 1)->get();
                            // if (count($user_details) > 0) {
                            //     if ($user_details[0]->department_id == 21 || $user_details[0]->department_id == 22 || $user_details[0]->department_id == 23 || $user_details[0]->id == 8 || $user_details[0]->id == '461') {
                            //         $result .= '<button type="button" class="btn btn-sm btn-block btn-warning btn-edit-pdf" data-toggle="modal" data-target="#modalTest" application-id='.$application->id.' ><i class="fa fa-chevron-right"></i> Edit PDF</button>';
                            //     }
                            // }

                            // $result .= '<button type="button" class="btn btn-sm btn-block btn-warning btn-edit-pdf" data-toggle="modal" data-target="#modalTest" application-id='.$application->id.' ><i class="fa fa-chevron-right"></i> Edit PDF</button>';
                            break;
                        }
                    case 9: { //DOCUMENT CONTROLLED
                            $result = "";
                            break;
                        }
                    default: {
                            $result = '---';
                            break;
                        }
                }
                $view_edit = 0;

                if($application->application_originator === $originator_id || $originator_id == '461') {
                    $view_edit = 1;
                }

                $result .= ' <button type="button" class="btn btn-sm btn-block btn-primary btn-view-application" data-toggle="modal" data-target="#modalViewApplication" view-edit=' . $view_edit . ' title="View/Edit Application" application-id=' . $application->id . '><i class="fa fa-edit"></i> View Application </button>';

                return $result;
            })
            ->addColumn('approver_status', function ($application){
                $result = "";
                if($application->esign_approver_details && count($application->esign_approver_details) > 0){
                    foreach ($application->esign_approver_details AS $esign_approver) {
                        if($esign_approver->status == 0 && $application->status == 4){ //Current Approver
                            $badge = 'badge-primary';
                            $result .= '<br><span class="badge '.$badge.'">Current Approver: <br>' . $esign_approver->user_details->name . '</span>';

                            break; // Stop the loop when found
                        }elseif($esign_approver->status == 2 && $application->status == 5){

                            $badge = 'badge-danger';
                            $result .= '<br><span class="badge '.$badge.'">Disapproved By: <br>' . $esign_approver->user_details->name . '</span>';
                            $result .= '<br><span class="badge badge-secondary">Remarks:</span><br>';
                            $result .= $esign_approver->remarks;
                            break; // Stop the loop when found
                        }else if($application->status > 6){
                            $result .= '<br><span class="badge badge-success">APPROVED</span>';
                            break; // Stop the loop when found
                        }
                    }
                }else{
                    $result .= '<br><span class="badge badge-secondary">---</span>';
                }

                return $result;
            })
            // ->rawColumns(['status', 'application_approvers', 'uploaded_file', 'doc_no', 'new_doc_no', 'uploaded_excel_file', 'original_file_external', 'uploaded_file_external', 'approver_status', 'action'])
            ->rawColumns(['status', 'application_approvers', 'uploaded_file', 'doc_no', 'new_doc_no', 'uploaded_excel_file', 'approver_status', 'action'])
            ->make(true);
    }

    public function load_acdcs_app_data(Request $request){
        session_start();
        $originator_id = $_SESSION['rapidx_user_id'];
        $rapidx_user_dcc = RapidXUser::whereIn('department_id', [21, 22, 23])->where('id', $originator_id)->get();
        $applications = [];
        if (count($rapidx_user_dcc) > 0) {
            $applications = Applications::with(['head_approval_details', 'qs_validation_details' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])->where('logdel', 0)->orderBy('created_at', 'desc')->get();
        } else {
            $applications = Applications::with(['head_approval_details', 'qs_validation_details' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])->where('application_originator', $originator_id)->orWhere('application_section_head', $originator_id)->orWhere('application_prod_head', $originator_id)->orWhere('application_qc_head', $originator_id)->orWhere('application_eng_head', $originator_id)->orWhere('qs_inspector', $originator_id)->get();
        }
        return $applications2 = $applications->where('logdel', 0);
    }

    public function load_acdcs_documents_table(Request $request){
        $documents = [];
        if (isset($request->document_search_type) && isset($request->document_wildcard)) {
            $documents = AcdcsDocs::where('logdel', 0);

            if ($request->document_search_type == 1) {
                $documents->where('doc_no', 'like', '%' . $request->document_wildcard . '%');
            } else {
                $documents->where('doc_title', 'like', '%' . $request->document_wildcard . '%');
            }

            $documents->get();
        }

        return DataTables::of($documents)
            ->addColumn('doc_no', function ($document) {
                $result = $document->doc_no;
                return $result;
            })
            ->addColumn('doc_title', function ($document) {
                $result = $document->doc_title;
                return $result;
            })
            ->addColumn('rev_no', function ($document) {
                $result = $document->rev_no;
                return $result;
            })
            ->addColumn('action', function ($document) use ($request) {
                $result = "";
                switch ($request->document_hidden_action) {
                    case 1: {
                            $result = 'New Document Details';
                            $result = '<button type="button" class="btn btn-sm btn-info btn-add-document-details" addition-type="1" active-doc-id="' . $document->pkid . '" title="Add Document Details"><i class="fa fa-download"></i></button>';
                            break;
                        }
                    case 2: {
                            $result = 'New Affected Document Details';
                            $result = '<button type="button" class="btn btn-sm btn-primary btn-add-affected-document-details" addition-type="2" active-doc-id="' . $document->pkid . '" data-toggle="modal" data-target="#modalAddaffectedDocumentDetails" title="Add Affected Document Details"><i class="fa fa-plus-square"></i></button>';
                            break;
                        }
                    case 3: {
                            $result = 'New Affected Document Details';
                            $result = '<button type="button" class="btn btn-sm btn-primary btn-add-affected-document-details" addition-type="3" active-doc-id="' . $document->pkid . '" data-toggle="modal" data-target="#modalAddaffectedDocumentDetails" title="Add Affected Document Details"><i class="fa fa-plus-square"></i></button>';
                            break;
                        }
                    case 4: {
                            $result = 'New Affected Document Details';
                            $result = '<button type="button" class="btn btn-sm btn-primary btn-add-affected-document-details" addition-type="4" active-doc-id="' . $document->pkid . '" data-toggle="modal" data-target="#modalAddaffectedDocumentDetails" title="Add Affected Document Details"><i class="fa fa-plus-square"></i></button>';
                            break;
                        }
                    case 5: {
                            $result = 'Change DCC Documents';
                            $result = '<button type="button" class="btn btn-sm btn-primary btn-dcc-edit-document" addition-type="5" active-doc-id="' . $document->pkid . '" title="Add Document Details"><i class="fa fa-download"></i></button>';
                            break;
                        }
                    default: {
                            $result = '---';
                            break;
                        }
                }
                return $result;
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function load_new_affected_documents_table(Request $request){
        $affected_documents = AffectedDocuments::with(['pic_details'])->where('application_id', $request->application_id)->where('document_status', 1)->where('logdel', 0)->get();
        return DataTables::of($affected_documents)
            ->addColumn('checkpoint_type', function ($document) {
                $result = '';
                switch ($document->approver_type) {
                    case 1: {
                            $result = "Affected Document";
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
            ->addColumn('doc_no', function ($document) {
                $result = $document->document_number;
                return $result;
            })
            ->addColumn('doc_title', function ($document) {
                $result = $document->document_name;
                return $result;
            })
            ->addColumn('rev_no', function ($document) {
                $result = $document->document_revision_number;
                return $result;
            })
            ->addColumn('person_in_charge', function ($document) {
                $result = $document->pic_details->name;
                return $result;
            })
            ->addColumn('revision_due_date', function ($document) {
                $result = $document->document_revision_due_date;
                return $result;
            })
            ->addColumn('originator_remarks', function ($document) {

                if ($document->document_remarks != null) {
                    $result = $document->document_remarks;
                } else {
                    $result = "---";
                }
                return $result;
            })
            ->addColumn('action', function ($document) {
                $result = "---";
                return $result;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function load_new_dcc_validations_table(Request $request){
        $validations = DccValidations::with(['dcc_validator_details'])->where('application_id', $request->application_id)->where('logdel', 0)/*->orderBy('created_at','desc')*/->get();

        return DataTables::of($validations)
            ->addColumn('validation_datetime', function ($validation) {
                $result = $validation->created_at;
                return $result;
            })
            ->addColumn('dcc_in_charge', function ($validation) {
                $result = $validation->dcc_validator_details->name;
                return $result;
            })
            ->addColumn('judgement', function ($validation) {
                switch ($validation->dcc_validation_judgement) {
                    case 1: {
                            $result = "PASSED";
                            break;
                        }
                    case 2: {
                            $result = "FOR MINOR REVISIONS";
                            break;
                        }
                    case 3: {
                            $result = "FOR MAJOR REVISIONS";
                            break;
                        }

                    default: {
                            $result = "---";
                            break;
                        }
                }
                return $result;
            })
            ->addColumn('checkpoint_similar', function ($validation) {
                switch ($validation->dcc_checkpoint_similar) {
                    case 1: {
                            $result = "COMPLIANT";
                            break;
                        }
                    case 2: {
                            $result = "NON-COMPLIANT";
                            break;
                        }
                    default: {
                            $result = "---";
                            break;
                        }
                }
                return $result;
            })
            ->addColumn('checkpoint_alignment', function ($validation) {
                switch ($validation->dcc_checkpoint_alignment) {
                    case 1: {
                            $result = "COMPLIANT";
                            break;
                        }
                    case 2: {
                            $result = "NON-COMPLIANT";
                            break;
                        }
                    default: {
                            $result = "---";
                            break;
                        }
                }
                return $result;
            })
            ->addColumn('checkpoint_standard', function ($validation) {
                switch ($validation->dcc_checkpoint_standard) {
                    case 1: {
                            $result = "COMPLIANT";
                            break;
                        }
                    case 2: {
                            $result = "NON-COMPLIANT";
                            break;
                        }

                    default: {
                            $result = "---";
                            break;
                        }
                }
                return $result;
            })
            ->addColumn('dcc_remarks', function ($validation) {

                if ($validation->dcc_validation_remarks != null) {
                    $result = $validation->dcc_validation_remarks;
                } else {
                    $result = "---";
                }
                return $result;
            })
            ->make(true);
    }

    public function load_new_head_approvals_table(Request $request){
        $approvals = HeadApprovals::with(['approver_details'])->where('application_id', $request->application_id)->where('logdel', 0)/*->orderBy('created_at','desc')*/->get();

        return DataTables::of($approvals)
            ->addColumn('approval_datetime', function ($approval) {

                $result = $approval->created_at;

                return $result;
            })
            ->addColumn('approver', function ($approval) {

                $result = $approval->approver_details->name;

                return $result;
            })
            ->addColumn('approving_as', function ($approval) {

                switch ($approval->approving_as) {
                    case 1: {
                            $result = "PROD HEAD";

                            break;
                        }
                    case 2: {
                            $result = "QC HEAD";

                            break;
                        }
                    case 3: {
                            $result = "ENG'G HEAD";

                            break;
                        }
                    case 4: {
                            $result = "SECTION HEAD";

                            break;
                        }

                    default: {
                            $result = "---";

                            break;
                        }
                }

                return $result;
            })
            ->addColumn('judgement', function ($approval) {

                switch ($approval->head_approval_status) {
                    case 1: {
                            $result = "APPROVED";

                            break;
                        }
                    case 2: {
                            $result = "DISAPPROVED";

                            break;
                        }
                    case 3: {
                            $result = "APPROVED BEFORE MAJOR REVISIONS";

                            break;
                        }

                    default: {
                            $result = "---";

                            break;
                        }
                }

                return $result;
            })
            ->addColumn('approval_remarks', function ($approval) {

                $result = $approval->head_approval_remarks;

                return $result;
            })
            ->make(true);
    }

    public function load_new_document_revisions_table(Request $request){
        $revisions = ApplicationRevisions::where('application_id', $request->application_id)->where('logdel', 0)/*->orderBy('created_at','desc')*/->get();
        return DataTables::of($revisions)
            ->addColumn('revision_datetime', function ($revision) {
                $result = $revision->created_at;
                return $result;
            })
            ->addColumn('current_status', function ($revision) {
                $result = $revision->current_status;
                return $result;
            })
            ->addColumn('attachment', function ($revision) {
                $result = $revision->original_filename;
                return $result;
            })
            ->addColumn('originator_remarks', function ($revision) {
                $result = $revision->originator_remarks;
                return $result;
            })
            ->addColumn('doc_no', function ($revision) {
                $result = $revision->document_number;
                return $result;
            })
            ->addColumn('doc_title', function ($revision) {
                $result = $revision->document_name;
                return $result;
            })
            ->addColumn('rev_no', function ($revision) {
                $result = $revision->document_revision_number;
                return $result;
            })
            ->make(true);
    }
    // DATATABLES END

    public function submit_new_application(Request $request){
        session_start();
        date_default_timezone_set('Asia/Manila');
        $validator = '';

        if ($request->add_doc_type == 1) { //NEW DOCUMENT
            $validator = Validator::make($request->all(), [
                'add_attachment' => 'required',
                'add_doc_category' => 'required',
                'add_doc_type' => 'required',
                'add_for_group' => 'required',
                'add_department' => 'required',
                'add_doc_title' => 'required',
                'add_attachment_excel' => 'required',
            ]);
        }else{ // REVISION
            $validator = Validator::make($request->all(), [
                'add_attachment' => 'required',
                'add_doc_category' => 'required',
                'add_doc_type' => 'required',
                'add_for_group' => 'required',
                'add_department' => 'required',
                'add_doc_title' => 'required',
                'add_doc_title' => 'required',
                'add_doc_rev_no' => 'required',
                'add_attachment_excel' => 'required',
            ]);
        }

        if($validator->passes()){
            $aidrc_control_number = '';
            $month = date('m');
            $year = date('y');
            $year2 = date('Y');
            $counter = 0;

            $applications = Applications::where('logdel', 0)->whereYear('created_at', $year2)->orderBy('id', 'desc')->first();

            if($applications != null){
                $control_number = $applications->aidrc_control_number;
                $number = explode('-', $control_number);
                $counter = intval($number[1]) + 1;
            }else{
                $counter = 1;
            }

            //AUTO GENERATED AIDRC CONTROL NUMBER
            $aidrc_control_number = $month . $year  . "-" . str_pad($counter, 3, "0", STR_PAD_LEFT);

            //ORIGINATOR/APPLICATION CREATOR
            $originator_id = $_SESSION['rapidx_user_id'];

            // ORIGINAL PDF FILENAME
            // $filename = $request->file('add_attachment')->getClientOriginalName();
            // $file_extension = $request->file('add_attachment')->getClientOriginalExtension();

            $uploadedPdfFile = $request->file('add_attachment');

            // Get the original filename parts
            $filename_pdf = pathinfo($uploadedPdfFile->getClientOriginalName(), PATHINFO_FILENAME);
            $file_extension_pdf = $uploadedPdfFile->getClientOriginalExtension();

            // 🔹 Remove special characters (keep only letters, numbers, spaces, dash, underscore)
            $cleanName = preg_replace('/[^A-Za-z0-9 _-]/', '', $filename_pdf);
            // 🔹 Replace spaces with underscores for safety
            $cleanName = str_replace(' ', '_', $cleanName);
            // 🔹 Add timestamp or unique ID if needed
            $cleanedFilenamePdf = $cleanName . '.' . $file_extension_pdf;

            // ORIGINAL EXCEL FILENAME
            if($request->hasFile('add_attachment_excel')){
                // $filename_excel = $request->file('add_attachment_excel')->getClientOriginalName();
                // $file_extension_excel = $request->file('add_attachment_excel')->getClientOriginalExtension();

                $uploadedRawFile = $request->file('add_attachment_excel');

                // Get the original filename parts
                $filename_excel = pathinfo($uploadedRawFile->getClientOriginalName(), PATHINFO_FILENAME);
                $file_extension_excel = $uploadedRawFile->getClientOriginalExtension();

                // 🔹 Remove special characters (keep only letters, numbers, spaces, dash, underscore)
                $cleanName = preg_replace('/[^A-Za-z0-9 _-]/', '', $filename_excel);
                // 🔹 Replace spaces with underscores for safety
                $cleanName = str_replace(' ', '_', $cleanName);
                // 🔹 Add timestamp or unique ID if needed
                $cleanedFilenameRaw = $cleanName . '.' . $file_extension_excel;

                //FILENAME EXCEL CLARK 02/07/2025
                $generated_filename_excel = "excel_aidrc_attachment_" . date('YmdHis');
                $aidrc_filename_excel = $generated_filename_excel . "." . $file_extension_excel;

                Storage::putFileAs('public/file_attachments', $request->add_attachment_excel, $aidrc_filename_excel);
            }else{
                $cleanedFilenameRaw = '';
                $aidrc_filename_excel = '';
            }

            //FILENAME
            $generated_filename = $aidrc_control_number. "_aidrc_attachment_" . date('YmdHis');
            $aidrc_filename = $generated_filename . "." . $file_extension_pdf;

            //INSERT INTO APPLICATION
            try {
                // old status is set to 1
                $signatureData = json_decode($request->signatureData, true);
                if($signatureData != null) {
                    $status = 0;
                }else{
                    $status = 0; //NO SIGNATURES
                }

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
                    'application_section_head' => $request->add_section_head_approver,

                    // 'application_prod_head' => $request->add_production_head,
                    // 'application_qc_head' => $request->add_qc_head,
                    // 'application_eng_head' => $request->add_eng_head,
                    // 'qs_inspector' => $request->add_qs_inspector,
                    // 'approver_priority' => $request->add_approver_priority,
                    'originator_remarks' => $request->add_remarks,
                    // 'aidrc_filename' => $aidrc_filename,
                    'aidrc_filename' => "modified_{$aidrc_filename}",
                    'original_filename' => $cleanedFilenamePdf,
                    'aidrc_excel_filename' => $aidrc_filename_excel,
                    'excel_filename' => $cleanedFilenameRaw,
                    'application_type' => $request->add_application_type ?: 1,
                    'status' => $status,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'logdel' => 0
                ]);

                if($signatureData != null){
                    // Retrieve Approver Data from JSON
                    $signatureData = json_decode($request->signatureData, true);
                    foreach ($signatureData as $approver) {
                        EsignApprover::insert([
                            'application_id' => $application_id,
                            'approver_id' => $approver['approver'],
                            'approval_order' => $approver['approval_order'],
                            'page_no' => $approver['page'],
                            'coordinates' => $approver['x'].'|'.$approver['y'],
                            // 'dimensions' => $approver['canvasWidth'].'|'.$approver['canvasHeight'].'|'.$approver['pdfWidth'].'|'.$approver['pdfHeight'].'|'.$approver['path']
                        ]);
                    }
                }

                // return $signatureData;

                // Load PDF and Attach E-Signatures
                // $modifiedFilePath = storage_path("app/public/file_attachments/modified_{$aidrc_filename}");
                // $this->attachSignaturesToPDF(storage_path("app/public/file_attachments/{$aidrc_filename}"), $modifiedFilePath, $signatureData);

                return response()->json(['result' => 1, 'application_id' => $application_id]);
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
                return response()->json(['result1' => $e]);
            }
        } else {
            return response()->json(['result' => 0, 'error' => $validator->messages()]);
        }
    }

    public function submit_external_application(Request $request){
        session_start();
        date_default_timezone_set('Asia/Manila');
        $originator_id = $_SESSION['rapidx_user_id'];
        $status = 0;
        if($request->submit_mode == 'Final'){
            $status = 1;
        }

        $orig_document_info = Applications::select('aidrc_filename','original_filename','aidrc_excel_filename','excel_filename')->where('id', $request->application_id_external_app)->first();
        // return $orig_document_info;

        //UPDATE APPLICATIONS USING THE FILE FROM EXTERNAL APPLICATION
        if($status == 1){
            $control_number = $request->application_control_number;

            // ORIGINAL PDF FILENAME
            $generated_filename = $control_number."_aidrc_attachment_" . date('YmdHis');
            // $generated_filename_excel = "excel_aidrc_attachment_" . date('YmdHis');

            if($request->hasFile('reupload_attachment')){
                $uploadedPdfFile = $request->file('reupload_attachment');

                // Get the original filename parts
                $filename_pdf = pathinfo($uploadedPdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $file_extension_pdf = $uploadedPdfFile->getClientOriginalExtension();

                // 🔹 Remove special characters (keep only letters, numbers, spaces, dash, underscore)
                $cleanName = preg_replace('/[^A-Za-z0-9 _-]/', '', $filename_pdf);
                $cleanName = str_replace(' ', '_', $cleanName);
                $cleanedFilenamePdf = $cleanName . '.' . $file_extension_pdf;
                $aidrc_filename = $generated_filename . "." . $file_extension_pdf;

                Storage::putFileAs('public/file_attachments', $request->reupload_attachment, $aidrc_filename);
            }else{
                $original_filename = $request->input('reupload_attachment_pdf_name');
                $file_extension = pathinfo($original_filename, PATHINFO_EXTENSION);
                $file_extension = strtolower($file_extension);
                $aidrc_filename = $generated_filename . "." . $file_extension;

                $external_app_file = ExternalApplication::where('id', $request->external_application_id)->first();

                if ($external_app_file && Storage::exists("public/file_attachments/{$external_app_file->external_aidrc_filename}")){
                    // Storage::delete("public/file_attachments/{$aidrc_filename}");
                    Storage::copy(
                                "public/file_attachments/{$external_app_file->external_aidrc_filename}",
                                "public/file_attachments/{$aidrc_filename}"
                            );
                }
            }

            // // ORIGINAL EXCEL FILENAME
            // if($request->hasFile('reupload_attachment_raw')){
            //     // $original_filename_excel = $request->file('reupload_attachment_raw')->getClientOriginalName();
            //     // $file_extension_excel = $request->file('reupload_attachment_raw')->getClientOriginalExtension();

            //     $uploadedRawFile = $request->file('reupload_attachment_raw');

            //     // Get the original filename parts
            //     $filename_raw = pathinfo($uploadedRawFile->getClientOriginalName(), PATHINFO_FILENAME);
            //     $file_extension_raw = $uploadedRawFile->getClientOriginalExtension();

            //     // 🔹 Remove special characters (keep only letters, numbers, spaces, dash, underscore)
            //     $cleanName = preg_replace('/[^A-Za-z0-9 _-]/', '', $filename_raw);
            //     // 🔹 Replace spaces with underscores for safety
            //     $cleanName = str_replace(' ', '_', $cleanName);
            //     // 🔹 Add timestamp or unique ID if needed
            //     $cleanedFilenameRaw = $cleanName . '.' . $file_extension_raw;

            //     $aidrc_filename_excel = $generated_filename_excel . "." . $file_extension_raw;

            //     Storage::putFileAs('public/file_attachments', $request->reupload_attachment_raw, $aidrc_filename_excel);
            // }else{
            //     $original_filename_excel = $request->input('reupload_attachment_raw_name');
            //     $file_extension_excel = pathinfo($original_filename_excel, PATHINFO_EXTENSION);
            //     $file_extension_excel = strtolower($file_extension_excel);
            //     $aidrc_filename_excel = $generated_filename_excel . "." . $file_extension_excel;

            //     $external_app_file = ExternalApplication::where('id', $request->external_application_id)->first();

            //     if ($external_app_file && Storage::exists("public/file_attachments/{$external_app_file->external_aidrc_excel_filename}")) {
            //         // Storage::delete("public/file_attachments/{$aidrc_filename_excel}");
            //         Storage::copy(
            //                 "public/file_attachments/{$external_app_file->external_aidrc_excel_filename}",
            //                 "public/file_attachments/{$aidrc_filename_excel}"
            //             );
            //     }
            // }

            $app_data_arr = [
                'aidrc_filename' => "modified_{$aidrc_filename}",
                'original_filename' => $cleanedFilenamePdf,
                // 'aidrc_excel_filename' => $aidrc_filename_excel,
                // 'excel_filename' => $cleanedFilenameRaw,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            Applications::where('id', $request->application_id_external_app)->update($app_data_arr);
        }

        if(!isset($request->external_application_id)){ //INSERT
            $validator = Validator::make($request->all(), [
                        'reupload_attachment' => 'required',
            ]);

            if($validator->passes()){
                    try{
                        // ORIGINAL PDF FILENAME
                        // $original_filename = $request->file('reupload_attachment')->getClientOriginalName();
                        // $file_extension = $request->file('reupload_attachment')->getClientOriginalExtension();
                        $uploadedPdfFile = $request->file('reupload_attachment');

                        // Get the original filename parts
                        $filename_pdf = pathinfo($uploadedPdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                        $file_extension_pdf = $uploadedPdfFile->getClientOriginalExtension();

                        // 🔹 Remove special characters (keep only letters, numbers, spaces, dash, underscore)
                        $cleanName = preg_replace('/[^A-Za-z0-9 _-]/', '', $filename_pdf);
                        // 🔹 Replace spaces with underscores for safety
                        $cleanName = str_replace(' ', '_', $cleanName);
                        // 🔹 Add timestamp or unique ID if needed
                        $cleanedFilenamePdf = $cleanName . '.' . $file_extension_pdf;

                        $generated_filename = "pdf_ex_aidrc_attachment_" . date('YmdHis');
                        $aidrc_filename = $generated_filename . "." . $file_extension_pdf;

                        // ORIGINAL EXCEL FILENAME
                        // $original_filename_excel = $request->file('reupload_attachment_raw')->getClientOriginalName();
                        // $file_extension_excel = $request->file('reupload_attachment_raw')->getClientOriginalExtension();

                        // $uploadedRawFile = $request->file('reupload_attachment_raw');

                        // // Get the original filename parts
                        // $filename_raw = pathinfo($uploadedRawFile->getClientOriginalName(), PATHINFO_FILENAME);
                        // $file_extension_raw = $uploadedRawFile->getClientOriginalExtension();

                        // // 🔹 Remove special characters (keep only letters, numbers, spaces, dash, underscore)
                        // $cleanName = preg_replace('/[^A-Za-z0-9 _-]/', '', $filename_raw);
                        // // 🔹 Replace spaces with underscores for safety
                        // $cleanName = str_replace(' ', '_', $cleanName);
                        // // 🔹 Add timestamp or unique ID if needed
                        // $cleanedFilenameRaw = $cleanName . '.' . $file_extension_raw;

                        // $generated_filename_excel = "raw_ex_aidrc_attachment_" . date('YmdHis');
                        // $aidrc_filename_excel = $generated_filename_excel . "." . $file_extension_raw;

                        // Storage::putFileAs('public/file_attachments', $request->reupload_attachment_raw, $aidrc_filename_excel);
                        Storage::putFileAs('public/file_attachments', $request->reupload_attachment, $aidrc_filename);

                        ExternalApplication::insert([
                            'status' => $status,
                            'application_id' => $request->application_id_external_app,

                            'orig_aidrc_filename' => $orig_document_info->aidrc_filename,
                            'orig_original_filename' => $orig_document_info->original_filename,
                            // 'orig_excel_filename' => $orig_document_info->excel_filename,
                            // 'orig_aidrc_excel_filename' => $orig_document_info->aidrc_excel_filename,

                            'external_aidrc_filename' => $aidrc_filename,
                            'external_original_filename' => $cleanedFilenamePdf,
                            // 'external_aidrc_excel_filename' => $aidrc_filename_excel,
                            // 'external_excel_filename' => $cleanedFilenameRaw,
                            'remarks' => $request->reupload_remarks,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                        return response()->json(['result' => 1]);
                    }catch (\Exception $e){
                        DB::rollback();
                        throw $e;
                        return response()->json(['result1' => $e]);
                    }
            }else{
                return response()->json(['result' => 0, 'error' => $validator->messages()]);
            }
        }else{ //UPDATE
            try{
                $data_arr = [
                    'status' => $status,
                    'application_id' => $request->application_id_external_app,

                    'orig_aidrc_filename' => $orig_document_info->aidrc_filename,
                    'orig_original_filename' => $orig_document_info->original_filename,
                    // 'orig_excel_filename' => $orig_document_info->excel_filename,
                    // 'orig_aidrc_excel_filename' => $orig_document_info->aidrc_excel_filename,

                    'remarks' => $request->reupload_remarks,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                if(isset($request->reupload_attachment)){
                    // ORIGINAL PDF FILENAME
                    // $filename = $request->file('reupload_attachment')->getClientOriginalName();
                    // $file_extension = $request->file('reupload_attachment')->getClientOriginalExtension();
                    $uploadedFile = $request->file('reupload_attachment');

                    // Get the original filename parts
                    $filename_pdf = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $file_extension_pdf = $uploadedFile->getClientOriginalExtension();

                    // 🔹 Remove special characters (keep only letters, numbers, spaces, dash, underscore)
                    $cleanName = preg_replace('/[^A-Za-z0-9 _-]/', '', $filename_pdf);
                    // 🔹 Replace spaces with underscores for safety
                    $cleanName = str_replace(' ', '_', $cleanName);
                    // 🔹 Add timestamp or unique ID if needed
                    $cleanedFilenamePdf = $cleanName . '.' . $file_extension_pdf;

                    $generated_filename = "pdf_ex_aidrc_attachment_" . date('YmdHis');
                    $aidrc_filename = $generated_filename . "." . $file_extension_pdf;

                    Storage::putFileAs('public/file_attachments', $request->reupload_attachment, $aidrc_filename);

                    $data_arr['external_aidrc_filename'] = $aidrc_filename;
                    $data_arr['external_original_filename'] = $cleanedFilenamePdf;
                }

                // if(isset($request->reupload_attachment_raw)){
                //     // ORIGINAL EXCEL FILENAME
                //     $uploadedFile = $request->file('reupload_attachment_raw');

                //     // Get the original filename parts
                //     $filename_excel = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                //     $file_extension_excel = $uploadedFile->getClientOriginalExtension();

                //     // 🔹 Remove special characters (keep only letters, numbers, spaces, dash, underscore)
                //     $cleanName = preg_replace('/[^A-Za-z0-9 _-]/', '', $filename_excel);
                //     // 🔹 Replace spaces with underscores for safety
                //     $cleanName = str_replace(' ', '_', $cleanName);
                //     // 🔹 Add timestamp or unique ID if needed
                //     $cleanedFilenameExcel = $cleanName . '.' . $file_extension_excel;

                //     // $filename_excel = $request->file('reupload_attachment_raw')->getClientOriginalName();
                //     // $file_extension_excel = $request->file('reupload_attachment_raw')->getClientOriginalExtension();

                //     $generated_filename_excel = "raw_ex_aidrc_attachment_" . date('YmdHis');
                //     $aidrc_filename_excel = $generated_filename_excel . "." . $file_extension_excel;

                //     Storage::putFileAs('public/file_attachments', $request->reupload_attachment_raw, $aidrc_filename_excel);

                //     $data_arr['external_aidrc_excel_filename'] = $aidrc_filename_excel;
                //     $data_arr['external_excel_filename'] = $cleanedFilenameExcel;
                // }

                ExternalApplication::where('id', $request->external_application_id)->update($data_arr);

                return response()->json(['result' => 1]);
            }catch (\Exception $e){
                DB::rollback();
                throw $e;
                return response()->json(['result1' => $e]);
            }
        }
    }

    public function load_application_details(Request $request){
        $application_details = Applications::with(['esign_approver_details.user_details','external_app_details'])->where('id', $request->application_id)->where('logdel', 0)->get();
        if (count($application_details) > 0) {
            return response()->json(['result' => 1, 'application_details' => $application_details]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    // ESIGNATURE APPROVAL
    public function submit_new_head_approval(Request $request){
        // return $request->head_application_id;
        // return response()->json(['result' => 1, 'application_id' => $request->head_application_id, 'approval_order' => $request->approval_order]);

        session_start();
        date_default_timezone_set('Asia/Manila');

        $validator = '';
        if ($request->head_approval_status == 2) {
            $validator = Validator::make($request->all(), [
                'head_application_id' => 'required',
                'head_approval_status' => 'required',
                'head_approval_remarks' => 'required',
                // 'head_approving_as' => 'required',
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'head_application_id' => 'required',
                'head_approval_status' => 'required',
                // 'head_approving_as' => 'required',
            ]);
        }

        if ($validator->passes()) {
            $application_details = Applications::with(['esign_approver_details'])->where('id', $request->head_application_id)->where('logdel', 0)->get();
            if (count($application_details) > 0) {
                DB::beginTransaction();
                try {
                    $esign_status = '';
                    $app_status = '';

                    if($request->head_approval_status == 1){ //  APPROVED
                        $esign_status = 1; //update the esign approver status
                        if($application_details[0]->status == 4){
                            $app_remaining_approval = $application_details[0]->esign_approver_details;
                            if($app_remaining_approval[count($app_remaining_approval) - 1]->approval_order == $request->approval_order){ //LAST APPROVER
                                $app_status = 7;
                            }
                        }
                    }else{ //DISAPPROVED
                        $esign_status = 2; //update the esign approver status
                        $app_status = 5; //update the application status
                    }

                    // $signatureData = EsignApprover::where('approval_order', $request->approval_order)->where('application_id', $request->head_application_id)->first();

                    // $modifiedFilePath = storage_path("app/public/file_attachments/modified_{$aidrc_filename}");
                    // $this->attachSignaturesToPDF(storage_path("app/public/file_attachments/{$aidrc_filename}"), $modifiedFilePath, $signatureData);

                    if($request->approval_order != null){
                        $attachment = Applications::with([
                            // 'esign_approver_details',
                            'esign_approver_details' => function ($query09) use ($request) {
                                $query09->where('approval_order', $request->approval_order);
                            }, 'esign_approver_details.user_details'
                        ])->where('id', $request->head_application_id)->where('logdel', 0)->first();

                        $newFilename = str_replace('modified_', '', $attachment->aidrc_filename);
                        // $file =  storage_path()."/app/public/file_attachments/".$newFilename;

                        $file =  storage_path("app/public/file_attachments/{$newFilename}");
                        $approver_details = $attachment->esign_approver_details[0];

                        // CLARK COMMENT 07/10/2025
                        // $this->attachSignature($file, $approver_details);

                        EsignApprover::where('approval_order', $request->approval_order)->where('application_id', $request->head_application_id)->update([
                            'status' => $esign_status,
                            'remarks' => $request->head_approval_remarks,
                            'updated_at' => NOW(),
                        ]);
                    }

                    if($app_status != ''){
                        Applications::where('id', $request->head_application_id)->update([
                            'status' => $app_status,
                            'updated_at' => NOW(),
                        ]);
                    }

                    DB::commit();
                    return response()->json(['result' => 1, 'application_id' => $request->head_application_id, 'approval_order' => $request->approval_order]);
                }catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                    return response()->json(['result' => $e]);
                }
            }else{
                return response()->json(['result' => 2]);
            }
        }else{
            return response()->json(['result' => 0, 'error' => $validator->messages()]);
        }
    }

    public function submit_new_dcc_validation(Request $request){
        session_start();
        $dcc_validator = $_SESSION['rapidx_user_id'];
        date_default_timezone_set('Asia/Manila');
        $validator = '';
        $validator = Validator::make($request->all(), [
            'dcc_checkpoint_similar' => 'required',
            'dcc_checkpoint_alignment' => 'required',
            'dcc_checkpoint_standard' => 'required',
            'dcc_validation_judgement' => 'required',
        ]);

        if ($validator->passes()) {
            try {
                DccValidations::insert([
                    'application_id' => $request->dcc_application_id,
                    'dcc_checkpoint_similar' => $request->dcc_checkpoint_similar,
                    'dcc_checkpoint_alignment' => $request->dcc_checkpoint_alignment,
                    'dcc_checkpoint_standard' => $request->dcc_checkpoint_standard,
                    'dcc_validation_judgement' => $request->dcc_validation_judgement,
                    'dcc_validation_remarks' => $request->dcc_validation_remarks,
                    'dcc_in_charge' => $dcc_validator,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'logdel' => 0,
                ]);

                $application = Applications::with([
                            'esign_approver_details',
                            'esign_approver_details.user_details'
                        ])->where('id', $request->dcc_application_id)->where('logdel', 0)->first();

                if($request->dcc_validation_judgement == 1 && $application->esign_approver_details != null && count($application->esign_approver_details) == 0){
                    $status = 7; //application approved, for edit pdf
                }else{
                    if($request->dcc_validation_judgement == 1){
                        $status = 4; //application validated
                    }else if ($request->dcc_validation_judgement == 2) {
                        $status = 2; //minor revision
                    }else if ($request->dcc_validation_judgement == 3) {
                        $status = 3; //major revision
                    }
                }

                Applications::where('id', $request->dcc_application_id)->update([
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                // if ($status == 8) {
                //     HeadApprovals::where('application_id', $request->dcc_application_id)->where('head_approval_status', 1)->update([
                //         'head_approval_status' => 3,
                //         'updated_at' => date('Y-m-d H:i:s'),
                //     ]);
                // }

                return response()->json(['result' => 1, 'application_id' => $request->dcc_application_id]);
            } catch (\Exception $e) {
                DB::rollback();
                // throw $e;
                return response()->json(['result' => $e]);
            }
        }else{
            return response()->json(['result' => 0, 'error' => $validator->messages()]);
        }
    }

    public function submit_new_edit_application(Request $request){
        session_start();
        date_default_timezone_set('Asia/Manila');
        $validator = '';
        $validator = Validator::make($request->all(), [
            'view_application_id' => 'required',
            'view_doc_category' => 'required',
            'view_doc_title' => 'required',
        ]);
        $application_details = Applications::where('id', $request->view_application_id)->where('logdel', 0)->get();

        if ($validator->passes()) {

            if (count($application_details) > 0) {
                try {
                    ApplicationRevisions::insert([
                        'application_id' => $application_details[0]->id,
                        'aidrc_attachment_filename' => $application_details[0]->aidrc_filename,
                        'current_status' => $application_details[0]->status,
                        'original_filename' => $application_details[0]->original_filename,
                        'document_category' => $application_details[0]->document_category,
                        'document_number' => $application_details[0]->document_number,
                        'document_name' => $application_details[0]->document_name,
                        'document_revision_number' => $application_details[0]->document_revision_number,
                        'originator_remarks' => $application_details[0]->originator_remarks,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'logdel' => 0
                    ]);

                    if (isset($request->edit_attachment)) {
                        $control_number = $application_details[0]->aidrc_control_number;
                        $generated_filename = $control_number."_aidrc_attachment_" . date('YmdHis');

                        // $filename = $request->file('edit_attachment')->getClientOriginalName();
                        // $file_extension = $request->file('edit_attachment')->getClientOriginalExtension();

                        $uploadedPdfFile = $request->file('edit_attachment');

                        // Get the original filename parts
                        $filename_pdf = pathinfo($uploadedPdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                        $file_extension_pdf = $uploadedPdfFile->getClientOriginalExtension();

                        // 🔹 Remove special characters (keep only letters, numbers, spaces, dash, underscore)
                        $cleanName = preg_replace('/[^A-Za-z0-9 _-]/', '', $filename_pdf);
                        // 🔹 Replace spaces with underscores for safety
                        $cleanName = str_replace(' ', '_', $cleanName);
                        // 🔹 Add timestamp or unique ID if needed
                        $cleanedFilenamePdf = $cleanName . '.' . $file_extension_pdf;

                        $aidrc_filename = $generated_filename . "." . $file_extension_pdf;

                        Storage::putFileAs('public/file_attachments', $request->edit_attachment, $aidrc_filename);

                        // ORIGINAL EXCEL FILENAME
                        if($request->hasFile('edit_attachment_excel')){
                            // $filename_excel = $request->file('edit_attachment_excel')->getClientOriginalName();
                            // $file_extension_excel = $request->file('edit_attachment_excel')->getClientOriginalExtension();

                            $uploadedRawFile = $request->file('edit_attachment_excel');

                            // Get the original filename parts
                            $filename_raw = pathinfo($uploadedRawFile->getClientOriginalName(), PATHINFO_FILENAME);
                            $file_extension_raw = $uploadedRawFile->getClientOriginalExtension();

                            // 🔹 Remove special characters (keep only letters, numbers, spaces, dash, underscore)
                            $cleanName = preg_replace('/[^A-Za-z0-9 _-]/', '', $filename_raw);
                            // 🔹 Replace spaces with underscores for safety
                            $cleanName = str_replace(' ', '_', $cleanName);
                            // 🔹 Add timestamp or unique ID if needed
                            $cleanedFilenameRaw = $cleanName . '.' . $file_extension_raw;

                            //FILENAME EXCEL CLARK 02/07/2025
                            $generated_filename_excel = "excel_aidrc_attachment_" . date('YmdHis');
                            $aidrc_filename_excel = $generated_filename_excel . "." . $file_extension_raw;

                            Storage::putFileAs('public/file_attachments', $request->edit_attachment_excel, $aidrc_filename_excel);
                        }else{
                            $cleanedFilenameRaw = '';
                            $aidrc_filename_excel = '';
                        }

                        Applications::where('id', $request->view_application_id)->update([
                            'document_category' => $request->view_doc_category,
                            'document_name' => $request->view_doc_title,
                            'originator_remarks' => $request->view_remarks,
                            // 'aidrc_filename' => $aidrc_filename,
                            'aidrc_filename' => "modified_{$aidrc_filename}",
                            'original_filename' => $cleanedFilenamePdf,
                            'aidrc_excel_filename' => $aidrc_filename_excel,
                            'excel_filename' => $cleanedFilenameRaw,
                            'application_type' => $request->view_application_type ?: 1,
                            'application_section_head' => $request->view_section_head_approver,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'status' => 1, //Back to Start Status
                        ]);
                    }else{
                        // ORIGINAL EXCEL FILENAME
                        if($request->hasFile('edit_attachment_excel')){
                            // $filename_excel = $request->file('edit_attachment_excel')->getClientOriginalName();
                            // $file_extension_excel = $request->file('edit_attachment_excel')->getClientOriginalExtension();

                            $uploadedRawFile = $request->file('edit_attachment_excel');

                            // Get the original filename parts
                            $filename_raw = pathinfo($uploadedRawFile->getClientOriginalName(), PATHINFO_FILENAME);
                            $file_extension_raw = $uploadedRawFile->getClientOriginalExtension();

                            // 🔹 Remove special characters (keep only letters, numbers, spaces, dash, underscore)
                            $cleanName = preg_replace('/[^A-Za-z0-9 _-]/', '', $filename_raw);
                            // 🔹 Replace spaces with underscores for safety
                            $cleanName = str_replace(' ', '_', $cleanName);
                            // 🔹 Add timestamp or unique ID if needed
                            $cleanedFilenameRaw = $cleanName . '.' . $file_extension_raw;

                            //FILENAME EXCEL CLARK 02/07/2025
                            $generated_filename_excel = "excel_aidrc_attachment_" . date('YmdHis');
                            $aidrc_filename_excel = $generated_filename_excel . "." . $file_extension_raw;

                            Storage::putFileAs('public/file_attachments', $request->edit_attachment_excel, $aidrc_filename_excel);
                        }else{
                            $cleanedFilenameRaw = '';
                            $aidrc_filename_excel = '';
                        }

                        Applications::where('id', $request->view_application_id)->update([

                            'document_category' => $request->view_doc_category,
                            'document_name' => $request->view_doc_title,
                            'originator_remarks' => $request->view_remarks,

                            'application_section_head' => $request->view_section_head_approver,
                            'aidrc_excel_filename' => $aidrc_filename_excel,
                            'excel_filename' => $cleanedFilenameRaw,
                            'application_type' => $request->view_application_type ?: 1,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'status' => 1, //Back to Start Status
                        ]);
                    }

                    $signatureData = json_decode($request->signatureData, true);
                    if($signatureData != null) {
                        $status = 1;
                    }else{
                        $status = 1; //NO SIGNATURES
                    }

                    if($signatureData != null){
                        // Retrieve Approver Data from JSON

                        //🔴 Delete existing data
                        EsignApprover::where('application_id', $request->view_application_id)->delete();

                        $signatureData = json_decode($request->signatureData, true);
                        foreach ($signatureData as $approver) {
                            EsignApprover::insert([
                                'application_id' => $request->view_application_id,
                                'approver_id' => $approver['approver'],
                                'approval_order' => $approver['approval_order'],
                                'page_no' => $approver['page'],
                                'coordinates' => $approver['x'].'|'.$approver['y'],
                                // 'dimensions' => $approver['canvasWidth'].'|'.$approver['canvasHeight'].'|'.$approver['pdfWidth'].'|'.$approver['pdfHeight'].'|'.$approver['path']
                            ]);
                        }
                    }

                    return response()->json(['result' => 1, 'application_id' => $request->view_application_id]);
                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                    return response()->json(['result' => $e]);
                }
            } else {
                return response()->json(['result' => 2]);
            }
        } else {
            return response()->json(['result' => 0, 'error' => $validator->messages()]);
        }
    }

    public function load_application_logs(Request $request){
        $application = Applications::with(['qs_validation_details', 'head_approval_details', 'dcc_validation_details', 'application_details'])->where('id', $request->application_id)->where('logdel', 0)->get();
        return $application;
    }

    public function submit_affected_document(Request $request){
        session_start();
        date_default_timezone_set('Asia/Manila');
        $validator = '';
        $validator = Validator::make($request->all(), [

            'affected_doc_no' => 'required',
            'affected_doc_title' => 'required',
            'affected_doc_rev_no' => 'required',
            'affected_checkpoint_type' => 'required',
            'affected_person_in_charge' => 'required',
            'affected_rev_due_date' => 'required',
        ]);

        if ($validator->passes()) {
            try {
                $affected_document_id = AffectedDocuments::insertGetId([

                    'document_number' => $request->affected_doc_no,
                    'document_name' => $request->affected_doc_title,
                    'document_revision_number' => $request->affected_doc_rev_no,
                    'document_revision_due_date' => $request->affected_rev_due_date,
                    'person_in_charge' => $request->affected_person_in_charge,
                    'document_remarks' => $request->affected_doc_remarks,
                    'approver_type' => $request->affected_checkpoint_type,
                    'document_status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'logdel' => 0
                ]);

                return response()->json(['result' => 1, 'affected_document_id' => $affected_document_id]);
            } catch (\Exception $e) {
                DB::rollback();
                // throw $e;
                return response()->json(['result' => $e]);
            }
        } else {
            return response()->json(['result' => 0, 'error' => $validator->messages()]);
        }
    }

    public function load_originator_affected_documents_table(Request $request){
        $affected_documents = [];

        if (isset($request->array_documents)) {
            $affected_documents = AffectedDocuments::/*with(['pic_details'])->*/whereIn('id', $request->array_documents)->where('approver_type', 1)->where('logdel', 0)->get();
        }

        return DataTables::of($affected_documents)
            ->addColumn('doc_no', function ($document) {
                $result = $document->document_number;
                return $result;
            })
            ->addColumn('doc_title', function ($document) {
                $result = $document->document_name;
                return $result;
            })
            ->addColumn('rev_no', function ($document) {
                $result = $document->document_revision_number;
                return $result;
            })
            ->addColumn('person_in_charge', function ($document) {
                $result = $document->pic_details->name;
                return $result;
            })
            ->addColumn('revision_due_date', function ($document) {
                $result = $document->document_revision_due_date;
                return $result;
            })
            ->addColumn('action', function ($document) {
                $result = '<button type="button" class="btn btn-sm btn-danger btn-remove-document" title="Remove Affected Document" document-id="' . $document->id . '"><i class="fa fa-times-circle"></i></button>';
                return $result;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function load_qs_validation_checkpoints_table(Request $request){
        $affected_documents = [];

        if (isset($request->array_documents)) {
            $affected_documents = AffectedDocuments::/*with(['pic_details'])->*/whereIn('id', $request->array_documents)->whereIn('approver_type', [2, 3, 4])->where('logdel', 0)->get();
        }

        return DataTables::of($affected_documents)
            ->addColumn('checkpoint_type', function ($document) {
                $result = '';
                switch ($document->approver_type) {
                    case 1: {
                            $result = "Affected Document";

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
            ->addColumn('doc_no', function ($document) {
                $result = $document->document_number;
                return $result;
            })
            ->addColumn('doc_title', function ($document) {
                $result = $document->document_name;
                return $result;
            })
            ->addColumn('rev_no', function ($document) {
                $result = $document->document_revision_number;
                return $result;
            })
            ->addColumn('person_in_charge', function ($document) {
                $result = $document->pic_details->name;
                return $result;
            })
            ->addColumn('revision_due_date', function ($document) {
                $result = $document->document_revision_due_date;
                return $result;
            })
            ->addColumn('action', function ($document) {
                $result = '<button type="button" class="btn btn-sm btn-danger btn-remove-document" title="Remove Affected Document" document-id="' . $document->id . '"><i class="fa fa-times-circle"></i></button>';
                return $result;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function retrieve_documents_for_approval(Request $request){
        $applications = Applications::where('id', $request->application_id)->where('logdel', 0)->get();
        $array_documents = [];
        $array_documents = AffectedDocuments::with(['application_details'])->where('application_id', $request->application_id)->where('logdel', 0);

        switch ($request->approving_as) {
            case 1: {
                    if ($applications[0]->approver_priority == 1) {
                        $array_documents->where('approver_type', 1);
                    }else{
                        $array_documents->where('approver_type', 999);
                    }
                    break;
                }

            case 2: {
                    if ($applications[0]->approver_priority == 2) {
                        $array_documents->whereIn('approver_type', [1, 3]);
                    }else{
                        $array_documents->where('approver_type', 3);
                    }
                    break;
                }

            case 3: {
                    if ($applications[0]->approver_priority == 3) {
                        $array_documents->whereIn('approver_type', [1, 2, 4]);
                    }else{
                        $array_documents->whereIn('approver_type', [2, 4]);
                    }

                    break;
                }

            case 4: {
                    $array_documents->where('approver_type', 1);
                    break;
                }

            default: {
                    $array_documents->where('approver_type', 999);
                    break;
                }
        }

        $array_documents = $array_documents->pluck('id')->toArray();

        if (count($array_documents) > 0) {
            return response()->json(['result' => 1, 'array_documents' => $array_documents]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function retrive_documents_for_qs_inspection(Request $request){
        $array_documents = [];

        $array_documents = AffectedDocuments::with(['application_details'])->where('application_id', $request->application_id)->whereIn('approver_type', [2, 3, 4])->where('document_status', 1)->where('logdel', 0);

        $array_documents = $array_documents->pluck('id')->toArray();

        if (count($array_documents) > 0) {
            return response()->json(['result' => 1, 'array_documents' => $array_documents]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function load_approver_checkpoints_table(Request $request){
        $affected_documents = [];

        if (isset($request->array_documents)) {
            $affected_documents = AffectedDocuments::/*with(['pic_details'])->*/whereIn('id', $request->array_documents)->where('logdel', 0)->get();
        }

        return DataTables::of($affected_documents)
            ->addColumn('checkpoint_type', function ($document) {

                $result = '';

                switch ($document->approver_type) {
                    case 1: {
                            $result = "Affected Document";

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
            ->addColumn('doc_no', function ($document) {

                $result = $document->document_number;
                return $result;
            })
            ->addColumn('doc_title', function ($document) {

                $result = $document->document_name;
                return $result;
            })
            ->addColumn('rev_no', function ($document) {

                $result = $document->document_revision_number;
                return $result;
            })
            ->addColumn('person_in_charge', function ($document) {

                $result = $document->pic_details->name;
                return $result;
            })
            ->addColumn('revision_due_date', function ($document) {

                $result = $document->document_revision_due_date;
                return $result;
            })
            ->addColumn('action', function ($document) {

                if($document->document_status == 1) {
                    $result = '<button type="button" class="btn btn-sm btn-primary btn-edit-affected-document-details" affected-doc-id="' . $document->id . '" data-toggle="modal" data-target="#modalEditaffectedDocumentDetails" title="Edit Affected Document Details"><i class="fa fa-edit"></i></button>';
                }else{
                    $result = '<strong>DISAPPROVED</strong>';
                }

                return $result;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function link_checkpoints_to_application(Request $request){
        date_default_timezone_set('Asia/Manila');

        try {
            if (count($request->array_documents) > 0) {
                AffectedDocuments::whereIn('id', $request->array_documents)->update([
                    'application_id' => $request->application_id
                ]);
            }

            return response()->json(['result' => 1]);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }

    public function load_affected_documents_details(Request $request){
        $affected_document_details = AffectedDocuments::where('id', $request->affected_doc_id)->where('logdel', 0)->get();

        if (count($affected_document_details) > 0) {
            return response()->json(['result' => 1, 'affected_document_details' => $affected_document_details]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function submit_disapprove_affected_document(Request $request){
        date_default_timezone_set('Asia/Manila');

        session_start();

        $approver_id = $_SESSION['rapidx_user_id'];

        try {
            AffectedDocuments::where('id', $request->affected_doc_id)->update([

                'document_status' => 2,
                'approver_id' => $approver_id,
                'approver_remarks' => $request->approver_remarks,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return response()->json(['result' => 1]);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }

    public function submit_edit_affected_document(Request $request){
        session_start();

        $approver_id = $_SESSION['rapidx_user_id'];

        date_default_timezone_set('Asia/Manila');

        try {
            AffectedDocuments::where('id', $request->edit_affected_doc_id)->update([

                'document_number' => $request->edit_affected_doc_no,
                'document_name' => $request->edit_affected_doc_title,
                'document_revision_number' => $request->edit_affected_doc_rev_no,
                'document_revision_due_date' => $request->edit_affected_rev_due_date,
                'person_in_charge' => $request->edit_affected_person_in_charge,
                'document_remarks' => $request->edit_affected_doc_remarks,
                'approver_type' => $request->edit_affected_checkpoint_type,
                'approver_id' => $approver_id,
                'approver_remarks' => $request->edit_affected_approver_remarks,
                'document_status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'logdel' => 0
            ]);

            return response()->json(['result' => 1]);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }
    public function new_cancel_application(Request $request){
        date_default_timezone_set('Asia/Manila');
        try {
            Applications::where('id', $request->application_id)->update([
                'status' => 6,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            return response()->json(['result' => 1]);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }

    public function remove_document(Request $request){
        date_default_timezone_set('Asia/Manila');

        try {
            AffectedDocuments::where('id', $request->document_id)->update([
                'logdel' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            return response()->json(['result' => 1]);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }

    public function changeApplicationStatus(Request $request){
        date_default_timezone_set('Asia/Manila');
        try {
            Applications::where('id', $request->application_id)->update([
                            'status' => $request->status,
                            'updated_at' => NOW(),
                        ]);
            return response()->json(['result' => 1]);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }

    public function send_new_mailer(Request $request){
        $application = Applications::with(['esign_approver_details.user_details', 'originator_details', 'department_details', 'dcc_validation_details' => function ($query3){
            $query3->where('logdel', 0)->orderBy('created_at', 'desc');
        }, 'dcc_validation_details.dcc_validator_details', 'head_approval_details' => function ($query){
            $query->where('logdel', 0)->orderBy('created_at', 'desc');
        }, 'application_revision_details'])->where('id', $request->application_id)->where('logdel', 0)->get();

        if(isset($request->approval_order) && isset($request->application_id)){
            $approver_details = EsignApprover::with(['user_details'])->where('approval_order', $request->approval_order)->where('application_id', $request->application_id)->get();
        }else{
            $approver_details = [];
        }
        // return $test;
        // approval_order
        // return $approver_details;
        $data = ['application' => $application, 'approver_details' => $approver_details];

        // $application[0]->status = 5;

        if(count($application) > 0){
            switch ($application[0]->status) {
                case 1: {
                        $send_to = ['dmmarmol@pricon.ph', 'stomela@pricon.ph', 'nvquidlat@pricon.ph'];
                        $send_cc = [$application[0]->originator_details->email];

                        Mail::send('mail.aidrc_dcc_validation', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)->cc($send_cc)->subject('AIDRCV2: DCC Validation');
                        });
                        break;
                    }
                case 2: {
                        $send_to = [$application[0]->originator_details->email];
                        $send_cc = [$application[0]->dcc_validation_details[0]->dcc_validator_details->email];

                        Mail::send('mail.aidrc_minor_revisions', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)->cc($send_cc)->subject('AIDRCV2: Application for Minor Revisions');
                        });
                        break;
                    }
                case 3: {
                        $send_to = [$application[0]->originator_details->email];
                        $send_cc = [$application[0]->dcc_validation_details[0]->dcc_validator_details->email];

                        Mail::send('mail.aidrc_major_revisions', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)->cc($send_cc)->subject('AIDRCV2: Application for Major Revisions');
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
                            $message->to($send_to)->cc($send_cc)->subject('AIDRCV2: Application for Approval');
                        });
                        break;
                    }
                case 5: {
                        $send_to = [$application[0]->originator_details->email];
                        $send_cc = ['nvquidlat@pricon.ph'];

                        Mail::send('mail.aidrc_disapproved', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)->cc($send_cc)->subject('AIDRCV2: Application Disapproved');
                        });
                        break;
                    }
                case 6: {
                        $send_to = [$application[0]->originator_details->email];
                        $send_cc = ['nvquidlat@pricon.ph'];

                        Mail::send('mail.aidrc_cancelled', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)->cc($send_cc)->subject('AIDRCV2: Application Cancelled');
                        });
                        break;
                    }
                case 7: {
                        $send_to = [$application[0]->originator_details->email];
                        $send_cc = ['nvquidlat@pricon.ph'];

                        Mail::send('mail.aidrc_validated', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)->cc($send_cc)->subject('AIDRCV2: Application Validated!, For Control');
                        });
                        break;
                    }
                case 8: {
                        $send_to = [$application[0]->originator_details->email];
                        $send_cc = ['nvquidlat@pricon.ph'];

                        Mail::send('mail.aidrc_validated', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)->cc($send_cc)->subject('AIDRCV2: Application Validated!, For Control');
                        });
                        break;
                    }
                case 9: {
                        $send_to = [$application[0]->originator_details->email];
                        $send_cc = [$application[0]->dcc_validation_details[0]->dcc_validator_details->email];

                        Mail::send('mail.aidrc_validated', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)->cc($send_cc)->subject('AIDRCV2: DOCUMENT CONTROLLED!');
                        });
                        break;
                    }
                default: {
                        $result = "---";
                        break;
                    }
            }
            return response()->json(['result' => 1]);
        }else{
            return response()->json(['result' => 2]);
        }
    }


    public function load_for_control_status_email(Request $request){
        $applications =  Applications::with(['control_details' => function ($query2){
            $query2->where('logdel', 0);
        }])->where('logdel', 0)->where('status', 9)->orderBy('created_at', 'desc')->get();

        $applications2 = [];

        for ($i = 0; $i < count($applications); $i++) {
            $search_revision_no = $applications[$i]->document_revision_number;

            if ($applications[$i]->control_details != null) {
                if ($applications[$i]->control_details->rev_no != $search_revision_no) {
                    $applications2[] = $applications[$i]->id;
                }
            }
        }

        $applications_final = Applications::with(['control_details' => function ($query2) {

            $query2->where('logdel', 0);
        }])->whereIn('id', $applications2)->where('logdel', 0)->get();

        $applications_collect = collect($applications_final)->where('created_at', '<=', Carbon::now()->subDays(7)->toDateTimeString())->flatten(1);

        // return $applications_collect;

        if (count($applications_collect) > 0) {
            $data = ['applications' => $applications_collect];

            $send_to = [];
            $send_cc = ['dmmarmol@pricon.ph', 'stomela@pricon.ph'];

            for ($x = 0; $x < count($applications_collect); $x++) {
                if (!in_array($applications_collect[$x]->originator_details->email, $send_to)) {
                    $send_to[] = $applications_collect[$x]->originator_details->email;
                }

                if ($applications_collect[$x]->for_group == 1) {
                    switch ($applications_collect[$x]->approver_priority) {
                        case 1: {
                                if (!in_array($applications_collect[$x]->prod_head_details->email, $send_cc)) {
                                    $send_cc[] = $applications_collect[$x]->prod_head_details->email;
                                }

                                break;
                            }
                        case 2: {
                                if (!in_array($applications_collect[$x]->qc_head_details->email, $send_cc)) {
                                    $send_cc[] = $applications_collect[$x]->qc_head_details->email;
                                }
                                break;
                            }
                        case 3: {
                                if (!in_array($applications_collect[$x]->eng_head_details->email, $send_cc)) {
                                    $send_cc[] = $applications_collect[$x]->eng_head_details->email;
                                }
                                break;
                            }
                        default: {
                                break;
                            }
                    }
                } else {
                    if (!in_array($applications_collect[$x]->section_head_details->email, $send_cc)) {
                        $send_cc[] = $applications_collect[$x]->section_head_details->email;
                    }
                }
            }

            Mail::send('mail.aidrc_for_control_applications', $data, function ($message) use ($send_to, $send_cc) {
                $message->to($send_to)
                    ->cc($send_cc)
                    ->bcc('mclegaspi@pricon.ph')
                    ->subject('AIDRCV2: Applications for Control');
            });

            return response()->json(['result' => 1]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function load_for_control_affected_documents_email_three_days(Request $request){
        $date_three_days_after = Carbon::now()->addDays(14)->toDateString();
        $documents = AffectedDocuments::with(['control_details' => function ($query2) {
            $query2->where('logdel', 0);
        }])->where('logdel', 0)->where('document_status', 1)->get();
        $documents2 = [];

        for ($i = 0; $i < count($documents); $i++) {
            $search_revision_no = $documents[$i]->document_revision_number;
            if ($documents[$i]->control_details != null) {
                if ($documents[$i]->control_details->rev_no != $search_revision_no) {
                    $documents2[] = $documents[$i]->id;
                }
            }
        }

        $documents_final = AffectedDocuments::with(['application_details', 'control_details' => function ($query2) {
            $query2->where('logdel', 0);
        }])->whereIn('id', $documents2)->where('logdel', 0)->get();

        $documents_collect = collect($documents_final)->where('document_revision_due_date', $date_three_days_after)->flatten(1);

        if (count($documents_collect) > 0) {
            $data = ['documents' => $documents_collect];

            $send_to = [];
            $send_cc = ['dmmarmol@pricon.ph', 'stomela@pricon.ph'];

            for ($x = 0; $x < count($documents_collect); $x++) {
                if (!in_array($documents_collect[$x]->pic_details->email, $send_to)) {
                    $send_to[] = $documents_collect[$x]->pic_details->email;
                }
            }
            Mail::send('mail.aidrc_affected_documents_three_days', $data, function ($message) use ($send_to, $send_cc) {
                $message->to($send_to)
                    ->cc($send_cc)
                    ->subject('AIDRCV2: Affected Documents for Control (Due in Three Days)');
            });

            return response()->json(['result' => 1]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function load_for_control_affected_documents_email_overdue(Request $request){
        $date_now = Carbon::now()->toDateString();
        $documents = AffectedDocuments::with(['application_details', 'control_details' => function ($query2) {

            $query2->where('logdel', 0);
        }])->where('logdel', 0)->where('document_status', 1)->get();
        $documents2 = [];

        for ($i = 0; $i < count($documents); $i++) {
            $search_revision_no = $documents[$i]->document_revision_number;

            if ($documents[$i]->control_details != null) {
                if ($documents[$i]->application_details != null) {
                    if ($documents[$i]->application_details->status == 9) {
                        if ($documents[$i]->control_details->rev_no != $search_revision_no) {
                            if ($documents[$i]->application_id != null) {
                                $documents2[] = $documents[$i]->id;
                            }
                        }
                    }
                }
            }
        }

        $documents_final = AffectedDocuments::with(['application_details', 'control_details' => function ($query2) {

            $query2->where('logdel', 0);
        }])->whereIn('id', $documents2)->where('logdel', 0)->get();

        $documents_collect = collect($documents_final)->where('document_revision_due_date', '<', $date_now)->flatten(1);

        if (count($documents_collect) > 0) {
            $data = ['documents' => $documents_collect];
            $send_to = [];
            $send_cc = ['dmmarmol@pricon.ph', 'stomela@pricon.ph'];

            for ($x = 0; $x < count($documents_collect); $x++) {
                if (!in_array($documents_collect[$x]->pic_details->email, $send_to)) {
                    $send_to[] = $documents_collect[$x]->pic_details->email;
                }
            }

            Mail::send('mail.aidrc_affected_documents_overdue', $data, function ($message) use ($send_to, $send_cc) {

                $message->to($send_to)
                    ->cc($send_cc)
                    ->subject('AIDRCV2: Affected Documents for Control (OVERDUE!)');
            });

            return response()->json(['result' => 1]);
        } else {
            return response()->json(['result' => 2]);
        }

        return 1;
    }

    public function get_external_application_data(Request $request){
        date_default_timezone_set('Asia/Manila');
        $external_app_data = ExternalApplication::where('application_id', $request->application_id)->whereNull('deleted_at')->first();

        return response()->json(['result' => 1, 'external_app_data' => $external_app_data]);
    }

    public function download_external_application(Request $request, $id, $category){
        $external_app_for_dl = ExternalApplication::where('id', $id)->whereNull('deleted_at')->first();
        switch ($category) {
            // case 'orig_pdf':
            //     $filename = str_replace('modified_', '', $external_app_for_dl->orig_aidrc_filename);
            //     $filePath = storage_path("app/public/file_attachments/{$filename}");
            //     $newFileName = $external_app_for_dl->orig_original_filename;
            //     break;
            case 'orig_raw':
                $filePath = storage_path("app/public/file_attachments/{$external_app_for_dl->orig_aidrc_excel_filename}");
                $newFileName = $external_app_for_dl->orig_excel_filename;
                break;
            case 'external_pdf':
                $filePath = storage_path("app/public/file_attachments/{$external_app_for_dl->external_aidrc_filename}");
                $newFileName = $external_app_for_dl->external_original_filename;
                break;
            case 'external_raw':
                $filePath = storage_path("app/public/file_attachments/{$external_app_for_dl->external_aidrc_excel_filename}");
                $newFileName = $external_app_for_dl->external_excel_filename;
                break;
            default:
                return response()->json(['result' => 0, 'message' => 'Invalid category.']);
        }

        $mimeType = mime_content_type($filePath);

        // 🔹 PDFs: show inline
        if (str_contains($mimeType, 'pdf')) {
            return response()->file($filePath, [
                'Content-Type'        => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $newFileName . '"',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'        => 'no-cache',
                'Expires'       => '0',
            ]);
        }

        // 🔹 Non-PDFs (Excel, etc): force download with custom name
        return response()->download($filePath, $newFileName, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'        => 'no-cache',
            'Expires'       => '0',
        ]);
    }
}
