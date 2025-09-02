@extends('layouts.admin_layout')

  @section('title', 'User')

  @section('content_page')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>User</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item active">Section Heads and Approvals</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Section Heads</h3>
              </div>

              <!-- Start Page Content -->
              <div class="card-body">
                  <div style="float: right;">

                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalAddUser" id="btnShowAddUserModal"><i class="fa fa-user-plus"></i> Add User</button>
                  </div> <br><br>
                  <div class="table responsive">
                    <table id="tblUsers" class="table table-sm table-bordered table-striped table-hover" style="width: 100%;">
                      <thead>
                        <tr>
                          <th>Action</th>
                          <th>Username</th>
                          <th>Full Name</th>
                          <th>Approver Type</th>
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

  <div class="modal fade" id="modalAddUser">
    <div class="modal-dialog modal-md">
      <div class="modal-content">

        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-user-plus"></i> Add User</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        </div>

        <div class="modal-body">

          <form id="formAddUser" method="post">
          @csrf

          <div class="row">
            <div class="col-sm-12">
              <label>RapidX User</label>
              <select class="form-control sel-rapidx-user-list" id="rapidx_user" name="rapidx_user">
                <option selected disabled>-- Select One --</option>
              </select>
            </div>
          </div>

           <div class="row">
            <div class="col-sm-12">
              <label>Access Level</label>
              <select class="form-control" id="approver_type" name="approver_type">
                <option selected disabled>-- Select One --</option>
                <option value="1">Production Head</option>
                <option value="2">QC Head</option>
                <option value="3">Engineering Head</option>
                <option value="4">SG/PPC Section Head</option>
              </select>
            </div>
          </div>

          </form>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="btnSaveAddUser">Save User</button>
        </div>

      </div>
    </div>
  </div>

  @endsection

  @section('js_content')

  <script type="text/javascript">

    let dt_users;

  $(document).ready(function () {
    bsCustomFileInput.init();

    LoadRapidXUserList($('.sel-rapidx-user-list'));

    $('.sel-rapidx-user-list').select2({
        theme: "bootstrap4"
    });

    dt_users = $('#tblUsers').DataTable({

    //    "processing" : true,
    //     "serverSide" : true,
    //     "ajax": {
    //         url: "load_accesslevel_table",
    //     },
    //     "columns": [
    //     { "data": "action"},
    //     { "data": "username"},
    //     { "data": "fullname"},
    //     { "data": "access_level"},
    //     ],
    //     "order": [0, 'desc']
    });

  });

  function SubmitApproverType()
  {
    $.ajax({

      url: "submit_approver_type",
      method: "post",
      data: $('#formAddUser').serialize(),
      dataType: "json",
      beforeSend: function()
      {

      },
      success: function(JsonObject)
      {
        if(JsonObject['result'] == 1)
        {
          //$('#modalAddUser').modal('hide');
          $('#formAddUser')[0].reset();

          toastr.success('Added user as an approver!');

          dt_users.draw();
        }
        else
        {
          toastr.error('Error Saving Details!');

          if(JsonObject['error']['rapidx_user'] === undefined)
            {
              $('#rapidx_user').removeClass('is-invalid');
            }
            else
            {
              $('#rapidx_user').addClass('is-invalid');
            }

            if(JsonObject['error']['approver_type'] === undefined)
            {
              $('#approver_type').removeClass('is-invalid');
            }
            else
            {
              $('#approver_type').addClass('is-invalid');
            }
        }
      },
       error: function(data, xhr, status){
        toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
       }
    });
  }

  $('#btnSaveAddUser').click(function(){

    $('#formAddUser').submit();

  });

  $('#formAddUser').submit(function(e){

    e.preventDefault();
    SubmitApproverType();

  });


  </script>

  @endsection
