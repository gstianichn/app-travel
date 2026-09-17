<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard Admin Utama
     */
    public function index()
    {
        $user = Auth::user();

        return view(
            'dashboard.admin.index',
            compact('user')
        );
    }


    /**
     * =========================================================
     * KELOLA USER
     * =========================================================
     */


    /**
     * Menampilkan daftar user.
     */
    public function users(Request $request)
    {
        $user = Auth::user();

        $query = User::query();

        /*
        |--------------------------------------------------------------------------
        | Hanya mengambil akun dengan role user
        |--------------------------------------------------------------------------
        */

        $query->where('role', 'user');


        /*
        |--------------------------------------------------------------------------
        | Pencarian
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'email',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'phone',
                    'like',
                    "%{$search}%"
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Filter Status
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter Verifikasi
        |--------------------------------------------------------------------------
        */

        if ($request->filled('verification')) {

            if (
                $request->verification ===
                'verified'
            ) {

                $query->whereNotNull(
                    'email_verified_at'
                );

            }

            if (
                $request->verification ===
                'unverified'
            ) {

                $query->whereNull(
                    'email_verified_at'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Daftar User
        |--------------------------------------------------------------------------
        */

        $users = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        */

        $totalUsers = User::where(
            'role',
            'user'
        )->count();


        $activeUsers = User::where(
            'role',
            'user'
        )
            ->where(
                'status',
                'active'
            )
            ->count();


        $inactiveUsers = User::where(
            'role',
            'user'
        )
            ->where(
                'status',
                'inactive'
            )
            ->count();


        $unverifiedUsers = User::where(
            'role',
            'user'
        )
            ->whereNull(
                'email_verified_at'
            )
            ->count();


        return view(
            'dashboard.admin.kelola-user.index',
            compact(
                'user',
                'users',
                'totalUsers',
                'activeUsers',
                'inactiveUsers',
                'unverifiedUsers'
            )
        );
    }


    /**
     * Menampilkan form tambah user.
     */
    public function createUser()
    {
        $user = Auth::user();

        return view(
            'dashboard.admin.kelola-user.create',
            compact('user')
        );
    }


    /**
     * Menyimpan user baru.
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

        ], [

            'name.required' =>
                'Nama wajib diisi.',

            'email.required' =>
                'Email wajib diisi.',

            'email.email' =>
                'Format email tidak valid.',

            'email.unique' =>
                'Email sudah digunakan.',

            'password.required' =>
                'Password wajib diisi.',

            'password.min' =>
                'Password minimal 8 karakter.',

            'password.confirmed' =>
                'Konfirmasi password tidak sesuai.',

        ]);


        User::create([

            'name' =>
                $validated['name'],

            'email' =>
                $validated['email'],

            'phone' =>
                $validated['phone'] ?? null,

            'password' =>
                Hash::make(
                    $validated['password']
                ),

            /*
            |--------------------------------------------------------------------------
            | User yang dibuat Admin Utama
            |--------------------------------------------------------------------------
            */

            'role' =>
                'user',

            'status' =>
                'active',

        ]);


        return redirect()
            ->route(
                'admin.kelola-user'
            )
            ->with(
                'success',
                'User berhasil ditambahkan.'
            );
    }


    /**
     * Menampilkan detail user.
     */
    public function showUser(User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | Pastikan akun adalah user biasa
        |--------------------------------------------------------------------------
        */

        abort_if(
            $user->role !== 'user',
            404
        );


        $admin = Auth::user();


        return view(
            'dashboard.admin.kelola-user.show',
            compact(
                'user',
                'admin'
            )
        );
    }


    /**
     * Menampilkan form edit user.
     */
    public function editUser(User $user)
    {
        abort_if(
            $user->role !== 'user',
            404
        );


        $admin = Auth::user();


        return view(
            'dashboard.admin.kelola-user.edit',
            compact(
                'user',
                'admin'
            )
        );
    }


    /**
     * Memperbarui data user.
     */
    public function updateUser(
        Request $request,
        User $user
    ) {

        abort_if(
            $user->role !== 'user',
            404
        );


        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',

                Rule::unique(
                    'users',
                    'email'
                )->ignore(
                    $user->id
                ),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],

        ], [

            'name.required' =>
                'Nama wajib diisi.',

            'email.required' =>
                'Email wajib diisi.',

            'email.email' =>
                'Format email tidak valid.',

            'email.unique' =>
                'Email sudah digunakan.',

            'password.min' =>
                'Password minimal 8 karakter.',

            'password.confirmed' =>
                'Konfirmasi password tidak sesuai.',

            'status.required' =>
                'Status user wajib dipilih.',

        ]);


        /*
        |--------------------------------------------------------------------------
        | Update data utama
        |--------------------------------------------------------------------------
        */

        $user->name =
            $validated['name'];

        $user->email =
            $validated['email'];

        $user->phone =
            $validated['phone'] ?? null;

        $user->status =
            $validated['status'];


        /*
        |--------------------------------------------------------------------------
        | Update password jika diisi
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $validated['password']
            )
        ) {

            $user->password =
                Hash::make(
                    $validated['password']
                );
        }


        $user->save();


        return redirect()
            ->route(
                'admin.kelola-user'
            )
            ->with(
                'success',
                'Data user berhasil diperbarui.'
            );
    }


    /**
     * Mengaktifkan / menonaktifkan user.
     */
    public function toggleUserStatus(
        User $user
    ) {

        abort_if(
            $user->role !== 'user',
            404
        );


        if (
            $user->status ===
            'active'
        ) {

            $user->status =
                'inactive';

        } else {

            $user->status =
                'active';
        }


        $user->save();


        return redirect()
            ->back()
            ->with(
                'success',
                'Status user berhasil diubah.'
            );
    }


    /**
     * Menghapus user.
     */
    public function destroyUser(
        User $user
    ) {

        abort_if(
            $user->role !== 'user',
            404
        );


        /*
        |--------------------------------------------------------------------------
        | Jangan menghapus akun admin
        |--------------------------------------------------------------------------
        */

        if (
            $user->id ===
            Auth::id()
        ) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Anda tidak dapat menghapus akun yang sedang digunakan.'
                );
        }


        $user->delete();


        return redirect()
            ->route(
                'admin.kelola-user'
            )
            ->with(
                'success',
                'User berhasil dihapus.'
            );
    }
}