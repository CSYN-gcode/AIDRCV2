<!-- jQuery -->
<script src="{{ asset('public/template/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('public/template/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- bs-custom-file-input -->
<script src="{{ asset('public/template/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('public/template/dist/js/adminlte.min.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('public/template/dist/js/demo.js') }}"></script>
<!-- DataTables -->
<script src="{{ asset('public/template/plugins/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('public/template/plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>

<!-- Select2 -->
<script src="{{ asset('public/template/plugins/select2/js/select2.full.min.js') }}"></script>

<!-- Select2 -->
{{-- <script src="{{ asset('public/template/plugins/select2_new/js/select2.min.js') }}"></script> --}}

<!-- SweetAlert2 -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<!-- Toastr -->
<script src="{{ asset('public/template/plugins/toastr/toastr.min.js') }}"></script>

<!-- PDF -->
<script src="{{ asset('public/template/plugins/pdf/pdf.min.js') }}"></script>
<script src="{{ asset('public/template/plugins/pdf/pdf.worker.min.js') }}"></script>

<!-- Custom JS -->
<script src="@php echo asset("public/js/my_js/UserLevel.js?".date("YmdHis")) @endphp"></script>
<script src="@php echo asset("public/js/my_js/User.js?".date("YmdHis")) @endphp"></script>
<script src="@php echo asset("public/js/my_js/Application.js?".date("YmdHis")) @endphp"></script>
<script src="@php echo asset("public/js/my_js/Common.js?".date("YmdHis")) @endphp"></script>
<script src="@php echo asset("public/js/my_js/Pdf.js?".date("YmdHis")) @endphp"></script>

<!--DATERANGE-->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script>
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "100",
        "hideDuration": "100",
        "timeOut": "3000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        "iconClass":  "toast-custom"
    };
</script>
