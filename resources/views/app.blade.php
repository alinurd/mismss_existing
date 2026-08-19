<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{url('assets/dist/pic/favicon.ico')}}">
    <title>MisMass Apps</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ url('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/plugins/select2/css/select2.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ url('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ url('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ url('assets/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- sweet alert -->
    <link rel="stylesheet" href="{{ url('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ url('assets/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/styleku.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/custom.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/newCustom.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/newLoader.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/scrollTop.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/daterangepicker-custom.css?v='.date('YmdHis')) }}">
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">

    <div class="preloaderz">
            <div class="preloaderz-wrapper" style="text-align:center">
                <img style="animation:shake 1s infinite" src="{{url('assets/dist/pic/logo-adm-notext.png')}}" height="90" width="90" style='opacity:1'>
                <div class="text">Loading...</div>
                <!-- <span class="loaderz"></span> -->
            </div>
        </div>

    <div class="wrapper">

        <!-- Preloader -->
        <!-- <div class="preloader flex-column justify-content-center align-items-center" style="font-size:50px">
            <img class="animation__shake" src="{{url('assets/dist/pic/logo-adm-notext.png')}}" height="60" width="60"> -->
            <!-- <i class="animation__shake fas fa-store-alt brand-image img-circle"></i> -->
        <!-- </div> -->

        <!-- Tempat Topbar -->
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">

            <ul class="navbar-nav" style='flex:none'>
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- <ul class="navbar-nav">
                <li class="nav-item"> -->
                <div class="marquee-container">
                    <div class="marquee">{{$announcer}}</div>
                </div>
                <!-- </li>
            </ul> -->

             <!-- Right navbar links -->
             <ul class="navbar-nav ml-auto" style='flex:none'>
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        Halo, {{$username}}<i class="fas fa-user ml-2"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <div class="dropdown-item">
                            <div class="media">
                                <img src="{{url('/assets/dist/img/default.jpg')}}" alt="User Avatar" class="mr-3 img-circle" style="width:80px">
                                <div class="media-body">
                                    <div class="media-idname" style="display:flex">
                                        <h3 class="dropdown-item-title">
                                            {{$username}}
                                        </h3>
                                    </div>
                                    @if(Auth::user()->announcer_update)
                                        <p style="margin-top:1rem"><a href="#" id="updateAnnouncerBtn" class="text-sm">Update Marquee</a></p>
                                    @endif
                                    <p><a href="#" data-id="profile" link="{{url('/profile')}}" class="text-sm">View Profile</a></p>
                                    <p><a onclick="logout('apps')" class="text-sm logout-btn pointlink"><i class="fas fa-sign-out"></i> Logout</a></p>
                                </div>
                            </div>
                        </div>
                    </div>

                </li>
            </ul>
            
        </nav>
        <!-- /.navbar -->

        <!-- Tempat Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="/" class="brand-link">
                <img src="{{url('/assets/dist/pic/logo-adm-2.png')}}" alt="AdminLTE Logo" class="brand-image" style="">
                <!--<i class="fas fa-store-alt brand-image img-circle elevation-3"></i>-->
                <!--<span class="brand-text font-weight-light">Londrian</span>-->
            </a>

            <!-- Sidebar -->
            <div class="sidebar">

                <!-- <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image" style="font-size:20px;color:#c2c7d0;margin-left:7px">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="info">
                        <a href="#" class="d-block" id="sidebar-balance"></a>
                    </div>
                </div> -->

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                        {{-- DASHBOARD --}}
                        @if(Auth::user()->dashboard_page)
                        <li class="nav-item">
                            <a href="#" data-id="dashboard" link="{{url('/dashboard')}}" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        @endif


                        <!-- ============================= -->
                        <!-- MAIN MENU                     -->
                        <!-- ============================= -->
                        <li class="nav-header text-primary border-bot-header text-primary border-bot-header">Main Menu</li>

                        @if(Auth::user()->neworder_page)
                        <li class="nav-item">
                            <a href="#" data-id="newship" link="{{url('/newship')}}" class="nav-link">
                                <i class="nav-icon fas fa-keyboard"></i>
                                <p>Create Shipment</p>
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->shiptrip_page)
                        <li class="nav-item">
                            <a href="#" data-id="shiptrip" link="{{url('/shiptrip')}}" class="nav-link">
                                <i class="nav-icon fas fa-box-open"></i>
                                <p>Shipment Trip</p>
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->shiplist_page)
                        <li class="nav-item">
                            <a href="#" data-id="shiplist" link="{{url('/shiplist')}}" class="nav-link">
                                <i class="nav-icon fas fa-table"></i>
                                <p>Shipment List</p>
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->custlist_page)
                        <li class="nav-item">
                            <a href="#" data-id="custlist" link="{{url('/custlist')}}" class="nav-link">
                                <i class="nav-icon fas fa-user-tie"></i>
                                <p>Customer List</p>
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->warelist_page)
                        <li class="nav-item">
                            <a href="#" data-id="ware" link="{{url('/warehouse')}}" class="nav-link">
                                <i class="nav-icon fas fa-warehouse"></i>
                                <p>Warehouse</p>
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->servlist_page)
                        <li class="nav-item">
                            <a href="#" data-id="servlist" link="{{url('/servlist')}}" class="nav-link">
                                <i class="nav-icon fas fa-concierge-bell"></i>
                                <p>Service List</p>
                            </a>
                        </li>
                        @endif


                        <!-- ============================= -->
                        <!-- TOOLS                         -->
                        <!-- ============================= -->
                        <li class="nav-header text-primary border-bot-header">Tools</li>

                        @if(Auth::user()->export_page)
                        <li class="nav-item">
                            <a href="#" data-id="backup" link="{{url('/backup')}}" class="nav-link">
                                <i class="nav-icon fas fa-file-excel"></i>
                                <p>Export Data</p>
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->void_page)
                        <li class="nav-item">
                            <a href="#" data-id="void" link="{{url('/void')}}" class="nav-link">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>Void Invoice</p>
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->history_page)
                        <li class="nav-item">
                            <a href="#" data-id="history" link="{{url('/history')}}" class="nav-link">
                                <i class="nav-icon fas fa-history"></i>
                                <p>Track History</p>
                            </a>
                        </li>
                        @endif

                        <li class="nav-item">
                            <a href="#" data-id="tracking" link="{{url('/shiptrip/tracking/admin')}}" class="nav-link">
                                <i class="nav-icon fas fa-route"></i>
                                <p>Tracking Resi</p>
                            </a>
                        </li>

                        @if(Auth::user()->bulky_page)
                        <li class="nav-item">
                            <a href="#" data-id="bulky" link="{{url('/tool/bulky')}}" class="nav-link">
                                <i class="nav-icon fas fa-database"></i>
                                <p>Bulky</p>
                            </a>
                        </li>
                        @endif


                        <!-- ============================= -->
                        <!-- UTILITIES                     -->
                        <!-- ============================= -->
                        <li class="nav-header text-primary border-bot-header">Utilities</li>

                        @if(Auth::user()->mail_page)
                        <li class="nav-item">
                            <a href="https://mail.hostinger.com/old" target="_blank" data-id="mail" class="nav-link">
                                <i class="nav-icon fas fa-user"></i>
                                <p>Login Webmail</p>
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->troubleshoot_page)
                        <li class="nav-item">
                            <a href="#" data-id="troubleshoot" link="{{url('/troubleshoot')}}" class="nav-link">
                                <i class="nav-icon fas fa-exclamation-triangle"></i>
                                <p>Troubleshoot</p>
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->job_history_page)
                        <li class="nav-item">
                            <a href="#" data-id="jobhistory" link="{{url('/jobhistory')}}" class="nav-link">
                                <i class="nav-icon fas fa-user-cog"></i>
                                <p>Job History</p>
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->tutorial_page)
                        <li class="nav-item">
                            <a href="https://drive.google.com/file/d/1H-Yfm-j4lU-lipc2p9lkc2wiZdsOrM9i/view?usp=sharing" target="_blank" data-id="tutorial" class="nav-link">
                                <i class="nav-icon fas fa-book"></i>
                                <p>Panduan Sistem</p>
                            </a>
                        </li>
                        @endif

                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">

            <!-- Tempat Konten -->
            <div id="page-content"></div>

            @include('components.announcer')

        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> {{env('APP_VERSION')}}
            </div>
            <strong>Copyright &copy; {{ date('Y') }} <a href="#">@MisMass</a>.</strong> All rights reserved.
        </footer>

    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="{{ url('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ url('assets/plugins/jquery-mask/jquery.mask.min.js') }}"></script>
    <script src="{{ url('assets/plugins/jquery-validation/jquery.validate.js') }}"></script>
    <script src="{{ url('assets/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <!-- date-range-picker -->
    <script src="{{ url('assets/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ url('assets/plugins/moment/moment-with-locales.min.js') }}"></script>
    <script src="{{ url('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- sweetalert2 -->
    <script src="{{ url('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ url('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{ url('assets/plugins/datatables/jquery.dataTables.min.js') }}" defer></script>
    <script src="{{ url('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}" defer></script>
    <script src="{{ url('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}" defer></script>
    <script src="{{ url('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}" defer></script>
    <script src="{{ url('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}" defer></script>
    <script src="{{ url('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}" defer></script>
    <!-- Chart -->
    <script src="{{ url('assets/plugins/chart.js/Chart.min.js') }}"></script>
    <script src="{{ url('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- Peke Upload -->
    <script src="{{ url('assets/dist/js/pekeUpload/pekeUpload.js')}}"></script>
    <!-- AdminLTE App -->
    <script src="{{ url('assets/dist/js/adminlte.min.js') }}"></script>
    <!-- Scriptku -->
    <script src="{{ url('assets/customs/js/script.js?v='.env('APP_VERSION')) }}"></script>
    <script src="{{ url('assets/customs/js/custom-methods.js?v='.env('APP_VERSION')) }}"></script>
    <script src="{{ url('assets/customs/js/announcer.js?v='.env('APP_VERSION')) }}"></script>

    <script>
        $(function() {
            var firstNav = $(".sidebar nav ul li a").first().attr("data-id"),
                url = location.origin+"/"+firstNav,
                token = "{{ csrf_token() }}";
            
            $(".sidebar nav ul li a").first().addClass("active");
            pageReload(url, token);

            $("nav ul li a[data-widget='pushmenu']").on('click', function() {
                if ($('body').hasClass('sidebar-collapse')) {
                    $(".main-sidebar .brand-link img").attr("src", "{{ url('assets/dist/pic/logo-adm-2.png') }}");
                } else {
                    $(".main-sidebar .brand-link img").attr("src", "{{ url('assets/dist/pic/logo-adm-notext.png') }}");
                }
            });

            $("aside").hover(function() {
                if ($('body').hasClass('sidebar-collapse')) {
                    $(".main-sidebar .brand-link img").attr("src", "{{ url('assets/dist/pic/logo-adm-2.png') }}");
                };
            }, function() {
                if ($('body').hasClass('sidebar-collapse')) {
                    $(".main-sidebar .brand-link img").attr("src", "{{ url('assets/dist/pic/logo-adm-notext.png') }}");
                }
            });

            $(document).on("click", ".sidebar .nav-link, a[data-id='profile'], a[data-id='diskonlist'], .backPageBtn", function() {
                console.log("triggered");
                var id = $(this).attr('data-id');
                var url = $(this).attr('link');

                if(id!="tutorial"&&id!="mail"&&id!="sandbox"){
                    $('.nav-link').removeClass('active');
                    $("a[data-id='"+id+"']").addClass('active');

                    if ($('body').hasClass('sidebar-open')) {
                        $('body').removeClass('sidebar-open');
                        $('body').addClass('sidebar-closed sidebar-collapse');
                    }

                    token = "{{ csrf_token() }}";
                    pageReload(url, token);
                    checkSes();
                }

            });

            setInterval(() => {
                checkSes();
            }, 1000 * 60 * 15);

            setInterval(() => {
                checkExpired();
            }, 5000);

            // $("#example1").DataTable({
            //     "responsive": true,
            //     "lengthChange": false,
            //     "autoWidth": false,
            //     "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            // }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

        });
    </script>
</body>

</html>