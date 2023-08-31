<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ApplicationsExport implements FromView, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
   /* public function collection()
    {
        //
    }*/

    protected $applications;

    function __construct($applications)
    {
    	$this->applications = $applications;
    }

    public function view(): View
	{
		$applications = $this->applications;

		return view('exports.applications_export',compact('applications'));
    }

}
