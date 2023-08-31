<?php

namespace App\Imports;

use App\Model\AssemblyLine;
use Maatwebsite\Excel\Concerns\ToModel;

class CSVAssemblyLineImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new AssemblyLine([
            'name' => $row[0],
        ]);
    }
}
