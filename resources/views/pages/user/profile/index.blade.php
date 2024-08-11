@extends('layout.base')

@section('title-head')
    <title>Profile</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Profile</h4>
                        <div class="row">
                            <div class="col-md-6 grid-margin stretch-card">
                                <div class="card border">
                                    <div class="card-body">
                                        <h4 class="card-title">Form Edit Profile</h4>
                                        <p class="card-description"> Silahkan isi untuk mengubah data profil anda </p>
                                        <form action="{{ route('profile.update') }}" class="forms-sample" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-group row">
                                                <label for="photo" class="col-sm-3 col-form-label">Photo</label>
                                                <div class="col-sm-9">
                                                    <div class="nav-profile-img mb-2">
                                                        <img src="{{ asset('storage/' . auth()->user()->photo ?? '#') }}"
                                                            class="img-thumbnail" alt="Photo profile belum diupload"
                                                            style="width: 200px" id="previewImagePhoto">
                                                        <span class="availability-status online"></span>
                                                    </div>
                                                    <input type="file" class="form-control" id="photo" name="photo"
                                                        placeholder="Input photo" accept="image/*">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="ttd" class="col-sm-3 col-form-label">Photo TTD</label>
                                                <div class="col-sm-9">
                                                    <div class="nav-profile-img mb-2">
                                                        <img src="{{ asset('storage/' . auth()->user()->ttd ?? '#') }}"
                                                            class="img-thumbnail" alt="Photo TTD belum diupload"
                                                            style="width: 200px" id="previewImageTTD">
                                                        <span class="availability-status online"></span>
                                                    </div>
                                                    <input type="file" class="form-control" id="ttd" name="ttd"
                                                        placeholder="Input ttd" accept="image/*">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="exampleInputUsername2"
                                                    class="col-sm-3 col-form-label">Name</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="exampleInputUsername2"
                                                        placeholder="Input name" value="{{ auth()->user()->name ?? '' }}"
                                                        disabled>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="exampleInputEmail2"
                                                    class="col-sm-3 col-form-label">Email</label>
                                                <div class="col-sm-9">
                                                    <input type="email" class="form-control" id="exampleInputEmail2"
                                                        placeholder="Email" value="{{ auth()->user()->email ?? '-' }}"
                                                        disabled>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="exampleInputMobile" class="col-sm-3 col-form-label">No
                                                    HP</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="exampleInputMobile"
                                                        placeholder="Mobile number" name="no_hp"
                                                        value="{{ auth()->user()->no_hp ?? '-' }}" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="gender" class="col-sm-3 col-form-label">Gender</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="gender"
                                                        placeholder="Mobile number"
                                                        value="{{ auth()->user()->gender->name ?? '-' }}" disabled>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="jabatan" class="col-sm-3 col-form-label">Jabatan</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="jabatan"
                                                        placeholder="Mobile number"
                                                        value="{{ auth()->user()->jabatan->name ?? '-' }}" disabled>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="tipe" class="col-sm-3 col-form-label">Tipe User</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="tipe"
                                                        placeholder="Mobile number"
                                                        value="{{ auth()->user()->tipe_employee->name ?? '-' }}" disabled>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="perusahaan" class="col-sm-3 col-form-label">Perusahaan</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="perusahaan"
                                                        placeholder="Mobile number"
                                                        value="{{ auth()->user()->perusahaan->name ?? '-' }}" disabled>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="section" class="col-sm-3 col-form-label">Section</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="section"
                                                        placeholder="Mobile number"
                                                        value="{{ auth()->user()->relasi_struktur->seksi->name ?? '-' }}"
                                                        disabled>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="department" class="col-sm-3 col-form-label">Department</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="department"
                                                        placeholder="Mobile number"
                                                        value="{{ auth()->user()->relasi_struktur->departemen->name ?? '-' }}"
                                                        disabled>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="divisi" class="col-sm-3 col-form-label">Divisi</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="divisi"
                                                        placeholder="Mobile number"
                                                        value="{{ auth()->user()->relasi_struktur->divisi->name ?? '-' }}"
                                                        disabled>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="direktorat" class="col-sm-3 col-form-label">Direktorat</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="direktorat"
                                                        placeholder="Mobile number"
                                                        value="{{ auth()->user()->relasi_struktur->direktorat->name ?? '-' }}"
                                                        disabled>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 grid-margin stretch-card">
                                <div class="card border">
                                    <div class="card-body">
                                        <h4 class="card-title">Form Change Password (on development)</h4>
                                        <p class="card-description"> Silahkan isi untuk mengubah password akun anda </p>
                                        <form class="forms-sample">
                                            <div class="form-group row">
                                                <label for="exampleInputPassword2" class="col-sm-3 col-form-label">Old
                                                    Password</label>
                                                <div class="col-sm-9">
                                                    <input type="password" class="form-control"
                                                        id="exampleInputPassword2" placeholder="Input Old Password">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="exampleInputPassword2" class="col-sm-3 col-form-label">New
                                                    Password</label>
                                                <div class="col-sm-9">
                                                    <input type="password" class="form-control"
                                                        id="exampleInputPassword2" placeholder="Input New Password">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="exampleInputConfirmPassword2"
                                                    class="col-sm-3 col-form-label">Re
                                                    New Password</label>
                                                <div class="col-sm-9">
                                                    <input type="password" class="form-control"
                                                        id="exampleInputConfirmPassword2"
                                                        placeholder="Konfirmasi New Password">
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('dashboard.index') }}" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            const imageInput = document.getElementById('photo');
            const previewImage = document.getElementById('previewImagePhoto');

            imageInput.addEventListener('change', function(event) {
                const selectedFile = event.target.files[0];

                if (selectedFile) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewImage.style.display = 'block';
                    }

                    reader.readAsDataURL(selectedFile);
                }
            });

            const imageInputTTD = document.getElementById('ttd');
            const previewImageTTD = document.getElementById('previewImageTTD');

            imageInputTTD.addEventListener('change', function(event) {
                const selectedFile = event.target.files[0];

                if (selectedFile) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        previewImageTTD.src = e.target.result;
                        previewImageTTD.style.display = 'block';
                    }

                    reader.readAsDataURL(selectedFile);
                }
            });
        });
    </script>
@endsection
