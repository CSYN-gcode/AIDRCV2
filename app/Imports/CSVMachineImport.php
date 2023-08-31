<?php

namespace App\Imports;

use App\Model\Machine;
use Maatwebsite\Excel\Concerns\ToModel;
use Auth;

class CSVMachineImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Machine([
            'name' => $row[0],
            'barcode' => $row[1],
            // 'status' => 1,
            // 'created_by' => Auth::user()->id,
            // 'last_updated_by' => Auth::user()->id,
            // 'update_version' => 1,
            // 'updated_at' => date('Y-m-d H:i:s'),
            // 'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
