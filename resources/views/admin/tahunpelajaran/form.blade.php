<x-modal data-backdrop="static" data-keyboard="false" size="modal-md">
    <x-slot name="title">
        Tambah
    </x-slot>

    @method('POST')

    <div class="row">
        <div class="col-md-12 col-12">
            <div class="form-group">
                <label for="tahun_ajaran">Tahun Ajaran</label>
                <input type="text" class="form-control" name="name" id="tahun_ajaran" autocomplete="off"
                    placeholder="2023/2024">
            </div>
        </div>
    </div>

    <div class="col-md-12 col-12">
        <div class="form-group">
            <label for="semester">Semester</label>
            <select class="form-control" name="semester" id="semester">
                <option value="" disabled selected>Pilih Semester</option>
                <option value="Ganjil">Ganjil</option>
                <option value="Genap">Genap</option>
            </select>
        </div>
    </div>

    <div class="col-md-12 col-12">
        <div class="form-group">
            <label for="start_date">Tanggal Mulai</label>
            <input type="text" class="form-control datetimepicker" name="start_date" id="start_date"
                placeholder="Pilih tanggal mulai" autocomplete="off" data-toggle="datetimepicker">
        </div>
    </div>

    <div class="col-md-12 col-12">
        <div class="form-group">
            <label for="end_date">Tanggal Selesai</label>
            <input type="text" class="form-control datetimepicker" name="end_date" id="end_date"
                placeholder="Pilih tanggal selesai" autocomplete="off" data-toggle="datetimepicker">
        </div>
    </div>

    <div class="col-md-12 col-12">
        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" name="status" id="status">
                <option value="" disabled selected>Pilih Status</option>
                <option value="Sedang Berlangsung">Sedang Berlangsung</option>
                <option value="Telah Berakhir">Telah Berakhir</option>
                <option value="Belum Terlaksana">Belum Terlaksana</option>
            </select>
        </div>
    </div>

    <x-slot name="footer">
        <button type="button" onclick="submitForm(this.form)" class="btn btn-sm btn-outline-danger" id="submitBtn">
            <span id="spinner-border" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <i class="fas fa-save mr-1"></i>
            Simpan</button>
        <button type="button" data-dismiss="modal" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-times"></i>
            Close
        </button>
    </x-slot>
</x-modal>
