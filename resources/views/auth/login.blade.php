@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="container-fluid h-100">
        <div class="row h-100 no-gutters">
            <!-- Gambar Latar Belakang -->
            <div class="col-md-6 d-none d-md-flex bg-image"></div>

            <!-- Form Login -->
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <div class="login-form-container">
                    <div class="text-center mb-4">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="custom-logo mb-4">
                        </a>
                        <h4>Selamat Datang Di <span class="login-heading">Aplikasi CBT</span></h4>
                        <p class="text-muted">Sebelum login pastikan anda sudah punya akun</p>
                    </div>

                    <form id="loginForm" action="{{ route('login') }}" method="post">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="auth">Username</label>
                            <input type="text" class="form-control @error('auth') is-invalid @enderror" id="auth"
                                name="auth" value="{{ old('auth') }}" autocomplete="off" placeholder="Username">

                            @error('auth')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3 position-relative">
                            <label for="password">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror password"
                                id="password" name="password" autocomplete="off" placeholder="***********">
                            <span class="fas fa-eye" id="togglePassword"
                                style="position: absolute; right: 10px; top: 70%; transform: translateY(-50%); cursor: pointer; color: #6c757d;"></span>

                            @error('password')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        {{--  <div class="form-group d-flex justify-content-between align-items-center mb-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="customCheck1">
                                <label for="customCheck1" class="custom-control-label">Show Password</label>
                            </div>
                        </div>  --}}

                        <button type="button" onclick="login()" id="loginButton"
                            class="btn btn-lg btn-primary btn-login mb-2">
                            <i class="fas fa-sign-in-alt"></i> <span id="buttonText">Masuk</span>
                            <span id="loadingSpinner" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>

                        <div class="text-center mt-3">
                            <div class="text-muted">
                                Jika belum punya akun silahkan registrasi
                                <a href="{{ route('register') }}" class="text-muted">disini</a>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <div class="text-muted">
                                Copyright &copy; Developer MI Bustanul Huda Dawuhan {{ date('Y') }}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('keypress', function(e) {
            if (e.which == 13) {
                login();
            }
        });

        function login() {
            let auth = $('#auth').val();
            let password = $('.password').val();

            if (!auth) {
                toastr.info('Username wajib diisi');
                return;
            }

            if (!password) {
                toastr.info('Password wajib diisi');
                return;
            }

            const loginButton = $('#loginButton');
            const buttonText = $('#buttonText');
            const loadingSpinner = $('#loadingSpinner');

            loginButton.attr('disabled', true);
            buttonText.hide();
            loadingSpinner.show();

            $.ajax({
                type: 'POST',
                url: '{{ route('login') }}',
                data: $('#loginForm').serialize(),
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Login berhasil',
                        text: 'Selamat anda berhasil login ke dalam sistem kami',
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        window.location.href = '{{ route('dashboard') }}';
                    });
                },
                error: function(errors) {
                    loopErrors(errors.responseJSON.errors);
                    toastr.error(errors.responseJSON.message);
                },
                complete: function() {
                    loginButton.attr('disabled', false);
                    buttonText.show();
                    loadingSpinner.hide();
                }
            });
        }

        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            this.classList.toggle('fa-eye-slash');
        });
    </script>
@endpush
