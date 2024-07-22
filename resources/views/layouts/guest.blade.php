<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link rel="icon" href="{{ asset('/img/favicon.png') }}" type="image/*">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('adminlte') }}/dist/css/adminlte.min.css?v=3.2.0">

    @stack('css_vendor')

    <link rel="stylesheet" href="{{ asset('adminLTE') }}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="{{ asset('adminLTE/plugins/toastr/toastr.min.css') }}">

    @stack('css')
    <style>
        body {
            margin: 0;
            height: 100vh;
            overflow: hidden;
            background: #f8f9fa;
        }

        .bg-image {
            background: url('{{ asset('images/bg-login.png') }}') no-repeat center center;
            background-size: cover;
            height: 100vh;
        }

        .login-form-container {
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
        }

        .login-heading {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .btn-login {
            width: 100%;
            padding: 10px;
            font-size: 1.1rem;
        }

        .custom-control-label {
            font-size: 0.875rem;
        }

        .custom-logo {
            max-width: 150px;
            /* Atur lebar maksimum sesuai kebutuhan */
            height: auto;
            /* Memastikan tinggi proporsional terhadap lebar */
        }

        .custom-logo-register {
            max-width: 90px;
            /* Atur lebar maksimum sesuai kebutuhan */
            height: auto;
            /* Memastikan tinggi proporsional terhadap lebar */
        }
    </style>
</head>

<body class="hold-transition login-page">

    @yield('content')

    <script src="{{ asset('adminlte') }}/plugins/jquery/jquery.min.js"></script>
    <script src="{{ asset('adminlte') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('AdminLTE/plugins/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('AdminLTE') }}/plugins/sweetalert2/sweetalert2.min.js"></script>

    @stack('scripts_vendor')
    <script src="{{ asset('adminlte') }}/dist/js/adminlte.min.js?v=3.2.0"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    @stack('scripts')

    <script>
        // Show password
        $('#customCheck1').on('click', function() {
            if ($(this).is(':checked')) {
                $('.password').attr('type', 'text');
            } else {
                $('.password').attr('type', 'password');
            }
        })
    </script>
</body>

</html>
