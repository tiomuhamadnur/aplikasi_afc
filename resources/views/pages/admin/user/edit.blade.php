@extends('layout.base')

@section('title-head')
    <title>Admin | Edit User</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Data User</h4>
                        <form id="editForm" action="{{ route('user.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="text" name="id" value="{{ $user->id }}" hidden>
                            <div class="form-group">
                                <label for="name" class="required">Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Input Name"
                                    autocomplete="off" required value="{{ $user->name }}">
                            </div>
                            <div class="form-group">
                                <label for="email" class="required">Email</label>
                                <input type="email" class="form-control" placeholder="Input Email" autocomplete="off" required
                                    value="{{ $user->email }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="no_hp" class="required">No HP</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="Input No HP"
                                    autocomplete="off" required value="{{ $user->no_hp }}">
                            </div>
                            <div class="form-group">
                                <label for="gender_id" class="required">Gender</label>
                                <select class="tom-select-class" name="gender_id" id="gender_id" required>
                                    <option value="" selected disabled>- pilih gender -</option>
                                    @if ($user->gender_id != null)
                                        @foreach ($gender as $item)
                                            <option value="{{ $item->id }}"
                                                @if ($item->id == $user->gender->id) selected @endif>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        @foreach ($gender as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tipe_employee_id" class="required">Tipe Employee</label>
                                <select class="tom-select-class" name="tipe_employee_id" id="tipe_employee_id"
                                    required>
                                    <option value="" selected disabled>- pilih tipe employee -</option>
                                    @if ($user->tipe_employee_id != null)
                                        @foreach ($tipe_employee as $item)
                                            <option value="{{ $item->id }}"
                                                @if ($item->id == $user->tipe_employee->id) selected @endif>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        @foreach ($tipe_employee as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="perusahaan_id" class="required">Perusahaan</label>
                                <select class="tom-select-class" name="perusahaan_id" id="perusahaan_id"
                                    required>
                                    <option value="" selected disabled>- pilih perusahaan -</option>
                                    @if ($user->perusahaan_id != null)
                                        @foreach ($perusahaan as $item)
                                            <option value="{{ $item->id }}"
                                                @if ($item->id == $user->perusahaan->id) selected @endif>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        @foreach ($perusahaan as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="relasi_struktur_id" class="required">Struktur</label>
                                <select class="tom-select-class" name="relasi_struktur_id"
                                    id="relasi_struktur_id" required>
                                    <option value="" selected disabled>- pilih struktur -</option>
                                    @if ($user->relasi_struktur_id != null)
                                        @foreach ($relasi_struktur as $item)
                                            <option value="{{ $item->id }}"
                                                @if ($item->id == $user->relasi_struktur->id) selected @endif>
                                                Seksi {{ $item->seksi->code }} -
                                                Departemen {{ $item->departemen->code }} - Divisi
                                                {{ $item->divisi->code }} -
                                                Direktorat {{ $item->direktorat->code }}
                                            </option>
                                        @endforeach
                                    @else
                                        @foreach ($relasi_struktur as $item)
                                            <option value="{{ $item->id }}">
                                                Seksi {{ $item->seksi->code }} -
                                                Departemen {{ $item->departemen->code }} - Divisi
                                                {{ $item->divisi->code }} -
                                                Direktorat {{ $item->direktorat->code }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="jabatan_id" class="required">Jabatan</label>
                                <select class="tom-select-class" name="jabatan_id" id="jabatan_id" required>
                                    <option value="" selected disabled>- pilih jabatan -</option>
                                    @if ($user->jabatan_id != null)
                                        @foreach ($jabatan as $item)
                                            <option value="{{ $item->id }}"
                                                @if ($item->id == $user->jabatan->id) selected @endif>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        @foreach ($jabatan as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="role_id" class="required">Role</label>
                                <select class="tom-select-class" name="role_id" id="role_id" required>
                                    <option value="" selected disabled>- pilih role -</option>
                                    @if ($user->role_id != null)
                                        @foreach ($role as $item)
                                            <option value="{{ $item->id }}"
                                                @if ($item->id == $user->role->id) selected @endif>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        @foreach ($role as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('user.index') }}" type="button" class="btn btn-secondary">Cancel</a>
                                <button type="submit" form="editForm" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
