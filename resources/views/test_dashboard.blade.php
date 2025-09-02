@php
$layout = 'layouts.user_layout';
@endphp
@extends($layout)
@section('title', 'Dashboard')
@section('content_page')
    <style type="text/css">
        .modal-xl-custom {
            width: 95% !important;
            min-width: 90% !important;
        }

        .modal-xl1-custom {
            width: 70% !important;
            min-width: 70% !important;
        }

        .modal-pdf-custom {
            height: auto; /* Allow the modal to grow based on content */
            width: 60% !important;
            min-width: 60% !important;
            max-width: 90%; /* Adjust modal width as necessary */
        }

        /* PDF preview container should take up all the space inside the modal */
        #pdfPreviewContainer {
            position: relative;
            padding: 0;
            overflow: hidden;
        }

        /* Wrapper around the PDF to control sizing */
        #pdfPreviewWrapper {
            position: relative;
            width: 100%;
            height: 100%;
        }

        #pdfPreview {
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: flex-start; /* Or use center if you want vertical centering */
            overflow: auto;        /* Allows scroll if content is too big */
            max-width: 100%;       /* Optional: prevent overflow on wide screens */
            height: auto;          /* Let it expand based on content */
            overflow-y: auto;
            box-sizing: border-box;
        }

        #pdfPreview canvas {
            display: block;
            margin: 0 auto;
            max-width: none !important; /* Prevent auto-scaling */
            width: auto !important;
            height: auto !important;
        }

        #pdfEditPdfWrapper{
            position: relative;
            width: 100%;
            height: 100%;
        }

        #pdfEditPdfCanvas {
            overflow: auto;        /* Allows scroll if content is too big */
            max-width: 100%;       /* Optional: prevent overflow on wide screens */
            height: auto;          /* Let it expand based on content */
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: flex-start; /* Or use center if you want vertical centering */
            overflow: auto;
            box-sizing: border-box;
        }

        #pdfEditPdfCanvas canvas {
            display: block;
            margin: 0 auto;
            max-width: none !important; /* Prevent auto-scaling */
            width: auto !important;
            height: auto !important;
        }

        .previewTextOverlay {
            font-family: Arial, sans-serif;
            color: black;
        }

        .select2-container--bootstrap4 .select2-selection {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da !important;
            border-radius: 0.25rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
            min-height: calc(2.25rem + 2px);
        }

        .select2-container--bootstrap4 .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
        }

        .select2-container--bootstrap4.select2-container--focus .select2-selection {
            border-color: #80bdff !important;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
    </style>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>

                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">TEST DASHBOARD</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Filters:</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <input type="hidden" id="hidden_check_status" name="hidden_check_status" value="1">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend w-50">
                                                <div class="input-group-text">
                                                    <input type="checkbox" id="check_filter_section_dept">
                                                </div>
                                                <span class="input-group-text w-100"
                                                    id="basic-addon1">SECTION/DEPARTMENT</span>
                                            </div>
                                            <input type="hidden" id="hidden_check_section_dept" name="hidden_check_section_dept">
                                            <select class="form-control sel-rapidx-department-list-2" id="filter_department" name="filter_department[]" multiple disabled>
                                                <option value="0" selected disabled>-- Select One --</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend w-50">
                                                <div class="input-group-text">
                                                    <input type="checkbox" id="check_filter_originator">
                                                </div>
                                                <span class="input-group-text w-100" id="basic-addon1">ORIGINATOR</span>
                                            </div>
                                            <input type="hidden" id="hidden_check_originator" name="hidden_check_originator">
                                            <select class="form-control sel-originator-list" id="filter_originator" multiple name="filter_originator[]" disabled></select>
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <button type="button" class="btn btn-info" id="btnFilterTable" title="Load Filtered Table"><i class="fa fa-retweet"></i> Filter Table</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <input type="checkbox" id="check_filter_status">
                                <label for="check_filter_status">Load all applications for Approval</label>
                                <div class="float-sm-right">
                                    <button type="button" class="btn btn-sm btn-success" id="btnAddNewApplication" data-toggle="modal" data-target="#modalAddApplication" title="Add New AIDRC Application">
                                        <i class="fa fa-plus"></i> Add New Application
                                    </button>
                                </div>
                            </div>

                            <!-- Start Page Content -->
                            <div class="card-body">
                                <!--AFFECTED DOCUMENTS TABLE-->
                                <div class="row">
                                    <div class="col">
                                        <div class="dt-responsive table-responsive">
                                            <table id="tbl_applications"
                                                class="table table-sm table-bordered table-striped table-hover"
                                                style="width: 100%; font-size: 85%;">
                                                <thead>
                                                    <tr>
                                                        <th>Action</th>
                                                        <th style="width: 20%;">Approval Status</th>
                                                        <th>Control Number</th>
                                                        <th>Status</th>
                                                        <th>Application Date/Time</th>
                                                        <th>Originator</th>
                                                        <th>Section / Department</th>
                                                        <th>Document Number</th>
                                                        <th>Document Title</th>
                                                        <th>Rev #</th>
                                                        <th>Uploaded File</th>
                                                        <th>Excel File</th>
                                                        <th>Application Approvers</th>
                                                        <th>Remarks</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- !-- End Page Content -->
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <div class="modal fade" id="modalAddApplication">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-user-plus"></i> Add New Application</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="formAddApplication">
                    @csrf
                    <div class="modal-body">
                        <!-- Tabs Nav -->
                        <ul class="nav nav-tabs" id="uploadTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Document Details</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link disabled" id="set-coordinates-tab" data-toggle="tab" href="#generateRow" role="tab" aria-controls="generateRow" aria-selected="false">Set Approver w/ E-sign</a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mt-3" id="uploadTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <div class="row">
                                    <div class="col-sm-7">
                                        <!--PDF ATTACHMENT-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">PDF ATTACHMENT (PDF Only)</span>
                                                    </div>
                                                    <input type="file" class="form-control" id="add_attachment" name="add_attachment" accept=".pdf">
                                                </div>
                                            </div>
                                        </div>

                                        <!--EXCEL ATTACHMENT-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">EXCEL ATTACHMENT (Excel/Word File)</span>
                                                    </div>

                                                    <input type="file" class="form-control" id="add_attachment_excel" name="add_attachment_excel" accept=".xlsx, .csv, .docx, .doc">
                                                </div>
                                            </div>
                                        </div>

                                        <!--DOCUMENT CATEGORY-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">DOCUMENT CATEGORY</span>
                                                    </div>

                                                    <select class="form-control" id="add_doc_category" name="add_doc_category">
                                                        <option value="0" selected disabled>-- Select One --</option>
                                                        <option value="1">PGS</option>
                                                        <option value="2">PQS</option>
                                                        <option value="3">PPS</option>
                                                        <option value="4">WI</option>
                                                        <option value="5">PP</option>
                                                        <option value="6">FMEA</option>
                                                        <option value="7">CP</option>
                                                        <option value="8">IG</option>
                                                        <option value="9">Others</option>
                                                        <option value="10">IMS</option>
                                                        <option value="11">QMS</option>
                                                        <option value="12">EMS</option>
                                                        <option value="13">SEI</option>
                                                        <option value="14">FORMS</option>
                                                        <option value="15">IG</option>
                                                        <option value="16">VIG</option>
                                                        <option value="17">DIG</option>
                                                        <option value="18">PIG</option>
                                                        <option value="19">PRR</option>
                                                        <option value="20">TD</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!--DOCUMENT TYPE-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">DOCUMENT TYPE</span>
                                                    </div>

                                                    <select class="form-control" id="add_doc_type" name="add_doc_type">
                                                        <option value="0" selected disabled>-- Select One --</option>
                                                        <option value="1">New Document</option>
                                                        <option value="2">Revised</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!--FOR GROUP-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">GROUP</span>
                                                    </div>

                                                    <select class="form-control" id="add_for_group" name="add_for_group">
                                                        <option value="0" selected disabled>-- Select One --</option>
                                                        <option value="1">Operations</option>
                                                        <option value="2">Support Group</option>
                                                        <option value="3">Operations - PPC/Warehouse </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!--DEPARTMENT-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">SECTION/DEPARTMENT</span>
                                                    </div>

                                                    <select class="form-control sel-rapidx-department-list" id="add_department" name="add_department">
                                                        <option value="0" selected disabled>-- Select One --</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!--DEPARTMENT-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">ORIGINATOR REMARKS</span>
                                                    </div>
                                                    <textarea class="form-control" id="add_remarks" name="add_remarks" rows="3" style="resize: none;" placeholder="(Optional)"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-5">
                                        <div class="row">
                                            <div class="col">
                                                <div class="card card-primary">
                                                    <div class="card-header">
                                                        <h5 class="card-title"><i class="fa fa-info-circle"></i> <strong>Note:
                                                             <br> To enable approval with e-signature tab, tick the checkbox below</strong></h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="withEsignature" style="height: 20px; width: 20px;" disabled>
                                                            <label class="form-check-label ml-1" for="withEsignature">
                                                                <strong> Document requires an E-Signature </strong>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col">
                                                <div class="card card-primary">
                                                    <div class="card-header">
                                                        <h5 class="card-title"><i class="fa fa-file"></i> <strong>Document Details</strong></h5>
                                                    </div>

                                                    <div class="card-body">
                                                        <!--DOCUMENT NUMBER-->
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">
                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100" id="basic-addon1">DOCUMENT NUMBER</span>
                                                                    </div>
                                                                    <input type="text" class="form-control" id="add_doc_no" name="add_doc_no" readonly>
                                                                    <div class="input-group-prepend">
                                                                        <button type="button" class="btn btn-sm btn-info btn-document-action" document-action="1" title="Add Document Details" id="btnAddDocumentDetails" data-toggle="modal" data-target="#modalSearchDocumentDetails" disabled>
                                                                            <i class="fa fa-file"></i>&nbsp;<i class="fa fa-plus"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!--DOCUMENT TITLE-->
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">
                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100" id="basic-addon1">DOCUMENT TITLE</span>
                                                                    </div>
                                                                    <input type="text" class="form-control" id="add_doc_title" name="add_doc_title">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!--DOCUMENT REVISION NUMBER-->
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">
                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100" id="basic-addon1">REVISION NUMBER</span>
                                                                    </div>
                                                                    <input type="text" class="form-control" id="add_doc_rev_no" name="add_doc_rev_no" value="0" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="card card-primary">
                                            <div class="card-header">
                                                <h5 class="card-title"><i class="fa fa-info-circle"></i> <strong>Affected Documents</strong></h5>
                                                <br>
                                                <p>(Optional): To be filled up by Process Owner/Originator if there are other
                                                    documents which needs to be updated to match with the revised documents
                                                    mentioned above. Will be approved by Selected Head.
                                                </p>
                                            </div>

                                            <div class="card-body">

                                                <div class="row">
                                                    <div class="col">
                                                        <div class="float-sm-right">
                                                            <button type="button" class="btn btn-sm btn-info btn-document-action"
                                                                document-action="4" title="Add Document Details" data-toggle="modal"
                                                                data-target="#modalSearchDocumentDetails"><i
                                                                    class="fa fa-plus-circle"></i> Add Affected Document</button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!--AFFECTED DOCUMENTS TABLE-->
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="dt-responsive table-responsive">
                                                            <table id="tbl_affected_documents"
                                                                class="table table-sm table-bordered table-striped table-hover"
                                                                style="width: 100%; font-size: 85%;">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Document No</th>
                                                                        <th>Document Title</th>
                                                                        <th>Revision No</th>
                                                                        <th>Person-in-Charge</th>
                                                                        <th>Revision Due Date</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </thead>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--QAS HEAD APPROVAL BY CATEGORY-->
                                <div class="row qas-head-approval d-none">
                                    <div class="col">
                                        <div class="card card-primary">
                                            <div class="card-header">
                                                <h5 class="card-title"><i class="fa fa-user"></i><strong>Application Approver</strong></h5>
                                            </div>
                                            <div class="card-body">
                                                <!--APPLICATION MAIN APPROVER-->
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="input-group input-group-sm mb-3">
                                                            <div class="input-group-prepend w-50">
                                                                <span class="input-group-text w-100" id="basic-addon1">QAS HEAD</span>
                                                            </div>
                                                            <select class="form-control" id="add_section_head_approver" name="add_section_head_approver">
                                                                <option value="564" selected disabled>Nian V. Lim</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--SECTION HEAD/APPROVER FOR OPERATIONS-->
                                <div class="row operation-approvers d-none">
                                    <div class="col">
                                        <div class="card card-primary">
                                            <div class="card-header">
                                                <h5 class="card-title">
                                                    <i class="fa fa-users"></i> <strong>Application Approvers</strong>
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col">
                                                        <p><i class="fa fa-info-circle"></i> Select your Section Head by clicking
                                                            one of the radio buttons below. They will be the first one to approve
                                                            the application, followed by the others in any order.</p>
                                                    </div>
                                                </div>

                                                <!--APPLICATION MAIN APPROVER-->
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="input-group input-group-sm mb-3">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">
                                                                    <input type="radio" name="add_approver_priority" value="1">
                                                                </div>
                                                            </div>
                                                            <div class="input-group-prepend w-50">
                                                                <span class="input-group-text w-100" id="basic-addon1">PRODUCTION HEAD</span>
                                                            </div>
                                                            <select class="form-control sel-rapidx-prod-head" id="add_production_head" name="add_production_head">
                                                                <option value="0" selected disabled>-- Select One --</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!--APPLICATION MAIN APPROVER-->
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="input-group input-group-sm mb-3">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">
                                                                    <input type="radio" name="add_approver_priority" value="2">
                                                                </div>
                                                            </div>
                                                            <div class="input-group-prepend w-50">
                                                                <span class="input-group-text w-100" id="basic-addon1">QC HEAD</span>
                                                            </div>
                                                            <select class="form-control sel-rapidx-qc-head" id="add_qc_head" name="add_qc_head">
                                                                <option value="0" selected disabled>-- Select One --</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!--APPLICATION MAIN APPROVER-->
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="input-group input-group-sm mb-3">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">
                                                                    <input type="radio" name="add_approver_priority" value="3">
                                                                </div>
                                                            </div>
                                                            <div class="input-group-prepend w-50">
                                                                <span class="input-group-text w-100" id="basic-addon1">ENGINEERING HEAD</span>
                                                            </div>
                                                            <select class="form-control sel-rapidx-eng-head" id="add_eng_head" name="add_eng_head">
                                                                <option value="0" selected disabled>-- Select One --</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!--APPLICATION MAIN APPROVER-->
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="input-group input-group-sm mb-3">
                                                            <div class="input-group-prepend w-50">
                                                                <span class="input-group-text w-100" id="basic-addon1">QS Staff</span>
                                                            </div>
                                                            <select class="form-control sel-rapidx-user-list" id="add_qs_inspector" name="add_qs_inspector">
                                                                <option value="0" selected disabled>-- Select One --</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Generate Row tab -->
                            <div class="tab-pane fade" id="generateRow" role="tabpanel" aria-labelledby="set-coordinates-tab">
                                <h5>Select Approver & Assign Position of E-signature</h5>
                                <!-- Button to generate a new row -->
                                <button type="button" class="btn btn-success" id="generateRowButton">Add Approver</button>

                                <!-- Table to append rows to -->
                                <table class="table table-bordered mt-3" id="dynamicTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Select Person</th>
                                            <th>Page #</th>
                                            <th>Coordinates</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- New rows will be appended here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-success" id="btnSubmitApplication"><i
                        class="fa fa-upload"></i> Submit Application
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Previewing PDF (Full Screen) -->
    <div class="modal fade" id="pdfPreviewModal" tabindex="-1" role="dialog" aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl1-custom" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfPreviewModalLabel">Preview PDF</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="pdfPreviewWrapper">
                    <div class="row">
                        <div class="col">
                            <!-- This will be used to show the e-signature (selected by the user in the row) -->
                            <img id="eSignature" style="display:none; width: 100px; height: auto;" src="" alt="e-Signature">
                        </div>
                    </div>

                    <!-- Page selection and coordinates input field in one row -->
                    <div class="row mx-auto">
                        <!-- Page Selection Dropdown -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="pageSelect">Select Page</label>
                                <select class="form-control form-control-sm" id="pageSelect">
                                <!-- Options will be added dynamically based on the number of pages -->
                                </select>
                            </div>
                        </div>

                        <!-- Coordinates Input Field -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="coordinatesInput">Selected Coordinates</label>
                                <input type="text" class="form-control form-control-sm" id="coordinatesInput" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center" id="pdfPreviewContainer">
                        <!-- PDF preview will be rendered here -->
                        <div class="col-auto">
                            <div id="pdfPreview" class="text-center mx-3" style="border: solid 1px;">
                                <!-- Rendered PDF page will be added here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- Submit button to finalize coordinate selection -->
                    <button type="button" class="btn btn-sm btn-primary" id="submitCoordinatesButton" disabled>Submit Coordinates</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalQSValidation">
        <div class="modal-dialog modal-xl-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-microscope"></i> QS Validation</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!--APPLICATION DETAILS-->
                        <div class="col-sm-4">
                            <div class="row">
                                <div class="col">
                                    <div class="card card-primary">
                                        <div class="card-header">
                                            <h5 class="card-title"><i class="fa fa-file"></i> Document Details</h5>
                                        </div>
                                        <div class="card-body">
                                            <!--DOCUMENT NUMBER-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT NUMBER</span>
                                                        </div>
                                                        <input type="text" class="form-control" id="qs_doc_no" name="qs_doc_no" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--DOCUMENT TITLE-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT TITLE</span>
                                                        </div>
                                                        <input type="text" class="form-control" id="qs_doc_title" name="qs_doc_title" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--DOCUMENT REVISION NUMBER-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">REVISION NUMBER</span>
                                                        </div>
                                                        <input type="text" class="form-control" id="qs_doc_rev_no" name="qs_doc_rev_no" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="card card-primary">
                                        <div class="card-header">
                                            <h5 class="card-title"><i class="fa fa-info-circle"></i> Application Details</h5>
                                        </div>
                                        <div class="card-body">
                                            <!--CONTROL NUMBER-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">AIDRC CONTROL NUMBER</span>
                                                        </div>
                                                        <input type="text" class="form-control" id="qs_aidrc_control_number" name="qs_aidrc_control_number" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--DOCUMENT CATEGORY-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT CATEGORY</span>
                                                        </div>
                                                        <select class="form-control" id="qs_doc_category"
                                                            name="qs_doc_category" disabled>
                                                            <option value="0" selected disabled>-- Select One --</option>
                                                            <option value="1">PGS</option>
                                                            <option value="2">PQS</option>
                                                            <option value="3">PPS</option>
                                                            <option value="4">WI</option>
                                                            <option value="5">PP</option>
                                                            <option value="6">FMEA</option>
                                                            <option value="7">CP</option>
                                                            <option value="8">IG</option>
                                                            <option value="9">Others</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--DOCUMENT TYPE-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT TYPE</span>
                                                        </div>
                                                        <select class="form-control" id="qs_doc_type" name="qs_doc_type" disabled>
                                                            <option value="0" selected disabled>-- Select One --</option>
                                                            <option value="1">New Document</option>
                                                            <option value="2">Revised</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--FOR GROUP-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">GROUP</span>
                                                        </div>
                                                        <select class="form-control" id="qs_for_group" name="qs_for_group" disabled>
                                                            <option value="0" selected disabled>-- Select One --</option>
                                                            <option value="1">Operations</option>
                                                            <option value="2">Support Group</option>
                                                            <option value="3">Operations - PPC/Warehouse </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--DEPARTMENT-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">SECTION/DEPARTMENT</span>
                                                        </div>
                                                        <select class="form-control sel-rapidx-department-list" id="qs_department" name="qs_department" disabled>
                                                            <option value="0" selected disabled>-- Select One --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--APPLICATION MAIN APPROVER-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">ORIGINATOR</span>
                                                        </div>
                                                        <select class="form-control sel-rapidx-user-list" id="qs_originator" name="qs_originator" disabled>
                                                            <option value="0" selected disabled>-- Select One --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--DATE/TIME-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">APPLICATION CREATED AT</span>
                                                        </div>
                                                        <input type="text" class="form-control" id="qs_created_at" name="qs_created_at" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--DEPARTMENT-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">ORIGINATOR REMARKS</span>
                                                        </div>
                                                        <textarea class="form-control" id="qs_remarks" name="qs_remarks" rows="3" style="resize: none;" placeholder="(Optional)" readonly></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--QS VALIDATION-->
                        <div class="col-sm-8">
                            <div class="row">
                                <div class="col">
                                    <p><i class="fa fa-info-circle"></i><strong>VALIDATION CHECK</strong>: To be filled by
                                        QS Staff. Press the action button per approver to include Validation Checkpoints
                                        (Can add multiple FMEA, Control Plan, Pre-Production Checksheet). <strong><i>Note:
                                                Even if there are no affected documents assigned, Eng'g, QC head will still
                                                review the application.</i></strong>
                                    </p>
                                </div>
                            </div>

                            <!--APPLICATION MAIN APPROVER-->
                            <div class="row">
                                <div class="col">
                                    <div class="float-sm-right">
                                        <button type="button" class="btn btn-sm btn-info btn-document-action" document-action="2" title="Add Document Details" data-toggle="modal" data-target="#modalSearchDocumentDetails">
                                            <i class="fa fa-plus-circle"></i>Add QS Checkpoints
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="dt-responsive table-responsive">
                                        <table id="tbl_qs_validations" class="table table-sm table-bordered table-striped table-hover" style="width: 100%; font-size: 85%;">
                                            <caption>QS Validation Checkpoints</caption>
                                            <thead>
                                                <tr>
                                                    <th>Checkpoint</th>
                                                    <th>Document No</th>
                                                    <th>Document Title</th>
                                                    <th>Revision No</th>
                                                    <th>Person-In-Charge</th>
                                                    <th>Revision Due Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <form id="formQsValidation" method="post">
                                @csrf
                                <input type="hidden" class="form-control form-control-sm" id="qs_application_id" name="qs_application_id" readonly>
                                <input type="hidden" class="form-control form-control-sm" id="qs_validation_status" name="qs_validation_status">
                                <div class="row">
                                    <div class="col">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend w-50">
                                                <span class="input-group-text w-100" id="basic-addon1">QS VALIDATION REMARKS</span>
                                            </div>
                                            <textarea class="form-control" id="qs_validation_remarks" name="qs_validation_remarks" rows="3" style="resize: none;" placeholder="(Required!)"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger mr-auto" id="btnQsDisapproveApplication">
                        <i class="fa fa-times-circle"></i> Disapprove Application
                    </button>

                    <button type="button" class="btn btn-sm btn-success" id="btnQsApproveApplication">
                        <i class="fa fa-check-circle"></i> Submit QS Validation
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--MODAL HEAD APPROVERS-->
    <div class="modal fade" id="modalHeadApprover">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-check-circle"></i> Review Application</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Tabs Nav -->
                    <ul class="nav nav-tabs" id="viewTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="view-home-tab" data-toggle="tab" href="#viewHome" role="tab" aria-controls="Home" aria-selected="true">Document Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="view-approver-tab" data-toggle="tab" href="#viewEsignApprover" role="tab" aria-controls="viewEsignApprover" aria-selected="false">E-sign Approver</a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content mt-3" id="viewTabContent">
                        <div class="tab-pane fade show active" id="viewHome" role="tabpanel" aria-labelledby="view-home-tab">
                            <div class="row">
                                <!--APPLICATION DETAILS-->
                                <div class="col-sm-5">
                                    <div class="row">
                                        <div class="col">
                                            <div class="card card-primary">
                                                <div class="card-header">
                                                    <h5 class="card-title"><i class="fa fa-file"></i> Document Details
                                                    </h5>
                                                </div>

                                                <div class="card-body">
                                                    <!--DOCUMENT NUMBER-->
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="input-group input-group-sm mb-3">
                                                                <div class="input-group-prepend w-50">
                                                                    <span class="input-group-text w-100" id="basic-addon1">DOCUMENT NUMBER</span>
                                                                </div>
                                                                <input type="text" class="form-control" id="head_doc_no" name="head_doc_no" readonly>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!--DOCUMENT TITLE-->
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="input-group input-group-sm mb-3">
                                                                <div class="input-group-prepend w-50">
                                                                    <span class="input-group-text w-100" id="basic-addon1">DOCUMENT TITLE</span>
                                                                </div>
                                                                <input type="text" class="form-control" id="head_doc_title" name="head_doc_title" readonly>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!--DOCUMENT REVISION NUMBER-->
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="input-group input-group-sm mb-3">
                                                                <div class="input-group-prepend w-50">
                                                                    <span class="input-group-text w-100" id="basic-addon1">REVISION NUMBER</span>
                                                                </div>
                                                                <input type="text" class="form-control" id="head_doc_rev_no" name="head_doc_rev_no" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <div class="card card-primary">
                                                <div class="card-header">
                                                    <h5 class="card-title"><i class="fa fa-info-circle"></i> Application Details</h5>
                                                </div>
                                                <div class="card-body">
                                                    <!--CONTROL NUMBER-->
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="input-group input-group-sm mb-3">
                                                                <div class="input-group-prepend w-50">
                                                                    <span class="input-group-text w-100" id="basic-addon1">AIDRC CONTROL NUMBER</span>
                                                                </div>
                                                                <input type="text" class="form-control" id="head_aidrc_control_number" name="head_aidrc_control_number" readonly>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!--DOCUMENT CATEGORY-->
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="input-group input-group-sm mb-3">
                                                                <div class="input-group-prepend w-50">
                                                                    <span class="input-group-text w-100" id="basic-addon1">DOCUMENT CATEGORY</span>
                                                                </div>
                                                                <select class="form-control" id="head_doc_category"
                                                                    name="head_doc_category" disabled>
                                                                    <option value="0" selected disabled>-- Select One --</option>
                                                                    <option value="1">PGS</option>
                                                                    <option value="2">PQS</option>
                                                                    <option value="3">PPS</option>
                                                                    <option value="4">WI</option>
                                                                    <option value="5">PP</option>
                                                                    <option value="6">FMEA</option>
                                                                    <option value="7">CP</option>
                                                                    <option value="8">IG</option>
                                                                    <option value="9">Others</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!--DOCUMENT TYPE-->
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="input-group input-group-sm mb-3">
                                                                <div class="input-group-prepend w-50">
                                                                    <span class="input-group-text w-100" id="basic-addon1">DOCUMENT TYPE</span>
                                                                </div>
                                                                <select class="form-control" id="head_doc_type"
                                                                    name="head_doc_type" disabled>
                                                                    <option value="0" selected disabled>-- Select One --</option>
                                                                    <option value="1">New Document</option>
                                                                    <option value="2">Revised</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!--FOR GROUP-->
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="input-group input-group-sm mb-3">
                                                                <div class="input-group-prepend w-50">
                                                                    <span class="input-group-text w-100" id="basic-addon1">GROUP</span>
                                                                </div>
                                                                <select class="form-control" id="head_for_group"
                                                                    name="head_for_group" disabled>
                                                                    <option value="0" selected disabled>-- Select One --</option>
                                                                    <option value="1">Operations</option>
                                                                    <option value="2">Support Group</option>
                                                                    <option value="3">Operations - PPC/Warehouse </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!--DEPARTMENT-->
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="input-group input-group-sm mb-3">
                                                                <div class="input-group-prepend w-50">
                                                                    <span class="input-group-text w-100" id="basic-addon1">SECTION/DEPARTMENT</span>
                                                                </div>
                                                                <select class="form-control sel-rapidx-department-list"id="head_department" name="head_department" disabled>
                                                                    <option value="0" selected disabled>-- Select One --</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!--APPLICATION MAIN APPROVER-->
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="input-group input-group-sm mb-3">
                                                                <div class="input-group-prepend w-50">
                                                                    <span class="input-group-text w-100" id="basic-addon1">ORIGINATOR</span>
                                                                </div>
                                                                <select class="form-control sel-rapidx-user-list" id="head_originator" name="head_originator" disabled>
                                                                    <option value="0" selected disabled>-- Select One --</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!--DATE/TIME-->
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="input-group input-group-sm mb-3">
                                                                <div class="input-group-prepend w-50">
                                                                    <span class="input-group-text w-100" id="basic-addon1">APPLICATION CREATED AT</span>
                                                                </div>
                                                                <input type="text" class="form-control" id="head_created_at" name="head_created_at" readonly>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!--DEPARTMENT-->
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="input-group input-group-sm mb-3">
                                                                <div class="input-group-prepend w-50">
                                                                    <span class="input-group-text w-100" id="basic-addon1">ORIGINATOR REMARKS</span>
                                                                </div>
                                                                <textarea class="form-control" id="head_remarks" name="head_remarks" rows="3" style="resize: none;" placeholder="(Optional)" readonly></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-7">
                                    <div class="row">
                                        <div class="col">
                                            <p><i class="fa fa-info-circle"></i> <strong>APPLICATION REVIEW</strong>: Application
                                                for checking of selected head. All documents which are not disapproved will be
                                                considered "Approved" after the review.<i>(Note if for Operations: Once approved by
                                                    the first approver set by the originator, other approvers can now review in any
                                                    sequence.)</i></p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <div class="float-sm-right">
                                                <button type="button" class="btn btn-sm btn-info btn-document-action" document-action="3" title="Add Document Details" data-toggle="modal" data-target="#modalSearchDocumentDetails">
                                                    <i class="fa fa-plus-circle"></i> Add Affected Document
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <div class="dt-responsive table-responsive">
                                                <table id="tbl_head_approval_documents" class="table table-sm table-bordered table-striped table-hover" style="width: 100%; font-size: 85%;">
                                                    <caption>Affected Documents / Checkpoints for Approval</caption>
                                                    <thead>
                                                        <tr>
                                                            <th>Checkpoint</th>
                                                            <th>Document No</th>
                                                            <th>Document Title</th>
                                                            <th>Revision No</th>
                                                            <th>Person-In-Charge</th>
                                                            <th>Revision Due Date</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <form id="formHeadApproval" method="post">
                                        @csrf

                                        <input type="hidden" class="form-control form-control-sm" id="head_application_id" name="head_application_id" readonly>
                                        {{-- <input type="hidden" class="form-control form-control-sm" id="head_approving_as" name="head_approving_as" readonly> --}}
                                        <input type="hidden" class="form-control form-control-sm" id="head_approval_status" name="head_approval_status">
                                        <input type="hidden" class="form-control form-control-sm" id="approvalOrder" name="approval_order">

                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">APPROVER REMARKS</span>
                                                    </div>
                                                    <textarea class="form-control" id="head_approval_remarks" name="head_approval_remarks" rows="3" style="resize: none;" placeholder="(Required if Disapproved; Optional if Approved)"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Generate Row tab -->
                        <div class="tab-pane fade" id="viewEsignApprover" role="tabpanel" aria-labelledby="view-approver-tab">
                            <h5>Select Approver & Assign Position of E-signature</h5>
                            <!-- Button to generate a new row -->
                            {{-- <button type="button" class="btn btn-success" id="generateRowButton">Add Approver</button> --}}

                            <!-- Table to append rows to -->
                            <table class="table mt-3" id="viewEsignApproverTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Select Person</th>
                                        <th>Page #</th>
                                        <th>Coordinates</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- New rows will be appended here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger mr-auto" id="btnHeadDisapproveApplication">
                        <i class="fa fa-times-circle"></i> Disapprove Application
                    </button>

                    <button type="button" class="btn btn-sm btn-success" id="btnHeadApproveApplication">
                        <i class="fa fa-check-circle"></i> Approve Application
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--DCC VALIDATIONS-->
    <div class="modal fade" id="modalDccValidations">
        <div class="modal-dialog modal-xl-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-chevron-right"></i> DCC Validation Check</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!--APPLICATION DETAILS-->
                        <div class="col-sm-4">
                            <div class="row">
                                <div class="col">
                                    <div class="card card-primary">
                                        <div class="card-header">
                                            <h5 class="card-title"><i class="fa fa-file"></i> Document Details</h5>
                                        </div>
                                        <div class="card-body">
                                            <!--DOCUMENT NUMBER-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT NUMBER</span>
                                                        </div>
                                                        <input type="text" class="form-control" id="dcc_doc_no" name="dcc_doc_no" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--DOCUMENT TITLE-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT TITLE</span>
                                                        </div>
                                                        <input type="text" class="form-control" id="dcc_doc_title" name="dcc_doc_title" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--DOCUMENT REVISION NUMBER-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">REVISION NUMBER</span>
                                                        </div>
                                                        <input type="text" class="form-control" id="dcc_doc_rev_no" name="dcc_doc_rev_no" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="card card-primary">
                                        <div class="card-header">
                                            <h5 class="card-title"><i class="fa fa-info-circle"></i> Application Details</h5>
                                        </div>
                                        <div class="card-body">
                                            <!--CONTROL NUMBER-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">AIDRC CONTROL NUMBER</span>
                                                        </div>
                                                        <input type="text" class="form-control" id="dcc_aidrc_control_number" name="dcc_aidrc_control_number" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--DOCUMENT CATEGORY-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT CATEGORY</span>
                                                        </div>
                                                        <select class="form-control" id="dcc_doc_category" name="dcc_doc_category" disabled>
                                                            <option value="0" selected disabled>-- Select One --</option>
                                                            <option value="1">PGS</option>
                                                            <option value="2">PQS</option>
                                                            <option value="3">PPS</option>
                                                            <option value="4">WI</option>
                                                            <option value="5">PP</option>
                                                            <option value="6">FMEA</option>
                                                            <option value="7">CP</option>
                                                            <option value="8">IG</option>
                                                            <option value="9">Others</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--DOCUMENT TYPE-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT TYPE</span>
                                                        </div>
                                                        <select class="form-control" id="dcc_doc_type" name="dcc_doc_type" disabled>
                                                            <option value="0" selected disabled>-- Select One --</option>
                                                            <option value="1">New Document</option>
                                                            <option value="2">Revised</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--FOR GROUP-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">GROUP</span>
                                                        </div>
                                                        <select class="form-control" id="dcc_for_group" name="dcc_for_group" disabled>
                                                            <option value="0" selected disabled>-- Select One --</option>
                                                            <option value="1">Operations</option>
                                                            <option value="2">Support Group</option>
                                                            <option value="3">Operations - PPC/Warehouse </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--DEPARTMENT-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">SECTION/DEPARTMENT</span>
                                                        </div>
                                                        <select class="form-control sel-rapidx-department-list" id="dcc_department" name="dcc_department" disabled>
                                                            <option value="0" selected disabled>-- Select One --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--APPLICATION MAIN APPROVER-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">ORIGINATOR</span>
                                                        </div>
                                                        <select class="form-control sel-rapidx-user-list" id="dcc_originator" name="dcc_originator" disabled>
                                                            <option value="0" selected disabled>-- Select One --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--DATE/TIME-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">APPLICATION CREATED AT</span>
                                                        </div>
                                                        <input type="text" class="form-control" id="dcc_created_at" name="dcc_created_at" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--DEPARTMENT-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">ORIGINATOR REMARKS</span>
                                                        </div>
                                                        <textarea class="form-control" id="dcc_remarks" name="dcc_remarks" rows="3" style="resize: none;" placeholder="(Optional)" readonly></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-8">
                            <div class="row">
                                <div class="col">
                                    <p><i class="fa fa-info-circle"></i> <strong>INITIAL CONFIRMATION</strong>: To be
                                        filled-up by QAD-DCC</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="dt-responsive table-responsive">
                                        <table id="tbl_dcc_affected_documents"
                                            class="table table-sm table-bordered table-striped table-hover"
                                            style="width: 100%; font-size: 85%;">
                                            <caption>Affected Documents / Checkpoints for Approval</caption>
                                            <thead>
                                                <tr>
                                                    <th>Checkpoint</th>
                                                    <th>Document No</th>
                                                    <th>Document Title</th>
                                                    <th>Revision No</th>
                                                    <th>Person-In-Charge</th>
                                                    <th>Revision Due Date</th>
                                                    <!-- <th>Action</th> -->
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <form id="formDccValidation" method="post">
                                @csrf
                                <input type="hidden" class="form-control form-control-sm" id="dcc_application_id" name="dcc_application_id" readonly>
                                <div class="row">
                                    <div class="col">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend w-50">
                                                <span class="input-group-text w-100" id="basic-addon1">NO SIMILAR/COMMON PROCEDURE FROM OTHER DOCUMENT</span>
                                            </div>
                                            <select class="form-control dcc-checkpoint" id="dcc_checkpoint_similar" name="dcc_checkpoint_similar">
                                                <option selected disabled>-- Select One --</option>
                                                <option value="1">COMPLIANT</option>
                                                <option value="2">NON-COMPLIANT</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend w-50">
                                                <span class="input-group-text w-100" id="basic-addon1">ALIGNMENT OF PROCEDURE IN REFERENCE TO DESCRIPTION OF CHANGE</span>
                                            </div>
                                            <select class="form-control dcc-checkpoint" id="dcc_checkpoint_alignment" name="dcc_checkpoint_alignment">
                                                <option selected disabled>-- Select One --</option>
                                                <option value="1">COMPLIANT</option>
                                                <option value="2">NON-COMPLIANT</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend w-50">
                                                <span class="input-group-text w-100" id="basic-addon1">USE OF STANDARD TEMPLATE/FORMAT</span>
                                            </div>
                                            <select class="form-control dcc-checkpoint" id="dcc_checkpoint_standard" name="dcc_checkpoint_standard">
                                                <option selected disabled>-- Select One --</option>
                                                <option value="1">COMPLIANT</option>
                                                <option value="2">NON-COMPLIANT</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend w-50">
                                                <span class="input-group-text w-100" id="basic-addon1">DCC VALIDATION JUDGEMENT</span>
                                            </div>
                                            <select class="form-control" id="dcc_validation_judgement" name="dcc_validation_judgement">
                                                <option selected disabled>-- Select One --</option>
                                                <option disabled class="dcc-passed" value="1">PASSED</option>
                                                <option disabled class="dcc-revisions" value="2">FOR MINOR REVISIONS</option>
                                                <option disabled class="dcc-revisions" value="3">FOR MAJOR REVISIONS</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend w-50">
                                                <span class="input-group-text w-100" id="basic-addon1">DCC REMARKS</span>
                                            </div>
                                            <textarea class="form-control" id="dcc_validation_remarks" name="dcc_validation_remarks" rows="3" style="resize: none;" placeholder="(Optional)"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!--   <button type="button" class="btn btn-sm btn-danger mr-auto" id="btnHeadDisapproveApplication"><i class="fa fa-times-circle"></i> Disapprove Application</button> -->
                    <button type="button" class="btn btn-sm btn-success" id="btnSubmitDccValidation">
                        <i class="fa fa-check-circle"></i> Submit DCC Validation
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSearchDocumentDetails">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Add Document Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <p><i class="fa fa-info-circle"></i> Click the Action Button to add the Document Details in your Application</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <!--SEARCH TYPE-->
                            <div class="row">
                                <div class="col">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend w-50">
                                            <span class="input-group-text w-100" id="basic-addon1">SEARCH TYPE</span>
                                        </div>
                                        <select class="form-control" id="document_search_type" name="document_search_type">
                                            <option value="0" selected disabled>-- Any Filters --</option>
                                            <option value="1">Document Number</option>
                                            <option value="2">Document Title</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!--SEARCH WILDCARD-->
                            <div class="row">
                                <div class="col">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend w-50">
                                            <span class="input-group-text w-100" id="basic-addon1">SEARCH DOCUMENT WILDCARD</span>
                                        </div>
                                        <input type="text" class="form-control" id="document_wildcard" name="document_wildcard" placeholder='e.g. PGS-B17-004, Internal Audit Procedures'>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-sm btn-primary" id="btnSearchDocument">
                                                <i class="fa fa-arrow-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <input type="hidden" class="form-control" id="document_hidden_action" name="document_hidden_action" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="float-sm-right">
                                <button class="btn btn-sm btn-primary btn-preprod" addition-type="3" data-toggle="modal" data-target="#modalAddaffectedDocumentDetails" title="Add Pre-Production Checksheet to the application">
                                    <i class="fa fa-file"></i> Add Pre-Production Checksheet
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <hr>
                            <!--AFFECTED DOCUMENTS TABLE-->
                            <div class="row">
                                <div class="col">
                                    <div class="dt-responsive table-responsive">
                                        <table id="tbl_add_document_details"
                                            class="table table-sm table-bordered table-striped table-hover"
                                            style="width: 100%; font-size: 85%;">
                                            <thead>
                                                <tr>
                                                    <th>Document No</th>
                                                    <th>Document Title</th>
                                                    <th>Revision No</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!--VIEW/EDIT APPLICATION-->
    <div class="modal fade" id="modalViewApplication">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-edit"></i> View Application</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formEditApplication" method="post">
                        @csrf

                        <!-- Tabs Nav -->
                        <ul class="nav nav-tabs" id="uploadTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="editHome-tab" data-toggle="tab" href="#editHome" role="tab" aria-controls="editHome" aria-selected="true">Document Details</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link disabled" id="editApprover-tab" data-toggle="tab" href="#editApprover" role="tab" aria-controls="editApprover" aria-selected="false">Set Approver w/ E-sign</a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mt-3" id="uploadTabContent">
                            <div class="tab-pane fade show active" id="editHome" role="tabpanel" aria-labelledby="editHome-tab">
                                <div class="row">
                                    <!--APPLICATION DETAILS-->
                                    <div class="col-sm-7">
                                        <div class="row">
                                            <div class="col">
                                                <div class="card card-primary">
                                                    <div class="card-header">
                                                        <h5 class="card-title"><i class="fa fa-info-circle"></i> Application Details</h5>
                                                        <div class="float-sm-right view-edit">
                                                            <button type="button" class="btn btn-sm btn-primary" id="btnEditApplicationDetails">
                                                                <i class="fa fa-edit"></i> Edit Application
                                                            </button>
                                                        </div>

                                                        <div class="float-sm-right view-edit mr-1">
                                                            <button type="button" class="btn btn-sm btn-secondary" id="btnCancelEditApplication" disabled>
                                                                <i class="fa fa-times-circle"></i> Cancel Edit
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="card-body">

                                                        <!--CONTROL NUMBER-->
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">
                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100" id="basic-addon1">AIDRC CONTROL NUMBER</span>
                                                                    </div>
                                                                    <input type="text" class="form-control" id="view_aidrc_control_number" name="view_aidrc_control_number" readonly>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!--DOCUMENT CATEGORY-->
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">
                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100" id="basic-addon1">DOCUMENT CATEGORY</span>
                                                                    </div>
                                                                    <select class="form-control" id="view_doc_category" name="view_doc_category" disabled>
                                                                        <option value="0" selected disabled>-- Select One --</option>
                                                                        <option value="1">PGS</option>
                                                                        <option value="2">PQS</option>
                                                                        <option value="3">PPS</option>
                                                                        <option value="4">WI</option>
                                                                        <option value="5">PP</option>
                                                                        <option value="6">FMEA</option>
                                                                        <option value="7">CP</option>
                                                                        <option value="8">IG</option>
                                                                        <option value="9">Others</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!--DOCUMENT TYPE-->
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">
                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100" id="basic-addon1">DOCUMENT TYPE</span>
                                                                    </div>
                                                                    <select class="form-control" id="view_doc_type" name="view_doc_type" disabled>
                                                                        <option value="0" selected disabled>-- Select One --</option>
                                                                        <option value="1">New Document</option>
                                                                        <option value="2">Revised</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!--FOR GROUP-->
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">
                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100" id="basic-addon1">GROUP</span>
                                                                    </div>
                                                                    <select class="form-control" id="view_for_group" name="view_for_group" disabled>
                                                                        <option value="0" selected disabled>-- Select One --</option>
                                                                        <option value="1">Operations</option>
                                                                        <option value="2">Support Group</option>
                                                                        <option value="3">Operations - PPC/Warehouse </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!--DEPARTMENT-->
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">
                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100" id="basic-addon1">SECTION/DEPARTMENT</span>
                                                                    </div>
                                                                    <select class="form-control sel-rapidx-department-list" id="view_department" name="view_department" disabled>
                                                                        <option value="0" selected disabled>-- Select One --</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!--APPLICATION MAIN APPROVER-->
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">
                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100" id="basic-addon1">ORIGINATOR</span>
                                                                    </div>
                                                                    <select class="form-control sel-rapidx-user-list" id="view_originator" name="view_originator" disabled>
                                                                        <option value="0" selected disabled>-- Select One --</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!--DATE/TIME-->
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">
                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100" id="basic-addon1">APPLICATION CREATED AT</span>
                                                                    </div>
                                                                    <input type="text" class="form-control" id="view_created_at" name="view_created_at" readonly>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!--PDF ATTACHMENT-->
                                                        <div class="row view-edit">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">
                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100" id="basic-addon1">NEW PDF ATTACHMENT (PDF Only)</span>
                                                                    </div>
                                                                    <input type="file" class="form-control" id="edit_attachment" name="edit_attachment" data-application-id="" accept=".pdf" disabled>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!--EXCEL ATTACHMENT-->
                                                        <div class="row view-edit">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">
                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100" id="basic-addon1">NEW EXCEL ATTACHMENT (Excel/Word)</span>
                                                                    </div>
                                                                    <input type="file" class="form-control" id="edit_attachment_excel" name="edit_attachment_excel" accept=".xlsx, .csv, .docx, .doc" disabled>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!--DEPARTMENT-->
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">
                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100" id="basic-addon1">ORIGINATOR REMARKS</span>
                                                                    </div>
                                                                    <textarea class="form-control" id="view_remarks" name="view_remarks" rows="3" style="resize: none;" placeholder="(Optional)" readonly></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!--SECTION HEAD/APPROVER FOR SG/PPC/WHS-->
                                        <div class="row view-qas-head-approver d-none">
                                            <div class="col">
                                                <div class="card card-primary">
                                                    <div class="card-header">
                                                        <h5 class="card-title"><i class="fa fa-user"></i>
                                                            <strong>Application Approver</strong>
                                                        </h5>
                                                    </div>

                                                    <div class="card-body">


                                                        <!--APPLICATION MAIN APPROVER-->
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">
                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100"
                                                                            id="basic-addon1">SECTION HEAD / APPROVER</span>
                                                                    </div>

                                                                    <select class="form-control" id="view_section_head_approver" name="view_section_head_approver" disabled>
                                                                        {{-- <option value="0" selected disabled>-- Select One --
                                                                        </option> --}}
                                                                        <option value="564" selected disabled>Nian V. Lim</option>
                                                                    </select>

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!--SECTION HEAD/APPROVER FOR OPERATIONS-->
                                        <div class="row view-operations-approver d-none">
                                            <div class="col">
                                                <div class="card card-primary">
                                                    <div class="card-header">
                                                        <h5 class="card-title"><i class="fa fa-users"></i>
                                                            <strong>Application Approvers</strong>
                                                        </h5>
                                                    </div>

                                                    <div class="card-body">

                                                        <div class="row">
                                                            <div class="col">
                                                                <p><i class="fa fa-info-circle"></i> Select your Section Head by
                                                                    clicking one of the radio buttons below. They will be the first
                                                                    one to approve the application, followed by the others in any
                                                                    order.</p>
                                                            </div>
                                                        </div>

                                                        <!--APPLICATION MAIN APPROVER-->
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">

                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text">
                                                                            <input type="radio" name="view_approver_priority"
                                                                                class="view_approver_priority" value="1" disabled>
                                                                        </div>
                                                                    </div>

                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100"
                                                                            id="basic-addon1">PRODUCTION HEAD</span>
                                                                    </div>

                                                                    <select class="form-control sel-rapidx-prod-head"
                                                                        id="view_production_head" name="view_production_head"
                                                                        disabled>
                                                                        <option value="0" selected disabled>-- Select One --
                                                                        </option>
                                                                    </select>

                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!--APPLICATION MAIN APPROVER-->
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">

                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text">
                                                                            <input type="radio" name="view_approver_priority"
                                                                                class="view_approver_priority" value="2" disabled>
                                                                        </div>
                                                                    </div>

                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100" id="basic-addon1">QC
                                                                            HEAD</span>
                                                                    </div>

                                                                    <select class="form-control sel-rapidx-qc-head"
                                                                        id="view_qc_head" name="view_qc_head" disabled>
                                                                        <option value="0" selected disabled>-- Select One --
                                                                        </option>
                                                                    </select>

                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!--APPLICATION MAIN APPROVER-->
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">

                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text">
                                                                            <input type="radio" name="view_approver_priority"
                                                                                class="view_approver_priority" value="3" disabled>
                                                                        </div>
                                                                    </div>

                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100"
                                                                            id="basic-addon1">ENGINEERING HEAD</span>
                                                                    </div>

                                                                    <select class="form-control sel-rapidx-eng-head"
                                                                        id="view_eng_head" name="view_eng_head" disabled>
                                                                        <option value="0" selected disabled>-- Select One --
                                                                        </option>
                                                                    </select>

                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!--APPLICATION MAIN APPROVER-->
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">
                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100" id="basic-addon1">QS
                                                                            Staff</span>
                                                                    </div>

                                                                    <select class="form-control sel-rapidx-user-list"
                                                                        id="view_qs_inspector" name="view_qs_inspector" disabled>
                                                                        <option value="0" selected disabled>-- Select One --
                                                                        </option>
                                                                    </select>

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="row">
                                            <div class="col">
                                                <div class="card card-primary">
                                                    <div class="card-header">
                                                        <h5 class="card-title"><i class="fa fa-info-circle"></i> <strong>Note:
                                                            <br> To enable approval with e-signature tab, tick the checkbox below</strong></h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="editApproverWithEsignature" style="height: 20px; width: 20px;">
                                                            <label class="form-check-label ml-1" for="editApproverWithEsignature">
                                                                <strong> Document requires an E-Signature </strong>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col">
                                                <div class="card card-primary">
                                                    <div class="card-header">
                                                        <h5 class="card-title"><i class="fa fa-file"></i> Document Details </h5>
                                                    </div>
                                                    <div class="card-body">

                                                        <input type="hidden" class="form-control" id="view_application_id" name="view_application_id" readonly>

                                                        <!--DOCUMENT NUMBER-->
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">
                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100" id="basic-addon1">DOCUMENT NUMBER</span>
                                                                    </div>
                                                                    <input type="text" class="form-control" id="view_doc_no" name="view_doc_no" readonly>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!--DOCUMENT TITLE-->
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">
                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100" id="basic-addon1">DOCUMENT TITLE</span>
                                                                    </div>
                                                                    <input type="text" class="form-control" id="view_doc_title" name="view_doc_title" readonly>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!--DOCUMENT REVISION NUMBER-->
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="input-group input-group-sm mb-3">
                                                                    <div class="input-group-prepend w-50">
                                                                        <span class="input-group-text w-100" id="basic-addon1">REVISION NUMBER</span>
                                                                    </div>
                                                                    <input type="text" class="form-control" id="view_doc_rev_no" name="view_doc_rev_no" readonly>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> {{-- new row --}}
                                <br>
                                <div class="row"> {{-- new row --}}
                                    <div class="col">
                                        <div class="row">
                                            <div class="col">
                                                <h5><i class="fa fa-info-circle"></i> <strong>APPLICATION TRACEABILITY:</strong> See all activity of the application</h5>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="dt-responsive table-responsive">
                                                    <table id="tbl_view_dcc_validations"
                                                        class="table table-sm table-bordered table-striped table-hover"
                                                        style="width: 100%; font-size: 85%;">
                                                        <p><i class="fas fa-chevron-circle-down"></i><strong>DCC Template Validation</strong></p>
                                                        <thead>
                                                            <tr>
                                                                <th>Validation Date/Time</th>
                                                                <th>DCC-In-Charge</th>
                                                                <th>Judgement</th>
                                                                <th>No Similar/Common</th>
                                                                <th>Alignment of Procedure</th>
                                                                <th>Use of Standard Template/Format</th>
                                                                <th>Validation Remarks</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col">
                                                <div class="dt-responsive table-responsive">
                                                    <table id="tbl_view_affected_documents"
                                                        class="table table-sm table-bordered table-striped table-hover"
                                                        style="width: 100%; font-size: 85%;">
                                                        <p><i class="fas fa-chevron-circle-down"></i> <strong>Affected Documents / Checkpoints for Approval</strong></p>
                                                        <thead>
                                                            <tr>
                                                                <th>Checkpoint</th>
                                                                <th>Document No</th>
                                                                <th>Document Title</th>
                                                                <th>Revision No</th>
                                                                <th>Person-In-Charge</th>
                                                                <th>Revision Due Date</th>
                                                                <th>Remarks</th>
                                                                <!--  <th>Action</th> -->
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Generate Row tab -->
                            <div class="tab-pane fade" id="editApprover" role="tabpanel" aria-labelledby="editApprover-tab">
                                <h5>Select Approver & Assign Position of E-signature</h5>
                                <!-- Button to generate a new row -->
                                <button type="button" class="btn btn-success" id="editApproverButton" data-filepath>Add Approver</button>

                                <!-- Table to append rows to -->
                                <table class="table table-bordered mt-3" id="editApproverTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Select Person</th>
                                            <th>Page #</th>
                                            <th>Coordinates</th>
                                            <th id="editApproverStatus" class='d-none'>Status</th>
                                            <th id="editApproverRemarks" class='d-none'>Remarks</th>
                                            <th id="editApproverAction">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- New rows will be appended here -->
                                    </tbody>
                                </table>
                            </div>

                            <div class="row view-edit">
                                <div class="col">
                                    <button type="button" class="btn btn-sm btn-danger" id="btnCancelApplication" disabled>
                                        <i class="fa fa-times-circle"></i> Cancel Application
                                    </button>

                                    <div class="float-sm-right">
                                        <button type="button" class="btn btn-sm btn-success" id="btnSubmitEditApplication" disabled>
                                            <i class="fa fa-check-circle"></i> Submit Changes
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <!--   <button type="button" class="btn btn-sm btn-danger mr-auto" id="btnHeadDisapproveApplication"><i class="fa fa-times-circle"></i> Disapprove Application</button> -->
                    <!--   <button type="button" class="btn btn-sm btn-success" id="btnSubmitDccValidation"><i class="fa fa-check-circle"></i> Submit DCC Validation</button> -->
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAddaffectedDocumentDetails">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-plus-square"></i> Add Affected Document Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="formAffectedDocument" method="post">
                    @csrf

                    <div class="modal-body">

                        <div class="row">
                            <div class="col">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h5 class="card-title"><i class="fa fa-file"></i> Document Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <!--DOCUMENT NUMBER-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">DOCUMENT NUMBER</span>
                                                    </div>
                                                    <input type="text" class="form-control" id="affected_doc_no" name="affected_doc_no" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <!--DOCUMENT TITLE-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">DOCUMENT TITLE</span>
                                                    </div>
                                                    <input type="text" class="form-control" id="affected_doc_title" name="affected_doc_title" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <!--DOCUMENT REVISION NUMBER-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">REVISION NUMBER</span>
                                                    </div>
                                                    <input type="text" class="form-control" id="affected_doc_rev_no" name="affected_doc_rev_no" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h5 class="card-title"><i class="fa fa-info-circle"></i> Other Details</h5>
                                    </div>

                                    <div class="card-body">

                                        <!--PIC-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">CHECKPOINT TYPE</span>
                                                    </div>
                                                    <select class="form-control" id="affected_checkpoint_type" name="affected_checkpoint_type">
                                                        <option selected disabled>-- Select One --</option>
                                                        <option class="class-affected-doc" value="1">Affected Document</option>
                                                        <option class="class-checkpoint" value="2">FMEA</option>
                                                        <option class="class-checkpoint" value="3">Control Plan</option>
                                                        <option class="class-checkpoint-preprod" value="4">Pre-Production Checksheet</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!--PIC-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">PERSON-IN-CHARGE</span>
                                                    </div>
                                                    <select class="form-control sel-rapidx-user-list" id="affected_person_in_charge" name="affected_person_in_charge">
                                                        <option value="0" selected disabled>-- Select One --</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!--DUE DATE-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">REVISION DUE DATE</span>
                                                    </div>
                                                    <input type="date" class="form-control" id="affected_rev_due_date" name="affected_rev_due_date">
                                                </div>
                                            </div>
                                        </div>

                                        <!--ADDITION REMARKS-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">ADDITION REMARKS</span>
                                                    </div>
                                                    <textarea class="form-control" id="affected_doc_remarks" name="affected_doc_remarks" rows="3" style="resize: none;"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger mr-auto" class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i>Cancel</button>
                    <button type="button" class="btn btn-sm btn-success" id="btnSubmitAffectedDocument"><i class="fa fa-check-circle"></i> Add Document</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditaffectedDocumentDetails">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-plus-square"></i> Edit Affected Document Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="formEditAffectedDocument" method="post">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" class="form-control" id="edit_affected_doc_id" name="edit_affected_doc_id" readonly>
                        <div class="row">
                            <div class="col">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h5 class="card-title"><i class="fa fa-file"></i> Document Details</h5>
                                    </div>

                                    <div class="card-body">
                                        <!--DOCUMENT NUMBER-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">DOCUMENT NUMBER</span>
                                                    </div>
                                                    <input type="text" class="form-control" id="edit_affected_doc_no" name="edit_affected_doc_no" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <!--DOCUMENT TITLE-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">DOCUMENT TITLE</span>
                                                    </div>
                                                    <input type="text" class="form-control" id="edit_affected_doc_title" name="edit_affected_doc_title" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <!--DOCUMENT REVISION NUMBER-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">REVISION NUMBER</span>
                                                    </div>
                                                    <input type="text" class="form-control" id="edit_affected_doc_rev_no" name="edit_affected_doc_rev_no" readonly>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h5 class="card-title"><i class="fa fa-info-circle"></i> Other Details</h5>
                                    </div>

                                    <div class="card-body">
                                        <!--PIC-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">CHECKPOINT TYPE</span>
                                                    </div>
                                                    <select class="form-control" id="edit_affected_checkpoint_type"name="edit_affected_checkpoint_type">
                                                        <option selected disabled>-- Select One --</option>
                                                        <option class="class-affected-doc" value="1">Affected Document</option>
                                                        <option class="class-checkpoint" value="2">FMEA</option>
                                                        <option class="class-checkpoint" value="3">Control Plan</option>
                                                        <option class="class-checkpoint" value="4">Pre-Production Checksheet</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!--PIC-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">PERSON-IN-CHARGE</span>
                                                    </div>
                                                    <select class="form-control sel-rapidx-user-list" id="edit_affected_person_in_charge" name="edit_affected_person_in_charge">
                                                        <option value="0" selected disabled>-- Select One --</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!--DUE DATE-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">REVISION DUE DATE</span>
                                                    </div>
                                                    <input type="date" class="form-control" id="edit_affected_rev_due_date" name="edit_affected_rev_due_date">
                                                </div>
                                            </div>
                                        </div>

                                        <!--ADDITION REMARKS-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">ADDITION  REMARKS</span>
                                                    </div>
                                                    <textarea class="form-control" id="edit_affected_doc_remarks" name="edit_affected_doc_remarks" rows="3" style="resize: none;"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h5 class="card-title"><i class="fa fa-user"></i> Approver Remarks</h5>
                                    </div>

                                    <div class="card-body">
                                        <!--ADDITION REMARKS-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">APPROVER REMARKS</span>
                                                    </div>
                                                    <textarea class="form-control" id="edit_affected_approver_remarks" name="edit_affected_approver_remarks" placeholder="(Optional if Approved, Required if Disapproved)" rows="3" style="resize: none;"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger mr-auto" id="btnDisapproveDocument"><i class="fa fa-times-circle"></i> Disapprove Document</button>
                    <button type="button" class="btn btn-sm btn-secondary" class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i>Cancel</button>
                    <button type="button" class="btn btn-sm btn-success" id="btnSubmitEditAffectedDocument"><i class="fa fa-check-circle"></i> Edit Document</button>
                </div>
            </div>
        </div>
    </div>

    <!--MODAL HEAD APPROVERS-->
    <div class="modal fade" id="modalTest">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-check-circle"></i> Edit PDF File</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="formEditPdf" method="post">
                        @csrf
                        <div class="row">
                            <!--APPLICATION DETAILS-->
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col">
                                        <div class="card card-primary">
                                            <div class="card-header">
                                                <h5 class="card-title"><i class="fa fa-file"></i> Document Details </h5>
                                            </div>
                                            <div class="card-body">
                                                <h5>Input Data & Assign Coordinates</h5>

                                                <!-- Button to generate a new row -->
                                                <button type="button" class="btn btn-success" id="AddRowButton">Add Row</button>
                                                <input type="hidden" class="form-control form-control-sm" id="txtApplicationId" name="application_id">
                                                <input type="hidden" class="form-control form-control-sm" id="txtFilePath" name="file_path" data-pdfPath="">
                                                <input type="hidden" class="form-control form-control-sm" id="txtFileName" name="file_name" data-pdfName="">
                                                <!-- Table to append rows to -->
                                                <table class="table mt-3" id="viewDataCoordinateTable">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Data</th>
                                                            <th>Font Size</th>
                                                            <th>Page #</th>
                                                            <th>Coordinates</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary" id="btnSaveChanges">
                        <i class="fa fa-check-circle"></i> Save to Drafts
                    </button>

                    <button type="button" class="btn btn-sm btn-success" id="btnSubmitChanges">
                        <i class="fa fa-check-circle"></i> Submit Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Previewing PDF (Full Screen) -->
    <div class="modal fade" id="pdfEditPdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfEditPdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl1-custom" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfEditPdfModalLabel">Edit PDF</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="pdfEditPdfWrapper">
                    <!-- Page selection and coordinates input field in one row -->
                    <div class="row mx-auto">
                        <!-- Page Selection Dropdown -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="pageSelectEditPdf">Select Page</label>
                                <select class="form-control form-control-sm" id="pageSelectEditPdf">
                                <!-- Options will be added dynamically based on the number of pages -->
                                </select>
                            </div>
                        </div>

                        <!-- Coordinates Input Field -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="coordinatesInputEditPdf">Selected Coordinates</label>
                                <input type="text" class="form-control form-control-sm" id="coordinatesInputEditPdf" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center" id="pdfEditPdfContainer">
                        <!-- PDF preview will be rendered here -->
                        <div class="col-auto">
                            <div id="pdfEditPdfCanvas" class="text-center mx-3" style="border: solid 1px;">
                                <!-- Rendered PDF page will be added here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- Submit button to finalize coordinate selection -->
                    <button type="button" class="btn btn-sm btn-primary" id="submitCoordinateEditPdfButton" disabled>Submit Coordinates</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm to delete MODAL START -->
        <div class="modal fade" id="modalConfirmToProceed">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger bg-gradient">
                        <h4 class="modal-title"><i class="fa-solid fa-trash-can"></i>&nbsp;&nbsp;Delete Application?</h4>
                        <button type="button" style="color: #fff" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" id="ChangeApplicationStatus">
                          @csrf
                        <div class="modal-body">
                            <div class="row d-flex justify-content-center">
                                <label class="text-secondary mt-2">Are you sure you want to delete this application?</label>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" id="btnConfirmProceed" class="btn btn-danger">Proceed</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <!-- Confirm to delete MODAL END -->

    <!-- Confirm to proceed MODAL START -->
        <div class="modal fade" id="modalSubmitApplication">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success bg-gradient">
                        <h4 class="modal-title"><i class="fa-solid fa-trash-can"></i>&nbsp;&nbsp;Submit Application?</h4>
                        <button type="button" style="color: #fff" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" id="ChangeApplicationStatus">
                        @csrf
                        <div class="modal-body">
                            <div class="row d-flex justify-content-center">
                                <label class="text-secondary mt-2">Are you sure you want to submit this application?</label>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" id="btnFinalSubmit" class="btn btn-success">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <!-- Confirm to proceed MODAL END -->

@endsection
@section('js_content')
    <script type="text/javascript">
        let arrayAffectedDocuments = [];

        $(document).ready(function(){
            let editPdfFile = null;
            let editPdfCurrentPage = 1; // Current page of the PDF
            let currentTextToPlace = '';
            let currentFontSizeToPlace = '';
            let currentRow = null; // To track which row will get the coordinates

            $(document).on('click', '.btnSubmitNewApplication', function(){
                let application_id = $(this).attr('application-id');
                $('#modalSubmitApplication').modal('show');

                // Bind confirm click (one-time only)
                $('#btnFinalSubmit').one('click', function(){
                    ChangeApplicationStatus(application_id, 1);
                });
            });

            $(document).on('click', '.btnCancelNewApplication', function(){
                let application_id = $(this).attr('application-id');
                $('#modalConfirmToProceed').modal('show');

                // Bind confirm click (one-time only)
                $('#btnConfirmProceed').one('click', function(){
                    ChangeApplicationStatus(application_id, 6);
                });
            });

            // Delete a row
            $(document).on('click', '.deleteAddedRow', function() {
                $(this).closest('tr').remove();
                // Update row numbers
                $('#viewDataCoordinateTable tbody tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
            });

            $('#AddRowButton').on('click', function(){
                var rowId = Date.now(); // Unique ID
                var addRowCount = $('#viewDataCoordinateTable tbody tr').length + 1;
                var rowHtml = `
                    <tr>
                        <td id="approvalOrder-${rowId}">${addRowCount}</td>
                        <td>
                            <input type="text" class="form-control form-control-sm txtAddData" id="txtAddData-${rowId}">
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm txtAddFontSize" id="txtAddFontSize-${rowId}">
                        </td>
                        <td id="pageNumber-${rowId}">N/A</td>
                        <td id="coordinates-${rowId}">No coordinates selected</td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm setCoordinatePdf" data-row="${rowId}">Set Coordinate</button>
                            <button type="button" class="btn btn-danger btn-sm deleteAddedRow">Delete Row</button>
                        </td>
                    </tr>
                `;
                $('#viewDataCoordinateTable tbody').append(rowHtml);
            });

            $(document).on('click', '.btn-edit-pdf', function() {
                let application_id = $(this).attr('application-id');
                $.ajax({
                    type: "get",
                    url: "get_application_attachment",
                    data:
                    {
                        application_id: application_id
                    },
                    dataType: "json",
                    success: function (response) {
                        $('#txtApplicationId').val(application_id);
                        $('#txtFilePath').val(response.file_path);
                        $('#txtFileName').val(response.file_name);
                        $('#viewDataCoordinateTable tbody').empty();
                        loadPatchData(application_id);
                    }
                });
            });

            function loadPatchData(applicationId) {
                $.ajax({
                    url: 'get_patch_data',
                    method: 'GET',
                    data: { application_id: applicationId },
                    dataType: 'json',
                    success: function(response) {
                        let tbody = $('#viewDataCoordinateTable tbody');
                        tbody.empty();

                        response.forEach((item, index) => {
                            const rowId = Date.now() + index;
                            // const rowId = Date.now(); // Unique ID
                            let coords = item.coordinates.split('|'); // "0.1885|0.1728" → [0.1885, 0.1728]
                            let coordinateText = `X: ${coords[0]}, Y: ${coords[1]}`;

                            // let coordinate = item.coordinates || "No coordinates selected";
                            let row = `
                                <tr>
                                    <td id="approvalOrder-${index + 1}">${index + 1}</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm txtAddData" id="txtAddData-${index + 1}" value="${item.patch_data ?? ''}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm txtAddFontSize" id="txtAddFontSize-${index + 1}" value="${item.font_size ?? 12}">
                                    </td>
                                    <td id="pageNumber-${index + 1}">${item.page_no}</td>
                                    <td id="coordinates-${index + 1}">${coordinateText}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm setCoordinatePdf" data-row="${index + 1}">Set Coordinate</button>
                                        <button type="button" class="btn btn-danger btn-sm deleteAddedRow">Delete Row</button>
                                    </td>
                                </tr>
                            `;
                            tbody.append(row);
                        });
                    },
                    error: function(err) {
                        toastr.error('Failed to load patch data.');
                        console.error(err);
                    }
                });
            }

            // Handle page selection change
            $('#pageSelectEditPdf').on('change', function() {
                const selected = parseInt($(this).val(), 10);
                if (!isNaN(selected)) {
                    editPdfCurrentPage = selected;
                    loadEditPdf(editPdfCurrentPage);
                } else {
                    console.warn('Invalid page selection:', $(this).val());
                }
            });

            $(document).on('click', '.setCoordinatePdf', function(){
                currentTextToPlace = '';
                const pdfFilePath = $('#txtFilePath').val();
                console.log('pdfFilePath', pdfFilePath);

                if (!pdfFilePath) {
                    alert('No PDF path found. Please fetch the file first.');
                    return;
                }

                var rowId = $(this).data('row');
                console.log('this', $(this));

                currentRow = rowId; // Store the current row for coordinates insertion
                // if(currentRow == 1){
                    var data = $(this).closest('tr').find('#txtAddData-' + currentRow).val();

                    if (!data){
                        alert('Please Insert Data first.');
                        return;
                    }

                    currentTextToPlace = data;
                    // $('#addDocNo').attr('src', docNo).show();
                    // $('#addDocNo').css('display', 'none');
                // }

                // if(currentRow == 2){
                    var fontSize = $(this).closest('tr').find('#txtAddFontSize-' + currentRow).val();

                    if (!fontSize){
                        alert('Please Insert Font Size first.');
                        return;
                    }

                    currentFontSizeToPlace = fontSize;
                    // $('#addEffectiveDate').attr('src', effectiveDate).show();
                    // $('#addEffectiveDate').css('display', 'none');
                // }

                if (pdfFilePath) {
                    // Clear previous preview if any
                    $('#pdfEditPdfCanvas').html('');

                    // Using pdf.js to load the PDF
                    pdfjsLib.getDocument(pdfFilePath).promise.then(function(pdf){
                        currentPdf = pdf;
                        totalPages = pdf.numPages;

                        // Update page select dropdown
                        $('#pageSelectEditPdf').empty();
                        for (var i = 1; i <= totalPages; i++) {
                            $('#pageSelectEditPdf').append('<option value="' + i + '">Page ' + i + '</option>');
                        }

                        // Load the first page
                        loadEditPdf(editPdfCurrentPage);

                        // Show the modal for previewing the PDF
                        $('#pdfEditPdfModal').modal('show');
                    });
                }
            });

            function loadEditPdf(pageNumber) {
                currentPdf.getPage(pageNumber).then(function(page){
                    // Get the original viewport (scale = 1)
                    const viewport = page.getViewport({ scale: 1 });

                    // Create a canvas to draw the PDF page
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');

                    // Set canvas dimensions to match original PDF size
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;

                    // Define render context
                    const renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };

                    // Clear old canvas
                    $('#pdfEditPdfCanvas').html('');
                    $('#pdfEditPdfCanvas').append(canvas);

                    page.render(renderContext).promise.then(() => {
                        console.log('Page rendered successfully:', pageNumber);
                    }).catch(error => {
                        console.error('Error rendering page:', error);
                    });

                    // ✅ Store the zoomed-in dimensions
                    $('#pdfEditPdfCanvas').data('pdfWidth', viewport.width);
                    $('#pdfEditPdfCanvas').data('pdfHeight', viewport.height);
                });
            }

            // function loadEditPdf(pageNumber){
            //     currentPdf.getPage(pageNumber).then(function(page) {
            //         // Dynamic Scaling - Chris
            //         page.getViewport({ scale: 1 });

            //         const desiredWidth = 827; // A4 at 96 DPI or your target container
            //         const baseViewport = page.getViewport({ scale: 1 }); // no scaling yet
            //         const scale = desiredWidth / baseViewport.width;
            //         const viewport = page.getViewport({ scale }); // dynamically scaled!
            //         const canvas = document.createElement('canvas');
            //         const context = canvas.getContext('2d');

            //         // ✅ Update the canvas size to match the zoomed-in view
            //         canvas.width = viewport.width;
            //         canvas.height = viewport.height;

            //         const renderContext = {
            //             canvasContext: context,
            //             viewport: viewport
            //         };

            //         // Clear old canvas
            //         $('#pdfEditPdfCanvas').html('');
            //         $('#pdfEditPdfCanvas').append(canvas);

            //         page.render(renderContext).promise.then(() => {
            //             console.log('Page rendered successfully:', pageNumber);
            //         }).catch(error => {
            //             console.error('Error rendering page:', error);
            //         });

            //         // ✅ Store the zoomed-in dimensions
            //         $('#pdfEditPdfCanvas').data('pdfWidth', viewport.width);
            //         $('#pdfEditPdfCanvas').data('pdfHeight', viewport.height);
            //     });
            // }

            // Handle click on the PDF preview to select coordinates
            $('#pdfEditPdfCanvas').on('click', function(event) {
                const canvas = $(this).find('canvas')[0];
                const rect = canvas.getBoundingClientRect();

                // Seting the ordinates
                // changing format from pixels to mm
                const clickedX = event.clientX - rect.left;
                const clickedY = event.clientY - rect.top;

                const xMM = (clickedX / canvas.width);
                const yMM = (clickedY / canvas.height);
                const formattedX = xMM.toFixed(4);
                const formattedY = yMM.toFixed(4);

                // ✅ Get the stored zoomed-in dimensions
                var pdfwidth = $('#pdfEditPdfCanvas').data('pdfWidth');
                var pdfheight = $('#pdfEditPdfCanvas').data('pdfHeight');

                selectedEditPDFCoordinates = {
                    x: formattedX,
                    y: formattedY,
                    canvasWidth: canvas.width,
                    canvasHeight: canvas.height,
                    page: editPdfCurrentPage,
                    pdfWidth: pdfwidth,
                    pdfHeight: pdfheight,
                    text: currentTextToPlace,        // ✅ The text value to be placed on the PDF
                    fontSize: currentFontSizeToPlace // ✅ The font size value to be placed on the PDF
                };

                $('#coordinatesInputEditPdf').val(`X: ${formattedX}, Y: ${formattedY}`);

                // ✅ Clear old placed signature preview
                $('#pdfEditPdfCanvas .previewTextOverlay').remove();

                if (currentTextToPlace) {
                    const textOverlay = $('<div class="previewTextOverlay"></div>')
                        .text(currentTextToPlace)
                        .css({
                            position: 'absolute',
                            left: clickedX + 'px',
                            top: clickedY + 'px',
                            fontSize: currentFontSizeToPlace + 'px',
                            transform: 'translate(-50%, -50%)',
                            pointerEvents: 'none',
                            border: '2px solid #333', // border
                            padding: '3px',            // visual padding inside border
                            backgroundColor: '#fff',   // white bg for contrast
                            boxShadow: '0 0 5px rgba(0,0,0,0.3)'
                        });

                    $('#pdfEditPdfCanvas').append(textOverlay);
                }

                // ✅ Enable submission after placing signature
                $('#submitCoordinateEditPdfButton').prop('disabled', false);
            });

            // Submit the coordinates to the selected row
            $('#submitCoordinateEditPdfButton').on('click', function(){
                console.log('currentRow', currentRow);
                console.log('X', selectedEditPDFCoordinates.x);
                console.log('Y', selectedEditPDFCoordinates.y);

                if (selectedEditPDFCoordinates.x !== null && selectedEditPDFCoordinates.y !== null) {
                    // Update the coordinates column of the selected row
                    $('#coordinates-' + currentRow).text('X: ' + selectedEditPDFCoordinates.x + ', Y: ' + selectedEditPDFCoordinates.y);

                    // Update the page number column of the selected row
                    $('#pageNumber-' + currentRow).text(editPdfCurrentPage);  // Update the "Page #" column

                    // Close the modal after submitting coordinates
                    $('#pdfEditPdfModal').modal('hide');
                    $('#pageSelectEditPdf').val(1).trigger('change');
                    // Reset the selected coordinates
                    selectedEditPDFCoordinates = null;
                    $('#submitCoordinateEditPdfButton').prop('disabled', true); // Disable button again until new coordinates are selected
                }else{
                    alert('Please select coordinates first!');
                }
            });

            let patchActionType;
            $('#btnSaveChanges').click(function() {
                patchActionType = 'save_to_draft';
                $('#formEditPdf').submit();
            });

            $('#btnSubmitChanges').click(function() {
                patchActionType = 'final_submit';
                $('#formEditPdf').submit();
            });

            $('#formEditPdf').submit(function(e) {
                e.preventDefault();
                SubmitEditPdfAttachment(patchActionType);
            });

            bsCustomFileInput.init();
            LoadAcdcsLayout();
            LoadRapidXDepartmentList($('.sel-rapidx-department-list'));
            LoadRapidXDepartmentList($('.sel-rapidx-department-list-2'));
            LoadRapidXUserList($('.sel-rapidx-user-list'));
            LoadOriginatorList($('.sel-originator-list'));
            // LoadSectionHeadList($('.sel-rapidx-section-heads'));
            // LoadProdHeadList($('.sel-rapidx-prod-head'));
            // LoadQcHeadList($('.sel-rapidx-qc-head'));
            // LoadEngHeadList($('.sel-rapidx-eng-head'));

            // $('#SelectApprover').select2({
            //     theme: "bootstrap4",
            // });

            // $('.select2bs5').select2({
            //     theme: 'bootstrap-4',
            // });

            $('.sel-filter-status').select2({
                theme: "bootstrap4",
            });

            $('.sel-originator-list').select2({
                theme: "bootstrap4",
            });

            $('.sel-rapidx-department-list-2').select2({
                theme: "bootstrap4",
            });

            dt_applications = $('#tbl_applications').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "load_acdcs_applications_table_test",
                    // url: "load_acdcs_applications_table",
                    data: function(param) {
                        param.check_section_department = $('#hidden_check_section_dept').val();
                        param.check_originator = $('#hidden_check_originator').val();
                        param.originator = $('#filter_originator').val();
                        param.section_department = $('#filter_department').val();
                        param.check_status = $('#hidden_check_status').val();
                        param.status = $('#filter_status').val();
                    }
                },
                "columns": [
                    { "data": "action" },
                    { "data": "approver_status", width: '50px'},
                    { "data": "control_number" },
                    { "data": "status" },
                    { "data": "application_datetime" },
                    { "data": "originator" },
                    { "data": "section_dept" },
                    { "data": "doc_no" },
                    { "data": "doc_title" },
                    { "data": "rev_no" },
                    { "data": "uploaded_file" },
                    { "data": "uploaded_excel_file" },
                    { "data": "application_approvers" },
                    { "data": "originator_remarks" },
                ],
                "order": [2, 'desc'],
                initComplete: function() {
                    // Set checkbox checked
                    $('#check_filter_status').prop('checked', true);
                    $('#check_filter_status').trigger('change'); // safe here
                }
                // "columnDefs": [
                //     { width: '50px', targets: 11 } // 3rd column (0-based index)
                // ]
            });

            dt_affected_documents = $('#tbl_affected_documents').DataTable({
                "paging": false,
                "info": false,
                "searching": false,
                "ordering": false,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "load_originator_affected_documents_table",
                    data: function(param) {
                        param.array_documents = arrayAffectedDocuments;
                    }
                },
                "columns": [
                    { "data": "doc_no" },
                    { "data": "doc_title" },
                    { "data": "rev_no" },
                    { "data": "person_in_charge" },
                    { "data": "revision_due_date" },
                    { "data": "action" },
                ],
            });

            dt_add_document_details = $('#tbl_add_document_details').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "load_acdcs_documents_table",
                    data: function(param) {
                        param.document_hidden_action = $("#document_hidden_action").val();
                        param.document_search_type = $('#document_search_type').val();
                        param.document_wildcard = $('#document_wildcard').val();
                    }
                },
                "columns": [
                    { "data": "doc_no" },
                    { "data": "doc_title" },
                    { "data": "rev_no" },
                    { "data": "action" },
                ],
            });

            dt_qs_validations = $('#tbl_qs_validations').DataTable({
                "paging": false,
                "info": false,
                "searching": false,
                "ordering": false,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "load_qs_validation_checkpoints_table",
                    data: function(param) {
                        param.array_documents = arrayAffectedDocuments;
                    }
                },
                "columns": [
                    { "data": "checkpoint_type" },
                    { "data": "doc_no" },
                    { "data": "doc_title" },
                    { "data": "rev_no" },
                    { "data": "person_in_charge" },
                    { "data": "revision_due_date" },
                    { "data": "action" },
                ],
            });

            dt_head_approval_documents = $('#tbl_head_approval_documents').DataTable({
                "paging": false,
                "info": false,
                "searching": false,
                /*"ordering": false,*/
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "load_approver_checkpoints_table",
                    data: function(param) {
                        param.array_documents = arrayAffectedDocuments;
                        // param.array_documents = [8];
                    }
                },
                "columns": [
                    { "data": "checkpoint_type" },
                    { "data": "doc_no" },
                    { "data": "doc_title" },
                    { "data": "rev_no" },
                    { "data": "person_in_charge" },
                    { "data": "revision_due_date" },
                    { "data": "action" },
                ],
            });

            dt_dcc_affected_documents = $('#tbl_dcc_affected_documents').DataTable({
                "paging": false,
                "info": false,
                "searching": false,
                "ordering": false,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "load_new_affected_documents_table",
                    data: function(param) {
                        param.application_id = $('#dcc_application_id').val();
                    }
                },
                "columns": [
                    { "data": "checkpoint_type" },
                    { "data": "doc_no" },
                    { "data": "doc_title" },
                    { "data": "rev_no" },
                    { "data": "person_in_charge" },
                    { "data": "revision_due_date" },
                ],
            });

            //VIEW TABLES
            dt_view_affected_documents = $('#tbl_view_affected_documents').DataTable({
                "paging": false,
                "info": false,
                "searching": false,
                "ordering": false,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "load_new_affected_documents_table",
                    data: function(param) {
                        param.application_id = $('#view_application_id').val();
                    }
                },
                "columns": [
                    { "data": "checkpoint_type" },
                    { "data": "doc_no" },
                    { "data": "doc_title" },
                    { "data": "rev_no" },
                    { "data": "person_in_charge" },
                    { "data": "revision_due_date" },
                    { "data": "originator_remarks" },
                ],
            });

            dt_view_dcc_validations = $('#tbl_view_dcc_validations').DataTable({
                "paging": false,
                "info": false,
                "searching": false,
                "ordering": false,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "load_new_dcc_validations_table",
                    data: function(param) {
                        param.application_id = $("#view_application_id").val();
                    }
                },
                "columns": [
                    { "data": "validation_datetime" },
                    { "data": "dcc_in_charge" },
                    { "data": "judgement" },
                    { "data": "checkpoint_similar" },
                    { "data": "checkpoint_alignment" },
                    { "data": "checkpoint_standard" },
                    { "data": "dcc_remarks" },
                ],
            });

            dt_view_approvals = $('#tbl_view_approvals').DataTable({

                "paging": false,
                "info": false,
                "searching": false,
                "ordering": false,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "load_new_head_approvals_table",
                    data: function(param) {
                        param.application_id = $("#view_application_id").val();
                    }
                },
                "columns": [
                    { "data": "approval_datetime" },
                    { "data": "approver" },
                    { "data": "approving_as" },
                    { "data": "judgement" },
                    { "data": "approval_remarks" },
                ],
            });
        });

        // $('#add_doc_category').change(function() {
        //     const trigger_category = '1,2,3,10,11,12';
        //     if(trigger_category.includes($('#add_doc_category').val())){
        //         console.log('true');
        //         // $('.operation-approvers').removeClass('d-none');
        //         $('.qas-head-approval').removeClass('d-none');
        //         $('#add_section_head_approver').prop('disabled', false);
        //     }else{
        //         console.log('false');
        //         $('.qas-head-approval').addClass('d-none');
        //         $('#add_section_head_approver').prop('disabled', true);
        //         // $('.operation-approvers').addClass('d-none');

        //     }
        // });

        $('#view_doc_category').change(function() {
            // LoadSectionHeadList($('.sel-rapidx-section-heads'));
            const trigger_category = '1,2,3,10,11,12';
            if(trigger_category.includes($('#view_doc_category').val())){
                $('.view-qas-head-approver').removeClass('d-none');
                // $('#view_section_head_approver').prop('disabled', false);
            }else{
                // $('#view_section_head_approver').prop('disabled', true);
                $('.view-qas-head-approver').addClass('d-none');
            }
        });

        $('#btnAddNewApplication').click(function (e) {
            e.preventDefault();
            console.log('testlcick');
            $('#uploadTab a[href="#home"]').tab('show');
        });

        $('#modalAddApplication').on('hidden.bs.modal', function(){
            $('.sg-approvers').addClass('d-none');
            $('.operation-approvers').addClass('d-none');
            $('#formAddApplication')[0].reset();

            $('#withEsignature').prop('disabled', true);
            $('#withEsignature').prop('checked', false);
            $('#set-coordinates-tab').addClass('disabled');
            $('#dynamicTable tbody').empty();
            arrayAffectedDocuments = [];
            dt_affected_documents.draw();
        });

        $(document).on('click', '.btn-document-action', function() {

            let document_action = $(this).attr('document-action');
            $('#document_hidden_action').val(document_action);

            if(document_action == 1 || document_action == 4){
                $('.btn-preprod').addClass('d-none');
            }else{
                $('.btn-preprod').removeClass('d-none');
            }
        });

        $('#document_wildcard').on('keyup', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                dt_add_document_details.draw();
            }
        });

        $('#btnSearchDocument').click(function() {
            dt_add_document_details.draw();
        });

        $('#modalSearchDocumentDetails').on('hidden.bs.modal', function() {
            $('#document_search_type').prop('selectedIndex', 0);
            $('#document_wildcard').val('');
            dt_add_document_details.draw();
        });

        $('#add_doc_type').on('change', function() {
            $('#add_doc_title').val('');
            $('#add_doc_no').val('');
            $('#add_doc_rev_no').val('');

            if ($('#add_doc_type').val() == 1) {
                $('#add_doc_rev_no').val(0);
                $('#add_doc_title').removeAttr('readonly');
                $('#btnAddDocumentDetails').prop('disabled', 'disabled');
            } else {
                $('#add_doc_rev_no').val('');
                $('#add_doc_title').removeAttr('readonly');
                $('#btnAddDocumentDetails').removeAttr('disabled');
            }
        });

        //add document details ------
        $(document).on('click', '.btn-add-document-details', function() {
            console.log('clicked add-document-details');

            let active_doc_id = $(this).attr('active-doc-id');
            let addition_type = $(this).attr('addition-type');
            LoadAidrcv2AddDocumentDetails(active_doc_id, addition_type);
        });

        $('#btnSubmitApplication').click(function() {
            $('#formAddApplication').submit();
        });

        $('#formAddApplication').submit(function(e) {
            e.preventDefault();
            SubmitNewApplication(arrayAffectedDocuments);
        });

        //QS VALIDATION
        $(document).on('click', '.btn-qs-validation', function() {
            let application_id = $(this).attr('application-id');
            LoadQsApplicationDetails(application_id);
            RetrieveDocumentsForQsInspection(application_id);
        });

        $('#btnQsApproveApplication').click(function() {
            $('#qs_validation_status').val(1);
            $('#formQsValidation').submit();
        });

        $('#btnQsDisapproveApplication').click(function() {
            $('#qs_validation_status').val(2);
            $('#formQsValidation').submit();
        });

        $('#formQsValidation').submit(function(e) {
            e.preventDefault();
            SubmitQsValidation(arrayAffectedDocuments);
        });

        //HEAD APPROVAL
        $(document).on('click', '.btn-head-approval', function() {
            let application_id = $(this).attr('application-id');
            let approving_as = $(this).attr('approving-as');
            let approval_order = $(this).attr('approval_order');
            LoadHeadApplicationDetails(application_id, approving_as, approval_order);
            RetrieveDocumentsForApproval(application_id, approving_as);
        });

        $('#btnHeadApproveApplication').click(function() {
            $('#head_approval_status').val(1);
            $('#formHeadApproval').submit();
        });

        $('#btnHeadDisapproveApplication').click(function() {
            $('#head_approval_status').val(2);
            $('#formHeadApproval').submit();
        });

        //HEAD APPROVAL
        $('#formHeadApproval').submit(function(e) {
            e.preventDefault();
            SubmitHeadApproval(arrayAffectedDocuments);
        });

        $(document).on('click', '.btn-dcc-validation', function() {
            let application_id = $(this).attr('application-id');
            LoadDccApplicationDetails(application_id);
        });

        $('#btnSubmitDccValidation').click(function() {
            $('#formDccValidation').submit();
        });

        $('#formDccValidation').submit(function(e){
            e.preventDefault();
            SubmitDccValidation();
        });

        $(document).on('click', '.btn-view-application', function() {
            let application_id = $(this).attr('application-id');
            let view_edit = $(this).attr('view-edit');

            $('#uploadTab a[href="#editHome"]').tab('show');
            LoadViewApplicationDetails(application_id, view_edit);
        });

        $('#edit_attachment').on('change', function () {
            let application_id = $(this).data('application-id');

            // reload with the uploaded file
            LoadViewApplicationDetails(application_id, 1);
        });

        $('#btnEditApplicationDetails').click(function() {
            $('#edit_attachment').prop('required', true);
            $('#edit_attachment_excel').prop('required', true);

            $('#edit_attachment').removeAttr('disabled');
            $('#edit_attachment_excel').removeAttr('disabled');
            $('#view_doc_category').removeAttr('disabled');
            $('#view_doc_title').removeAttr('readonly');
            $('#view_remarks').removeAttr('readonly');

            $('#btnCancelEditApplication').removeAttr('disabled');
            $('#btnSubmitEditApplication').removeAttr('disabled');
            $('#btnCancelApplication').removeAttr('disabled');

            $('#view_qc_head').removeAttr('disabled');
            $('#view_eng_head').removeAttr('disabled');
            $('#view_production_head').removeAttr('disabled');
            $('#view_qs_inspector').removeAttr('disabled');
            $('#view_section_head_approver').removeAttr('disabled');
            $('.view_approver_priority').removeAttr('disabled');
        });

        $('#modalViewApplication').on('hidden.bs.modal', function() {
            $('#edit_attachment').prop('disabled', 'disabled');
            $('#edit_attachment_excel').prop('disabled', 'disabled');
            $('#view_doc_category').prop('disabled', 'disabled');
            $('#view_doc_title').prop('readonly', 'readonly');
            $('#view_remarks').prop('readonly', 'readonly');

            $('#btnCancelEditApplication').prop('disabled', 'disabled');
            $('#btnSubmitEditApplication').prop('disabled', 'disabled');
            $('#btnCancelApplication').prop('disabled', 'disabled');

            $('#view_qc_head').prop('disabled', 'disabled');
            $('#view_eng_head').prop('disabled', 'disabled');
            $('#view_production_head').prop('disabled', 'disabled');
            $('#view_qs_inspector').prop('disabled', 'disabled');
            $('#view_section_head_approver').prop('disabled', 'disabled');
            $('.view_approver_priority').prop('disabled', 'disabled');
        });

        $('#btnCancelEditApplication').click(function() {
            let view_application_id = $('#view_application_id').val();
            let view_edit = "1";

            LoadViewApplicationDetails(view_application_id, view_edit);

            $('#edit_attachment').prop('required', false);
            $('#edit_attachment_excel').prop('required', false);

            $('#edit_attachment').prop('disabled', 'disabled');
            $('#edit_attachment_excel').prop('disabled', 'disabled');
            $('#view_doc_category').prop('disabled', 'disabled');
            $('#view_doc_title').prop('readonly', 'readonly');
            $('#view_remarks').prop('readonly', 'readonly');
            $('#btnCancelEditApplication').prop('disabled', 'disabled');
            $('#btnSubmitEditApplication').prop('disabled', 'disabled');
            $('#btnCancelApplication').prop('disabled', 'disabled');
            $('#view_qc_head').prop('disabled', 'disabled');
            $('#view_eng_head').prop('disabled', 'disabled');
            $('#view_production_head').prop('disabled', 'disabled');
            $('#view_qs_inspector').prop('disabled', 'disabled');
            $('#view_section_head_approver').prop('disabled', 'disabled');
            $('.view_approver_priority').prop('disabled', 'disabled');
        });

        $('#btnSubmitEditApplication').click(function() {
            $('#formEditApplication').submit();
        });

        $('#formEditApplication').submit(function(e) {
            e.preventDefault();
            SubmitEditApplication();
        });

        $(document).on('click', '.btn-add-affected-document-details', function() {
            let active_doc_id = $(this).attr('active-doc-id');
            let addition_type = $(this).attr('addition-type');
            LoadAffectedDocumentDetails(active_doc_id, addition_type);
        });

        $('#btnSubmitAffectedDocument').click(function() {
            $('#formAffectedDocument').submit();
        });

        $('#formAffectedDocument').submit(function(e) {
            e.preventDefault();
            SubmitAffectedDocument(arrayAffectedDocuments);
        });

        $('#modalQSValidation').on('hidden.bs.modal', function() {
            arrayAffectedDocuments = [];
            $('#formQsValidation')[0].reset();
            $('#qs_validation_remarks').removeClass('is-invalid');
        });

        function RetrieveDocumentsForApproval(application_id, approving_as) {
            $.ajax({
                url: "retrieve_documents_for_approval",
                method: "get",
                data: {
                    application_id: application_id,
                    approving_as: approving_as,
                },
                dataType: "json",
                beforeSend: function() {
                },
                success: function(JsonObject) {
                    if (JsonObject['result'] == 1) {
                        arrayAffectedDocuments = JsonObject['array_documents'];
                    } else {
                        arrayAffectedDocuments = [];
                    }
                    dt_head_approval_documents.draw();
                },
                error: function(data, xhr, status) {
                    toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" +
                        "Status: " + status);
                }
            });
        }

        function RetrieveDocumentsForQsInspection(application_id) {
            $.ajax({
                url: "retrive_documents_for_qs_inspection",
                method: "get",
                data: {
                    application_id: application_id,
                },
                dataType: "json",
                beforeSend: function() {
                },
                success: function(JsonObject) {
                    if (JsonObject['result'] == 1) {
                        arrayAffectedDocuments = JsonObject['array_documents'];
                    } else {
                        arrayAffectedDocuments = [];
                    }
                    dt_qs_validations.draw();
                },
                error: function(data, xhr, status) {
                    toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" +
                        "Status: " + status);
                }
            });
        }

        $(document).on('click', '.btn-edit-affected-document-details', function() {
            let affected_doc_id = $(this).attr('affected-doc-id');
            LoadAffectedDocDetails(affected_doc_id);
        });

        $('#btnDisapproveDocument').click(function() {
            if ($('#edit_affected_approver_remarks').val() == '' || $('#edit_affected_approver_remarks').val() ==
                null) {
                $('#edit_affected_approver_remarks').addClass('is-invalid');
                toastr.error('Please add your disapproval remarks!');
            } else {
                let affected_doc_id = $('#edit_affected_doc_id').val();
                let approver_remarks = $('#edit_affected_approver_remarks').val();
                DisapproveAffectedDocument(affected_doc_id, approver_remarks);
            }
        });

        $('#modalEditaffectedDocumentDetails').on('hidden.bs.modal', function() {
            $('#edit_affected_approver_remarks').removeClass('is-invalid');
            $('#formEditAffectedDocument')[0].reset();
        });

        $('#btnSubmitEditAffectedDocument').click(function() {
            $('#formEditAffectedDocument').submit();
        });

        $('#formEditAffectedDocument').submit(function(e) {
            e.preventDefault();
            SubmitEditAffectedDocument();
        });

        $(document).on('click', '.btn-preprod', function() {
            $('#affected_doc_no').val('---');
            $('#affected_doc_title').val('Pre-Production Checksheet');
            $('#affected_doc_rev_no').val('---');
            $('#affected_checkpoint_type').val(4).trigger('change');
            $('.class-checkpoint-preprod').removeAttr('hidden');
            $('.class-affected-doc').prop('hidden', 'hidden');
            $('.class-checkpoint').prop('hidden', 'hidden');
        });

        $('#btnCancelApplication').click(function() {
            // Show the modal first
            $('#modalConfirmToProceed').modal('show');

            // Bind confirm click (one-time only)
            $('#btnConfirmProceed').one('click', function(){
                console.log('deleted');
                let application_id = $('#view_application_id').val();
                NewCancelApplication(application_id);
                $('#modalConfirmToProceed').modal('hide');
            });
        });

        // $('#btnCancelApplication').click(function() {
        //     // modalConfirmToProceed
        //     let application_id = $('#view_application_id').val();
        //     NewCancelApplication(application_id);
        // });

        function RemoveDocument(document_id) {
            $.ajax({
                url: "remove_document",
                method: "get",
                data: {
                    document_id: document_id,
                },
                dataType: "json",
                beforeSend: function() {

                },
                success: function(JsonObject) {
                    if (JsonObject['result'] == 1) {
                        toastr.warning('Removed Document!');
                        dt_qs_validations.draw();
                        dt_affected_documents.draw();
                    } else {
                        toastr.error('Error Removing Document!');
                    }
                },
                error: function(data, xhr, status) {
                    toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" +
                        "Status: " + status);
                }
            });
        }

        $(document).on('click', '.btn-remove-document', function(){
            let document_id = $(this).attr('document-id');
            RemoveDocument(document_id);
        });

        $('#check_filter_section_dept').change(function(e) {
            if($('#check_filter_section_dept').is(':checked')){
                $('#hidden_check_section_dept').val(1);
                $('#filter_department').removeAttr('disabled');
            }else{
                $('#hidden_check_section_dept').val('');
                $('#filter_department').prop('disabled', 'disabled');
            }
        });

        $('#check_filter_originator').change(function(e) {
            if($('#check_filter_originator').is(':checked')){
                $('#hidden_check_originator').val(1);
                $('#filter_originator').removeAttr('disabled');
            }else{
                $('#hidden_check_originator').val('');
                $('#filter_originator').prop('disabled', 'disabled');
            }
        });

        $('#check_filter_status').change(function(e){
            if($('#check_filter_status').is(':checked')){
                $('#hidden_check_status').val(1);
                $('#filter_status').removeAttr('disabled');
            }else{
                $('#hidden_check_status').val('');
                $('#filter_status').prop('disabled', 'disabled');
            }
            dt_applications.draw();
        });

        $('#btnFilterTable').click(function(){
            dt_applications.draw();
        });

        //for auto
        $('.dcc-checkpoint').on('change', function() {
            let dcc_checkpoint_similar = $('#dcc_checkpoint_similar').val();
            let dcc_checkpoint_alignment = $('#dcc_checkpoint_alignment').val();
            let dcc_checkpoint_standard = $('#dcc_checkpoint_standard').val();
            let dcc_total = parseInt(dcc_checkpoint_similar) + parseInt(dcc_checkpoint_alignment) + parseInt(dcc_checkpoint_standard);
            console.log(dcc_total);
            if ($('#dcc_checkpoint_similar').val() != null && $('#dcc_checkpoint_alignment').val() != null && $(
                    '#dcc_checkpoint_standard').val() != null) {
                if (dcc_total == 3) {
                    $('#dcc_validation_judgement').val(1);
                    $('.dcc-passed').removeAttr('disabled');
                    $('.dcc_revisions').prop('disabled', 'disabled');
                } else {
                    $('#dcc_validation_judgement').val(2);
                    $('.dcc-passed').prop('disabled', 'disabled');
                    $('.dcc-revisions').removeAttr('disabled');
                }
            }
        });

        $('#modalDccValidations').on('hidden.bs.modal', function() {
            $('#formDccValidation')[0].reset();
            $('.dcc-revisions').prop('disabled', 'disabled');
            $('.dcc-passed').prop('disabled', 'disabled');
        });

        function SubmitEditPdfAttachment(patchActionType){
            let formData = new FormData($('#formEditPdf')[0]);
            let additionalData = [];
            // Validate: all rows must have coordinates
            let missingCoordinates = false;

            // console.log($("#viewDataCoordinateTable tbody tr").length);

            $("#viewDataCoordinateTable tbody tr").each(function(){
                let row = $(this);
                let rowIndex = row.find(".setCoordinatePdf").data("row"); // Get row number

                let approvalOrder = row.find(`#approvalOrder-${rowIndex}`).text().trim();
                let addData = row.find(`#txtAddData-${rowIndex}`).val().trim();
                let addFontSize = row.find(`#txtAddFontSize-${rowIndex}`).val().trim();
                let pageNumber = row.find(`#pageNumber-${rowIndex}`).text().trim();
                let coordinatesText = row.find(`#coordinates-${rowIndex}`).text().trim();
                // let signaturePath = row.find(`#esignature-${rowIndex}`).val();
                // let selectedApprover = row.find(`#approver-${rowIndex}`).val();

                // let canvas = $('#pdfEditPdfCanvas').find('canvas')[0];
                // let CanvasWidth = canvas.width;
                // let CanvasHeight = canvas.height;

                // ✅ Get the stored zoomed-in dimensions
                // let pdfwidth = $('#pdfEditPdfCanvas').data('pdfWidth');
                // let pdfheight = $('#pdfEditPdfCanvas').data('pdfHeight');

                if(coordinatesText !== "No coordinates selected") {
                    let matches = coordinatesText.match(/X:\s*([\d.]+),\s*Y:\s*([\d.]+)/);
                    if (matches) {
                        let x = parseFloat(matches[1]);
                        let y = parseFloat(matches[2]);

                        additionalData.push({
                            x: x,
                            y: y,
                            page: parseInt(pageNumber),
                            patchData: addData,
                            patchFontSize: addFontSize,
                        });
                    }
                }else{
                    missingCoordinates = true;
                    return false;
                }
            });

            if (missingCoordinates) {
                toastr.error("Please select coordinates for all rows before saving.");
                return;
            }

            formData.append("additionalData", JSON.stringify(additionalData)); // Append to formData
            formData.append("patchActionType", patchActionType); // Append to formData

            $.ajax({
                url: "save_pdf_patch_data", // NEW ROUTE
                method: "post",
                processData: false,
                contentType: false,
                data: formData,
                dataType: "json",
                success: function(JsonObject){
                    if(JsonObject['result'] == 1){
                        toastr.success('Patch data saved successfully!');
                        $('#modalTest').modal('hide');
                        $('#viewDataCoordinateTable tbody').empty();
                        dt_applications.draw();
                    } else {
                        toastr.error('Saving patch data failed!');
                    }
                },
                error: function(data, xhr, status){
                    toastr.error('An error occurred!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
                }
            });
        }
    </script>
@endsection

