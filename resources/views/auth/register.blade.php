@extends('layouts.guest')

@section('title', 'Registrasi')

@section('content')
    <div class="container-fluid h-100">
        <div class="row h-100 no-gutters">
            <!-- Gambar Latar Belakang -->
            <div class="col-md-6 d-none d-md-flex bg-image"></div>

            <!-- Form Registrasi -->
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <div class="registration-form-container w-100">
                    <div class="card shadow-lg p-4">
                        <div class="card-body">
                            <div class="text-center mb-2">
                                <a href="{{ url('/') }}">
                                    <img src="{{ asset('images/logo.png') }}" alt="Logo"
                                        class="custom-logo-register mb-2 mt-0">
                                </a>
                                <h4 class="registration-heading mb-4">Selamat Datang Di Aplikasi Ujian Sekolah!</h4>
                            </div>

                            <form id="registrationForm" action="{{ route('register') }}" method="post">
                                @csrf

                                <div class="form-group mb-3">
                                    <label for="name">Nama Lengkap</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') }}" autocomplete="off"
                                        placeholder="Masukkan nama lengkap">

                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}" autocomplete="off"
                                        placeholder="Masukkan email">

                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                        id="username" name="username" value="{{ old('username') }}" autocomplete="off"
                                        placeholder="NISN">

                                    @error('username')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3 position-relative">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" autocomplete="off" placeholder="Masukkan password">
                                    <span class="fas fa-eye" id="togglePassword"
                                        style="position: absolute; right: 10px; top: 70%; transform: translateY(-50%); cursor: pointer; color: #6c757d;"></span>

                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-4">
                                    <label for="password_confirmation">Konfirmasi Password</label>
                                    <input type="password"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        id="password_confirmation" name="password_confirmation" autocomplete="off"
                                        placeholder="Konfirmasi password">

                                    @error('password_confirmation')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <button type="button" onclick="register()" id="registerButton"
                                    class="btn btn-lg btn-primary btn-block mb-2">
                                    <i class="fas fa-user-plus"></i> <span id="buttonText">Daftar</span>
                                    <span id="loadingSpinner" style="display:none;"><i
                                            class="fas fa-spinner fa-spin"></i></span>
                                </button>

                                <div class="text-center mt-3">
                                    <div class="text-muted">
                                        Sudah punya akun? <a href="{{ route('login') }}" class="text-muted">Masuk
                                            disini</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .custom-logo {
            max-width: 150px;
            height: auto;
        }

        .registration-form-container {
            width: 100%;
            max-width: 500px;
        }

        .form-control {
            border-radius: 4px;
            box-shadow: none;
        }

        .invalid-feedback {
            display: block;
        }

        .text-center {
            text-align: center;
        }

        .position-relative {
            position: relative;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).on('keypress', function(e) {
            if (e.which == 13) {
                register();
            }
        });

        function register() {
            let name = $('#name').val();
            let email = $('#email').val();
            let password = $('#password').val();
            let password_confirmation = $('#password_confirmation').val();

            if (!name) {
                toastr.info('Nama lengkap wajib diisi');
                return;
            }

            if (!email) {
                toastr.info('Email wajib diisi');
                return;
            }

            if (!password) {
                toastr.info('Password wajib diisi');
                return;
            }

            if (password !== password_confirmation) {
                toastr.info('Password dan konfirmasi password tidak cocok');
                return;
            }

            const registerButton = $('#registerButton');
            const buttonText = $('#buttonText');
            const loadingSpinner = $('#loadingSpinner');

            registerButton.attr('disabled', true);
            buttonText.hide();
            loadingSpinner.show();

            $.ajax({
                type: 'POST',
                url: '{{ route('register') }}',
                data: $('#registrationForm').serialize(),
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pendaftaran berhasil',
                        text: 'Selamat anda berhasil mendaftar ke dalam sistem kami',
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        window.location.href = '{{ route('login') }}';
                    });
                },
                error: function(errors) {
                    loopErrors(errors.responseJSON.errors);
                    toastr.error(errors.responseJSON.message);
                },
                complete: function() {
                    registerButton.attr('disabled', false);
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
