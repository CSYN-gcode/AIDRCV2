<?php

use Illuminate\Database\Seeder;
use App\Model\UserLevel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserLevelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        date_default_timezone_set('Asia/Manila');
        DB::beginTransaction();

        try{
	        UserLevel::create([
	        	'name' => 'Super User',
	        	'status' => 1,
	        	'updated_at' => date('Y-m-d H:i:s'),
	        	'created_at' => date('Y-m-d H:i:s')
	        ]);

	        UserLevel::create([
	        	'name' => 'Administrator',
	        	'status' => 1,
	        	'updated_at' => date('Y-m-d H:i:s'),
	        	'created_at' => date('Y-m-d H:i:s')
	        ]);

	        UserLevel::create([
	        	'name' => 'User',
	        	'status' => 1,
	        	'updated_at' => date('Y-m-d H:i:s'),
	        	'created_at' => date('Y-m-d H:i:s')
	        ]);

	        DB::commit();
	    }
	    catch(\Exception $e) {
            DB::rollback();
            // throw $e;
        }
    }
}
