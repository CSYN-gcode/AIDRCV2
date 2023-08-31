<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AffectedDocumentsExport implements FromView, ShouldAutoSize
{
   /**
    * @return \Illuminate\Support\Collection
    */
   /* public function collection()
    {
        //
    }*/

    protected $affected_documents;

    function __construct($affected_documents)
    {
    	$this->affected_documents = $affected_documents;
    }

    public function view(): View
    {
    	$affected_documents = $this->affected_documents;

    	return view('exports.affected_documents_export',compact('affected_documents'));
    }
}
