<!DOCTYPE html>
<html lang="en" data-textdirection="ltr" class="loading">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Robust admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, robust admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>AIDRC | Auto Mailer</title>
    
    @include('shared.css_links.css_links')
  </head>
  <body data-open="click" data-menu="vertical-menu" data-col="1-column" class="vertical-layout vertical-menu 1-column  blank-page blank-page">
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <div class="app-content content container-fluid">
      <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body"><section class="flexbox-container">
    <div class="col-md-4 offset-md-4 col-xs-10 offset-xs-1  box-shadow-2 p-0">
        
        <div class="card card-primary">
            <div class="card-header">
                <h4 class="card-title">AIDRC Auto Mailer</h4>
            </div>

            <div class="card-body">
                Please do not close. sends every 7:30 AM!
            </div>
        </div>

    </div>
</section>

        </div>
      </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->

    <!-- BEGIN VENDOR JS-->
    @include('shared.js_links.js_links')

    <script type="text/javascript">
        // window.onbeforeunload = bunload;

        // function bunload(){
        //     dontleave="Are you sure you want to leave?";
        //     return dontleave;
        // }

        function LoadForControlStatusEmail()
        {   
             toastr.options = {
                  "closeButton": false,
                  "debug": false,
                  "newestOnTop": true,
                  "progressBar": true,
                  "positionClass": "toast-top-right",
                  "preventDuplicates": false,
                  "onclick": null,
                  "showDuration": "300",
                  "hideDuration": "3000",
                  "timeOut": "3000",
                  "extendedTimeOut": "3000",
                  "showEasing": "swing",
                  "hideEasing": "linear",
                  "showMethod": "fadeIn",
                  "hideMethod": "fadeOut",
                };

            $.ajax({

                url: "load_for_control_status_email",
                method: "get",
                data:
                {

                },
                dataType: "json",
                beforeSend: function()
                {

                },
                success: function(JsonObject)
                {
                    if(JsonObject['result'] == 1)
                    {
                        toastr.success('E-Mail Sent to Recipients!');
                    }
                    else
                    {
                        toastr.success('No Unrecorded Invoices. Nice!');
                    }
                },
                error: function(data, xhr, status){
                    toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
                }

            });
        }

        function LoadForControlAffectedDocumentsEmailThreeDays()
        {   
             toastr.options = {
                  "closeButton": false,
                  "debug": false,
                  "newestOnTop": true,
                  "progressBar": true,
                  "positionClass": "toast-top-right",
                  "preventDuplicates": false,
                  "onclick": null,
                  "showDuration": "300",
                  "hideDuration": "3000",
                  "timeOut": "3000",
                  "extendedTimeOut": "3000",
                  "showEasing": "swing",
                  "hideEasing": "linear",
                  "showMethod": "fadeIn",
                  "hideMethod": "fadeOut",
                };

            $.ajax({

                url: "load_for_control_affected_documents_email_three_days",
                method: "get",
                data:
                {

                },
                dataType: "json",
                beforeSend: function()
                {

                },
                success: function(JsonObject)
                {
                    if(JsonObject['result'] == 1)
                    {
                        toastr.success('E-Mail Sent to Recipients!');
                    }
                    else
                    {
                        toastr.success('No Unrecorded Invoices. Nice!');
                    }
                },
                error: function(data, xhr, status){
                    toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
                }

            });
        }

        function LoadForControlAffectedDocumentsEmailOverdue()
        {   
             toastr.options = {
                  "closeButton": false,
                  "debug": false,
                  "newestOnTop": true,
                  "progressBar": true,
                  "positionClass": "toast-top-right",
                  "preventDuplicates": false,
                  "onclick": null,
                  "showDuration": "300",
                  "hideDuration": "3000",
                  "timeOut": "3000",
                  "extendedTimeOut": "3000",
                  "showEasing": "swing",
                  "hideEasing": "linear",
                  "showMethod": "fadeIn",
                  "hideMethod": "fadeOut",
                };

            $.ajax({

                url: "load_for_control_affected_documents_email_overdue",
                method: "get",
                data:
                {

                },
                dataType: "json",
                beforeSend: function()
                {

                },
                success: function(JsonObject)
                {
                    if(JsonObject['result'] == 1)
                    {
                        toastr.success('E-Mail Sent to Recipients!');
                    }
                    else
                    {
                        toastr.success('No Unrecorded Invoices. Nice!');
                    }
                },
                error: function(data, xhr, status){
                    toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
                }

            });
        }

        $(document).ready(function(){

            let scheduledTime = '07:30:00';

            setInterval(function(){
                let now = new Date();
                let timeNow = ("0" + now.getHours()).slice(-2) + ':' + ("0" + now.getMinutes()).slice(-2) + ':' + ("0" + now.getSeconds()).slice(-2);

                if(scheduledTime == timeNow){
                    console.log('Auto Mailer Run At: ' + timeNow);
                    
                    LoadForControlStatusEmail();
                    LoadForControlAffectedDocumentsEmailThreeDays();
                    LoadForControlAffectedDocumentsEmailOverdue();
                }

            }, 1000);
        });

   


     
     

       
    </script>
  </body>
</html>