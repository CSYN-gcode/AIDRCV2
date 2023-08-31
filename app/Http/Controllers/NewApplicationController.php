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
use App\Model\QSValidations;
use App\Model\HeadApprovals;
use App\Model\DccValidations;
use App\Model\RapidXUser;
use App\Model\ApplicationRevisions;
use App\Model\AffectedDocuments;

use App\Model\SectionHeads;

use Mail;

use Carbon\Carbon;

class NewApplicationController extends Controller
{
    public function load_for_control_status_email(Request $request)
    {
        $applications =  Applications::with(['control_details' => function ($query2) {

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
            $send_cc = ['nvlim@pricon.ph', 'dmmarmol@pricon.ph', 'mdalcaraz@pricon.ph'];

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
                    ->bcc('cdcasuyon@pricon.ph')
                    ->bcc('mclegaspi@pricon.ph')
                    ->subject('AIDRC: Applications for Control');
            });

            return response()->json(['result' => 1]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function load_for_control_affected_documents_email_three_days(Request $request)
    {
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
            $send_cc = ['nvlim@pricon.ph', 'dmmarmol@pricon.ph', 'mdalcaraz@pricon.ph'];

            for ($x = 0; $x < count($documents_collect); $x++) {
                if (!in_array($documents_collect[$x]->pic_details->email, $send_to)) {
                    $send_to[] = $documents_collect[$x]->pic_details->email;
                }

                /*if(!in_array($documents_collect[$x]->pic_details->department_details->department_email, $send_cc))
                {
                     $send_cc[] = $documents_collect[$x]->pic_details->department_details->department_email;
                }*/
            }

            //return $send_cc;

            Mail::send('mail.aidrc_affected_documents_three_days', $data, function ($message) use ($send_to, $send_cc) {

                $message->to($send_to)
                    ->cc($send_cc)
                    ->bcc('cdcasuyon@pricon.ph')
                    ->subject('AIDRC: Affected Documents for Control (Due in Three Days)');
            });

            return response()->json(['result' => 1]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function load_for_control_affected_documents_email_overdue(Request $request)
    {
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
            //return $documents_collect;

            $data = ['documents' => $documents_collect];

            $send_to = [];
            $send_cc = ['nvlim@pricon.ph', 'dmmarmol@pricon.ph', 'mdalcaraz@pricon.ph'];

            for ($x = 0; $x < count($documents_collect); $x++) {
                if (!in_array($documents_collect[$x]->pic_details->email, $send_to)) {
                    $send_to[] = $documents_collect[$x]->pic_details->email;
                }

                /* if(!in_array($documents_collect[$x]->pic_details->department_details->department_email, $send_cc))
                {
                     $send_cc[] = $documents_collect[$x]->pic_details->department_details->department_email;
                }*/
            }

            //return $send_cc;

            Mail::send('mail.aidrc_affected_documents_overdue', $data, function ($message) use ($send_to, $send_cc) {

                $message->to($send_to)
                    ->cc($send_cc)
                    ->bcc('cdcasuyon@pricon.ph')
                    ->subject('AIDRC: Affected Documents for Control (OVERDUE!)');
            });

            return response()->json(['result' => 1]);
        } else {
            return response()->json(['result' => 2]);
        }

        return 1;
    }

    public function load_acdcs_applications_table(Request $request)
    {
        session_start();

        $originator_id = $_SESSION['rapidx_user_id'];

        $rapidx_user_dcc = RapidXUser::whereIn('department_id', [21, 22, 23])->where('id', $originator_id)->get();

        $applications = [];

        if (count($rapidx_user_dcc) > 0) {
            $applications = Applications::with(['control_details' => function ($query2) {

                $query2->where('logdel', 0);
            }, 'head_approval_details', 'qs_validation_details' => function ($query) {

                $query->orderBy('created_at', 'desc');
            }])->where('logdel', 0)->orderBy('created_at', 'desc')->get();
        } else {
            $applications = Applications::with(['control_details' => function ($query2) {

                $query2->where('logdel', 0);
            }, 'head_approval_details', 'qs_validation_details' => function ($query) {

                $query->orderBy('created_at', 'desc');
            }])->where('application_originator', $originator_id)->orWhere('application_section_head', $originator_id)->orWhere('application_prod_head', $originator_id)->orWhere('application_qc_head', $originator_id)->orWhere('application_eng_head', $originator_id)->orWhere('qs_inspector', $originator_id)->get();
        }

        $applications2 = $applications->where('logdel', 0);

        return DataTables::of($applications2)
            ->addColumn('control_number', function ($application) {

                $result = $application->aidrc_control_number;

                return $result;
            })
            ->addColumn('status', function ($application) {

                $result = "";

                switch ($application->status) {
                    case 1: {
                            //APPLICATION FILED

                            //$result = "<strong style='color: #17a2b8;'>FOR APPROVAL</strong>";


                            //NEW
                            if ($application->for_group == 1) {
                                $result = "<strong style='color: #fd7e14;'>FOR QS VALIDATION CHECK</strong>";
                            } else {
                                $result = "<strong style='color: #17a2b8;'>FOR APPLICATION REVIEW (INITIAL)</strong>";
                            }




                            break;
                        }
                    case 2: {
                            //APPROVED BY SECTION HEAD (OPERATIONS)
                            // $result = "<strong style='color: #fd7e14;'>FOR QS VALIDATION CHECK</strong>";

                            //NEW
                            $result = "<strong style='color: #17a2b8;'>FOR APPLICATION REVIEW (INITIAL)</strong>";

                            break;
                        }
                    case 3: {
                            //QS INSPECTION FILED
                            //$result = "<strong style='color: #007bff;'>FOR CHECKPOINTS APPROVAL</strong>";

                            $result = "<strong>DISAPPROVED BY QS STAFF</strong>";

                            break;
                        }
                    case 4: {
                            //APPROVED BY ALL, FOR INITIAL DCC VALIDATION
                            $result = "<strong style='color: #007bff;'>FOR APPLICATION REVIEW (SECONDARY) </strong>";

                            break;
                        }
                    case 5: {
                            //MINOR REVISIONS
                            $result = "<strong style='color: #20c997;'>DISAPPROVED BY PRIMARY APPROVER</strong>";

                            break;
                        }
                    case 6: {
                            //MINOR REVISIONS
                            $result = "<strong style='color: #6610f2;'>FOR DCC VALIDATION</strong>";

                            break;
                        }
                        /*case 7:
                {

                    $search_revision_no = $application->document_revision_number;

                    if($application->control_details != null)
                    {
                        if($application->control_details->rev_no == $search_revision_no)
                        {
                            $result = "<strong style='color: #28a745;'>DOCUMENT CONTROLLED</strong>";
                        }
                        else
                        {
                            //DCC APPROVED, FOR CONTROL
                            $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                        }
                    }
                    else
                    {
                        //DCC APPROVED, FOR CONTROL
                            $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                    }

                    break;
                }*/
                    case 7: {
                            //DCC APPROVED, FOR CONTROL
                            $result = "<strong style='color: #20c997;'>FOR MINOR REVISIONS</strong>";

                            break;
                        }
                    case 8: {
                            //DCC APPROVED, FOR CONTROL
                            $result .= '<strong style="color: #dc3545;">FOR MAJOR REVISIONS</strong>';

                            break;
                        }
                    case 9: {
                            $search_revision_no = $application->document_revision_number;

                            if ($application->control_details != null) {
                                if ($application->control_details->rev_no == $search_revision_no) {
                                    $result = "<strong style='color: #28a745;'>DOCUMENT CONTROLLED</strong>";
                                } else {
                                    //DCC APPROVED, FOR CONTROL
                                    $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                                }
                            } else {
                                $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                            }

                            break;
                        }
                    case 10: {
                            $result .= '<strong>APPLICATION CANCELLED</strong>';
                            break;
                        }
                    case 11: {
                            $result .= '<strong>DISAPPROVED BY SECONDARY APPROVER</strong>';
                            break;
                        }
                    default: {
                            $result = "---";

                            break;
                        }
                }

                return $result;
            })
            ->addColumn('application_datetime', function ($application) {

                $result = $application->created_at;

                return $result;
            })
            ->addColumn('originator', function ($application) {

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
            ->addColumn('doc_title', function ($application) {

                $result = $application->document_name;

                return $result;
            })
            ->addColumn('rev_no', function ($application) {

                $result = $application->document_revision_number;

                return $result;
            })
            ->addColumn('uploaded_file', function ($application) {

                $result = '<a href="http://rapidx/aidrc/download_attached_document/' . $application->id . '" title="Click to download file">' . $application->original_filename . '</a>';

                return $result;
            })
            ->addColumn('application_approvers', function ($application) {

                $result = "";

                switch ($application->status) {
                    case 1: {
                            if ($application->for_group == 1) {
                                $result = '<span class="badge badge-info">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                                $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';
                            } else {
                                $result = '<span class="badge badge-info">SECTION HEAD APPROVER: ' . $application->section_head_details->name . '</span>';
                            }

                            break;
                        }
                    case 2: {
                            $result = '<span class="badge badge-success">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                            switch ($application->approver_priority) {
                                case 1: {
                                        $result .= '<br><span class="badge badge-info">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                        break;
                                    }
                                case 2: {
                                        $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-info">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                        break;
                                    }
                                case 3: {
                                        $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-info">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                        break;
                                    }
                                default: {
                                        $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                        break;
                                    }
                            }

                            break;
                        }
                    case 3: {
                            $result = '<span class="badge badge-danger">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                            $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                            $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                            $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                            break;
                        }
                    case 4: {
                            $result = '<span class="badge badge-success">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                            switch ($application->approver_priority) {
                                case 1: {
                                        $result .= '<br><span class="badge badge-success">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-info">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-info">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                        break;
                                    }
                                case 2: {
                                        $result .= '<br><span class="badge badge-info">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-info">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-success">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                        break;
                                    }
                                case 3: {
                                        $result .= '<br><span class="badge badge-info">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-success">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-info">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                        break;
                                    }
                                default: {
                                        $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                        break;
                                    }
                            }

                            break;
                        }
                    case 5: {
                            if ($application->for_group == 1) {
                                $result = '<span class="badge badge-success">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                                switch ($application->approver_priority) {
                                    case 1: {
                                            $result .= '<br><span class="badge badge-danger">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                            break;
                                        }
                                    case 2: {
                                            $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-danger">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                            break;
                                        }
                                    case 3: {
                                            $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-danger">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                            break;
                                        }
                                    default: {
                                            $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                            break;
                                        }
                                }
                            } else {
                                $result = '<span class="badge badge-danger">SECTION HEAD APPROVER: ' . $application->section_head_details->name . '</span>';
                            }



                            break;
                        }
                    case 6: {
                            if ($application->for_group == 1) {
                                $result = '<span class="badge badge-success">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                                $result .= '<br><span class="badge badge-success">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-success">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-success">QC HEAD: ' . $application->qc_head_details->name . '</span>';
                            } else {


                                $result = '<span class="badge badge-success">SECTION HEAD APPROVER: ' . $application->section_head_details->name . '</span>';
                            }

                            break;
                        }
                    case 7: {
                            if ($application->for_group == 1) {
                                $result = '<span class="badge badge-success">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                                $result .= '<br><span class="badge badge-success">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-success">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-success">QC HEAD: ' . $application->qc_head_details->name . '</span>';
                            } else {


                                $result = '<span class="badge badge-success">SECTION HEAD APPROVER: ' . $application->section_head_details->name . '</span>';
                            }

                            break;
                        }
                    case 8: {
                            if ($application->for_group == 1) {
                                $result = '<span class="badge badge-secondary">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                                $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';
                            } else {


                                $result = '<span class="badge badge-secondary">SECTION HEAD APPROVER: ' . $application->section_head_details->name . '</span>';
                            }

                            break;
                        }
                    case 9: {
                            if ($application->for_group == 1) {
                                $result = '<span class="badge badge-success">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                                $result .= '<br><span class="badge badge-success">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-success">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-success">QC HEAD: ' . $application->qc_head_details->name . '</span>';
                            } else {

                                $result = '<span class="badge badge-success">SECTION HEAD APPROVER: ' . $application->section_head_details->name . '</span>';
                            }

                            break;
                        }
                    case 10: {
                            if ($application->for_group == 1) {
                                $result = '<span class="badge badge-secondary">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                                $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';
                            } else {


                                $result = '<span class="badge badge-secondary">SECTION HEAD APPROVER: ' . $application->section_head_details->name . '</span>';
                            }

                            break;
                        }
                    case 11: {
                            $result = '<span class="badge badge-success">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                            switch ($application->approver_priority) {
                                case 1: {
                                        $result .= '<br><span class="badge badge-success">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-danger">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-danger">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                        break;
                                    }
                                case 2: {
                                        $result .= '<br><span class="badge badge-danger">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-danger">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-success">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                        break;
                                    }
                                case 3: {
                                        $result .= '<br><span class="badge badge-danger">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-success">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-danger">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                        break;
                                    }
                                default: {
                                        $result .= '<br><span class="badge badge-danger">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-danger">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-danger">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                        break;
                                    }
                            }

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
                    case 1: {
                            if ($application->for_group == 1) {
                                if ($application->qs_inspector === $originator_id) {
                                    $result = '<button type="button" class="btn btn-sm btn-block btn-info btn-qs-validation" data-toggle="modal" data-target="#modalQSValidation" title="Submit QS Validation" application-id=' . $application->id . '><i class="fa fa-microscope"></i> QS Validation</button>';
                                }
                            } else {
                                if ($application->application_section_head === $originator_id) {
                                    /*$result = "Priority Approver: Production Head";*/

                                    $result = '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id=' . $application->id . ' approving-as="4"><i class="fa fa-check-circle"></i> Review Application </button>';
                                }
                            }

                            break;
                        }
                    case 2: {
                            $result = "";

                            switch ($application->approver_priority) {
                                case 1: {
                                        if ($application->application_prod_head === $originator_id) {
                                            /*$result = "Priority Approver: Production Head";*/

                                            $result = '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id=' . $application->id . ' approving-as="1"><i class="fa fa-check-circle"></i> Review Application (PROD)</button>';
                                        }

                                        break;
                                    }

                                case 2: {
                                        if ($application->application_qc_head === $originator_id) {
                                            /*$result = "Priority Approver: QC Head";*/

                                            $result = '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id=' . $application->id . ' approving-as="2"><i class="fa fa-check-circle"></i> Review Application (QC)</button>';
                                        }

                                        break;
                                    }

                                case 3: {
                                        if ($application->application_eng_head === $originator_id) {
                                            /*$result = "Priority Approver: Engineering Head";*/

                                            $result = '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id=' . $application->id . ' approving-as="3"><i class="fa fa-check-circle"></i> Review Application (ENG)</button>';
                                        }

                                        break;
                                    }

                                default: {
                                        $result = '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id=' . $application->id . ' approving-as="4"><i class="fa fa-check-circle"></i> Review Application (SG/PPC)</button>';

                                        break;
                                    }
                            }

                            break;
                        }

                    case 3: {
                            //$result = "QS Validation NG";

                            break;
                        }

                    case 4: {
                            //$result = "1st Approver OK, for approval of the two others";
                            $result = "";

                            switch ($application->approver_priority) {
                                case 1: {
                                        /*if($application->application_prod_head === $originator_id)
                            {
                                $result = "Priority Approver: Production Head";

                                $result = '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id='.$application->id.'><i class="fa fa-check-circle"></i> Review Application </button>';
                            }*/

                                        if ($application->application_qc_head === $originator_id) {
                                            /*$result = "Priority Approver: QC Head";*/
                                            $qc_head = HeadApprovals::where('application_id', $application->id)->where('approving_as', 2)->where('head_approval_status', 1)->where('logdel', 0)->count();

                                            if ($qc_head == 0) {
                                                $result .= '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" approving-as="2" application-id=' . $application->id . ' ><i class="fa fa-check-circle"></i> Review Application (QC)</button>';
                                            }
                                        }

                                        if ($application->application_eng_head === $originator_id) {
                                            /*$result = "Priority Approver: Engineering Head";*/
                                            $eng_head = HeadApprovals::where('application_id', $application->id)->where('approving_as', 3)->where('head_approval_status', 1)->where('logdel', 0)->count();

                                            if ($eng_head == 0) {
                                                $result .= '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id=' . $application->id . ' approving-as="3"><i class="fa fa-check-circle"></i> Review Application (ENG)</button>';
                                            }
                                        }

                                        break;
                                    }

                                case 2: {
                                        /*if($application->application_qc_head === $originator_id)
                            {
                                $result = "Priority Approver: QC Head";

                                $result = '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id='.$application->id.'><i class="fa fa-check-circle"></i> Review Application </button>';
                            }*/

                                        if ($application->application_prod_head === $originator_id) {
                                            /*$result = "Priority Approver: Production Head";*/
                                            $prod_head = HeadApprovals::where('application_id', $application->id)->where('approving_as', 1)->where('head_approval_status', 1)->where('logdel', 0)->count();

                                            if ($prod_head == 0) {
                                                $result .= '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id=' . $application->id . ' approving-as="1"><i class="fa fa-check-circle"></i> Review Application (PROD)</button>';
                                            }
                                        }

                                        if ($application->application_eng_head === $originator_id) {
                                            /*$result = "Priority Approver: Engineering Head";*/
                                            $eng_head = HeadApprovals::where('application_id', $application->id)->where('approving_as', 3)->where('head_approval_status', 1)->where('logdel', 0)->count();

                                            if ($eng_head == 0) {
                                                $result .= '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id=' . $application->id . ' approving-as="3"><i class="fa fa-check-circle"></i> Review Application (ENG)</button>';
                                            }
                                        }

                                        break;
                                    }

                                case 3: {
                                        /*if($application->application_eng_head === $originator_id)
                            {
                               $result = "Priority Approver: Engineering Head";

                               $result = '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id='.$application->id.'><i class="fa fa-check-circle"></i> Review Application </button>';
                            }*/

                                        if ($application->application_prod_head === $originator_id) {
                                            /*$result = "Priority Approver: Production Head";*/
                                            $prod_head = HeadApprovals::where('application_id', $application->id)->where('approving_as', 1)->where('head_approval_status', 1)->where('logdel', 0)->count();

                                            if ($prod_head == 0) {
                                                $result .= '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id=' . $application->id . ' approving-as="1"><i class="fa fa-check-circle"></i> Review Application (PROD)</button>';
                                            }
                                        }

                                        if ($application->application_qc_head === $originator_id) {
                                            /*$result = "Priority Approver: QC Head";*/
                                            $qc_head = HeadApprovals::where('application_id', $application->id)->where('approving_as', 2)->where('head_approval_status', 1)->where('logdel', 0)->count();

                                            if ($qc_head == 0) {
                                                $result .= '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" approving-as="2" application-id=' . $application->id . '><i class="fa fa-check-circle"></i> Review Application (QC)</button>';
                                            }
                                        }
                                        break;
                                    }

                                default: {
                                        $result = "Default Result";

                                        break;
                                    }
                            }

                            break;
                        }

                    case 5: {
                            /*$result = "1st Approver NG";*/

                            break;
                        }

                    case 6: {
                            /*$result = "For DCC Validation";*/

                            $user_details = RapidXUser::where('id', $originator_id)->where('user_stat', 1)->get();

                            if (count($user_details) > 0) {
                                if ($user_details[0]->department_id == 21 || $user_details[0]->department_id == 22 || $user_details[0]->department_id == 23 || $user_details[0]->id == 8) {
                                    $result .= '<button type="button" class="btn btn-sm btn-block btn-warning btn-dcc-validation" data-toggle="modal" data-target="#modalDccValidations" title="For Initial DCC Confirmation" application-id=' . $application->id . '><i class="fa fa-chevron-right"></i> DCC Validation Check</button>';
                                }
                            }


                            break;
                        }
                    case 7: {
                            $result = "";

                            break;
                        }
                    case 8: {
                            $result = "";

                            break;
                        }
                    case 9: {
                            $result = "";

                            break;
                        }
                    case 10: {
                            $result = "";

                            break;
                        }
                    case 11: {
                            $result = "";

                            break;
                        }



                    default: {
                            $result = '---';

                            break;
                        }
                }

                $view_edit = 0;

                if ($application->application_originator === $originator_id) {
                    $view_edit = 1;
                }

                $result .= ' <button type="button" class="btn btn-sm btn-block btn-primary btn-view-application" data-toggle="modal" data-target="#modalViewApplication" view-edit=' . $view_edit . ' title="View/Edit Application" application-id=' . $application->id . '><i class="fa fa-edit"></i> View Application </button>';

                return $result;
            })
            ->rawColumns(['status', 'application_approvers', 'uploaded_file', 'action'])
            ->make(true);
    }
    //
    public function load_acdcs_applications_table_test(Request $request)
    {
        session_start();

        $originator_id = $_SESSION['rapidx_user_id'];

        $rapidx_user_dcc = RapidXUser::whereIn('department_id', [21, 22, 23])->where('id', $originator_id)->get();

        $applications = [];

        if (count($rapidx_user_dcc) > 0) {
            $applications = Applications::with(['control_details' => function ($query2) {

                $query2->where('logdel', 0);
            }, 'department_details', 'self_details', 'head_approval_details', 'qs_validation_details' => function ($query) {

                $query->orderBy('created_at', 'desc');
            }])->where('logdel', 0)->orderBy('created_at', 'desc')->get();
        } else {
            $applications = Applications::with(['control_details' => function ($query2) {

                $query2->where('logdel', 0);
            }, 'department_details', 'self_details', 'head_approval_details', 'qs_validation_details' => function ($query) {

                $query->orderBy('created_at', 'desc');
            }])->where('application_originator', $originator_id)->orWhere('application_section_head', $originator_id)->orWhere('application_prod_head', $originator_id)->orWhere('application_qc_head', $originator_id)->orWhere('application_eng_head', $originator_id)->orWhere('qs_inspector', $originator_id)->get();
        }

        if (isset($request->check_section_department)) {
            $applications = $applications->whereIn('department', $request->section_department);
        }

        if (isset($request->check_originator)) {
            $applications = $applications->whereIn('application_originator', $request->originator);
        }

        $applications_final = collect($applications)->flatten(1);
        $arrayApplications = [];

        if (isset($request->check_status)) {
            for ($i = 0; $i < count($applications_final); $i++) {
                if ($applications_final[$i]->status == 1) {
                    if ($applications_final[$i]->for_group != 1) {
                        if ($applications_final[$i]->application_section_head == $originator_id) {
                            $arrayApplications[] = $applications_final[$i]->id;
                        }
                    }
                } else if ($applications_final[$i]->status == 2) {
                    switch ($applications_final[$i]->approver_priority) {
                        case 1: {
                                if ($applications_final[$i]->application_prod_head == $originator_id) {
                                    $arrayApplications[] = $applications_final[$i]->id;
                                }

                                break;
                            }
                        case 2: {
                                if ($applications_final[$i]->application_qc_head == $originator_id) {
                                    $arrayApplications[] = $applications_final[$i]->id;
                                }

                                break;
                            }
                        case 3: {
                                if ($applications_final[$i]->application_eng_head == $originator_id) {
                                    $arrayApplications[] = $applications_final[$i]->id;
                                }

                                break;
                            }
                        default: {
                                break;
                            }
                    }
                } else if ($applications_final[$i]->status == 4) {
                    switch ($applications_final[$i]->approver_priority) {
                        case 1: {
                                if ($applications_final[$i]->application_eng_head == $originator_id || $applications_final[$i]->application_qc_head == $originator_id) {
                                    $arrayApplications[] = $applications_final[$i]->id;
                                }

                                break;
                            }
                        case 2: {
                                if ($applications_final[$i]->application_eng_head == $originator_id || $applications_final[$i]->application_prod_head == $originator_id) {
                                    $arrayApplications[] = $applications_final[$i]->id;
                                }

                                break;
                            }
                        case 3: {
                                if ($applications_final[$i]->application_qc_head == $originator_id || $applications_final[$i]->application_prod_head == $originator_id) {
                                    $arrayApplications[] = $applications_final[$i]->id;
                                }

                                break;
                            }
                        default: {
                                break;
                            }
                    }
                } else {
                }
            }
        } else {
            for ($i = 0; $i < count($applications_final); $i++) {
                $arrayApplications[] = $applications_final[$i]->id;
            }
        }

        $applications3 =  Applications::with(['control_details' => function ($query2) {

            $query2->where('logdel', 0);
        }, 'department_details', 'self_details', 'head_approval_details', 'qs_validation_details' => function ($query) {

            $query->orderBy('created_at', 'desc');
        }])->where('logdel', 0)->whereIn('id', $arrayApplications)->orderBy('created_at', 'desc')->get();

        $applications2 = $applications3->where('logdel', 0);

        return DataTables::of($applications2)
            ->addColumn('control_number', function ($application) {

                $result = $application->aidrc_control_number;

                return $result;
            })
            ->addColumn('status', function ($application) {

                $result = "";

                switch ($application->status) {
                    case 1: {
                            //APPLICATION FILED

                            //$result = "<strong style='color: #17a2b8;'>FOR APPROVAL</strong>";


                            //NEW
                            if ($application->for_group == 1) {
                                $result = "<strong style='color: #fd7e14;'>FOR QS VALIDATION CHECK</strong>";
                            } else {
                                $result = "<strong style='color: #17a2b8;'>FOR APPLICATION REVIEW (INITIAL)</strong>";
                            }
                            break;
                        }
                    case 2: {
                            //APPROVED BY SECTION HEAD (OPERATIONS)
                            // $result = "<strong style='color: #fd7e14;'>FOR QS VALIDATION CHECK</strong>";

                            //NEW
                            $result = "<strong style='color: #17a2b8;'>FOR APPLICATION REVIEW (INITIAL)</strong>";

                            break;
                        }
                    case 3: {
                            //QS INSPECTION FILED
                            //$result = "<strong style='color: #007bff;'>FOR CHECKPOINTS APPROVAL</strong>";

                            $result = "<strong>DISAPPROVED BY QS STAFF</strong>";

                            break;
                        }
                    case 4: {
                            //APPROVED BY ALL, FOR INITIAL DCC VALIDATION
                            $result = "<strong style='color: #007bff;'>FOR APPLICATION REVIEW (SECONDARY) </strong>";

                            break;
                        }
                    case 5: {
                            //MINOR REVISIONS
                            $result = "<strong style='color: #20c997;'>DISAPPROVED BY PRIMARY APPROVER</strong>";

                            break;
                        }
                    case 6: {
                            //MINOR REVISIONS
                            $result = "<strong style='color: #6610f2;'>FOR DCC VALIDATION</strong>";

                            break;
                        }
                        /*case 7:
                {

                    $search_revision_no = $application->document_revision_number;

                    if($application->control_details != null)
                    {
                        if($application->control_details->rev_no == $search_revision_no)
                        {
                            $result = "<strong style='color: #28a745;'>DOCUMENT CONTROLLED</strong>";
                        }
                        else
                        {
                            //DCC APPROVED, FOR CONTROL
                            $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                        }
                    }
                    else
                    {
                        //DCC APPROVED, FOR CONTROL
                            $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                    }

                    break;
                }*/
                    case 7: {
                            //DCC APPROVED, FOR CONTROL
                            $result = "<strong style='color: #20c997;'>FOR MINOR REVISIONS</strong>";

                            break;
                        }
                    case 8: {
                            //DCC APPROVED, FOR CONTROL
                            $result .= '<strong style="color: #dc3545;">FOR MAJOR REVISIONS</strong>';

                            break;
                        }
                    case 9: {

                            $search_aidrc_revision_no = $application->document_revision_number;
                            $document_no = $application->document_number;
                            $document_name = $application->document_name;


                            if ($application->control_details != null) {
                                if ($document_no != "") {
                                    $acdcs_rev_no = $application->control_details->rev_no;
                                    // $acdcs_obsolete = intval($acdcs_rev_no);

                                    if ($acdcs_rev_no != 0 && $acdcs_rev_no == $search_aidrc_revision_no) {
                                        $result = "<strong style='color: #28a745;'>DOCUMENT CONTROLLED</strong>";
                                    }
                                    //=====
                                    elseif ($acdcs_rev_no != 0 && $acdcs_rev_no > $search_aidrc_revision_no) {
                                        $result = "<strong style='color: #28a745;'>DOCUMENT CONTROLLED</strong>";
                                    }
                                    //===== REVISION NUMBER IS EQUAL TO 0 =====//
                                    elseif ($acdcs_rev_no == 0 && $acdcs_rev_no == $search_aidrc_revision_no) {
                                        $result = "<strong style='color: #28a745;'>DOCUMENT CONTROLLED</strong>";
                                    }
                                    //=====  =====//
                                    else {
                                        $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                                    }
                                } else {
                                    $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                                }
                            } else {
                                $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                            }




                            // if ($application->control_details != null) {
                            //     if ($document_no != "") {
                            //         $acdcs_rev_no = $application->control_details->rev_no;
                            //         // $acdcs_obsolete = intval($acdcs_rev_no);

                            //         if ($acdcs_rev_no != 0 && $acdcs_rev_no == $search_aidrc_revision_no) {
                            //             $result = "<strong style='color: #28a745;'>DOCUMENT CONTROLLED</strong>";
                            //         }
                            //         //=====
                            //         elseif ($acdcs_rev_no != 0 && $acdcs_rev_no > $search_aidrc_revision_no) {
                            //             $result = "<strong style='color: #28a745;'>DOCUMENT CONTROLLED</strong>";
                            //         }
                            //         //===== REVISION NUMBER IS EQUAL TO 0 =====//
                            //         else {
                            //             $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                            //         }
                            //     } else {
                            //         $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                            //     }
                            // } else {
                            //     $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                            // }

                            // $search_revision_no = $application->document_revision_number;
                            // $document_no = $application->document_number;

                            // $check_number_of_applications = Applications::where('document_number', $document_no)
                            //     ->get();

                            // $count_check_number_of_applications = count($check_number_of_applications);

                            // $get_rev_no = array();
                            // for ($i = 0; $i < $count_check_number_of_applications; $i++) {
                            //     $get_rev_no[] = $check_number_of_applications[$i]->document_revision_number;
                            // }

                            // if ($get_rev_no > 1) {
                            //     $latest_rev_no = max($get_rev_no);
                            //     $get_latest_rev_no = array_search($latest_rev_no, $get_rev_no);

                            //     $last_rev_no = min($get_rev_no);
                            //     $get_last_rev_no = array_search($last_rev_no, $get_rev_no);
                            // }


                            // //===== CHECK IN ACDCS IF NOT NULL =====//
                            // if ($application->control_details != null) {
                            //     //===== SUBTRACT 1 IN REVISION NUMBER FROM ACDCS =====//
                            //     $obsolete = intval($application->control_details->rev_no) - 1;

                            //     //===== IF DOC NO. FROM AIDRC IS NOT NULL =====//
                            //     if ($document_no != "") {
                            //         // if ($application->control_details->rev_no == null) {
                            //         //===== COMPARE ACDCS REV NO. TO AIDRC LATEST REV NO. =====//
                            //         if ($application->control_details->rev_no == $get_rev_no[$get_latest_rev_no]) {
                            //             $result = "<strong style='color: #28a745;'>DOCUMENT CONTROLLED</strong>";
                            //         }
                            //         //===== COMPARE LAST REVISION NO. FROM AIDRC TO THE REVISION NO. FROM ACDCS MINUS 1 =====//
                            //         elseif ($obsolete == $get_rev_no[$get_last_rev_no]) {
                            //             $result = "<strong style='color: #445626;'>DOCUMENT CONTROLLED</strong>";
                            //         }
                            //         // }
                            //         //===== IF THERE IS NO DATA FROM ACDCS =====//
                            //         elseif ($application->control_details->rev_no == null) {
                            //             $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                            //         }
                            //         //===== IF THERE IS DATA  =====//
                            //         elseif ($application->control_details->rev_no != null) {
                            //             $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                            //         }
                            //     } else {
                            //         $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                            //     }
                            // } else {
                            //     $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                            // }


                            //====== REVISED CONDITION =====//
                            // if ($application->control_details != null) {
                            //     // $obsolete =  $application->control_details->rev_no - 1;

                            //     if ($document_no != null) {
                            //         if ($application->control_details->rev_no == $search_revision_no) {
                            //             $result = "<strong style='color: #28a745;'>DOCUMENT CONTROLLED</strong>";
                            //         } else {
                            //             $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                            //         }
                            //     } else {
                            //         $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                            //     }
                            // } else {
                            //     $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                            // }


                            //====== ORIGINAL CONDITION ======//
                            // if ($application->control_details != null) {
                            //     if ($application->control_details->rev_no == $search_revision_no) {
                            //         $result = "<strong style='color: #28a745;'>DOCUMENT CONTROLLED</strong>";
                            //     } else {
                            //         $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                            //     }
                            // } else {
                            //     $result = "<strong style='color: #445626;'>APPLICATION APPROVED, FOR CONTROL</strong>";
                            // }

                            break;
                        }
                    case 10: {
                            $result .= '<strong>APPLICATION CANCELLED</strong>';
                            break;
                        }
                    case 11: {
                            $result .= '<strong>DISAPPROVED BY SECONDARY APPROVER</strong>';
                            break;
                        }
                    default: {
                            $result = "---";

                            break;
                        }
                }

                return $result;
            })
            ->addColumn('application_datetime', function ($application) {

                $result = $application->created_at;

                return $result;
            })
            ->addColumn('originator', function ($application) {

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
            ->addColumn('doc_title', function ($application) {

                $result = $application->document_name;

                return $result;
            })
            ->addColumn('rev_no', function ($application) {

                $result = $application->document_revision_number;

                return $result;
            })
            ->addColumn('uploaded_file', function ($application) {

                $result = '<a href="http://rapidx/aidrc/download_attached_document/' . $application->id . '" title="Click to download file">' . $application->original_filename . '</a>';

                return $result;
            })
            ->addColumn('application_approvers', function ($application) {

                $result = "";

                switch ($application->status) {
                    case 1: {
                            if ($application->for_group == 1) {
                                $result = '<span class="badge badge-info">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                                $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';
                            } else {
                                $result = '<span class="badge badge-info">SECTION HEAD APPROVER: ' . $application->section_head_details->name . '</span>';
                            }

                            break;
                        }
                    case 2: {
                            if ($application->for_group == 1) {
                                $result = '<span class="badge badge-success">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                                switch ($application->approver_priority) {
                                    case 1: {
                                            $result .= '<br><span class="badge badge-info">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                            break;
                                        }
                                    case 2: {
                                            $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-info">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                            break;
                                        }
                                    case 3: {
                                            $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-info">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                            break;
                                        }
                                    default: {
                                            $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                            break;
                                        }
                                }
                            } else {
                                $result = '<span class="badge badge-info">SECTION HEAD APPROVER: ' . $application->section_head_details->name . '</span>';
                            }

                            break;
                        }
                    case 3: {
                            $result = '<span class="badge badge-danger">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                            $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                            $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                            $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                            break;
                        }
                    case 4: {
                            $result = '<span class="badge badge-success">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                            switch ($application->approver_priority) {
                                case 1: {
                                        $result .= '<br><span class="badge badge-success">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-info">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-info">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                        break;
                                    }
                                case 2: {
                                        $result .= '<br><span class="badge badge-info">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-info">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-success">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                        break;
                                    }
                                case 3: {
                                        $result .= '<br><span class="badge badge-info">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-success">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-info">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                        break;
                                    }
                                default: {
                                        $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                        break;
                                    }
                            }

                            break;
                        }
                    case 5: {
                            if ($application->for_group == 1) {
                                $result = '<span class="badge badge-success">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                                switch ($application->approver_priority) {
                                    case 1: {
                                            $result .= '<br><span class="badge badge-danger">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                            break;
                                        }
                                    case 2: {
                                            $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-danger">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                            break;
                                        }
                                    case 3: {
                                            $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-danger">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                            break;
                                        }
                                    default: {
                                            $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                            $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                            break;
                                        }
                                }
                            } else {
                                $result = '<span class="badge badge-danger">SECTION HEAD APPROVER: ' . $application->section_head_details->name . '</span>';
                            }



                            break;
                        }
                    case 6: {
                            if ($application->for_group == 1) {
                                $result = '<span class="badge badge-success">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                                $result .= '<br><span class="badge badge-success">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-success">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-success">QC HEAD: ' . $application->qc_head_details->name . '</span>';
                            } else {

                                $result = '<span class="badge badge-success">SECTION HEAD APPROVER: ' . $application->section_head_details->name . '</span>';
                            }

                            break;
                        }
                    case 7: {
                            if ($application->for_group == 1) {
                                $result = '<span class="badge badge-success">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                                $result .= '<br><span class="badge badge-success">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-success">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-success">QC HEAD: ' . $application->qc_head_details->name . '</span>';
                            } else {


                                $result = '<span class="badge badge-success">SECTION HEAD APPROVER: ' . $application->section_head_details->name . '</span>';
                            }

                            break;
                        }
                    case 8: {
                            if ($application->for_group == 1) {
                                $result = '<span class="badge badge-secondary">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                                $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';
                            } else {


                                $result = '<span class="badge badge-secondary">SECTION HEAD APPROVER: ' . $application->section_head_details->name . '</span>';
                            }

                            break;
                        }
                    case 9: {
                            if ($application->for_group == 1) {
                                $result = '<span class="badge badge-success">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                                $result .= '<br><span class="badge badge-success">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-success">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-success">QC HEAD: ' . $application->qc_head_details->name . '</span>';
                            } else {


                                $result = '<span class="badge badge-success">SECTION HEAD APPROVER: ' . $application->section_head_details->name . '</span>';
                            }

                            break;
                        }
                    case 10: {
                            if ($application->for_group == 1) {
                                $result = '<span class="badge badge-secondary">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                                $result .= '<br><span class="badge badge-secondary">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-secondary">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                $result .= '<br><span class="badge badge-secondary">QC HEAD: ' . $application->qc_head_details->name . '</span>';
                            } else {


                                $result = '<span class="badge badge-secondary">SECTION HEAD APPROVER: ' . $application->section_head_details->name . '</span>';
                            }

                            break;
                        }
                    case 11: {
                            $result = '<span class="badge badge-success">QS STAFF: ' . $application->qs_inspector_details->name . '</span>';

                            switch ($application->approver_priority) {
                                case 1: {
                                        $result .= '<br><span class="badge badge-success">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-danger">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-danger">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                        break;
                                    }
                                case 2: {
                                        $result .= '<br><span class="badge badge-danger">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-danger">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-success">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                        break;
                                    }
                                case 3: {
                                        $result .= '<br><span class="badge badge-danger">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-success">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-danger">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                        break;
                                    }
                                default: {
                                        $result .= '<br><span class="badge badge-danger">PROD HEAD: ' . $application->prod_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-danger">ENG HEAD: ' . $application->eng_head_details->name . '</span>';
                                        $result .= '<br><span class="badge badge-danger">QC HEAD: ' . $application->qc_head_details->name . '</span>';

                                        break;
                                    }
                            }

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
                    case 1: {
                            if ($application->for_group == 1) {
                                if ($application->qs_inspector === $originator_id) {
                                    $result = '<button type="button" class="btn btn-sm btn-block btn-info btn-qs-validation" data-toggle="modal" data-target="#modalQSValidation" title="Submit QS Validation" application-id=' . $application->id . '><i class="fa fa-microscope"></i> QS Validation</button>';
                                }
                            } else {
                                if ($application->application_section_head === $originator_id) {
                                    /*$result = "Priority Approver: Production Head";*/

                                    $result = '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id=' . $application->id . ' approving-as="4"><i class="fa fa-check-circle"></i> Review Application </button>';
                                }
                            }

                            break;
                        }
                    case 2: {
                            $result = "";

                            switch ($application->approver_priority) {
                                case 1: {
                                        if ($application->application_prod_head === $originator_id) {
                                            /*$result = "Priority Approver: Production Head";*/

                                            $result = '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id=' . $application->id . ' approving-as="1"><i class="fa fa-check-circle"></i> Review Application (PROD)</button>';
                                        }

                                        break;
                                    }

                                case 2: {
                                        if ($application->application_qc_head === $originator_id) {
                                            /*$result = "Priority Approver: QC Head";*/

                                            $result = '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id=' . $application->id . ' approving-as="2"><i class="fa fa-check-circle"></i> Review Application (QC)</button>';
                                        }

                                        break;
                                    }

                                case 3: {
                                        if ($application->application_eng_head === $originator_id) {
                                            /*$result = "Priority Approver: Engineering Head";*/

                                            $result = '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id=' . $application->id . ' approving-as="3"><i class="fa fa-check-circle"></i> Review Application (ENG)</button>';
                                        }

                                        break;
                                    }

                                default: {
                                        $result = '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id=' . $application->id . ' approving-as="4"><i class="fa fa-check-circle"></i> Review Application (SG/PPC)</button>';

                                        break;
                                    }
                            }

                            break;
                        }

                    case 3: {
                            //$result = "QS Validation NG";

                            break;
                        }

                    case 4: {
                            //$result = "1st Approver OK, for approval of the two others";
                            $result = "";

                            switch ($application->approver_priority) {
                                case 1: {
                                        /*if($application->application_prod_head === $originator_id)
                            {
                                $result = "Priority Approver: Production Head";

                                $result = '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id='.$application->id.'><i class="fa fa-check-circle"></i> Review Application </button>';
                            }*/

                                        if ($application->application_qc_head === $originator_id) {
                                            /*$result = "Priority Approver: QC Head";*/
                                            $qc_head = HeadApprovals::where('application_id', $application->id)->where('approving_as', 2)->where('head_approval_status', 1)->where('logdel', 0)->count();

                                            if ($qc_head == 0) {
                                                $result .= '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" approving-as="2" application-id=' . $application->id . ' ><i class="fa fa-check-circle"></i> Review Application (QC)</button>';
                                            }
                                        }

                                        if ($application->application_eng_head === $originator_id) {
                                            /*$result = "Priority Approver: Engineering Head";*/
                                            $eng_head = HeadApprovals::where('application_id', $application->id)->where('approving_as', 3)->where('head_approval_status', 1)->where('logdel', 0)->count();

                                            if ($eng_head == 0) {
                                                $result .= '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id=' . $application->id . ' approving-as="3"><i class="fa fa-check-circle"></i> Review Application (ENG)</button>';
                                            }
                                        }

                                        break;
                                    }

                                case 2: {
                                        /*if($application->application_qc_head === $originator_id)
                            {
                                $result = "Priority Approver: QC Head";

                                $result = '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id='.$application->id.'><i class="fa fa-check-circle"></i> Review Application </button>';
                            }*/

                                        if ($application->application_prod_head === $originator_id) {
                                            /*$result = "Priority Approver: Production Head";*/
                                            $prod_head = HeadApprovals::where('application_id', $application->id)->where('approving_as', 1)->where('head_approval_status', 1)->where('logdel', 0)->count();

                                            if ($prod_head == 0) {
                                                $result .= '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id=' . $application->id . ' approving-as="1"><i class="fa fa-check-circle"></i> Review Application (PROD)</button>';
                                            }
                                        }

                                        if ($application->application_eng_head === $originator_id) {
                                            /*$result = "Priority Approver: Engineering Head";*/
                                            $eng_head = HeadApprovals::where('application_id', $application->id)->where('approving_as', 3)->where('head_approval_status', 1)->where('logdel', 0)->count();

                                            if ($eng_head == 0) {
                                                $result .= '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id=' . $application->id . ' approving-as="3"><i class="fa fa-check-circle"></i> Review Application (ENG)</button>';
                                            }
                                        }

                                        break;
                                    }

                                case 3: {
                                        /*if($application->application_eng_head === $originator_id)
                            {
                               $result = "Priority Approver: Engineering Head";

                               $result = '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id='.$application->id.'><i class="fa fa-check-circle"></i> Review Application </button>';
                            }*/

                                        if ($application->application_prod_head === $originator_id) {
                                            /*$result = "Priority Approver: Production Head";*/
                                            $prod_head = HeadApprovals::where('application_id', $application->id)->where('approving_as', 1)->where('head_approval_status', 1)->where('logdel', 0)->count();

                                            if ($prod_head == 0) {
                                                $result .= '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" application-id=' . $application->id . ' approving-as="1"><i class="fa fa-check-circle"></i> Review Application (PROD)</button>';
                                            }
                                        }

                                        if ($application->application_qc_head === $originator_id) {
                                            /*$result = "Priority Approver: QC Head";*/
                                            $qc_head = HeadApprovals::where('application_id', $application->id)->where('approving_as', 2)->where('head_approval_status', 1)->where('logdel', 0)->count();

                                            if ($qc_head == 0) {
                                                $result .= '<button type="button" class="btn btn-sm btn-block btn-success btn-head-approval" data-toggle="modal" data-target="#modalHeadApprover" title="Review Application" approving-as="2" application-id=' . $application->id . '><i class="fa fa-check-circle"></i> Review Application (QC)</button>';
                                            }
                                        }
                                        break;
                                    }

                                default: {
                                        $result = "Default Result";

                                        break;
                                    }
                            }

                            break;
                        }

                    case 5: {
                            /*$result = "1st Approver NG";*/

                            break;
                        }

                    case 6: {
                            /*$result = "For DCC Validation";*/

                            $user_details = RapidXUser::where('id', $originator_id)->where('user_stat', 1)->get();

                            if (count($user_details) > 0) {
                                if ($user_details[0]->department_id == 21 || $user_details[0]->department_id == 22 || $user_details[0]->department_id == 23 || $user_details[0]->id == 8) {
                                    $result .= '<button type="button" class="btn btn-sm btn-block btn-warning btn-dcc-validation" data-toggle="modal" data-target="#modalDccValidations" title="For Initial DCC Confirmation" application-id=' . $application->id . '><i class="fa fa-chevron-right"></i> DCC Validation Check</button>';
                                }
                            }


                            break;
                        }
                    case 7: {
                            $result = "";

                            break;
                        }
                    case 8: {
                            $result = "";

                            break;
                        }
                    case 9: {
                            $result = "";

                            break;
                        }
                    case 10: {
                            $result = "";

                            break;
                        }
                    case 11: {
                            $result = "";

                            break;
                        }



                    default: {
                            $result = '---';

                            break;
                        }
                }

                $view_edit = 0;

                if ($application->application_originator === $originator_id) {
                    $view_edit = 1;
                }

                $result .= ' <button type="button" class="btn btn-sm btn-block btn-primary btn-view-application" data-toggle="modal" data-target="#modalViewApplication" view-edit=' . $view_edit . ' title="View/Edit Application" application-id=' . $application->id . '><i class="fa fa-edit"></i> View Application </button>';

                return $result;
            })
            ->rawColumns(['status', 'application_approvers', 'uploaded_file', 'action'])
            ->make(true);
    }
    //

    public function load_acdcs_app_data(Request $request)
    {
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

    public function load_acdcs_documents_table(Request $request)
    {
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

    public function submit_new_application(Request $request)
    {
        session_start();

        date_default_timezone_set('Asia/Manila');

        $validator = '';

        switch ($request->add_for_group) {
            case 1: {
                    if ($request->add_doc_type == 1) {
                        $validator = Validator::make($request->all(), [

                            'add_attachment' => 'required',
                            'add_doc_category' => 'required',
                            'add_doc_type' => 'required',
                            'add_for_group' => 'required',
                            'add_department' => 'required',
                            'add_doc_title' => 'required',

                            'add_approver_priority' => 'required',
                            'add_production_head' => 'required',
                            'add_qc_head' => 'required',
                            'add_eng_head' => 'required',
                            'add_qs_inspector' => 'required'

                        ]);
                    } else {
                        $validator = Validator::make($request->all(), [

                            'add_attachment' => 'required',
                            'add_doc_category' => 'required',
                            'add_doc_type' => 'required',
                            'add_for_group' => 'required',
                            'add_department' => 'required',
                            'add_doc_title' => 'required',
                            'add_doc_title' => 'required',
                            'add_doc_rev_no' => 'required',

                            'add_approver_priority' => 'required',
                            'add_production_head' => 'required',
                            'add_qc_head' => 'required',
                            'add_eng_head' => 'required',
                            'add_qs_inspector' => 'required'

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
                            'add_section_head_approver' => 'required',

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
                            'add_section_head_approver' => 'required',

                        ]);
                    }

                    break;
                }

            case 3: {
                    if ($request->add_doc_type == 1) //OPERATIONS - PPC, New Document
                    {
                        $validator = Validator::make($request->all(), [

                            'add_attachment' => 'required',
                            'add_doc_category' => 'required',
                            'add_doc_type' => 'required',
                            'add_for_group' => 'required',
                            'add_department' => 'required',
                            'add_doc_title' => 'required',
                            'add_section_head_approver' => 'required',

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
                            'add_section_head_approver' => 'required',

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
                        'add_section_head_approver' => 'required',
                        'add_approver_priority' => 'required',
                        'add_production_head' => 'required',
                        'add_qc_head' => 'required',
                        'add_eng_head' => 'required',
                        'add_qs_inspector' => 'required'

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

            $applications = Applications::where('logdel', 0)->whereYear('created_at', $year2)->orderBy('id', 'desc')->first();

            if ($applications != null) {
                $control_number = $applications->aidrc_control_number;

                $number = explode('-', $control_number);

                $counter = intval($number[1]) + 1;
            } else {
                $counter = 1;
            }

            //AUTO GENERATED AIDRC CONTROL NUMBER
            $aidrc_control_number = $month . $year  . "-" . str_pad($counter, 3, "0", STR_PAD_LEFT);

            //ORIGINATOR/APPLICATION CREATOR
            $originator_id = $_SESSION['rapidx_user_id'];

            //FILENAME
            $generated_filename = "aidrc_attachment_" . date('YmdHis');
            $original_filename = $request->file('add_attachment')->getClientOriginalName();
            $file_extension = $request->file('add_attachment')->getClientOriginalExtension();
            $aidrc_filename = $generated_filename . "." . $file_extension;

            //INSERT INTO APPLICATION
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

                    'application_section_head' => $request->add_section_head_approver,
                    'application_prod_head' => $request->add_production_head,
                    'application_qc_head' => $request->add_qc_head,
                    'application_eng_head' => $request->add_eng_head,

                    'qs_inspector' => $request->add_qs_inspector,
                    'approver_priority' => $request->add_approver_priority,

                    'originator_remarks' => $request->add_remarks,

                    'aidrc_filename' => $aidrc_filename,
                    'original_filename' => $original_filename,

                    'status' => 1,

                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),

                    'logdel' => 0
                ]);

                return response()->json(['result' => 1, 'application_id' => $application_id]);
            } catch (\Exception $e) {
                DB::rollback();
                // throw $e;
                return response()->json(['result' => $e]);
            }
        } else {
            return response()->json(['result' => 0, 'error' => $validator->messages()]);
        }
    }

    public function load_application_details(Request $request)
    {
        $application_details = Applications::where('id', $request->application_id)->where('logdel', 0)->get();

        if (count($application_details) > 0) {
            return response()->json(['result' => 1, 'application_details' => $application_details]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function submit_new_qs_validation(Request $request)
    {
        session_start();

        date_default_timezone_set('Asia/Manila');

        $validator = '';

        $validator = Validator::make($request->all(), [

            'qs_application_id' => 'required',
            'qs_validation_status' => 'required',
            'qs_validation_remarks' => 'required',

        ]);


        if ($validator->passes()) {
            try {
                QSValidations::insert([
                    'application_id' => $request->qs_application_id,
                    'qs_validation_status' => $request->qs_validation_status,
                    'qs_validation_remarks' => $request->qs_validation_remarks,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'logdel' => 0,
                ]);

                $application_status = 2;

                if ($request->qs_validation_status == 2) {
                    $application_status = 3;
                }

                Applications::where('id', $request->qs_application_id)->update([

                    'status' => $application_status,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                return response()->json(['result' => 1, 'application_id' => $request->qs_application_id]);
            } catch (\Exception $e) {
                DB::rollback();
                // throw $e;
                return response()->json(['result' => $e]);
            }
        } else {
            return response()->json(['result' => 0, 'error' => $validator->messages()]);
        }
    }

    public function submit_new_head_approval(Request $request)
    {
        session_start();

        date_default_timezone_set('Asia/Manila');

        $validator = '';

        if ($request->head_approval_status == 2) {
            $validator = Validator::make($request->all(), [

                'head_application_id' => 'required',
                'head_approval_status' => 'required',
                'head_approval_remarks' => 'required',
                'head_approving_as' => 'required',

            ]);
        } else {
            $validator = Validator::make($request->all(), [

                'head_application_id' => 'required',
                'head_approval_status' => 'required',
                'head_approving_as' => 'required',

            ]);
        }

        if ($validator->passes()) {
            $application_details = Applications::where('id', $request->head_application_id)->where('logdel', 0)->get();

            if (count($application_details) > 0) {
                $status = 0;

                if ($application_details[0]->for_group == 1) {
                    if ($application_details[0]->status == 2) {
                        if ($request->head_approval_status == 1) {
                            $status = 4;
                        } else {
                            $status = 5;
                        }
                    } else {
                        $approval_count = HeadApprovals::where('application_id', $request->head_application_id)->where('head_approval_status', 1)->where('logdel', 0)->count();

                        if ($approval_count == 2 && $request->head_approval_status == 1) {
                            $status = 6;
                        } else if ($approval_count == 1 && $request->head_approval_status == 1) {
                            $status = 4;
                        } else {
                            $status = 11;
                        }
                    }
                } else {
                    if ($request->head_approval_status == 1) {
                        $status = 6;
                    } else {
                        $status = 5;
                    }
                }

                try {
                    HeadApprovals::insert([
                        'application_id' => $request->head_application_id,
                        'approver_id' => $_SESSION['rapidx_user_id'],
                        'head_approval_status' => $request->head_approval_status,
                        'approving_as' => $request->head_approving_as,
                        'head_approval_remarks' => $request->head_approval_remarks,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'logdel' => 0,
                    ]);

                    Applications::where('id', $request->head_application_id)->update([

                        'status' => $status,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    return response()->json(['result' => 1, 'application_id' => $request->head_application_id]);
                } catch (\Exception $e) {
                    DB::rollback();
                    // throw $e;
                    return response()->json(['result' => $e]);
                }
            } else {
                return response()->json(['result' => 2]);
            }
        } else {
            return response()->json(['result' => 0, 'error' => $validator->messages()]);
        }
    }

    public function submit_new_dcc_validation(Request $request)
    {
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

                if ($request->dcc_validation_judgement == 1) {
                    $status = 9;
                } else if ($request->dcc_validation_judgement == 2) {
                    $status = 7;
                } else {
                    $status = 8;
                }

                Applications::where('id', $request->dcc_application_id)->update([

                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                if ($status == 8) {
                    HeadApprovals::where('application_id', $request->dcc_application_id)->where('head_approval_status', 1)->update([

                        'head_approval_status' => 3,
                        'updated_at' => date('Y-m-d H:i:s'),

                    ]);
                }

                return response()->json(['result' => 1, 'application_id' => $request->dcc_application_id]);
            } catch (\Exception $e) {
                DB::rollback();
                // throw $e;
                return response()->json(['result' => $e]);
            }
        } else {
            return response()->json(['result' => 0, 'error' => $validator->messages()]);
        }
    }

    public function load_new_affected_documents_table(Request $request)
    {
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

    public function load_new_dcc_validations_table(Request $request)
    {
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

                //$result = $validation->dcc_checkpoint_alignment;

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

                //$result = $validation->dcc_checkpoint_standard;

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

    public function load_new_head_approvals_table(Request $request)
    {
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

    public function load_new_qs_validations_table(Request $request)
    {
        $validations = QSValidations::with(['application_details'])->where('application_id', $request->application_id)->where('logdel', 0)/*->orderBy('created_at','desc')*/->get();

        return DataTables::of($validations)
            ->addColumn('validation_datetime', function ($validation) {

                $result = $validation->created_at;

                return $result;
            })
            ->addColumn('inspector', function ($validation) {

                $result = $validation->application_details->qs_inspector_details->name;

                return $result;
            })
            ->addColumn('validation_status', function ($validation) {

                switch ($validation->qs_validation_status) {
                    case 1: {
                            $result = "APPROVED";

                            break;
                        }
                    case 2: {
                            $result = "DISAPPROVED";

                            break;
                        }

                    default: {
                            $result = "---";

                            break;
                        }
                }

                return $result;
            })
            ->addColumn('validation_remarks', function ($validation) {

                $result = $validation->qs_validation_remarks;

                return $result;
            })
            ->make(true);
    }

    public function load_new_document_revisions_table(Request $request)
    {
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

    public function submit_new_edit_application(Request $request)
    {
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

                    $current_status = $application_details[0]->status;
                    $status = 0;

                    switch ($current_status) {
                        case 1: {
                                $status = 1;

                                break;
                            }
                        case 2: {
                                $status = 1;

                                break;
                            }
                        case 3: {
                                $status = 1;

                                break;
                            }
                        case 4: {
                                $status = 4;

                                break;
                            }
                        case 5: {
                                $status = 2;

                                break;
                            }
                        case 6: {
                                $status = 6;

                                break;
                            }
                        case 7: {
                                $status = 6;

                                break;
                            }
                        case 8: {
                                $status = 1;

                                break;
                            }
                        case 11: {
                                $status = 4;

                                break;
                            }
                        default: {
                                $status = $application_details[0]->status;

                                break;
                            }
                    }

                    if (isset($request->edit_attachment)) {
                        $generated_filename = "aidrc_attachment_" . date('YmdHis');
                        $original_filename = $request->file('edit_attachment')->getClientOriginalName();
                        $file_extension = $request->file('edit_attachment')->getClientOriginalExtension();
                        $aidrc_filename = $generated_filename . "." . $file_extension;

                        Storage::putFileAs('public/file_attachments', $request->edit_attachment, $aidrc_filename);

                        Applications::where('id', $request->view_application_id)->update([

                            'document_category' => $request->view_doc_category,
                            'document_name' => $request->view_doc_title,
                            'originator_remarks' => $request->view_remarks,
                            'aidrc_filename' => $aidrc_filename,
                            'original_filename' => $original_filename,

                            'application_section_head' => $request->view_section_head_approver,

                            'application_prod_head' => $request->view_production_head,

                            'application_qc_head' => $request->view_qc_head,

                            'application_eng_head' => $request->view_eng_head,

                            'qs_inspector' => $request->view_qs_inspector,

                            'approver_priority' => $request->view_approver_priority,

                            'updated_at' => date('Y-m-d H:i:s'),
                            'status' => $status,

                        ]);
                    } else {
                        Applications::where('id', $request->view_application_id)->update([

                            'document_category' => $request->view_doc_category,
                            'document_name' => $request->view_doc_title,
                            'originator_remarks' => $request->view_remarks,

                            'application_section_head' => $request->view_section_head_approver,

                            'application_prod_head' => $request->view_production_head,

                            'application_qc_head' => $request->view_qc_head,

                            'application_eng_head' => $request->view_eng_head,

                            'qs_inspector' => $request->view_qs_inspector,

                            'approver_priority' => $request->view_approver_priority,


                            'updated_at' => date('Y-m-d H:i:s'),
                            'status' => $status,

                        ]);
                    }

                    return response()->json(['result' => 1, 'application_id' => $request->view_application_id]);
                } catch (\Exception $e) {
                    DB::rollback();
                    // throw $e;
                    return response()->json(['result' => $e]);
                }
            } else {
                return response()->json(['result' => 2]);
            }
        } else {
            return response()->json(['result' => 0, 'error' => $validator->messages()]);
        }
    }

    public function load_application_logs(Request $request)
    {
        $application = Applications::with(['qs_validation_details', 'head_approval_details', 'dcc_validation_details', 'application_details'])->where('id', $request->application_id)->where('logdel', 0)->get();

        return $application;
    }

    public function submit_affected_document(Request $request)
    {
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

    public function load_originator_affected_documents_table(Request $request)
    {
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

    public function load_qs_validation_checkpoints_table(Request $request)
    {
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

    public function retrieve_documents_for_approval(Request $request)
    {
        $applications = Applications::where('id', $request->application_id)->where('logdel', 0)->get();

        $array_documents = [];

        $array_documents = AffectedDocuments::with(['application_details'])->where('application_id', $request->application_id)->where('logdel', 0);

        switch ($request->approving_as) {
            case 1: {
                    if ($applications[0]->approver_priority == 1) {
                        $array_documents->where('approver_type', 1);
                    } else {
                        $array_documents->where('approver_type', 999);
                    }

                    break;
                }

            case 2: {
                    if ($applications[0]->approver_priority == 2) {
                        $array_documents->whereIn('approver_type', [1, 3]);
                    } else {
                        $array_documents->where('approver_type', 3);
                    }

                    break;
                }

            case 3: {
                    if ($applications[0]->approver_priority == 3) {
                        $array_documents->whereIn('approver_type', [1, 2, 4]);
                    } else {
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

    public function retrive_documents_for_qs_inspection(Request $request)
    {
        $array_documents = [];

        $array_documents = AffectedDocuments::with(['application_details'])->where('application_id', $request->application_id)->whereIn('approver_type', [2, 3, 4])->where('document_status', 1)->where('logdel', 0);

        $array_documents = $array_documents->pluck('id')->toArray();

        if (count($array_documents) > 0) {
            return response()->json(['result' => 1, 'array_documents' => $array_documents]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function load_approver_checkpoints_table(Request $request)
    {
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

                if ($document->document_status == 1) {
                    $result = '<button type="button" class="btn btn-sm btn-primary btn-edit-affected-document-details" affected-doc-id="' . $document->id . '" data-toggle="modal" data-target="#modalEditaffectedDocumentDetails" title="Edit Affected Document Details"><i class="fa fa-edit"></i></button>';
                } else {
                    $result = '<strong>DISAPPROVED</strong>';
                }



                return $result;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function link_checkpoints_to_application(Request $request)
    {
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

    public function load_affected_documents_details(Request $request)
    {
        $affected_document_details = AffectedDocuments::where('id', $request->affected_doc_id)->where('logdel', 0)->get();

        if (count($affected_document_details) > 0) {
            return response()->json(['result' => 1, 'affected_document_details' => $affected_document_details]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function submit_disapprove_affected_document(Request $request)
    {
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

    public function submit_edit_affected_document(Request $request)
    {
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

    public function send_new_mailer(Request $request)
    {
        $application = Applications::with(['originator_details', 'section_head_details', 'prod_head_details', 'qc_head_details', 'eng_head_details', 'qs_inspector_details', 'department_details', 'dcc_validation_details' => function ($query3) {

            $query3->where('logdel', 0)->orderBy('created_at', 'desc');
        }, 'dcc_validation_details.dcc_validator_details', 'head_approval_details' => function ($query) {

            $query->where('logdel', 0)->orderBy('created_at', 'desc');
        }, 'qs_validation_details' => function ($query2) {

            $query2->where('logdel', 0)->orderBy('created_at', 'desc');
        }, 'application_revision_details'])->where('id', $request->application_id)->where('logdel', 0)->get();

        $data = ['application' => $application];

        if (count($application) > 0) {
            switch ($application[0]->status) {
                case 1: {
                        if ($application[0]->for_group == 1) {
                            $send_to = [$application[0]->qs_inspector_details->email];
                            $send_cc = [$application[0]->originator_details->email];

                            Mail::send('mail.aidrc_qs_inspection', $data, function ($message) use ($send_to, $send_cc) {

                                $message->to($send_to)
                                    ->cc($send_cc)
                                    ->bcc('cdcasuyon@pricon.ph')
                                    ->subject('AIDRC: Application for QS Validation');
                            });
                        } else {
                            $send_to = [$application[0]->section_head_details->email];
                            $send_cc = [$application[0]->originator_details->email];

                            Mail::send('mail.aidrc_new_application', $data, function ($message) use ($send_to, $send_cc) {

                                $message->to($send_to)
                                    ->cc($send_cc)
                                    ->bcc('cdcasuyon@pricon.ph')
                                    ->subject('AIDRC: Application for Approval');
                            });
                        }

                        break;
                    }
                case 2: {
                        switch ($application[0]->approver_priority) {
                            case 1: {
                                    $send_to = [$application[0]->prod_head_details->email];

                                    break;
                                }
                            case 2: {
                                    $send_to = [$application[0]->qc_head_details->email];

                                    break;
                                }
                            case 3: {
                                    $send_to = [$application[0]->eng_head_details->email];

                                    break;
                                }
                            default: {
                                    $send_to = [$application[0]->originator_details->email];

                                    break;
                                }
                        }


                        $send_cc = [$application[0]->originator_details->email, $application[0]->qs_inspector_details->email];

                        Mail::send('mail.aidrc_new_application', $data, function ($message) use ($send_to, $send_cc) {

                            $message->to($send_to)
                                ->cc($send_cc)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRC: Application for Approval');
                        });

                        break;
                    }
                case 3: {
                        //DISAPPROVED BY QS VALIDATOR
                        $send_to = [$application[0]->originator_details->email];
                        $send_cc = [$application[0]->qs_inspector_details->email];

                        Mail::send('mail.aidrc_qs_inspection_disapproved', $data, function ($message) use ($send_to, $send_cc) {

                            $message->to($send_to)
                                ->cc($send_cc)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRC: Application Disapproved by QS Staff');
                        });

                        break;
                    }
                case 4: {
                        switch ($application[0]->approver_priority) {
                            case 1: {
                                    //$send_to = [$application[0]->prod_head_details->email];

                                    $send_to = [$application[0]->qc_head_details->email, $application[0]->eng_head_details->email];

                                    break;
                                }
                            case 2: {
                                    //$send_to = [$application[0]->qc_head_details->email];

                                    $send_to = [$application[0]->prod_head_details->email, $application[0]->eng_head_details->email];

                                    break;
                                }
                            case 3: {
                                    //$send_to = [$application[0]->eng_head_details->email];

                                    $send_to = [$application[0]->prod_head_details->email, $application[0]->qc_head_details->email];

                                    break;
                                }
                            default: {
                                    $send_to = [$application[0]->originator_details->email];

                                    break;
                                }
                        }

                        $send_cc = [$application[0]->originator_details->email, $application[0]->qs_inspector_details->email];

                        Mail::send('mail.aidrc_new_application', $data, function ($message) use ($send_to, $send_cc) {

                            $message->to($send_to)
                                ->cc($send_cc)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRC: Application for Approval');
                        });

                        break;
                    }
                case 5: {
                        $send_to = [$application[0]->originator_details->email];
                        $send_cc = [$application[0]->head_approval_details[0]->approver_details->email];

                        Mail::send('mail.aidrc_disapproved', $data, function ($message) use ($send_to, $send_cc) {

                            $message->to($send_to)
                                ->cc($send_cc)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRC: Application Disapproved');
                        });


                        break;
                    }
                case 6: {
                        $send_to = ['nvlim@pricon.ph', 'dmmarmol@pricon.ph', 'mdalcaraz@pricon.ph'];

                        //$send_to = ['cdcasuyon@pricon.ph'];

                        Mail::send('mail.aidrc_dcc_validation', $data, function ($message) use ($send_to) {
                            $message->to($send_to)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRC: DCC Validation');
                        });

                        break;
                    }
                case 7: {
                        $send_to = [$application[0]->originator_details->email];

                        $send_cc = [$application[0]->dcc_validation_details[0]->dcc_validator_details->email];

                        Mail::send('mail.aidrc_minor_revisions', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)
                                ->cc($send_cc)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRC: Application for Minor Revisions');
                        });


                        break;
                    }
                case 8: {
                        $send_to = [$application[0]->originator_details->email];

                        $send_cc = [$application[0]->dcc_validation_details[0]->dcc_validator_details->email];

                        Mail::send('mail.aidrc_major_revisions', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)
                                ->cc($send_cc)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRC: Application for Major Revisions');
                        });

                        break;
                    }
                case 9: {
                        $send_to = [$application[0]->originator_details->email];


                        if ($application[0]->for_group == 1) {
                            $send_cc = [$application[0]->prod_head_details->email, $application[0]->qc_head_details->email, $application[0]->eng_head_details->email, $application[0]->qs_inspector_details->email, $application[0]->dcc_validation_details[0]->dcc_validator_details->email];
                        } else {
                            $send_cc = [$application[0]->section_head_details->email, $application[0]->dcc_validation_details[0]->dcc_validator_details->email];
                        }

                        Mail::send('mail.aidrc_validated', $data, function ($message) use ($send_to, $send_cc) {

                            $message->to($send_to)
                                ->cc($send_cc)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRC: Application Validated!');
                        });

                        break;
                    }
                case 10: {
                        $send_to = [$application[0]->originator_details->email];

                        if ($application[0]->for_group == 1) {
                            $send_cc = [$application[0]->prod_head_details->email, $application[0]->qc_head_details->email, $application[0]->eng_head_details->email, $application[0]->qs_inspector_details->email];
                        } else {
                            $send_cc = [$application[0]->section_head_details->email];
                        }

                        Mail::send('mail.aidrc_cancelled', $data, function ($message) use ($send_to, $send_cc) {
                            $message->to($send_to)
                                ->cc($send_cc)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRC: Application Cancelled');
                        });

                        break;
                    }
                case 11: {
                        $send_to = [$application[0]->originator_details->email];
                        $send_cc = [$application[0]->head_approval_details[0]->approver_details->email];

                        Mail::send('mail.aidrc_disapproved', $data, function ($message) use ($send_to, $send_cc) {

                            $message->to($send_to)
                                ->cc($send_cc)
                                ->bcc('cdcasuyon@pricon.ph')
                                ->subject('AIDRC: Application Disapproved');
                        });


                        break;
                    }
                default: {
                        break;
                    }
            }

            return response()->json(['result' => 1]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function new_cancel_application(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        try {
            Applications::where('id', $request->application_id)->update([
                'status' => 10,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return response()->json(['result' => 1]);
        } catch (\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }

    public function remove_document(Request $request)
    {
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

    public function submit_approver_type(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        $validator = '';

        $validator = Validator::make($request->all(), [
            'rapidx_user' => 'required',
            'approver_type' => 'required',
        ]);

        if ($validator->passes()) {
            try {
                SectionHeads::insert([

                    'rapidx_user_id' => $request->rapidx_user,
                    'approver_type' => $request->approver_type,
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
        } else {
            return response()->json(['result' => 0, 'error' => $validator->messages()]);
        }
    }

    public function load_prod_head_list(Request $request)
    {
        $section_heads = SectionHeads::with(['rapidx_user_details'])->where('approver_type', 1)->where('logdel', 0)->get();

        if (count($section_heads) > 0) {
            return response()->json(['result' => 1, 'section_heads' => $section_heads]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function load_qc_head_list(Request $request)
    {
        $section_heads = SectionHeads::with(['rapidx_user_details'])->where('approver_type', 2)->where('logdel', 0)->get();

        if (count($section_heads) > 0) {
            return response()->json(['result' => 1, 'section_heads' => $section_heads]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function load_eng_head_list(Request $request)
    {
        $section_heads = SectionHeads::with(['rapidx_user_details'])->where('approver_type', 3)->where('logdel', 0)->get();

        if (count($section_heads) > 0) {
            return response()->json(['result' => 1, 'section_heads' => $section_heads]);
        } else {
            return response()->json(['result' => 2]);
        }
    }

    public function load_section_head_list(Request $request)
    {
        $section_heads = SectionHeads::with(['rapidx_user_details'])->where('logdel', 0)->get();

        if (count($section_heads) > 0) {
            return response()->json(['result' => 1, 'section_heads' => $section_heads]);
        } else {
            return response()->json(['result' => 2]);
        }
    }
}
