<?php

namespace App\Http\Controllers\admin;

use App\DataTables\UsersBannedDataTable;
use App\DataTables\UsersDataTable;
use App\Http\Controllers\Controller;
use App\Models\Gender;
use App\Models\Jabatan;
use App\Models\Perusahaan;
use App\Models\RelasiStruktur;
use App\Models\Role;
use App\Models\TipeEmployee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(UsersDataTable $dataTable)
    {
        $gender = Gender::all();
        $perusahaan = Perusahaan::all();
        $role = Role::all();
        $jabatan = Jabatan::all();
        $tipe_employee = TipeEmployee::all();
        $relasi_struktur = RelasiStruktur::all();

        return $dataTable->render('pages.admin.user.index', compact([
            'perusahaan',
            'role',
            'gender',
            'jabatan',
            'tipe_employee',
            'relasi_struktur',
        ]));
    }

    public function banned_user(UsersBannedDataTable $dataTable)
    {
        return $dataTable->render('pages.admin.user.banned_user');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'no_hp' => 'required|numeric',
            'perusahaan_id' => 'required|numeric',
            'role_id' => 'required|numeric',
            'gender_id' => 'required|numeric',
            'jabatan_id' => 'required|numeric',
            'tipe_employee_id' => 'required|numeric',
            'relasi_struktur_id' => 'required|numeric',
        ]);

        $password = 'user123';

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'gender_id' => $request->gender_id,
            'password' => Hash::make($password),
            'perusahaan_id' => $request->perusahaan_id,
            'role_id' => $request->role_id,
            'jabatan_id' => $request->jabatan_id,
            'tipe_employee_id' => $request->tipe_employee_id,
            'relasi_struktur_id' => $request->relasi_struktur_id,
        ]);

        return redirect()->route('user.index')->withNotify('Data berhasil ditambahkan');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        $gender = Gender::all();
        $perusahaan = Perusahaan::all();
        $role = Role::all();
        $jabatan = Jabatan::all();
        $tipe_employee = TipeEmployee::all();
        $relasi_struktur = RelasiStruktur::all();

        return view('pages.admin.user.edit', compact([
            'user',
            'perusahaan',
            'role',
            'gender',
            'jabatan',
            'tipe_employee',
            'relasi_struktur',
        ]));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
            'name' => 'required',
            'no_hp' => 'required|numeric',
            'gender_id' => 'required|numeric',
            'perusahaan_id' => 'required|numeric',
            'role_id' => 'required|numeric',
            'jabatan_id' => 'required|numeric',
            'tipe_employee_id' => 'required|numeric',
            'relasi_struktur_id' => 'required|numeric',
        ]);

        $data = User::findOrFail($request->id);
        $data->update([
            'name' => $request->name,
            'no_hp' => $request->no_hp,
            'gender_id' => $request->gender_id,
            'perusahaan_id' => $request->perusahaan_id,
            'role_id' => $request->role_id,
            'jabatan_id' => $request->jabatan_id,
            'tipe_employee_id' => $request->tipe_employee_id,
            'relasi_struktur_id' => $request->relasi_struktur_id,
        ]);

        return redirect()->route('user.index')->withNotify('Data berhasil diubah');
    }

    public function ban(Request $request)
    {
        $request->validate([
            'uuid' => 'required|string'
        ]);

        $user = User::where('uuid', $request->uuid)->firstOrFail();
        $user->ban();

        return redirect()->route('user.index')->withNotify('User berhasil di-banned');
    }

    public function change_password(Request $request)
    {
        $request->validate([
            'uuid' => 'required|string'
        ]);

        $user = User::where('uuid', $request->uuid)->firstOrFail();
        $default_password = "user123";
        $user->update([
            'password' => Hash::make($default_password),
        ]);

        return redirect()->route('user.index')->withNotify('Password user ' . $user->name . ' berhasil diubah default menjadi ' . $default_password);
    }

    public function unban(Request $request)
    {
        $request->validate([
            'uuid' => 'required|string'
        ]);

        $user = User::where('uuid', $request->uuid)->firstOrFail();
        $user->unban();

        return redirect()->route('user.banned')->withNotify('User berhasil di-unbanned');
    }
}
