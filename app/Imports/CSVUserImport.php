<?php

namespace App\Imports;

use App\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Auth;

class CSVUserImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User([
            'name' => $row[0],
            'username' => $row[1],
            'email' => $row[2],
            'employee_id' => $row[3],
            'position' => $row[4],
            'oqc_stamp' => $row[5],
        ]);
    }
}
