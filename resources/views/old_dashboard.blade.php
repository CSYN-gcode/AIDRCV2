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
                            <li class="breadcrumb-item active">Dashboard</li>
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
                                        <input type="hidden" id="hidden_check_status" name="hidden_check_status">

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

                                            <input type="hidden" id="hidden_check_section_dept"
                                                name="hidden_check_section_dept">


                                            <select class="form-control sel-rapidx-department-list-2" id="filter_department"
                                                name="filter_department[]" multiple disabled>
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

                                            <input type="hidden" id="hidden_check_originator"
                                                name="hidden_check_originator">

                                            <select class="form-control sel-originator-list" id="filter_originator" multiple
                                                name="filter_originator[]" disabled>
                                            </select>

                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <button type="button" class="btn btn-info" id="btnFilterTable"
                                            title="Load Filtered Table"><i class="fa fa-retweet"></i> Filter Table</button>
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
                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal"
                                        data-target="#modalAddApplication" title="Add New AIDRC Application"><i
                                            class="fa fa-plus"></i> Add New Application</button>
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
                                                        <th>Control Number</th>
                                                        <th>Status</th>
                                                        <th>Application Date/Time</th>
                                                        <th>Originator</th>
                                                        <th>Section / Department</th>
                                                        <th>Document Number</th>
                                                        <th>Document Title</th>
                                                        <th>Rev #</th>
                                                        <th>Uploaded File</th>
                                                        <th>Application Approvers</th>
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
        <div class="modal-dialog modal-lg">
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

                        <!--APPLICATION ATTACHMENT-->
                        <div class="row">
                            <div class="col">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend w-50">
                                        <span class="input-group-text w-100" id="basic-addon1">ATTACHMENT (only accepts .PDF
                                            Files)</span>
                                    </div>

                                    <input type="file" class="form-control" id="add_attachment" name="add_attachment"
                                        accept=".pdf">

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

                                    <select class="form-control sel-rapidx-department-list" id="add_department"
                                        name="add_department">
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

                                    <textarea class="form-control" id="add_remarks" name="add_remarks" rows="3"
                                        style="resize: none;" placeholder="(Optional)"></textarea>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">

                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h5 class="card-title"><i class="fa fa-file"></i> <strong>Document
                                                Details</strong></h5>
                                    </div>

                                    <div class="card-body">

                                        <!--DOCUMENT NUMBER-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">DOCUMENT
                                                            NUMBER</span>
                                                    </div>

                                                    <input type="text" class="form-control" id="add_doc_no"
                                                        name="add_doc_no" readonly>

                                                    <div class="input-group-prepend">
                                                        <button type="button"
                                                            class="btn btn-sm btn-info btn-document-action"
                                                            document-action="1" title="Add Document Details"
                                                            id="btnAddDocumentDetails" data-toggle="modal"
                                                            data-target="#modalSearchDocumentDetails" disabled><i
                                                                class="fa fa-file"></i>&nbsp;<i
                                                                class="fa fa-plus"></i></button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <!--DOCUMENT TITLE-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">DOCUMENT
                                                            TITLE</span>
                                                    </div>

                                                    <input type="text" class="form-control" id="add_doc_title"
                                                        name="add_doc_title">

                                                </div>
                                            </div>
                                        </div>

                                        <!--DOCUMENT REVISION NUMBER-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">REVISION
                                                            NUMBER</span>
                                                    </div>

                                                    <input type="text" class="form-control" id="add_doc_rev_no"
                                                        name="add_doc_rev_no" value="0" readonly>

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
                                        <h5 class="card-title"><i class="fa fa-info-circle"></i> <strong>Affected
                                                Documents</strong></h5>

                                        <br>

                                        <p>(Optional): To be filled up by Process Owner/Originator if there are other
                                            documents which needs to be updated to match with the revised documents
                                            mentioned above. Will be approved by Selected Head.</p>
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


                        <!--SECTION HEAD/APPROVER FOR SG/PPC/WHS-->
                        <div class="row sg-approvers d-none">
                            <div class="col">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h5 class="card-title"><i class="fa fa-user"></i> <strong>Application
                                                Approver</strong></h5>
                                    </div>

                                    <div class="card-body">


                                        <!--APPLICATION MAIN APPROVER-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">SECTION HEAD
                                                            / APPROVER</span>
                                                    </div>

                                                    <select class="form-control sel-rapidx-section-heads"
                                                        id="add_section_head_approver" name="add_section_head_approver">
                                                        <option value="0" selected disabled>-- Select One --</option>
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
                                        <h5 class="card-title"><i class="fa fa-users"></i> <strong>Application
                                                Approvers</strong></h5>
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
                                                        <span class="input-group-text w-100" id="basic-addon1">PRODUCTION
                                                            HEAD</span>
                                                    </div>

                                                    <select class="form-control sel-rapidx-prod-head"
                                                        id="add_production_head" name="add_production_head">
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
                                                        <span class="input-group-text w-100" id="basic-addon1">QC
                                                            HEAD</span>
                                                    </div>

                                                    <select class="form-control sel-rapidx-qc-head" id="add_qc_head"
                                                        name="add_qc_head">
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
                                                        <span class="input-group-text w-100" id="basic-addon1">ENGINEERING
                                                            HEAD</span>
                                                    </div>

                                                    <select class="form-control sel-rapidx-eng-head" id="add_eng_head"
                                                        name="add_eng_head">
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
                                                        <span class="input-group-text w-100" id="basic-addon1">QS
                                                            Staff</span>
                                                    </div>

                                                    <select class="form-control sel-rapidx-user-list" id="add_qs_inspector"
                                                        name="add_qs_inspector">
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

                </form>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-success" id="btnSubmitApplication"><i
                            class="fa fa-upload"></i> Submit Application</button>
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
                                            <h5 class="card-title"><i class="fa fa-file"></i> Document Details
                                            </h5>
                                        </div>

                                        <div class="card-body">

                                            <!--DOCUMENT NUMBER-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT
                                                                NUMBER</span>
                                                        </div>

                                                        <input type="text" class="form-control" id="qs_doc_no"
                                                            name="qs_doc_no" readonly>

                                                    </div>
                                                </div>
                                            </div>

                                            <!--DOCUMENT TITLE-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT
                                                                TITLE</span>
                                                        </div>

                                                        <input type="text" class="form-control" id="qs_doc_title"
                                                            name="qs_doc_title" readonly>

                                                    </div>
                                                </div>
                                            </div>

                                            <!--DOCUMENT REVISION NUMBER-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">REVISION
                                                                NUMBER</span>
                                                        </div>

                                                        <input type="text" class="form-control" id="qs_doc_rev_no"
                                                            name="qs_doc_rev_no" readonly>

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
                                            <h5 class="card-title"><i class="fa fa-info-circle"></i> Application
                                                Details</h5>


                                        </div>

                                        <div class="card-body">

                                            <!--CONTROL NUMBER-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">AIDRC
                                                                CONTROL NUMBER</span>
                                                        </div>

                                                        <input type="text" class="form-control"
                                                            id="qs_aidrc_control_number" name="qs_aidrc_control_number"
                                                            readonly>

                                                    </div>
                                                </div>
                                            </div>

                                            <!--DOCUMENT CATEGORY-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT
                                                                CATEGORY</span>
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
                                                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT
                                                                TYPE</span>
                                                        </div>

                                                        <select class="form-control" id="qs_doc_type" name="qs_doc_type"
                                                            disabled>
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
                                                            <span class="input-group-text w-100"
                                                                id="basic-addon1">GROUP</span>
                                                        </div>

                                                        <select class="form-control" id="qs_for_group"
                                                            name="qs_for_group" disabled>
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
                                                            <span class="input-group-text w-100"
                                                                id="basic-addon1">SECTION/DEPARTMENT</span>
                                                        </div>

                                                        <select class="form-control sel-rapidx-department-list"
                                                            id="qs_department" name="qs_department" disabled>
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
                                                            <span class="input-group-text w-100"
                                                                id="basic-addon1">ORIGINATOR</span>
                                                        </div>

                                                        <select class="form-control sel-rapidx-user-list"
                                                            id="qs_originator" name="qs_originator" disabled>
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
                                                            <span class="input-group-text w-100"
                                                                id="basic-addon1">APPLICATION CREATED AT</span>
                                                        </div>

                                                        <input type="text" class="form-control" id="qs_created_at"
                                                            name="qs_created_at" readonly>

                                                    </div>
                                                </div>
                                            </div>

                                            <!--DEPARTMENT-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100"
                                                                id="basic-addon1">ORIGINATOR REMARKS</span>
                                                        </div>

                                                        <textarea class="form-control" id="qs_remarks" name="qs_remarks"
                                                            rows="3" style="resize: none;" placeholder="(Optional)"
                                                            readonly></textarea>

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
                                    <p><i class="fa fa-info-circle"></i> <strong>VALIDATION CHECK</strong>: To be filled by
                                        QS Staff. Press the action button per approver to include Validation Checkpoints
                                        (Can add multiple FMEA, Control Plan, Pre-Production Checksheet). <strong><i>Note:
                                                Even if there are no affected documents assigned, Eng'g, QC head will still
                                                review the application.</i></strong></p>
                                </div>
                            </div>

                            <!--APPLICATION MAIN APPROVER-->
                            <div class="row">
                                <div class="col">
                                    <div class="float-sm-right">
                                        <button type="button" class="btn btn-sm btn-info btn-document-action"
                                            document-action="2" title="Add Document Details" data-toggle="modal"
                                            data-target="#modalSearchDocumentDetails"><i class="fa fa-plus-circle"></i>Add
                                            QS Checkpoints</button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="dt-responsive table-responsive">

                                        <table id="tbl_qs_validations"
                                            class="table table-sm table-bordered table-striped table-hover"
                                            style="width: 100%; font-size: 85%;">
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

                                <input type="hidden" class="form-control form-control-sm" id="qs_application_id"
                                    name="qs_application_id" readonly>

                                <input type="hidden" class="form-control form-control-sm" id="qs_validation_status"
                                    name="qs_validation_status">

                                <div class="row">
                                    <div class="col">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend w-50">
                                                <span class="input-group-text w-100" id="basic-addon1">QS VALIDATION
                                                    REMARKS</span>
                                            </div>

                                            <textarea class="form-control" id="qs_validation_remarks"
                                                name="qs_validation_remarks" rows="3" style="resize: none;"
                                                placeholder="(Required!)"></textarea>

                                        </div>
                                    </div>
                                </div>

                            </form>


                        </div>
                    </div>


                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-sm btn-danger mr-auto" id="btnQsDisapproveApplication"><i
                            class="fa fa-times-circle"></i> Disapprove Application</button>

                    <button type="button" class="btn btn-sm btn-success" id="btnQsApproveApplication"><i
                            class="fa fa-check-circle"></i> Submit QS Validation</button>
                </div>
            </div>

        </div>
    </div>


    <!--MODAL HEAD APPROVERS-->
    <div class="modal fade" id="modalHeadApprover">
        <div class="modal-dialog modal-xl-custom">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-check-circle"></i> Review Application</h4>
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
                                            <h5 class="card-title"><i class="fa fa-file"></i> Document Details
                                            </h5>
                                        </div>

                                        <div class="card-body">

                                            <!--DOCUMENT NUMBER-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT
                                                                NUMBER</span>
                                                        </div>

                                                        <input type="text" class="form-control" id="head_doc_no"
                                                            name="head_doc_no" readonly>

                                                    </div>
                                                </div>
                                            </div>

                                            <!--DOCUMENT TITLE-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT
                                                                TITLE</span>
                                                        </div>

                                                        <input type="text" class="form-control" id="head_doc_title"
                                                            name="head_doc_title" readonly>

                                                    </div>
                                                </div>
                                            </div>

                                            <!--DOCUMENT REVISION NUMBER-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">REVISION
                                                                NUMBER</span>
                                                        </div>

                                                        <input type="text" class="form-control" id="head_doc_rev_no"
                                                            name="head_doc_rev_no" readonly>

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
                                            <h5 class="card-title"><i class="fa fa-info-circle"></i> Application
                                                Details</h5>
                                        </div>

                                        <div class="card-body">

                                            <!--CONTROL NUMBER-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">AIDRC
                                                                CONTROL NUMBER</span>
                                                        </div>

                                                        <input type="text" class="form-control"
                                                            id="head_aidrc_control_number" name="head_aidrc_control_number"
                                                            readonly>

                                                    </div>
                                                </div>
                                            </div>

                                            <!--DOCUMENT CATEGORY-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT
                                                                CATEGORY</span>
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
                                                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT
                                                                TYPE</span>
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
                                                            <span class="input-group-text w-100"
                                                                id="basic-addon1">GROUP</span>
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
                                                            <span class="input-group-text w-100"
                                                                id="basic-addon1">SECTION/DEPARTMENT</span>
                                                        </div>

                                                        <select class="form-control sel-rapidx-department-list"
                                                            id="head_department" name="head_department" disabled>
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
                                                            <span class="input-group-text w-100"
                                                                id="basic-addon1">ORIGINATOR</span>
                                                        </div>

                                                        <select class="form-control sel-rapidx-user-list"
                                                            id="head_originator" name="head_originator" disabled>
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
                                                            <span class="input-group-text w-100"
                                                                id="basic-addon1">APPLICATION CREATED AT</span>
                                                        </div>

                                                        <input type="text" class="form-control" id="head_created_at"
                                                            name="head_created_at" readonly>

                                                    </div>
                                                </div>
                                            </div>

                                            <!--DEPARTMENT-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100"
                                                                id="basic-addon1">ORIGINATOR REMARKS</span>
                                                        </div>

                                                        <textarea class="form-control" id="head_remarks"
                                                            name="head_remarks" rows="3" style="resize: none;"
                                                            placeholder="(Optional)" readonly></textarea>

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
                                        <button type="button" class="btn btn-sm btn-info btn-document-action"
                                            document-action="3" title="Add Document Details" data-toggle="modal"
                                            data-target="#modalSearchDocumentDetails"><i class="fa fa-plus-circle"></i> Add
                                            Affected Document</button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="dt-responsive table-responsive">
                                        <table id="tbl_head_approval_documents"
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

                                <input type="hidden" class="form-control form-control-sm" id="head_application_id"
                                    name="head_application_id" readonly>

                                <input type="hidden" class="form-control form-control-sm" id="head_approving_as"
                                    name="head_approving_as" readonly>

                                <input type="hidden" class="form-control form-control-sm" id="head_approval_status"
                                    name="head_approval_status">

                                <div class="row">
                                    <div class="col">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend w-50">
                                                <span class="input-group-text w-100" id="basic-addon1">APPROVER
                                                    REMARKS</span>
                                            </div>

                                            <textarea class="form-control" id="head_approval_remarks"
                                                name="head_approval_remarks" rows="3" style="resize: none;"
                                                placeholder="(Required if Disapproved; Optional if Approved)"></textarea>

                                        </div>
                                    </div>
                                </div>

                            </form>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-sm btn-danger mr-auto" id="btnHeadDisapproveApplication"><i
                            class="fa fa-times-circle"></i> Disapprove Application</button>

                    <button type="button" class="btn btn-sm btn-success" id="btnHeadApproveApplication"><i
                            class="fa fa-check-circle"></i> Approve Application</button>
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
                                            <h5 class="card-title"><i class="fa fa-file"></i> Document Details
                                            </h5>
                                        </div>

                                        <div class="card-body">

                                            <!--DOCUMENT NUMBER-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT
                                                                NUMBER</span>
                                                        </div>

                                                        <input type="text" class="form-control" id="dcc_doc_no"
                                                            name="dcc_doc_no" readonly>

                                                    </div>
                                                </div>
                                            </div>

                                            <!--DOCUMENT TITLE-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT
                                                                TITLE</span>
                                                        </div>

                                                        <input type="text" class="form-control" id="dcc_doc_title"
                                                            name="dcc_doc_title" readonly>

                                                    </div>
                                                </div>
                                            </div>

                                            <!--DOCUMENT REVISION NUMBER-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">REVISION
                                                                NUMBER</span>
                                                        </div>

                                                        <input type="text" class="form-control" id="dcc_doc_rev_no"
                                                            name="dcc_doc_rev_no" readonly>

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
                                            <h5 class="card-title"><i class="fa fa-info-circle"></i> Application
                                                Details</h5>
                                        </div>

                                        <div class="card-body">

                                            <!--CONTROL NUMBER-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">AIDRC
                                                                CONTROL NUMBER</span>
                                                        </div>

                                                        <input type="text" class="form-control"
                                                            id="dcc_aidrc_control_number" name="dcc_aidrc_control_number"
                                                            readonly>

                                                    </div>
                                                </div>
                                            </div>

                                            <!--DOCUMENT CATEGORY-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT
                                                                CATEGORY</span>
                                                        </div>

                                                        <select class="form-control" id="dcc_doc_category"
                                                            name="dcc_doc_category" disabled>
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
                                                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT
                                                                TYPE</span>
                                                        </div>

                                                        <select class="form-control" id="dcc_doc_type"
                                                            name="dcc_doc_type" disabled>
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
                                                            <span class="input-group-text w-100"
                                                                id="basic-addon1">GROUP</span>
                                                        </div>

                                                        <select class="form-control" id="dcc_for_group"
                                                            name="dcc_for_group" disabled>
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
                                                            <span class="input-group-text w-100"
                                                                id="basic-addon1">SECTION/DEPARTMENT</span>
                                                        </div>

                                                        <select class="form-control sel-rapidx-department-list"
                                                            id="dcc_department" name="dcc_department" disabled>
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
                                                            <span class="input-group-text w-100"
                                                                id="basic-addon1">ORIGINATOR</span>
                                                        </div>

                                                        <select class="form-control sel-rapidx-user-list"
                                                            id="dcc_originator" name="dcc_originator" disabled>
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
                                                            <span class="input-group-text w-100"
                                                                id="basic-addon1">APPLICATION CREATED AT</span>
                                                        </div>

                                                        <input type="text" class="form-control" id="dcc_created_at"
                                                            name="dcc_created_at" readonly>

                                                    </div>
                                                </div>
                                            </div>

                                            <!--DEPARTMENT-->
                                            <div class="row">
                                                <div class="col">
                                                    <div class="input-group input-group-sm mb-3">
                                                        <div class="input-group-prepend w-50">
                                                            <span class="input-group-text w-100"
                                                                id="basic-addon1">ORIGINATOR REMARKS</span>
                                                        </div>

                                                        <textarea class="form-control" id="dcc_remarks"
                                                            name="dcc_remarks" rows="3" style="resize: none;"
                                                            placeholder="(Optional)" readonly></textarea>

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

                                <input type="hidden" class="form-control form-control-sm" id="dcc_application_id"
                                    name="dcc_application_id" readonly>


                                <div class="row">
                                    <div class="col">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend w-50">
                                                <span class="input-group-text w-100" id="basic-addon1">NO SIMILAR/COMMON
                                                    PROCEDURE FROM OTHER DOCUMENT</span>
                                            </div>

                                            <select class="form-control dcc-checkpoint" id="dcc_checkpoint_similar"
                                                name="dcc_checkpoint_similar">
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
                                                <span class="input-group-text w-100" id="basic-addon1">ALIGNMENT OF
                                                    PROCEDURE IN REFERENCE TO DESCRIPTION OF CHANGE</span>
                                            </div>

                                            <select class="form-control dcc-checkpoint" id="dcc_checkpoint_alignment"
                                                name="dcc_checkpoint_alignment">
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
                                                <span class="input-group-text w-100" id="basic-addon1">USE OF STANDARD
                                                    TEMPLATE/FORMAT</span>
                                            </div>

                                            <select class="form-control dcc-checkpoint" id="dcc_checkpoint_standard"
                                                name="dcc_checkpoint_standard">
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
                                                <span class="input-group-text w-100" id="basic-addon1">DCC VALIDATION
                                                    JUDGEMENT</span>
                                            </div>

                                            <select class="form-control" id="dcc_validation_judgement"
                                                name="dcc_validation_judgement">
                                                <option selected disabled>-- Select One --</option>
                                                <option disabled class="dcc-passed" value="1">PASSED</option>
                                                <option disabled class="dcc-revisions" value="2">FOR MINOR REVISIONS
                                                </option>
                                                <option disabled class="dcc-revisions" value="3">FOR MAJOR REVISIONS
                                                </option>
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

                                            <textarea class="form-control" id="dcc_validation_remarks"
                                                name="dcc_validation_remarks" rows="3" style="resize: none;"
                                                placeholder="(Optional)"></textarea>

                                        </div>
                                    </div>
                                </div>

                            </form>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <!--   <button type="button" class="btn btn-sm btn-danger mr-auto" id="btnHeadDisapproveApplication"><i class="fa fa-times-circle"></i> Disapprove Application</button> -->

                    <button type="button" class="btn btn-sm btn-success" id="btnSubmitDccValidation"><i
                            class="fa fa-check-circle"></i> Submit DCC Validation</button>
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
                            <p><i class="fa fa-info-circle"></i> Click the Action Button to add the Document Details in
                                your Application</p>
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

                                        <select class="form-control" id="document_search_type"
                                            name="document_search_type">
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
                                            <span class="input-group-text w-100" id="basic-addon1">SEARCH DOCUMENT
                                                WILDCARD</span>
                                        </div>

                                        <input type="text" class="form-control" id="document_wildcard"
                                            name="document_wildcard"
                                            placeholder='e.g. PGS-B17-004, Internal Audit Procedures'>

                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-sm btn-primary" id="btnSearchDocument"><i
                                                    class="fa fa-arrow-right"></i></button>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <input type="hidden" class="form-control" id="document_hidden_action"
                                        name="document_hidden_action" readonly>
                                </div>
                            </div>

                        </div>

                        <div class="col-sm-6">
                            <div class="float-sm-right">
                                <button class="btn btn-sm btn-primary btn-preprod" addition-type="3" data-toggle="modal"
                                    data-target="#modalAddaffectedDocumentDetails"
                                    title="Add Pre-Production Checksheet to the application"><i class="fa fa-file"></i>
                                    Add Pre-Production Checksheet</button>
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
        <div class="modal-dialog modal-xl-custom">
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

                        <div class="row">

                            <!--APPLICATION DETAILS-->
                            <div class="col-sm-4">

                                <div class="row">
                                    <div class="col">
                                        <div class="card card-primary">
                                            <div class="card-header">
                                                <h5 class="card-title"><i class="fa fa-file"></i> Document Details
                                                </h5>

                                                <div class="float-sm-right view-edit">
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        id="btnEditApplicationDetails"><i class="fa fa-edit"></i> Edit
                                                        Application</button>
                                                </div>

                                            </div>

                                            <div class="card-body">

                                                <input type="hidden" class="form-control" id="view_application_id"
                                                    name="view_application_id" readonly>

                                                <!--DOCUMENT NUMBER-->
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="input-group input-group-sm mb-3">
                                                            <div class="input-group-prepend w-50">
                                                                <span class="input-group-text w-100"
                                                                    id="basic-addon1">DOCUMENT NUMBER</span>
                                                            </div>

                                                            <input type="text" class="form-control" id="view_doc_no"
                                                                name="view_doc_no" readonly>

                                                        </div>
                                                    </div>
                                                </div>

                                                <!--DOCUMENT TITLE-->
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="input-group input-group-sm mb-3">
                                                            <div class="input-group-prepend w-50">
                                                                <span class="input-group-text w-100"
                                                                    id="basic-addon1">DOCUMENT TITLE</span>
                                                            </div>

                                                            <input type="text" class="form-control" id="view_doc_title"
                                                                name="view_doc_title" readonly>

                                                        </div>
                                                    </div>
                                                </div>

                                                <!--DOCUMENT REVISION NUMBER-->
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="input-group input-group-sm mb-3">
                                                            <div class="input-group-prepend w-50">
                                                                <span class="input-group-text w-100"
                                                                    id="basic-addon1">REVISION NUMBER</span>
                                                            </div>

                                                            <input type="text" class="form-control" id="view_doc_rev_no"
                                                                name="view_doc_rev_no" readonly>

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
                                                <h5 class="card-title"><i class="fa fa-info-circle"></i> Application
                                                    Details</h5>

                                                <div class="float-sm-right view-edit">
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        id="btnCancelApplication" disabled><i
                                                            class="fa fa-times-circle"></i> Cancel Application</button>
                                                </div>
                                            </div>

                                            <div class="card-body">

                                                <!--CONTROL NUMBER-->
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="input-group input-group-sm mb-3">
                                                            <div class="input-group-prepend w-50">
                                                                <span class="input-group-text w-100"
                                                                    id="basic-addon1">AIDRC CONTROL NUMBER</span>
                                                            </div>

                                                            <input type="text" class="form-control"
                                                                id="view_aidrc_control_number"
                                                                name="view_aidrc_control_number" readonly>

                                                        </div>
                                                    </div>
                                                </div>

                                                <!--DOCUMENT CATEGORY-->
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="input-group input-group-sm mb-3">
                                                            <div class="input-group-prepend w-50">
                                                                <span class="input-group-text w-100"
                                                                    id="basic-addon1">DOCUMENT CATEGORY</span>
                                                            </div>

                                                            <select class="form-control" id="view_doc_category"
                                                                name="view_doc_category" disabled>
                                                                <option value="0" selected disabled>-- Select One --
                                                                </option>
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
                                                                <span class="input-group-text w-100"
                                                                    id="basic-addon1">DOCUMENT TYPE</span>
                                                            </div>

                                                            <select class="form-control" id="view_doc_type"
                                                                name="view_doc_type" disabled>
                                                                <option value="0" selected disabled>-- Select One --
                                                                </option>
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
                                                                <span class="input-group-text w-100"
                                                                    id="basic-addon1">GROUP</span>
                                                            </div>

                                                            <select class="form-control" id="view_for_group"
                                                                name="view_for_group" disabled>
                                                                <option value="0" selected disabled>-- Select One --
                                                                </option>
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
                                                                <span class="input-group-text w-100"
                                                                    id="basic-addon1">SECTION/DEPARTMENT</span>
                                                            </div>

                                                            <select class="form-control sel-rapidx-department-list"
                                                                id="view_department" name="view_department" disabled>
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
                                                                <span class="input-group-text w-100"
                                                                    id="basic-addon1">ORIGINATOR</span>
                                                            </div>

                                                            <select class="form-control sel-rapidx-user-list"
                                                                id="view_originator" name="view_originator" disabled>
                                                                <option value="0" selected disabled>-- Select One --
                                                                </option>
                                                            </select>

                                                        </div>
                                                    </div>
                                                </div>

                                                <!--DATE/TIME-->
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="input-group input-group-sm mb-3">
                                                            <div class="input-group-prepend w-50">
                                                                <span class="input-group-text w-100"
                                                                    id="basic-addon1">APPLICATION CREATED AT</span>
                                                            </div>

                                                            <input type="text" class="form-control" id="view_created_at"
                                                                name="view_created_at" readonly>

                                                        </div>
                                                    </div>
                                                </div>

                                                <!--DATE/TIME-->
                                                <div class="row view-edit">
                                                    <div class="col">
                                                        <div class="input-group input-group-sm mb-3">
                                                            <div class="input-group-prepend w-50">
                                                                <span class="input-group-text w-100" id="basic-addon1">NEW
                                                                    ATTACHMENT</span>
                                                            </div>

                                                            <input type="file" class="form-control" id="edit_attachment"
                                                                name="edit_attachment" accept=".pdf" disabled>

                                                        </div>
                                                    </div>
                                                </div>

                                                <!--DEPARTMENT-->
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="input-group input-group-sm mb-3">
                                                            <div class="input-group-prepend w-50">
                                                                <span class="input-group-text w-100"
                                                                    id="basic-addon1">ORIGINATOR REMARKS</span>
                                                            </div>

                                                            <textarea class="form-control" id="view_remarks"
                                                                name="view_remarks" rows="3" style="resize: none;"
                                                                placeholder="(Optional)" readonly></textarea>

                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--SECTION HEAD/APPROVER FOR SG/PPC/WHS-->
                                <div class="row view-sg-approver d-none">
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

                                                            <select class="form-control sel-rapidx-section-heads"
                                                                id="view_section_head_approver"
                                                                name="view_section_head_approver" disabled>
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

                    </form>

                    <div class="row view-edit">
                        <div class="col">
                            <button type="button" class="btn btn-sm btn-secondary" id="btnCancelEditApplication"
                                disabled><i class="fa fa-times-circle"></i> Cancel Edit</button>

                            <div class="float-sm-right">
                                <button type="button" class="btn btn-sm btn-success" id="btnSubmitEditApplication"
                                    disabled><i class="fa fa-check-circle"></i> Submit Changes</button>
                            </div>
                        </div>
                    </div>



                </div>

                <div class="col-sm-8">

                    <div class="row">
                        <div class="col">
                            <h5><i class="fa fa-info-circle"></i> <strong>APPLICATION TRACEABILITY:</strong> See all
                                activity of the application</h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="dt-responsive table-responsive">
                                <table id="tbl_view_affected_documents"
                                    class="table table-sm table-bordered table-striped table-hover"
                                    style="width: 100%; font-size: 85%;">
                                    <p><i class="fas fa-chevron-circle-down"></i> <strong>Affected Documents / Checkpoints
                                            for Approval</strong></p>
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

                    <br>

                    <div class="row">
                        <div class="col">
                            <div class="dt-responsive table-responsive">
                                <table id="tbl_view_qs_validations"
                                    class="table table-sm table-bordered table-striped table-hover"
                                    style="width: 100%; font-size: 85%;">
                                    <p><i class="fas fa-chevron-circle-down"></i> <strong>QS Validations (If
                                            Operations)</strong></p>
                                    <thead>
                                        <tr>
                                            <th>Validation Date/Time</th>
                                            <th>QS Staff</th>
                                            <th>Validation Status</th>
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
                                <table id="tbl_view_approvals"
                                    class="table table-sm table-bordered table-striped table-hover"
                                    style="width: 100%; font-size: 85%;">
                                    <p><i class="fas fa-chevron-circle-down"></i> <strong>Approvals</strong></p>
                                    <thead>
                                        <tr>
                                            <th>Approval Date/Time</th>
                                            <th>Approver</th>
                                            <th>Approving As</th>
                                            <th>Approval Status</th>
                                            <th>Approval Remarks</th>
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
                                <table id="tbl_view_dcc_validations"
                                    class="table table-sm table-bordered table-striped table-hover"
                                    style="width: 100%; font-size: 85%;">
                                    <p><i class="fas fa-chevron-circle-down"></i> <strong>DCC Validation</strong></p>
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

                    <!--  <div class="row">
                                                                                <div class="col">
                                                                                   <div class="dt-responsive table-responsive">
                                                                                    <table id="tbl_view_revisions" class="table table-sm table-bordered table-striped table-hover" style="width: 100%; font-size: 85%;">
                                                                                      <caption><i class="fas fa-chevron-circle-up"></i> Document Submissions</caption>
                                                                                      <thead>
                                                                                        <tr>
                                                                                          <th>Revision Date/Time</th>
                                                                                          <th>Status when Revised</th>
                                                                                          <th>Attachment</th>
                                                                                          <th>Originator Remarks</th>
                                                                                          <th>Document Number</th>
                                                                                          <th>Document Title</th>
                                                                                          <th>Revision No.</th>
                                                                                        </tr>
                                                                                      </thead>
                                                                                    </table>
                                                                                  </div>
                                                                                </div>
                                                                              </div> -->


                </div>

            </div>

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
                                                        <span class="input-group-text w-100" id="basic-addon1">DOCUMENT
                                                            NUMBER</span>
                                                    </div>

                                                    <input type="text" class="form-control" id="affected_doc_no"
                                                        name="affected_doc_no" readonly>

                                                </div>
                                            </div>
                                        </div>

                                        <!--DOCUMENT TITLE-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">DOCUMENT
                                                            TITLE</span>
                                                    </div>

                                                    <input type="text" class="form-control" id="affected_doc_title"
                                                        name="affected_doc_title" readonly>

                                                </div>
                                            </div>
                                        </div>

                                        <!--DOCUMENT REVISION NUMBER-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">REVISION
                                                            NUMBER</span>
                                                    </div>

                                                    <input type="text" class="form-control" id="affected_doc_rev_no"
                                                        name="affected_doc_rev_no" readonly>

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
                                                        <span class="input-group-text w-100" id="basic-addon1">CHECKPOINT
                                                            TYPE</span>
                                                    </div>

                                                    <select class="form-control" id="affected_checkpoint_type"
                                                        name="affected_checkpoint_type">
                                                        <option selected disabled>-- Select One --</option>
                                                        <option class="class-affected-doc" value="1">Affected Document
                                                        </option>
                                                        <option class="class-checkpoint" value="2">FMEA</option>
                                                        <option class="class-checkpoint" value="3">Control Plan</option>
                                                        <option class="class-checkpoint-preprod" value="4">Pre-Production
                                                            Checksheet</option>
                                                    </select>


                                                </div>
                                            </div>
                                        </div>

                                        <!--PIC-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100"
                                                            id="basic-addon1">PERSON-IN-CHARGE</span>
                                                    </div>

                                                    <select class="form-control sel-rapidx-user-list"
                                                        id="affected_person_in_charge" name="affected_person_in_charge">
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
                                                        <span class="input-group-text w-100" id="basic-addon1">REVISION DUE
                                                            DATE</span>
                                                    </div>

                                                    <input type="date" class="form-control" id="affected_rev_due_date"
                                                        name="affected_rev_due_date">

                                                </div>
                                            </div>
                                        </div>

                                        <!--ADDITION REMARKS-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">ADDITION
                                                            REMARKS</span>
                                                    </div>

                                                    <textarea class="form-control" id="affected_doc_remarks"
                                                        name="affected_doc_remarks" rows="3"
                                                        style="resize: none;"></textarea>

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

                    <button type="button" class="btn btn-sm btn-danger mr-auto" class="close"
                        data-dismiss="modal"><i class="fa fa-times-circle"></i>Cancel</button>

                    <button type="button" class="btn btn-sm btn-success" id="btnSubmitAffectedDocument"><i
                            class="fa fa-check-circle"></i> Add Document</button>

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

                        <input type="hidden" class="form-control" id="edit_affected_doc_id" name="edit_affected_doc_id"
                            readonly>

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
                                                        <span class="input-group-text w-100" id="basic-addon1">DOCUMENT
                                                            NUMBER</span>
                                                    </div>

                                                    <input type="text" class="form-control" id="edit_affected_doc_no"
                                                        name="edit_affected_doc_no" readonly>

                                                </div>
                                            </div>
                                        </div>

                                        <!--DOCUMENT TITLE-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">DOCUMENT
                                                            TITLE</span>
                                                    </div>

                                                    <input type="text" class="form-control" id="edit_affected_doc_title"
                                                        name="edit_affected_doc_title" readonly>

                                                </div>
                                            </div>
                                        </div>

                                        <!--DOCUMENT REVISION NUMBER-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">REVISION
                                                            NUMBER</span>
                                                    </div>

                                                    <input type="text" class="form-control" id="edit_affected_doc_rev_no"
                                                        name="edit_affected_doc_rev_no" readonly>

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
                                                        <span class="input-group-text w-100" id="basic-addon1">CHECKPOINT
                                                            TYPE</span>
                                                    </div>

                                                    <select class="form-control" id="edit_affected_checkpoint_type"
                                                        name="edit_affected_checkpoint_type">
                                                        <option selected disabled>-- Select One --</option>
                                                        <option class="class-affected-doc" value="1">Affected Document
                                                        </option>
                                                        <option class="class-checkpoint" value="2">FMEA</option>
                                                        <option class="class-checkpoint" value="3">Control Plan</option>
                                                        <option class="class-checkpoint" value="4">Pre-Production
                                                            Checksheet</option>
                                                    </select>


                                                </div>
                                            </div>
                                        </div>

                                        <!--PIC-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100"
                                                            id="basic-addon1">PERSON-IN-CHARGE</span>
                                                    </div>

                                                    <select class="form-control sel-rapidx-user-list"
                                                        id="edit_affected_person_in_charge"
                                                        name="edit_affected_person_in_charge">
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
                                                        <span class="input-group-text w-100" id="basic-addon1">REVISION DUE
                                                            DATE</span>
                                                    </div>

                                                    <input type="date" class="form-control"
                                                        id="edit_affected_rev_due_date" name="edit_affected_rev_due_date">

                                                </div>
                                            </div>
                                        </div>

                                        <!--ADDITION REMARKS-->
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group input-group-sm mb-3">
                                                    <div class="input-group-prepend w-50">
                                                        <span class="input-group-text w-100" id="basic-addon1">ADDITION
                                                            REMARKS</span>
                                                    </div>

                                                    <textarea class="form-control" id="edit_affected_doc_remarks"
                                                        name="edit_affected_doc_remarks" rows="3"
                                                        style="resize: none;"></textarea>

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
                                                        <span class="input-group-text w-100" id="basic-addon1">APPROVER
                                                            REMARKS</span>
                                                    </div>

                                                    <textarea class="form-control" id="edit_affected_approver_remarks"
                                                        name="edit_affected_approver_remarks"
                                                        placeholder="(Optional if Approved, Required if Disapproved)"
                                                        rows="3" style="resize: none;"></textarea>

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

                    <button type="button" class="btn btn-sm btn-danger mr-auto" id="btnDisapproveDocument"><i
                            class="fa fa-times-circle"></i> Disapprove Document</button>

                    <button type="button" class="btn btn-sm btn-secondary" class="close" data-dismiss="modal"><i
                            class="fa fa-times-circle"></i>Cancel</button>


                    <button type="button" class="btn btn-sm btn-success" id="btnSubmitEditAffectedDocument"><i
                            class="fa fa-check-circle"></i> Edit Document</button>

                </div>

            </div>
        </div>
    </div>
