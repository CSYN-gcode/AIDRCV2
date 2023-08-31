@extends('layouts.admin_layout')

@section('title', 'Affected Documents')

@section('content_page')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Affected Documents</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Affected Documents</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">


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
                      <span class="input-group-text w-100" id="basic-addon1">REVISION DUE DATE</span>
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
                        <input type="checkbox" id="check_filter_document_type">
                      </div>
                      <span class="input-group-text w-100" id="basic-addon1">DOCUMENT TYPE</span>
                    </div>

                    <input type="hidden" id="hidden_check_document_type" name="hidden_check_document_type">

                    <select class="form-control" id="filter_document_type" name="filter_document_type" disabled>
                      <option value="0" selected disabled>-- Select One --</option>
                      <option value="1">AFFECTED DOCUMENT FROM APPLICATION</option>
                      <option value="2">FMEA</option>
                      <option value="3">CONTROL PLAN</option>
                    </select>

                  </div>
                </div>
              </div>

              <!-- <div class="row">
                <div class="col-sm-4">
                  <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend w-50">
                      <div class="input-group-text">
                        <input type="checkbox" id="check_filter_approver">
                      </div>
                      <span class="input-group-text w-100" id="basic-addon1">DOCUMENT APPROVER</span>
                    </div>

                    <input type="hidden" id="hidden_check_approver" name="hidden_check_approver">

                    <select class="form-control sel-approver-list" id="filter_document_approver" name="filter_document_approver[]" multiple disabled>
                    </select>

                  </div>
                </div>
              </div> -->

               <div class="row">
                <div class="col-sm-4">
                  <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend w-50">
                      <div class="input-group-text">
                        <input type="checkbox" id="check_filter_pic">
                      </div>
                      <span class="input-group-text w-100" id="basic-addon1">PERSON-IN-CHARGE</span>
                    </div>

                    <input type="hidden" id="hidden_check_pic" name="hidden_check_pic">

                    <select class="form-control sel-pic-list" id="filter_document_pic" name="filter_document_pic[]" multiple disabled>
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
                    <button type="button" class="btn btn-success btn-sm" id="btnExportAffectedDocuments"><i class="fa fa-file-excel"></i> Export Affected Documents</button>
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
                    <table id="tbl_affected_documents" class="table table-sm table-bordered table-striped table-hover" style="width: 100%; font-size:85%;">
                      <thead>
                        <tr>
                          <th>hidden id</th>
                          <th>AIDRC Control Number</th>
                          <th>Affected Document Type</th>
                          <th>Document Number</th>
                          <th>Document Name</th>
                          <th>Revision Number</th>
                          <th>Revision Due Date</th>
                          <th>Person in Charge</th>
 <!--                          <th>Document Approver</th> -->
                          <th>DCC in-Charge</th>
                          <th>Status</th>
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
@endsection

@section('js_content')
<script type="text/javascript">

  let dt_affected_documents;

  $(document).ready(function () {
    bsCustomFileInput.init();

    LoadAcdcsLayout();

    LoadAffectedApproverList($('.sel-approver-list'));
    LoadPicList($('.sel-pic-list'));

  $('.sel-approver-list').select2({
        theme: "bootstrap4",
        
    });

   $('.sel-pic-list').select2({
        theme: "bootstrap4",
  
    });


    
    $('#btnExpandHeader').trigger('click');


    dt_affected_documents = $('#tbl_affected_documents').DataTable({

       "processing" : true,
        "serverSide" : true,

        "ajax" : {
          url: "load_affected_documents_master_list",
          data: function (param){
            
            param.check_created_date = $('#hidden_check_date_range').val();
            param.check_document_type = $('#hidden_check_document_type').val();
           /* param.check_document_approver = $('#hidden_check_approver').val();*/
            param.check_person_in_charge = $('#hidden_check_pic').val();
            param.check_document_status = $('#hidden_check_document_status').val();

            param.created_date = $('#filter_date_range').val();
            param.document_type = $('#filter_document_type').val();
            /*param.document_approver = $('#filter_document_approver').val();*/
            param.person_in_charge = $('#filter_document_pic').val();
            param.document_status = $('#filter_document_status').val();
          }
        },

        "columns":[
          { "data" : "hidden_id" },
          { "data" : "aidrc_control_number" },
          { "data" : "approver_type" },
          { "data" : "document_number" }, 
          { "data" : "document_name" }, 
          { "data" : "revision_number" }, 
          { "data" : "revision_due_date" }, 
          { "data" : "person_in_charge" }, 
/*          { "data" : "document_approver" }, */
          { "data" : "dcc_in_charge" }, 
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

        "order":[6,'desc']

    });


  });

  $('#filter_date_range').daterangepicker({
    locale: {
            format: 'YYYY-MM-DD'
        }
  });

  $('#btnExportAffectedDocuments').click(function(){

    let check_created_date = $('#hidden_check_date_range').val();
    let check_document_type = $('#hidden_check_document_type').val();
    let check_document_approver = $('#hidden_check_approver').val();
    let check_person_in_charge = $('#hidden_check_pic').val();
    let check_document_status = $('#hidden_check_document_status').val();

    let created_date = $('#filter_date_range').val();
    let document_type = $('#filter_document_type').val();
    let document_approver = $('#filter_document_approver').val();
    let person_in_charge = $('#filter_document_pic').val();
    let document_status = $('#filter_document_status').val();

     window.open('export_affected_documents_report?check_created_date=' + check_created_date + '&check_document_type=' + check_document_type + '&check_document_approver=' + check_document_approver + '&check_person_in_charge=' + check_person_in_charge + '&check_document_status=' + check_document_status + '&created_date=' + created_date + '&document_type=' + document_type + '&document_approver=' + document_approver + '&person_in_charge=' + person_in_charge + '&document_status=' + document_status, '_blank');

  });

  $("#btnFilterTable").click(function(){

    dt_affected_documents.draw();

  });


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

  $('#check_filter_document_type').change(function(e){
    if($('#check_filter_document_type').is(':checked'))
    {
      $('#hidden_check_document_type').val(1);
      $('#filter_document_type').removeAttr('disabled');
    }
    else
    {
      $('#hidden_check_document_type').val('');
      $('#filter_document_type').prop('disabled','disabled');
    }
   });

/*   $('#check_filter_approver').change(function(e){
    if($('#check_filter_approver').is(':checked'))
    {
      $('#hidden_check_approver').val(1);
      $('#filter_document_approver').removeAttr('disabled');
    }
    else
    {
      $('#hidden_check_approver').val('');
      $('#filter_document_approver').prop('disabled','disabled');
    }
   });*/

    $('#check_filter_pic').change(function(e){
    if($('#check_filter_pic').is(':checked'))
    {
      $('#hidden_check_pic').val(1);
      $('#filter_document_pic').removeAttr('disabled');
    }
    else
    {
      $('#hidden_check_pic').val('');
      $('#filter_document_pic').prop('disabled','disabled');
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



</script>
@endsection