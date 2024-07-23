@extends('layouts.app')

@section('title', 'Tahun Ajaran')

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
                    <h5>Filter Data @yield('title')</h5>
                    <x-slot name="headerButton">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </x-slot>
                </x-slot>

                <!-- Filter Form -->
                <div class="mb-3">
                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status_filter">Status</label>
                                    <select id="status_filter" name="status_filter" class="form-control">
                                        <option value="" disabled selected>Semua</option>
                                        <option value="Sedang Berlangsung">Sedang Berlangsung</option>
                                        <option value="Telah Berakhir">Telah Berakhir</option>
                                        <option value="Belum Terlaksana">Belum Terlaksana</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="year">Tahun Ajaran</label>
                                    {{--  <input type="text" id="year" name="year" class="form-control"
                                        placeholder="Tahun Ajaran">  --}}
                                    <select name="year" id="year" class="form-control">
                                        <option value="" disabled selected>Semua</option>
                                        @foreach ($academicYears as $year)
                                            <option value="{{ $year->name }}">{{ $year->name }} - {{ $year->semester }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date_filter">Tanggal Mulai</label>
                                    <input type="text" id="start_date_filter" name="start_date_filter"
                                        data-toggle="datetimepicker" class="form-control datetimepicker"
                                        placeholder="Pilih Tanggal Mulai" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date_filter">Tanggal Selesai</label>
                                    <input type="text" id="end_date_filter" name="end_date_filter"
                                        class="form-control datetimepicker" data-toggle="datetimepicker"
                                        placeholder="Pilih Tanggal Selesai" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </x-card>

            <!-- Card untuk Tabel -->
            <x-card>
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>@yield('title') MI Bustanul Huda Dawuhan</h5>
                        <button onclick="addForm(`{{ route('academic-years.store') }}`)" class="btn btn-sm btn-primary"><i
                                class="fas fa-plus-circle"></i> Tambah Data</button>
                    </div>
                </x-slot>

                <x-table id="academicYears" class="academicYears" style="width: 100%">
                    <x-slot name="thead">
                        <th>No</th>
                        <th>Tahun Ajaran</th>
                        <th>Semester</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </x-slot>
                </x-table>
            </x-card>
        </div>
    </div>

    @includeIf('admin.tahunpelajaran.form')
@endsection

@include('include.datatable')
@include('include.datepicker')

@push('scripts')
    <script>
        let modal = '#modal-form';
        let button = '#submitBtn';
        let academicYearTable;

        $(function() {
            $('#spinner-border').hide();
            // Initialize Datepicker
            $('.datetimepicker').datetimepicker({
                icons: {
                    time: 'far fa-clock'
                },
                format: 'YYYY-MM-DD',
                locale: 'id'
            });

            // Filter Form Submission
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();

                let startDate = $('#start_date_filter').val();
                let endDate = $('#end_date_filter').val();
                let status = $('#status_filter').val();
                let year = $('#year').val();

                // Clear previous error message
                $('.datetimepicker').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                Swal.close();

                // Validate date range
                if (startDate && endDate) {
                    if (moment(startDate).isAfter(moment(endDate))) {
                        $('#start_date_filter').addClass('is-invalid');
                        $('#end_date_filter').addClass('is-invalid');
                        $('#end_date_filter').after(
                            '<div class="invalid-feedback">Tanggal selesai harus lebih besar atau sama dengan tanggal mulai.</div>'
                        );
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Tanggal selesai harus lebih besar atau sama dengan tanggal mulai.',
                            showConfirmButton: false,
                            timer: 3000
                        }).then(() => {
                            $('#end_date_filter').val('');
                        });
                        return;
                    }
                }

                // Reload DataTable if any filter is provided
                if (status || year || (startDate && endDate)) {
                    academicYearTable.ajax.reload();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Silakan isi status, tahun ajaran, atau tanggal untuk melakukan filter.',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });

            // Reset Filter Form
            $('#filterForm').on('reset', function() {
                $('.datetimepicker').removeClass('is-invalid'); // Clear invalid class on reset
                $('.invalid-feedback').remove(); // Remove error messages on reset
                academicYearTable.ajax.reload();
            });
        });


        // Initialize DataTable
        academicYearTable = $('#academicYears').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('academic-years.data') }}",
                data: function(d) {
                    d.status = $('#status_filter').val();
                    d.year = $('#year').val();
                    d.start_date = $('#start_date_filter').val();
                    d.end_date = $('#end_date_filter').val();
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
                    data: 'semester'
                },
                {
                    data: 'start_date'
                },
                {
                    data: 'end_date'
                },
                {
                    data: 'status'
                },
                {
                    data: 'action'
                }
            ]
        });


        function addForm(url, title = 'Form Tambah Data Tahun Ajaran') {
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
                                    academicYearTable.ajax.reload();
                                })
                                academicYearTable.ajax.reload();
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
                                academicYearTable.ajax.reload();
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
                            timer: 3000
                        }).then(() => {
                            $(button).prop('disabled', false);
                            $('#spinner-border').hide();

                            academicYearTable.ajax.reload();
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
