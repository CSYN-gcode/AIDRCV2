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
use App\Model\RapidXDepartment;
use App\Model\AccessLevel;
use App\Model\DccValidations;

use App\Model\AcdcsPendingDocuments;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AffectedDocumentsExport;
use App\Exports\ApplicationsExport;

use DateTime;

class MasterListController extends Controller
{
    public function submit_dcc_edit_document(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        $validator = '';

        $validator = Validator::make($request->all(), [
            'view_doc_no' => 'required',
            'view_doc_title' => 'required',
        ]);

        if($validator->passes())
        {
            try
            {
                Applications::where('id', $request->view_application_id)->update([
                    'document_number' => $request->view_doc_no,
                    'document_name' => $request->view_doc_title,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                return response()->json(['result' => 1]);
            }
            catch(\Exception $e) {
                DB::rollback();
                // throw $e;
                return response()->json(['result' => $e]);
            }
        }
        else
        {
            return response()->json(['result' => 0, 'error' => $validator->messages()]);
        }
    }

    public function load_acdcs_master_list(Request $request)
    {
    	$applications = Applications::with(['department_details','originator_details','dcc_validation_details' => function($query){

    		$query->where('logdel',0)->orderBy('created_at','desc');

    	}, 'dcc_validation_details.dcc_validator_details','control_details'])->where('logdel',0)->orderBy('created_at','desc');


        if(isset($request->check_created_date))
        {
            $start_date = explode(' - ', $request->created_date)[0] . " 00:00:00";
            $end_date = explode(' - ', $request->created_date)[1] . " 23:59:59";

            $applications = $applications->whereBetween('created_at',[$start_date, $end_date]);
        }

        if(isset($request->check_section_department))
        {
             $applications = $applications->whereIn('department', $request->section_department);
        }

        if(isset($request->check_for_group))
        {
            $applications = $applications->where('for_group', $request->for_group);
        }

        if(isset($request->check_originator))
        {
            $applications = $applications->whereIn('application_originator', $request->originator);
        }

        if(isset($request->check_approver))
        {
            $applications = $applications->whereIn('application_section_head', $request->approver);
        }

        if(isset($request->check_qs_inspector))
        {
            $applications = $applications->whereIn('application_qs_inspector', $request->qs_inspector);
        }

        $applications = $applications->get();

        $applications_final = collect($applications)->flatten(1);
        $arrayApplications = [];

        //return $applications_final;

        if(isset($request->check_document_status))
        {
            if($request->document_status == 2)
            {
                for($i = 0; $i < count($applications_final); $i++)
                {
                     if($applications_final[$i]->control_details != null)
                    {
                        $search_revision_no = $applications_final[$i]->document_revision_number;

                        if($applications_final[$i]->control_details->rev_no == $search_revision_no)
                        {
                            $arrayApplications[] = $applications_final[$i]->id;
                        }
                    }
                }
            }
            else if($request->document_status == 1)
            {
                 for($i = 0; $i < count($applications_final); $i++)
                {
                    if($applications_final[$i]->control_details == null)
                    {
                        $arrayApplications[] = $applications_final[$i]->id;
                    }
                    else
                    {
                         $search_revision_no = $applications_final[$i]->document_revision_number;

                        if($applications_final[$i]->control_details->rev_no != $search_revision_no)
                        {
                            $arrayApplications[] = $applications_final[$i]->id;
                        }
                    }
                }
            }
            else
            {
                for($i = 0; $i < count($applications_final); $i++)
                {
                    $arrayApplications[] = $applications_final[$i]->id;
                }
            }
        }
        else
        {
            for($i = 0; $i < count($applications_final); $i++)
            {
                $arrayApplications[] = $applications_final[$i]->id;
            }
        }

        $applications2 = Applications::with(['department_details','originator_details','dcc_validation_details' => function($query){

            $query->where('logdel',0)->orderBy('created_at','desc');

        }, 'dcc_validation_details.dcc_validator_details','control_details' => function($query2){

            $query2->where('logdel',0);

        }])->whereNotIn('status',[10])->where('logdel',0)->orderBy('created_at','desc')->whereIn('id', $arrayApplications)->get();

    	return DataTables::of($applications2)
    	->addColumn('hidden_id',function($application){

    		$result = $application->id;

    		return $result;
    	})
    	->addColumn('aidrc_control_number', function($application){

    		$result = $application->aidrc_control_number;

    		return $result;
    	})
    	->addColumn('section_department', function($application){

    		$result = $application->department_details->department_name;

    		return $result;
    	})
    	->addColumn('originator', function($application){

    		$result = $application->originator_details->name;

    		return $result;
    	})
    	->addColumn('application_datetime', function($application){

    		$result = $application->created_at;

    		return $result;
    	})
    	->addColumn('document_number', function($application){

    		$result = '';

    		if($application->document_number != null)
    		{
    			$result = $application->document_number;
    		}
    		else
    		{
    			$result = "";
    		}

    		return $result;
    	})
    	->addColumn('document_name', function($application){

    		$result = '';

    		if($application->document_name != null)
    		{
    			$result = $application->document_name;
    		}
    		else
    		{
    			$result = "";
    		}

    		return $result;
    	})
    	->addColumn('document_revision_number', function($application){

    		$result = '';

    		if($application->document_revision_number != null)
    		{
    			$result = $application->document_revision_number;
    		}
    		else
    		{
    			$result = "0";
    		}

    		return $result;
    	})
    	->addColumn('dcc_validation_date', function($application){

    		if($application->status <= 9)
    		{
                if(count($application->dcc_validation_details) > 0)
                {
                    $result = $application->dcc_validation_details[0]->created_at;
                }
                else
                {
                    $result = "---";
                }
    		}
    		else
    		{
    			$result = "---";
    		}

    		return $result;
    	})
    	->addColumn('dcc_validator', function($application){

    		if($application->status <= 9)
    		{
                if(count($application->dcc_validation_details) > 0)
                {
    			     $result = $application->dcc_validation_details[0]->dcc_validator_details->name;
                }
                else
                {
                    $result = "---";
                }
    		}
    		else
    		{
    			$result = "---";
    		}

    		return $result;
    	})
        ->addColumn('turnaround_time', function($application){



            if($application->status <= 9)
            {
                if(count($application->dcc_validation_details) > 0)
                {
                    $fdate = $application->created_at;
                    $tdate = $application->dcc_validation_details[0]->created_at;
                   /* $tdate = $application->control_details->date_time_created;*/
                    $datetime1 = new DateTime($fdate);
                    $datetime2 = new DateTime($tdate);
                    $interval = $datetime1->diff($datetime2);
                    $result = $interval->format('%a');//now do whatever you like with $days
                    $result .= " day(s)";
                }
                else
                {
                    $result = "---";
                }
            }
            else
            {
                $result = '---';
            }

            return $result;

        })
    	->addColumn('status', function($application){

    		$result = '';
            $search_revision_no = $application->document_revision_number;

            if($application->control_details != null){
                if($application->control_details->rev_no == $search_revision_no){
                    $result = "CONTROLLED";
                }else{
                    $result = "NOT CONTROLLED";
                }
            }else{
                $result = "NOT CONTROLLED";
            }

    		return $result;
    	})
    	->addColumn('document_control_date', function($application){

    		$result = "---";
            $search_revision_no = $application->document_revision_number;

            if($application->control_details != null){
                if($application->control_details->rev_no == $search_revision_no){
                    $result = $application->control_details->date_time_created;
                    //$result = $application->control_details->lastupdate;
                }else{
                    $result = "---";
                }
            }else{
                $result = "---";
            }

    		return $result;
    	})
    	->make(true);
    }

    public function load_affected_documents_master_list(Request $request)
    {
        $affected_documents = AffectedDocuments::with(['application_details' => function($query2){

            $query2->where('logdel',0);

        }
        ,'application_details.dcc_validation_details' => function($query){

            $query->where('logdel',0)->orderBy('created_at','desc');

        },'application_details.dcc_validation_details.dcc_validator_details','pic_details','control_details'])->where('logdel',0)->whereIn('approver_type',[1,2,3,4])->whereIn('document_status',[1]);

         if(isset($request->check_created_date))
        {
            $start_date = explode(' - ', $request->created_date)[0] . " 00:00:00";
            $end_date = explode(' - ', $request->created_date)[1] . " 23:59:59";

            $affected_documents = $affected_documents->whereBetween('document_revision_due_date',[$start_date, $end_date]);
        }

        if(isset($request->check_document_type))
        {
             $affected_documents = $affected_documents->where('approver_type', $request->document_type);
        }

  /*      if(isset($request->check_document_approver))
        {
            $affected_documents = $affected_documents->whereIn('affected_document_approver', $request->document_approver);
        }*/

        if(isset($request->check_person_in_charge))
        {
            $affected_documents = $affected_documents->whereIn('person_in_charge', $request->person_in_charge);
        }

        $affected_documents = $affected_documents->get();
        $affected_documents_final = collect($affected_documents)->flatten(1);
        $array_affected_documents = [];


        if(isset($request->check_document_status))
        {
            if($request->document_status == 2)
            {
                for($i = 0; $i < count($affected_documents_final); $i++)
                {
                     if($affected_documents_final[$i]->control_details != null)
                    {
                        $search_revision_no = $affected_documents_final[$i]->document_revision_number;

                        if($affected_documents_final[$i]->control_details->rev_no == $search_revision_no)
                        {
                            $array_affected_documents[] = $affected_documents_final[$i]->id;
                        }
                    }
                }
            }
            else if($request->document_status == 1)
            {
                 for($i = 0; $i < count($affected_documents_final); $i++)
                {
                    if($affected_documents_final[$i]->control_details == null)
                    {
                        $array_affected_documents[] = $affected_documents_final[$i]->id;
                    }
                    else
                    {
                         $search_revision_no = $affected_documents_final[$i]->document_revision_number;

                        if($affected_documents_final[$i]->control_details->rev_no != $search_revision_no)
                        {
                            $array_affected_documents[] = $affected_documents_final[$i]->id;
                        }
                    }
                }
            }
            else
            {
                for($i = 0; $i < count($affected_documents_final); $i++)
                {
                    $array_affected_documents[] = $affected_documents_final[$i]->id;
                }
            }
        }
        else
        {
            for($i = 0; $i < count($affected_documents_final); $i++)
            {
                $array_affected_documents[] = $affected_documents_final[$i]->id;
            }
        }

       /* for($i = 0; $i < count($affected_documents); $i++)
        {
            if($affected_documents[$i]->application_details != null)
            {
                array_push($array_affected_documents, $affected_documents[$i]->id);
            }
        }*/

        $affected_documents_final2 = AffectedDocuments::with(['application_details','control_details'])->whereIn('id', $array_affected_documents)->where('application_id', '!=', null)->get();

        return DataTables::of($affected_documents_final2)
        ->addColumn('hidden_id',function($document){

            $result = $document->id;

            return $result;

        })
        ->addColumn('aidrc_control_number',function($document){

            $result = $document->application_details->aidrc_control_number;

            //$result = "test";

            return $result;
        })
        ->addColumn('approver_type',function($document){

            $result = "";

            switch($document->approver_type)
            {
                case 1:
                {
                    $result = "Affected Document";
                    break;
                }
                case 2:
                {
                    $result = "FMEA";
                    break;
                }
                case 3:
                {
                    $result = "Control Plan";
                    break;
                }
                case 4:
                {
                    $result = "Pre Production Checksheet";
                    break;
                }
                default:
                {
                    $result = "---";
                    break;
                }
            }

            return $result;
        })
        ->addColumn('document_number',function($document){

            $result = $document->document_number;

            return $result;
        })
        ->addColumn('document_name',function($document){

            $result = $document->document_name;

            return $result;
        })
        ->addColumn('revision_number',function($document){

            $result = $document->document_revision_number;

            return $result;
        })
        ->addColumn('revision_due_date',function($document){

            $result =  $document->document_revision_due_date;

            return $result;
        })
        ->addColumn('person_in_charge',function($document){

            if($document->person_in_charge != null)
            {
                $result = $document->pic_details->name;
            }
            else
            {
                $result = "---";
            }

            return $result;
        })
        ->addColumn('document_approver',function($document){

            //$result = $document->approver_details->name;
            $result = "---";

            return $result;
        })
        ->addColumn('dcc_in_charge',function($document){

            $result = "---";

            $search_revision_no = $document->document_revision_number;

            if($document->control_details != null)
            {
                if($document->control_details->rev_no == $search_revision_no)
                {
                    $result = $document->control_details->controller_details->name;
                }
                else
                {
                    $result = "---";
                }
            }
            else
            {
                $result = "---";
            }

            return $result;
        })
        ->addColumn('status',function($document){

            $result = "---";

            $search_revision_no = $document->document_revision_number;

            if($document->control_details != null)
            {
                if($document->control_details->rev_no == $search_revision_no)
                {
                    $result = "CONTROLLED";
                }
                else
                {
                    $result = "NOT CONTROLLED";
                }
            }
            else
            {
                $result = "NOT CONTROLLED";
            }

            return $result;
        })
        ->addColumn('document_control_date',function($document){

            $result = "---";

            $search_revision_no = $document->document_revision_number;

            if($document->control_details != null)
            {
                if($document->control_details->rev_no == $search_revision_no)
                {
                    $result = $document->control_details->date_time_created;
                }
                else
                {
                    $result = "---";
                }
            }
            else
            {
                $result = "---";
            }



            return $result;
        })
        ->make(true);
    }

    public function load_acdcs_pending_documents(Request $request)
    {
        $pending_documents = AcdcsPendingDocuments::where('logdel',0)->where('doc_status',0)->where('dcc_approver_status', 2)->count();

        return $pending_documents;
    }

    public function export_applications_report(Request $request)
    {
       $applications = Applications::with(['department_details','originator_details','dcc_validation_details' => function($query){

            $query->where('logdel',0)->orderBy('created_at','desc');

        }, 'dcc_validation_details.dcc_validator_details','control_details'])->where('logdel',0)->orderBy('created_at','desc');


        if(isset($request->check_created_date))
        {
            $start_date = explode(' - ', $request->created_date)[0] . " 00:00:00";
            $end_date = explode(' - ', $request->created_date)[1] . " 23:59:59";

            $applications->whereBetween('created_at',[$start_date, $end_date]);
        }

         if(isset($request->check_section_department))
        {
             $applications->whereIn('department', explode(",",$request->section_department));
        }

        if(isset($request->check_for_group))
        {
            $applications->where('for_group', $request->for_group);
        }

        if(isset($request->check_originator))
        {
            $applications->whereIn('application_originator', explode(",",$request->originator));
        }

        if(isset($request->check_approver))
        {
            $applications->whereIn('application_section_head', explode(",",$request->approver));
        }

        if(isset($request->check_qs_inspector))
        {
            $applications->whereIn('application_qs_inspector', explode(",",$request->qs_inspector));
        }

        $applications = $applications->get();

        $applications_final = collect($applications)->flatten(1);
        $arrayApplications = [];

        //return $applications_final;

        if(isset($request->check_document_status))
        {
            if($request->document_status == 2)
            {
                for($i = 0; $i < count($applications_final); $i++)
                {
                     if($applications_final[$i]->control_details != null)
                    {
                        $search_revision_no = $applications_final[$i]->document_revision_number;

                        if($applications_final[$i]->control_details->rev_no == $search_revision_no)
                        {
                            $arrayApplications[] = $applications_final[$i]->id;
                        }
                    }
                }
            }
            else if($request->document_status == 1)
            {
                 for($i = 0; $i < count($applications_final); $i++)
                {
                    if($applications_final[$i]->control_details == null)
                    {
                        $arrayApplications[] = $applications_final[$i]->id;
                    }
                    else
                    {
                         $search_revision_no = $applications_final[$i]->document_revision_number;

                        if($applications_final[$i]->control_details->rev_no != $search_revision_no)
                        {
                            $arrayApplications[] = $applications_final[$i]->id;
                        }
                    }
                }
            }
            else
            {
                for($i = 0; $i < count($applications_final); $i++)
                {
                    $arrayApplications[] = $applications_final[$i]->id;
                }
            }
        }
        else
        {
            for($i = 0; $i < count($applications_final); $i++)
            {
                $arrayApplications[] = $applications_final[$i]->id;
            }
        }

        $applications2 = Applications::with(['affected_documents_details' => function($query2){

            $query2->where('logdel',0);

        },
            'department_details','originator_details','dcc_validation_details' => function($query){

            $query->where('logdel',0)->orderBy('created_at','desc');

        }, 'dcc_validation_details.dcc_validator_details','control_details'])->whereNotIn('status',[3,5,10,11])->where('logdel',0)->orderBy('created_at','desc')->whereIn('id', $arrayApplications)->get();

        $title = 'AIDRC Applications - ' . date('Y-m-d') . ".xlsx";


        //return $request->formData;

        try
        {
            if(count($applications2) > 0)
            {
                //return view('exports.applications_export')->with(compact('applications'));

                return Excel::download(new ApplicationsExport($applications2), $title);
            }
            else
            {
                echo "<script>";
                echo "alert('No data found');";
                echo "window.close();";
                echo "</script>";
            }
        }
        catch(\Exception $e) {

                echo "<script>";
                echo "alert('Error Exporting!');";
                echo "window.close();";
                echo "</script>";

        }
    }

    public function submit_edit_documents_dcc(Request $request)
    {
        session_start();
        date_default_timezone_set('Asia/Manila');

        try
        {
            Applications::where('id', $request->ml_hidden_id)->update([

                'document_number' => $request->ml_doc_no,
                'document_name' => $request->ml_doc_title,
                'document_revision_number' => $request->ml_doc_rev_no,
                'updated_at' => date('Y-m-d H:i:s'),

            ]);

            return response()->json(['result' => 1]);
        }
        catch(\Exception $e) {
            DB::rollback();
            // throw $e;
            return response()->json(['result' => $e]);
        }
    }


    public function export_affected_documents_report(Request $request)
    {
       $affected_documents = AffectedDocuments::with(['application_details' => function($query2){

            $query2->where('logdel',0);

        }
        ,'application_details.dcc_validation_details' => function($query){

            $query->where('logdel',0)->orderBy('created_at','desc');

        },'application_details.dcc_validation_details.dcc_validator_details','pic_details','control_details'])->where('logdel',0)->whereIn('approver_type',[1,2,3])->whereIn('document_status',[1]);

         if(isset($request->check_created_date))
        {
            $start_date = explode(' - ', $request->created_date)[0] . " 00:00:00";
            $end_date = explode(' - ', $request->created_date)[1] . " 23:59:59";

            $affected_documents = $affected_documents->whereBetween('document_revision_due_date',[$start_date, $end_date]);
        }

        if(isset($request->check_document_type))
        {
             $affected_documents = $affected_documents->where('approver_type', $request->document_type);
        }

        /*if(isset($request->check_document_approver))
        {
            $affected_documents = $affected_documents->whereIn('affected_document_approver', explode(",",$request->document_approver));
        }*/

        if(isset($request->check_person_in_charge))
        {
            $affected_documents = $affected_documents->whereIn('person_in_charge', explode(",",$request->person_in_charge));
        }

        $affected_documents = $affected_documents->get();
        $affected_documents_final = collect($affected_documents)->flatten(1);
        $array_affected_documents = [];


        if(isset($request->check_document_status))
        {
            if($request->document_status == 2)
            {
                for($i = 0; $i < count($affected_documents_final); $i++)
                {
                     if($affected_documents_final[$i]->control_details != null)
                    {
                        $search_revision_no = $affected_documents_final[$i]->document_revision_number;

                        if($affected_documents_final[$i]->control_details->rev_no == $search_revision_no)
                        {
                            $array_affected_documents[] = $affected_documents_final[$i]->id;
                        }
                    }
                }
            }
            else if($request->document_status == 1)
            {
                 for($i = 0; $i < count($affected_documents_final); $i++)
                {
                    if($affected_documents_final[$i]->control_details == null)
                    {
                        $array_affected_documents[] = $affected_documents_final[$i]->id;
                    }
                    else
                    {
                         $search_revision_no = $affected_documents_final[$i]->document_revision_number;

                        if($affected_documents_final[$i]->control_details->rev_no != $search_revision_no)
                        {
                            $array_affected_documents[] = $affected_documents_final[$i]->id;
                        }
                    }
                }
            }
            else
            {
                for($i = 0; $i < count($affected_documents_final); $i++)
                {
                    $array_affected_documents[] = $affected_documents_final[$i]->id;
                }
            }
        }
        else
        {
            for($i = 0; $i < count($affected_documents_final); $i++)
            {
                $array_affected_documents[] = $affected_documents_final[$i]->id;
            }
        }

       /* for($i = 0; $i < count($affected_documents); $i++)
        {
            if($affected_documents[$i]->application_details != null)
            {
                array_push($array_affected_documents, $affected_documents[$i]->id);
            }
        }*/

        $affected_documents_final3 = AffectedDocuments::with(['application_details'])->whereIn('id', $array_affected_documents)->get();

        $affected_documents_final2 = collect($affected_documents_final3)->where('application_details', '!=', [])->flatten(1);

         $title = 'AIDRC Affected Documents - ' . date('Y-m-d') . ".xlsx";

        try
        {
            if(count($affected_documents_final2) > 0)
            {
                //return $affected_documents_final2;

                //return view('exports.affected_documents_export')->with(compact('affected_documents'));



                return Excel::download(new AffectedDocumentsExport($affected_documents_final2), $title);

            }
            else
            {
                echo "<script>";
                echo "alert('No data found');";
                echo "window.close();";
                echo "</script>";
            }
        }
        catch(\Exception $e) {

                echo "<script>";
                echo "alert('Error Exporting!');";
                echo "window.close();";
                echo "</script>";

        }

    }
}
