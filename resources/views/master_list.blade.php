@extends('layouts.admin_layout')

@section('title', 'Master List')

@section('content_page')
 <style type="text/css">
    .modal-xl-custom{
      width: 95%!important;
      min-width: 90%!important;
    }
  </style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Master List</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Master List</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">

    <!-- <form id="formForDocumentExport" method="get"> -->

      <div class="row">
        <div class="col-md-12">
          <div class="card card-primary">

            <div class="card-header">
              <h5 class="card-title">Filters:</h5>
            </div>

            <div class="card-body">

              <!--DATE/RANGE-->
              <div class="row">
                <div class="col-sm-4">


                  <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend w-50">
                      <div class="input-group-text">
                        <input type="checkbox" id="check_filter_date_range">
                      </div>
                      <span class="input-group-text w-100" id="basic-addon1">APPLICATION CREATED AT</span>
                    </div>

                    <input type="hidden" id="hidden_check_date_range" name="hidden_check_date_range">

                    <input type="text" class="form-control" id="filter_date_range" name="filter_date_range" disabled>

                  </div>
                </div>
              </div>

              <div class="row">       
                <div class="col-sm-4">
                  <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend w-50">
                       <div class="input-group-text">
                        <input type="checkbox" id="check_filter_section_dept" >
                      </div>
                      <span class="input-group-text w-100" id="basic-addon1">SECTION/DEPARTMENT</span>
                    </div>

                    <input type="hidden" id="hidden_check_section_dept" name="hidden_check_section_dept">


                    <select class="form-control sel-rapidx-department-list" id="filter_department" name="filter_department[]" multiple disabled>
                      <option value="0" selected disabled>-- Select One --</option>
                    </select>

                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-sm-4">
                  <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend w-50">
                      <div class="input-group-text">
                        <input type="checkbox" id="check_filter_originator">
                      </div>
                      <span class="input-group-text w-100" id="basic-addon1">ORIGINATOR</span>
                    </div>

                    <input type="hidden" id="hidden_check_originator" name="hidden_check_originator">

                    <select class="form-control sel-originator-list" id="filter_originator" multiple name="filter_originator[]" disabled>
                    </select>

                  </div>
                </div>
              </div>

               <div class="row">
                <div class="col-sm-4">
                  <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend w-50">
                      <div class="input-group-text">
                        <input type="checkbox" id="check_filter_approver">
                      </div>
                      <span class="input-group-text w-100" id="basic-addon1">APPLICATION APPROVER</span>
                    </div>

                    <input type="hidden" id="hidden_check_approver" name="hidden_check_approver">

                    <select class="form-control sel-approver-list" id="filter_application_approver" name="filter_application_approver[]" multiple disabled>
                    </select>

                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-sm-4">
                  <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend w-50">
                      <div class="input-group-text">
                        <input type="checkbox" id="check_filter_qs_inspector">
                      </div>
                      <span class="input-group-text w-100" id="basic-addon1">QS INSPECTOR</span>
                    </div>

                    <input type="hidden" id="hidden_check_qs_inspector" name="hidden_check_qs_inspector">

                    <select class="form-control sel-qs-inspector-list" id="filter_qs_inspector" multiple name="filter_qs_inspector[]" disabled>
                    </select>

                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-sm-4">
                  <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend w-50">
                      <div class="input-group-text">
                        <input type="checkbox" id="check_filter_for_group">
                      </div>
                      <span class="input-group-text w-100" id="basic-addon1">FOR GROUP</span>
                    </div>

                    <input type="hidden" id="hidden_check_for_group" name="hidden_check_for_group">

                    <select class="form-control" id="filter_for_group" name="filter_for_group" disabled>
                      <option value="0" selected disabled>-- Select One --</option>
                      <option value="1">OPERATIONS</option>
                      <option value="2">SUPPORT GROUP</option>
                      <option value="3">OPERATIONS - PPC/WAREHOUSE</option>
                    </select>

                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-sm-4">
                  <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend w-50">
                      <div class="input-group-text">
                        <input type="checkbox" id="check_filter_document_status">
                      </div>
                      <span class="input-group-text w-100" id="basic-addon1">DOCUMENT STATUS</span>
                    </div>

                    <input type="hidden" id="hidden_check_document_status" name="hidden_check_document_status">

                    <select class="form-control" id="filter_document_status" name="filter_document_status" disabled>
                      <option value="0" selected disabled>-- Select One --</option>
                      <option value="1">NOT CONTROLLED</option>
                      <option value="2">CONTROLLED</option>
                    </select>

                  </div>
                </div>
              </div>

               <div class="row">
                <div class="col-sm-4">
                  <button type="button" class="btn btn-info btn-sm" id="btnFilterTable"><i class="fa fa-retweet"></i> Filter Table</button>

                  <div class="float-sm-right">
                    <button type="button" class="btn btn-success btn-sm" id="btnExportMasterList"><i class="fa fa-file-excel"></i> Export Master List</button>
                  </div>

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

            <!-- Start Page Content -->
            <div class="card-body">
               
               <div class="dt-responsive table-responsive">
                    <table id="tbl_applications_master_list" class="table table-sm table-bordered table-striped table-hover" style="width: 100%; font-size: 85%;">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>AIDRC Control Number</th>
                          <th>Section / Department</th>
                          <th>Originator</th>
                          <th>Application Date/Time</th>
                          <th>Document Number</th>
                          <th>Document Title</th>
                          <th>Revision Number</th>
                          <th>DCC Validation Date</th>
                          <th>DCC Validator</th>
                          <th>Turnaround Time</th>
                          <th>Document Status</th>
                          <th>Document Control Date</th>
                        </tr>
                      </thead>
                    </table>
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


          <div class="row">

          <!--APPLICATION DETAILS-->
          <div class="col-sm-4">

            <div class="row">
              <div class="col">
                <div class="card card-primary">
                  <div class="card-header">
                    <h5 class="card-title"><i class="fa fa-file"></i> Document Details</h5> 

                    <div class="float-sm-right view-edit">
                      <button type="button" class="btn btn-sm btn-primary" id="btnEditApplicationDetails"><i class="fa fa-edit"></i> Edit Application</button>
                    </div>

                  </div>

                  <div class="card-body">

                  <form id="formDccEditDocument" method="post">
                  @csrf

                    <input type="hidden" class="form-control" id="view_application_id" name="view_application_id" readonly>

                     <!--DOCUMENT NUMBER-->
                    <div class="row">
                      <div class="col">
                        <div class="input-group input-group-sm mb-3">
                          <div class="input-group-prepend w-50">
                            <span class="input-group-text w-100" id="basic-addon1">DOCUMENT NUMBER</span>
                          </div>
                          
                          <input type="text" class="form-control" id="view_doc_no" name="view_doc_no">

                          <div class="input-group-append">
                            <button type="button" class="btn btn-sm btn-primary btn-document-action" document-action="5" data-toggle="modal" data-target="#modalSearchDocumentDetails"><i class="fa fa-file"></i></button>
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
                          
                          <input type="text" class="form-control" id="view_doc_title" name="view_doc_title">

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

                  </form>

                  <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-success" id="btnDccSubmitChanges">Submit Document Changes</button>
                  </div>

                </div>
              </div>
            </div>

             <div class="row">
              <div class="col">
                <div class="card card-primary">
                  <div class="card-header">
                    <h5 class="card-title"><i class="fa fa-info-circle"></i> Application Details</h5> 

                   <div class="float-sm-right view-edit">
                        <button type="button" class="btn btn-sm btn-danger" id="btnCancelApplication" disabled><i class="fa fa-times-circle"></i> Cancel Application</button>
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
                            
                            <select class="form-control sel-rapidx-department-list-2" id="view_department" name="view_department" disabled>
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

                       <!--DATE/TIME-->
                    <div class="row view-edit">
                      <div class="col">
                        <div class="input-group input-group-sm mb-3">
                          <div class="input-group-prepend w-50">
                            <span class="input-group-text w-100" id="basic-addon1">NEW ATTACHMENT</span>
                          </div>
                          
                          <input type="file" class="form-control" id="edit_attachment" name="edit_attachment" accept=".pdf" disabled>

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

                      <div class="row view-edit">
                        <div class="col">
                          <button type="button" class="btn btn-sm btn-secondary" id="btnCancelEditApplication" disabled><i class="fa fa-times-circle"></i> Cancel Edit</button>

                          <div class="float-sm-right">
                            <button type="button" class="btn btn-sm btn-success" id="btnSubmitEditApplication" disabled><i class="fa fa-check-circle"></i> Submit Changes</button>
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
                <h5><i class="fa fa-info-circle"></i> <strong>APPLICATION TRACEABILITY:</strong> See all activity of the application</h5>
              </div>
            </div>

              <div class="row">
                <div class="col">
                   <div class="dt-responsive table-responsive">
                    <table id="tbl_view_affected_documents" class="table table-sm table-bordered table-striped table-hover" style="width: 100%; font-size: 85%;">
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

              <br>

              <div class="row">
                <div class="col">
                   <div class="dt-responsive table-responsive">
                    <table id="tbl_view_qs_validations" class="table table-sm table-bordered table-striped table-hover" style="width: 100%; font-size: 85%;">
                      <p><i class="fas fa-chevron-circle-down"></i> <strong>QS Validations (If Operations)</strong></p>
                      <thead>
                        <tr>
                          <th>Validation Date/Time</th>
                          <th>QS Inspector</th>
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
                    <table id="tbl_view_approvals" class="table table-sm table-bordered table-striped table-hover" style="width: 100%; font-size: 85%;">
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
                    <table id="tbl_view_dcc_validations" class="table table-sm table-bordered table-striped table-hover" style="width: 100%; font-size: 85%;">
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
                      <button type="button" class="btn btn-sm btn-primary" id="btnSearchDocument"><i class="fa fa-arrow-right"></i></button>
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
                <button class="btn btn-sm btn-primary btn-preprod" addition-type="3" data-toggle="modal" data-target="#modalAddaffectedDocumentDetails" title="Add Pre-Production Checksheet to the application"><i class="fa fa-file"></i> Add Pre-Production Checksheet</button>
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
                      <table id="tbl_add_document_details" class="table table-sm table-bordered table-striped table-hover" style="width: 100%; font-size: 85%;">
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

