<?php

namespace App\Imports;

use App\Model\Machine;
use Maatwebsite\Excel\Concerns\ToModel;
use Auth;

class CSVMachineImportUpdater implements ToModel
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
            'machine_code_number' => $row[2],
        ]);
    }
}