@endsection

@section('js_content')
    <script type="text/javascript">
        let arrayAffectedDocuments = [];
        $(document).ready(function() {
            bsCustomFileInput.init();

            LoadAcdcsLayout();
            LoadRapidXDepartmentList($('.sel-rapidx-department-list'));
            LoadRapidXDepartmentList($('.sel-rapidx-department-list-2'));
            LoadRapidXUserList($('.sel-rapidx-user-list'));
            LoadOriginatorList($('.sel-originator-list'));
            LoadSectionHeadList($('.sel-rapidx-section-heads'));
            LoadProdHeadList($('.sel-rapidx-prod-head'));
            LoadQcHeadList($('.sel-rapidx-qc-head'));
            LoadEngHeadList($('.sel-rapidx-eng-head'));

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
                    { "data": "control_number" },
                    { "data": "status" },
                    { "data": "application_datetime" },
                    { "data": "originator" },
                    { "data": "section_dept" },
                    { "data": "doc_no" },
                    { "data": "doc_title" },
                    { "data": "rev_no" },
                    { "data": "uploaded_file" },
                    { "data": "application_approvers" },
                ],
                "order": [2, 'desc']
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

            dt_view_qs_validations = $('#tbl_view_qs_validations').DataTable({

                "paging": false,
                "info": false,
                "searching": false,
                "ordering": false,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "load_new_qs_validations_table",
                    data: function(param) {
                        param.application_id = $("#view_application_id").val();

                    }
                },

                "columns": [{
                        "data": "validation_datetime"
                    },
                    {
                        "data": "inspector"
                    },
                    {
                        "data": "validation_status"
                    },
                    {
                        "data": "validation_remarks"
                    },
                ],
            });
        });

        $('#add_for_group').change(function() {
            if ($('#add_for_group').val() == 1) {
                $('.operation-approvers').removeClass('d-none');
                $('.sg-approvers').addClass('d-none');
            }else{
                $('.sg-approvers').removeClass('d-none');
                $('.operation-approvers').addClass('d-none');
            }
        });

        $('#modalAddApplication').on('hidden.bs.modal', function() {

            $('.sg-approvers').addClass('d-none');
            $('.operation-approvers').addClass('d-none');
            $('#formAddApplication')[0].reset();

            arrayAffectedDocuments = [];
            dt_affected_documents.draw();
        });

        //this changes for the datatables
        $(document).on('click', '.btn-document-action', function() {
            let document_action = $(this).attr('document-action');
            $('#document_hidden_action').val(document_action);

            if (document_action == 1 || document_action == 4) {
                $('.btn-preprod').addClass('d-none');
            } else {
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

        $(document).on('click', '.btn-add-document-details', function() {
            let active_doc_id = $(this).attr('active-doc-id');
            let addition_type = $(this).attr('addition-type');

            LoadAddDocumentDetails(active_doc_id, addition_type);
        });

        // $('#btnSubmitApplication').click(function() {
        //     $('#formAddApplication').submit();
        // });

        // $('#formAddApplication').submit(function(e) {
        //     e.preventDefault();
        //     SubmitNewApplication(arrayAffectedDocuments);
        // });

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
            LoadHeadApplicationDetails(application_id, approving_as);
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

        $('#formHeadApproval').submit(function(e) {

            e.preventDefault();
            SubmitHeadApproval(arrayAffectedDocuments);

        });

        //HEAD APPROVAL
        $(document).on('click', '.btn-dcc-validation', function() {
            let application_id = $(this).attr('application-id');
            LoadDccApplicationDetails(application_id);
        });

        $('#btnSubmitDccValidation').click(function() {

            $('#formDccValidation').submit();

        });

        $('#formDccValidation').submit(function(e) {

            e.preventDefault();
            SubmitDccValidation();

        });


        $(document).on('click', '.btn-view-application', function() {
            let application_id = $(this).attr('application-id');
            let view_edit = $(this).attr('view-edit');

            LoadViewApplicationDetails(application_id, view_edit);
        });

        $('#btnEditApplicationDetails').click(function() {

            $('#edit_attachment').removeAttr('disabled');
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

            $('#edit_attachment').prop('disabled', 'disabled');
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


        /*$('#modalSearchDocumentDetails').on('open.bs.modal', function(){

          alert($('#document_hidden_action').val());

        });*/

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

            let application_id = $('#view_application_id').val();

            NewCancelApplication(application_id);
        });

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

        $(document).on('click', '.btn-remove-document', function() {

            let document_id = $(this).attr('document-id');
            RemoveDocument(document_id);

        });

        $('#check_filter_section_dept').change(function(e) {
            if ($('#check_filter_section_dept').is(':checked')) {
                $('#hidden_check_section_dept').val(1);
                $('#filter_department').removeAttr('disabled');
            } else {
                $('#hidden_check_section_dept').val('');
                $('#filter_department').prop('disabled', 'disabled');
            }
        });

        $('#check_filter_originator').change(function(e) {
            if ($('#check_filter_originator').is(':checked')) {
                $('#hidden_check_originator').val(1);
                $('#filter_originator').removeAttr('disabled');
            } else {
                $('#hidden_check_originator').val('');
                $('#filter_originator').prop('disabled', 'disabled');
            }
        });

        $('#check_filter_status').change(function(e) {
            if ($('#check_filter_status').is(':checked')) {
                $('#hidden_check_status').val(1);
                $('#filter_status').removeAttr('disabled');
            } else {
                $('#hidden_check_status').val('');
                $('#filter_status').prop('disabled', 'disabled');
            }

            dt_applications.draw();
        });

        $('#btnFilterTable').click(function() {

            dt_applications.draw();

        });




        //for auto
        $('.dcc-checkpoint').on('change', function() {

            let dcc_checkpoint_similar = $('#dcc_checkpoint_similar').val();
            let dcc_checkpoint_alignment = $('#dcc_checkpoint_alignment').val();
            let dcc_checkpoint_standard = $('#dcc_checkpoint_standard').val();

            let dcc_total = parseInt(dcc_checkpoint_similar) + parseInt(dcc_checkpoint_alignment) + parseInt(
                dcc_checkpoint_standard);

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
    </script>
@endsection
