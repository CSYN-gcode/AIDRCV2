<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use DataTables;

use App\Model\AccessLevel;
use App\Model\RapidXUser;
use App\Model\RapidXDepartment;

use App\Model\Applications;
use App\Model\AffectedDocuments;

class AccessLevelController extends Controller
{
    public function load_rapidx_user_list(Request $request)
    {
    	$users = RapidXUser::where('user_stat', 1)->orderBy('name','asc')->whereNotIn('name',['Admin','Test QAD Admin Approver'])->get();

    	return response()->json(['users' => $users]);
    }

    public function load_pic_list(Request $request)
    {   
        $aidrc_users = AffectedDocuments::where('logdel',0)->whereIn('document_status',[1])->pluck('person_in_charge')->toArray();

        $array_final = array_unique($aidrc_users);

        $users = RapidXUser::where('user_stat', 1)->orderBy('name','asc')->whereIn('id',$array_final)->whereNotIn('name',['Admin','Test QAD Admin Approver'])->get();

        return response()->json(['users' => $users]);
    }

    public function load_originator_list(Request $request)
    {   
        $aidrc_users = Applications::where('logdel',0)->pluck('application_originator')->toArray();

        $array_final = array_unique($aidrc_users);

        $users = RapidXUser::where('user_stat', 1)->orderBy('name','asc')->whereIn('id',$array_final)->whereNotIn('name',['Admin','Test QAD Admin Approver'])->get();

        return response()->json(['users' => $users]);
    }

    public function load_approver_list(Request $request)
    {   
        $aidrc_users = Applications::where('logdel',0)->pluck('application_section_head')->toArray();

        $array_final = array_unique($aidrc_users);

        $users = RapidXUser::where('user_stat', 1)->orderBy('name','asc')->whereIn('id',$array_final)->whereNotIn('name',['Admin','Test QAD Admin Approver'])->get();

        return response()->json(['users' => $users]);
    }

    public function load_qs_inspector_list(Request $request)
    {   
        $aidrc_users = Applications::where('logdel',0)->pluck('qs_inspector')->toArray();

        $array_final = array_unique($aidrc_users);

        $users = RapidXUser::where('user_stat', 1)->orderBy('name','asc')->whereIn('id',$array_final)->whereNotIn('name',['Admin','Test QAD Admin Approver'])->get();

        return response()->json(['users' => $users]);
    }

    public function load_rapidx_user_list_sectionhead(Request $request)
    {
    	$users = AccessLevel::with(['rapidx_user_details' => function($query){

            $query->where('user_stat',1)->orderBy('name','asc');

        }])->whereIn('access_level',[3,6])->where('logdel', 0)->get();

    	return response()->json(['users' => $users]);
    }

    public function load_rapidx_user_list_qs_inspector(Request $request)
    {
        $users = AccessLevel::with(['rapidx_user_details'])->whereIn('access_level',[1,4])->where('logdel', 0)->get();

        return response()->json(['users' => $users]);
    }

    public function load_accesslevel_table(Request $request)
    {
    	$aidrc_users = AccessLevel::with(['rapidx_user_details'])->where('logdel',0)->get();

    	return DataTables::of($aidrc_users)
    	->addColumn('action',function($user){

    		$result = "---";

    		return $result;

    	})
    	->addColumn('username',function($user){
    		
    		$result = $user->rapidx_user_details->username;

    		return $result;

    	})
    	->addColumn('fullname',function($user){
    		
    		$result = $user->rapidx_user_details->name;

    		return $result;

    	})
    	->addColumn('access_level',function($user){
    		
    		switch($user->access_level)
    		{
    			case 1:
    			{
    				$result = 'Administrator';
    				break;
    			}
    			case 2:
    			{	
    				$result = 'QAD';
    				break;
    			}
    			case 3:
    			{
    				$result = 'Section Head';
    				break;
    			}
    			case 4:
    			{
    				$result = 'QS Inspector';
    				break;
    			}
    			case 5:
    			{
    				$result = 'Reqular User';
    				break;
    			}
                case 6:
                {
                    $result = 'Section Head with QAD Controls';
                    break;
                }
    			default:
    			{	
    				$result = 'Error!';
    				break;
    			}
    		}

    		return $result;
    	})
    	->rawColumns(['action'])
    	->make(true);
    }

    public function load_rapidx_department_list(Request $request)
    {
        $departments = RapidXDepartment::where('department_stat',1)->get();

        return response()->json(['departments' => $departments]);
    }

    public function submit_add_user(Request $request)
    {
    	session_start();

    	date_default_timezone_set('Asia/Manila');
        $added_by = $_SESSION['rapidx_user_id'];

        $data = $request->all();
        DB::beginTransaction();

        try
        {
            AccessLevel::insert([

                'rapidx_id' => $request->rapidx_user,
                'access_level' => $request->access_level,
                'created_at' => date('Y-m-d H:i:s'), 
                'updated_at' => date('Y-m-d H:i:s'), 
                'added_by' => $added_by,
                'logdel' => 0,
            ]);

            DB::commit();

            return response()->json(['result' => 1]);
        }
        catch(\Exception $e) {
                DB::rollback();
                // throw $e;
                return response()->json(['result' => $e]);
        }
    }
}
