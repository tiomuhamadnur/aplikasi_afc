<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @env('production')
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @endenv
    @yield('title-head')
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('build/assets/app-C3sVekwB.css') }}">
    <link rel="stylesheet" href="{{ asset('build/assets/app-Cq57AiTD.css') }}"> --}}
    @vite('resources/css/app.css')
    @vite('resources/sass/app.scss')
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{ asset('assets/images/logo-header.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/loading/barber-shop.css') }}">

    <style>
        .mrt-blue {
            background-color: #0053B2;
        }

        .mrt-green {
            background-color: #43B53A;
        }

        .mrt-orange {
            background-color: #FF834E;
        }

        .mrt-grey {
            background-color: #A3AAB1;
        }

        .mrt-dark-grey {
            background-color: #474747;
        }

        .dt-paging {
            display: flex !important;
            justify-content: flex-end !important;
        }
    </style>
</head>

<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        @include('layout.navbar')
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_sidebar.html -->
            @include('layout.sidebar')
            <!-- partial -->
            <div class="main-panel">
                @yield('content')
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                @include('layout.footer')
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>


    {{-- Script --}}
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{-- <script src="{{ asset('build/assets/app-C6MCKak-.js') }}"></script> --}}
    @vite('resources/js/app.js')
    {{-- <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script> --}}
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ asset('assets/vendors/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.cookie.js') }}" type="text/javascript"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js"></script>
    <!-- End custom js for this page -->

    @stack('scripts')
    @yield('javascript')
    <script>
        @if (session('notify'))
            swal("Yeheeey!", "{{ session('notify') ?? '-' }}", "success");
        @elseif (session('notifyerror'))
            swal("Ooopss!", "{{ session('notifyerror') ?? '-' }}", "error");
        @elseif ($errors->any())
            @php
                $messageError = implode('<br>', $errors->all());
            @endphp
            swal("Ooopss!", "{{ $messageError }}", "error");
        @endif
    </script>
</body>

</html>
