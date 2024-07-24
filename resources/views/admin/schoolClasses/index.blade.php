@extends('layouts.app')

@section('title', 'Data Kelas')

@section('breadcrumb')
    @parent
    <li class="breadcrumb-item active">@yield('title')</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-12">
            <!-- Card untuk Filter -->
            <x-card>
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Filter Data @yield('title')</h5>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </x-slot>

                <!-- Filter Form -->
                <form id="filterForm">
                    <div class="row">
                        <!-- Academic Year Filter -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="year">Tahun Ajaran</label>
                                <select name="year" id="year" class="form-control">
                                    <option value="">Tampilkan Semua</option>
                                    @foreach ($academicYears as $year)
                                        <option value="{{ $year->id }}">{{ $year->name }} - {{ $year->semester }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Level Filter -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="level2">Kelas</label>
                                <select name="level2" id="level2" class="form-control">
                                    <option value="">Tampilkan Semua</option>
                                    <option value="1">Kelas 1</option>
                                    <option value="2">Kelas 2</option>
                                    <!-- Tambahkan level lain sesuai kebutuhan -->
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Filter & Reset Buttons -->
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <button type="reset" class="btn btn-secondary">Reset</button>
                        </div>
                    </div>
                </form>
            </x-card>

            <!-- Card untuk Tabel -->
            <x-card>
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>@yield('title') MI Bustanul Huda Dawuhan</h5>
                        <button onclick="addForm(`{{ route('schoolClasses.store') }}`)" class="btn btn-sm btn-primary"><i
                                class="fas fa-plus-circle"></i> Tambah Data</button>
                    </div>
                </x-slot>

                <x-table id="schoolClass" class="schoolClass" style="width: 100%">
                    <x-slot name="thead">
                        <th>No</th>
                        <th>Nama Kelas</th>
                        <th>Tahun Ajaran</th>
                        <th>Aksi</th>
                    </x-slot>
                </x-table>
            </x-card>
        </div>
    </div>

    @includeIf('admin.schoolClasses.form')
@endsection

@include('include.datatable')
@include('include.datepicker')

@push('scripts')
    <script>
        let modal = '#modal-form';
        let button = '#submitBtn';
        let schoolClassTable;

        $(function() {
            // Filter Form Submission
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();

                let year = $('#year').val();

                // Clear previous error message
                $('.datetimepicker').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                Swal.close();

                // Reload DataTable if any filter is provided
                if (year) {
                    schoolClassTable.ajax.reload();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Silakan isi tahun ajaran untuk melakukan filter.',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });

            // Reset Filter Form
            $('#filterForm').on('reset', function() {
                $('.datetimepicker').removeClass('is-invalid'); // Clear invalid class on reset
                $('.invalid-feedback').remove(); // Remove error messages on reset
                schoolClassTable.ajax.reload();
            });
        });

        // Trigger DataTable reload on filter change
        $('#year, #level2').change(function() {
            schoolClassTable.ajax.reload();
        });

        // Initialize DataTable
        schoolClassTable = $('#schoolClass').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('schoolClasses.data') }}",
                data: function(d) {
                    d.year = $('#year').val();
                    d.level = $('#level2').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false
                },
                {
                    data: 'name'
                },
                {
                    data: 'academic_year_id'
                },
                {
                    data: 'action'
                }
            ]
        });

        function addForm(url, title = 'Form Tambah Data Kelas') {
            $(modal).modal('show');
            $(`${modal} .modal-title`).text(title);
            $(`${modal} form`).attr('action', url);
            $(`${modal} [name=_method]`).val('post');

            resetForm(`${modal} form`);
        }

        function editForm(url, name, title = 'Form Edit Data') {
            $.get(url)
                .done(response => {
                    $(modal).modal('show');
                    $(`${modal} .modal-title`).text(title + ' ' + name);
                    $(`${modal} form`).attr('action', url);
                    $(`${modal} [name=_method]`).val('put');

                    resetForm(`${modal} form`);
                    loopForm(response.data);
                })
                .fail(errors => {
                    $('#spinner-border').hide();
                    $(button).prop('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Opps! Gagal',
                        text: errors.responseJSON.message,
                        showConfirmButton: true,
                    });
                    if (errors.status == 422) {
                        $('#spinner-border').hide()
                        $(button).prop('disabled', false);
                        loopErrors(errors.responseJSON.errors);
                        return;
                    }
                });
        }

        function deleteData(url, name) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            })
            swalWithBootstrapButtons.fire({
                title: 'Hapus Data!',
                text: 'Apakah Anda yakin ingin menghapus ' + name + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "delete",
                        url: url,
                        dataType: "json",
                        success: function(response) {
                            if (response.status = 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 3000
                                }).then(() => {
                                    schoolClassTable.ajax.reload();
                                })
                                schoolClassTable.ajax.reload();
                            }
                        },
                        error: function(xhr, status, error) {
                            // Menampilkan pesan error
                            Swal.fire({
                                icon: 'error',
                                title: 'Opps! Gagal',
                                text: xhr.responseJSON.message,
                                showConfirmButton: true,
                            }).then(() => {
                                // Refresh tabel atau lakukan operasi lain yang diperlukan
                                schoolClassTable.ajax.reload();
                            });
                        }
                    });
                }
            });
        }

        function submitForm(originalForm) {
            $(button).prop('disabled', true);
            $('#spinner-border').show();

            $.post({
                    url: $(originalForm).attr('action'),
                    data: new FormData(originalForm),
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false
                })
                .done(response => {
                    $(modal).modal('hide');
                    if (response.status = 200) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 3500
                        }).then(() => {
                            $(button).prop('disabled', false);
                            $('#spinner-border').hide();

                            schoolClassTable.ajax.reload();
                        })
                    }
                })
                .fail(errors => {
                    $('#spinner-border').hide();
                    $(button).prop('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Opps! Gagal',
                        text: errors.responseJSON.message,
                        showConfirmButton: false,
                        timer: 3000
                    });
                    if (errors.status == 422) {
                        $('#spinner-border').hide()
                        $(button).prop('disabled', false);
                        loopErrors(errors.responseJSON.errors);
                        return;
                    }
                });
        }
    </script>
@endpush