@endsection

@section('js_content')
<script type="text/javascript">

  let dt_applications_master_list;
  let dt_affected_documents;
  let dt_search_document_details;

  $(document).ready(function () {
    bsCustomFileInput.init();

    LoadAcdcsLayout();

     LoadRapidXDepartmentList($('.sel-rapidx-department-list'));
      LoadRapidXDepartmentList($('.sel-rapidx-department-list-2'));
     LoadRapidXUserList($('.sel-rapidx-user-list'));
     LoadOriginatorList($('.sel-originator-list'));
     LoadApproverList($('.sel-approver-list'));
     LoadQsInspectorList($('.sel-qs-inspector-list'));

    $('.sel-rapidx-department-list').select2({
        theme: "bootstrap4",
        
    });

    $('.sel-originator-list').select2({
        theme: "bootstrap4",
        
    });

    $('.sel-approver-list').select2({
        theme: "bootstrap4",
        
    });

    $('.sel-qs-inspector-list').select2({
        theme: "bootstrap4",
        
    });

    $('#btnExpandHeader').trigger('click');

    dt_applications_master_list = $('#tbl_applications_master_list').DataTable({

        "processing" : true,
        "serverSide" : true,

        "ajax" : {
          url: "load_acdcs_master_list",
          data: function (param){

            param.check_created_date = $('#hidden_check_date_range').val();
            param.check_section_department = $('#hidden_check_section_dept').val();
            param.check_for_group = $('#hidden_check_for_group').val();
            param.check_document_status = $('#hidden_check_document_status').val();
            param.check_originator = $('#hidden_check_originator').val();
            param.check_approver = $('#hidden_check_approver').val();
            param.check_qs_inspector = $('#hidden_check_qs_inspector').val();

            param.originator = $('#filter_originator').val();
            param.approver = $('#filter_application_approver').val();
            param.qs_inspector = $('#filter_qs_inspector').val();
            param.created_date = $('#filter_date_range').val();
            param.section_department = $('#filter_department').val();
            param.for_group = $('#filter_for_group').val();
            param.document_status = $('#filter_document_status').val();

          }
        },

        "columns":[
          { "data" : "hidden_id" },
          { "data" : "aidrc_control_number" },
          { "data" : "section_department" },
          { "data" : "originator" }, 
          { "data" : "application_datetime" }, 
          { "data" : "document_number" }, 
          { "data" : "document_name" }, 
          { "data" : "document_revision_number" }, 
          { "data" : "dcc_validation_date" }, 
          { "data" : "dcc_validator" },
          { "data" : "turnaround_time" }, 
          { "data" : "status" }, 
          { "data" : "document_control_date" }, 
          //{ "data" : "action", orderable:false, searchable:false }, 
        ],

        "columnDefs": [
        {
            "targets": [ 0 ],
            "visible": false,
            "searchable": false
        }],

        "order":[4,'desc']

    });

      //VIEW TABLES
     dt_view_affected_documents = $('#tbl_view_affected_documents').DataTable({

        "paging":   false,
        "info":     false,
        "searching": false,
        "ordering": false,
        "processing" : true,
        "serverSide" : true,
        "ajax" : {
          url: "load_new_affected_documents_table",
          data: function (param){
              param.application_id = $('#view_application_id').val();
          }
        },
        
        "columns":[
          { "data" : "checkpoint_type"},
          { "data" : "doc_no" },
          { "data" : "doc_title" },
          { "data" : "rev_no" },
          { "data" : "person_in_charge" },
          { "data" : "revision_due_date" },
          { "data" : "originator_remarks" },
         /* { "data" : "action" },*/
          
        ],


     });

     dt_view_dcc_validations = $('#tbl_view_dcc_validations').DataTable({

        "paging":   false,
        "info":     false,
        "searching": false,
        "ordering": false,
        "processing" : true,
        "serverSide" : true,
        "ajax" : {
          url: "load_new_dcc_validations_table",
          data: function (param){
              param.application_id = $("#view_application_id").val();
             
          }
        },
        
        "columns":[
          { "data" : "validation_datetime" },
          { "data" : "dcc_in_charge" },
          { "data" : "judgement" },
          { "data" : "checkpoint_similar" },
          { "data" : "checkpoint_alignment" },
          { "data" : "checkpoint_standard" },
          { "data" : "dcc_remarks" },
          
        ],

     });

    dt_view_approvals = $('#tbl_view_approvals').DataTable({

        "paging":   false,
        "info":     false,
        "searching": false,
        "ordering": false,
        "processing" : true,
        "serverSide" : true,
        "ajax" : {
          url: "load_new_head_approvals_table",
          data: function (param){
              param.application_id = $("#view_application_id").val();
             
          }
        },
        
        "columns":[
          { "data" : "approval_datetime" },
          { "data" : "approver" },
          { "data" : "approving_as" },
          { "data" : "judgement" },
          { "data" : "approval_remarks" },
  
          
        ],

    });

    dt_view_qs_validations = $('#tbl_view_qs_validations').DataTable({

        "paging":   false,
        "info":     false,
        "searching": false,
        "ordering": false,
        "processing" : true,
        "serverSide" : true,
        "ajax" : {
          url: "load_new_qs_validations_table",
          data: function (param){
              param.application_id = $("#view_application_id").val();
             
          }
        },
        
        "columns":[
          { "data" : "validation_datetime" },
          { "data" : "inspector" },
          { "data" : "validation_status" },
          { "data" : "validation_remarks" },
  
          
        ],

    });

        dt_add_document_details = $('#tbl_add_document_details').DataTable({
        "processing" : true,
        "serverSide" : true,
        "ajax" : {
          url: "load_acdcs_documents_table",
          data: function (param){
              param.document_hidden_action = $("#document_hidden_action").val();
              param.document_search_type = $('#document_search_type').val();
              param.document_wildcard = $('#document_wildcard').val();
          }
        },
        
        "columns":[
          { "data" : "doc_no" },
          { "data" : "doc_title" },
          { "data" : "rev_no" },
          { "data" : "action" },
          
        ],
    });

   
  });

   $('#filter_date_range').daterangepicker({
    locale: {
            format: 'YYYY-MM-DD'
        }
  });

  $('#tbl_applications_master_list').on('click', 'tr', function () {
        var row = $("table#tbl_applications_master_list tr").index($(this).closest("tr"));
        var id = $('#tbl_applications_master_list').DataTable().row(row-1).data().id;

        //LoadMasterListApplicationDetails(id);
        let view_edit = 2;

        LoadViewApplicationDetails(id, view_edit);

        $('#modalViewApplication').modal("show");
    });

  $('#btnExportMasterList').click(function(){

      let check_created_date = $('#hidden_check_date_range').val();
      let check_section_department = $('#hidden_check_section_dept').val();
      let check_for_group = $('#hidden_check_for_group').val();
      let check_document_status = $('#hidden_check_document_status').val();
      let check_originator = $('#hidden_check_originator').val();
      let check_approver = $('#hidden_check_approver').val();
      let check_qs_inspector = $('#hidden_check_qs_inspector').val();

      let originator = $('#filter_originator').val();
      let approver = $('#filter_application_approver').val();
      let qs_inspector = $('#filter_qs_inspector').val();
      let created_date = $('#filter_date_range').val();
      let section_department = $('#filter_department').val();
      let for_group = $('#filter_for_group').val();
      let document_status = $('#filter_document_status').val();

     window.open('export_applications_report?check_created_date=' + check_created_date + '&check_section_department=' + check_section_department + '&check_for_group=' + check_for_group + '&check_document_status=' + check_document_status + '&check_originator=' + check_originator + '&check_approver=' + check_approver + '&check_qs_inspector=' + check_qs_inspector + '&originator=' + originator +'&approver=' + approver + '&qs_inspector=' + qs_inspector + '&created_date=' + created_date + '&section_department=' + section_department + '&for_group=' + for_group + '&document_status=' + document_status, '_blank');

  })

   //filters

   $('#check_filter_date_range').change(function(e){
    if($('#check_filter_date_range').is(':checked'))
    {
      $('#hidden_check_date_range').val(1);
      $('#filter_date_range').removeAttr('disabled');
    }
    else
    {
      $('#hidden_check_date_range').val('');
      $('#filter_date_range').prop('disabled','disabled');
    }
   });

   $('#check_filter_section_dept').change(function(e){
    if($('#check_filter_section_dept').is(':checked'))
    {
      $('#hidden_check_section_dept').val(1);
      $('#filter_department').removeAttr('disabled');
    }
    else
    {
      $('#hidden_check_section_dept').val('');
      $('#filter_department').prop('disabled','disabled');
    }
   });

   $('#check_filter_for_group').change(function(e){
    if($('#check_filter_for_group').is(':checked'))
    {
      $('#hidden_check_for_group').val(1);
      $('#filter_for_group').removeAttr('disabled');
    }
    else
    {
      $('#hidden_check_for_group').val('');
      $('#filter_for_group').prop('disabled','disabled');
    }
   });

   $('#check_filter_document_status').change(function(e){
    if($('#check_filter_document_status').is(':checked'))
    {
      $('#hidden_check_document_status').val(1);
      $('#filter_document_status').removeAttr('disabled');
    }
    else
    {
      $('#hidden_check_document_status').val('');
      $('#filter_document_status').prop('disabled','disabled');
    }
   });

    $('#check_filter_originator').change(function(e){
    if($('#check_filter_originator').is(':checked'))
    {
      $('#hidden_check_originator').val(1);
      $('#filter_originator').removeAttr('disabled');
    }
    else
    {
      $('#hidden_check_originator').val('');
      $('#filter_originator').prop('disabled','disabled');
    }
   });

  $('#check_filter_qs_inspector').change(function(e){
    if($('#check_filter_qs_inspector').is(':checked'))
    {
      $('#hidden_check_qs_inspector').val(1);
      $('#filter_qs_inspector').removeAttr('disabled');
    }
    else
    {
      $('#hidden_check_qs_inspector').val('');
      $('#filter_qs_inspector').prop('disabled','disabled');
    }
   });

   $('#check_filter_approver').change(function(e){
    if($('#check_filter_approver').is(':checked'))
    {
      $('#hidden_check_approver').val(1);
      $('#filter_application_approver').removeAttr('disabled');
    }
    else
    {
      $('#hidden_check_approver').val('');
      $('#filter_application_approver').prop('disabled','disabled');
    }
   });

   $('#btnFilterTable').click(function(){

    dt_applications_master_list.draw();

   });

   $(document).on('click', '.btn-add-master-list-details', function(){

    let active_doc_id = $(this).attr('active-doc-id');

    LoadMasterListDetails(active_doc_id);

   });


   //this changes for the datatables
$(document).on('click','.btn-document-action', function(){
  let document_action = $(this).attr('document-action');
  $('#document_hidden_action').val(document_action);

  if(document_action == 1 || document_action == 4 || document_action == 5)
  {
    $('.btn-preprod').addClass('d-none');
  }
  else
  {
    $('.btn-preprod').removeClass('d-none');
  }

});

$('#document_wildcard').on('keyup', function(e){
  if(e.key === 'Enter' || e.keyCode === 13)
  {
    dt_add_document_details.draw();
  }
});

$('#btnSearchDocument').click(function(){
   dt_add_document_details.draw();
});

$('#modalSearchDocumentDetails').on('hidden.bs.modal', function(){
  $('#document_search_type').prop('selectedIndex',0);
  $('#document_wildcard').val('');
  dt_add_document_details.draw();
});

$(document).on('click','.btn-dcc-edit-document', function(){

  let active_doc_id = $(this).attr('active-doc-id');

  LoadDccDocumentDetails(active_doc_id);

});

$('#btnDccSubmitChanges').click(function(){

  $('#formDccEditDocument').submit();
});

$('#formDccEditDocument').submit(function(e){
  e.preventDefault();
  SubmitDccEditDocument();

});

</script>
@endsection