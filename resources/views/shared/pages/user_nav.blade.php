<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="{{ route('dashboard') }}" class="brand-link">
    <img src="{{ asset('public/images/pricon_logo2.png') }}"
         alt="CNPTS"
         class="brand-image img-circle elevation-3"
         style="opacity: .8">
    <span class="brand-text font-weight-light">AIDRCV2</span>
  </a>

    <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->

        <li class="nav-item">
          <a href="{{ url('../RapidX') }}" class="nav-link">
            <i class="nav-icon fas fa-arrow-left"></i>
            <p>
              Return to RapidX
            </p>
          </a>
        </li>



        <li class="nav-item">
          <a href="{{ route('test_dashboard') }}" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              Dashboard
            </p>
          </a>
        </li>

        <li class="nav-item acdcs_layout d-none">
          <a href="{{ route('master_list') }}" class="nav-link">
            <i class="nav-icon fas fa-list"></i>
            <p>
              Applications Master List
            </p>
          </a>
        </li>


        <li class="nav-item acdcs_layout d-none">
          <a href="{{ route('affected_documents') }}" class="nav-link">
            <i class="nav-icon fas fa-file"></i>
            <p>
              Affected Documents
            </p>
          </a>
        </li>

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
