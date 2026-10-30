@extends('layouts.admin')

@section('title', 'Edit User')

@section('page-title', 'Edit User')

@section('page-subtitle', 'Perbarui informasi pengguna yang terdaftar pada sistem.')

@section('content')

<style>
    .form-page {
        max-width: 850px;
        margin: 0 auto;
    }

    .form-card {
        background: white;

        border: 1px solid #e2eae6;
        border-radius: 16px;

        box-shadow: 0 5px 20px rgba(0,0,0,.03);

        overflow: hidden;
    }

    .form-card-header {
        padding: 22px 25px;

        border-bottom: 1px solid #edf1ef;
    }

    .form-card-header h1 {
        margin: 0 0 5px;

        font-size: 18px;
        color: #17221d;
    }

    .form-card-header p {
        margin: 0;

        color: #89938e;
        font-size: 12px;
    }

    .form-body {
        padding: 25px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .form-group.full {
        grid-column: 1 / -1;
    }

    .form-label {
        color: #34423b;
        font-size: 12px;
        font-weight: 600;
    }

    .required {
        color: #dc3545;
    }

    .form-input,
    .form-select {
        width: 100%;

        padding: 11px 13px;

        border: 1px solid #dce5e1;
        border-radius: 9px;

        background: white;

        color: #26332d;

        font-family: inherit;
        font-size: 13px;

        outline: none;

        transition: .2s ease;
    }

    .form-input:focus,
    .form-select:focus {
        border-color: #087f55;

        box-shadow:
            0 0 0 3px rgba(8,127,85,.08);
    }

    .form-help {
        color: #98a39e;
        font-size: 10px;
    }

    .field-error {
        color: #d33b49;
        font-size: 10px;
    }

    .password-section {
        grid-column: 1 / -1;

        margin-top: 4px;
        padding-top: 20px;

        border-top: 1px solid #edf1ef;
    }

    .password-section-title {
        margin-bottom: 15px;

        color: #35423c;

        font-size: 13px;
        font-weight: 700;
    }

    .form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;

        gap: 10px;

        margin-top: 25px;
        padding-top: 20px;

        border-top: 1px solid #edf1ef;
    }

    .btn-cancel,
    .btn-save {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        padding: 11px 18px;

        border-radius: 9px;

        font-size: 12px;
        font-weight: 600;

        text-decoration: none;

        cursor: pointer;
    }

    .btn-cancel {
        border: 1px solid #dce5e1;

        background: white;
        color: #68766f;
    }

    .btn-save {
        border: none;

        background: #087f55;
        color: white;
    }

    .btn-save:hover {
        background: #066b47;
    }

    @media (max-width: 650px) {

        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full,
        .password-section {
            grid-column: auto;
        }

        .form-body {
            padding: 20px;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .btn-cancel,
        .btn-save {
            width: 100%;
        }

    }
</style>


<div class="form-page">

    <div class="form-card">


        <div class="form-card-header">

            <h1>
                Edit Data User
            </h1>

            <p>
                Perbarui informasi pengguna sesuai kebutuhan.
            </p>

        </div>


        <div class="form-body">

            <form
                action="{{ route('dashboard.admin.kelola-user.update', $user->id) }}"
                method="POST"
            >

                @csrf

                @method('PUT')


                <div class="form-grid">


                    {{-- NAMA --}}

                    <div class="form-group full">

                        <label class="form-label">
                            Nama Lengkap
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            class="form-input"
                            required
                        >

                        @error('name')
                            <span class="field-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- EMAIL --}}

                    <div class="form-group">

                        <label class="form-label">
                            Email
                            <span class="required">*</span>
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            class="form-input"
                            required
                        >

                        @error('email')
                            <span class="field-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- NOMOR HP --}}

                    <div class="form-group">

                        <label class="form-label">
                            Nomor HP
                        </label>

                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone', $user->phone) }}"
                            class="form-input"
                            placeholder="Contoh: 081234567890"
                        >

                        <span class="form-help">
                            Kosongkan apabila nomor HP belum tersedia.
                        </span>

                        @error('phone')
                            <span class="field-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- ROLE --}}

                    <div class="form-group">

                        <label class="form-label">
                            Role
                            <span class="required">*</span>
                        </label>

                        <select
                            name="role"
                            class="form-select"
                            required
                        >

                            <option
                                value="admin"
                                {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}
                            >
                                Admin Utama
                            </option>

                            <option
                                value="staff"
                                {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}
                            >
                                Staff
                            </option>

                            <option
                                value="cs"
                                {{ old('role', $user->role) == 'cs' ? 'selected' : '' }}
                            >
                                Customer Service
                            </option>

                            <option
                                value="driver"
                                {{ old('role', $user->role) == 'driver' ? 'selected' : '' }}
                            >
                                Driver
                            </option>

                            <option
                                value="user"
                                {{ old('role', $user->role) == 'user' ? 'selected' : '' }}
                            >
                                User
                            </option>

                        </select>

                        @error('role')
                            <span class="field-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- STATUS --}}

                    <div class="form-group">

                        <label class="form-label">
                            Status Akun
                            <span class="required">*</span>
                        </label>

                        <select
                            name="status"
                            class="form-select"
                            required
                        >

                            <option
                                value="active"
                                {{ old('status', $user->status ?? 'active') === 'active' ? 'selected' : '' }}
                            >
                                Aktif
                            </option>

                            <option
                                value="inactive"
                                {{ old('status', $user->status ?? 'active') === 'inactive' ? 'selected' : '' }}
                            >
                                Nonaktif
                            </option>

                        </select>

                        @error('status')
                            <span class="field-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- PASSWORD --}}

                    <div class="password-section">

                        <div class="password-section-title">
                            Ubah Password
                        </div>


                        <div class="form-grid">


                            <div class="form-group">

                                <label class="form-label">
                                    Password Baru
                                </label>

                                <input
                                    type="password"
                                    name="password"
                                    class="form-input"
                                    placeholder="Kosongkan jika tidak diubah"
                                >

                                <span class="form-help">
                                    Kosongkan apabila password tidak ingin diubah.
                                </span>

                                @error('password')
                                    <span class="field-error">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>


                            <div class="form-group">

                                <label class="form-label">
                                    Konfirmasi Password
                                </label>

                                <input
                                    type="password"
                                    name="password_confirmation"
                                    class="form-input"
                                    placeholder="Ulangi password baru"
                                >

                            </div>


                        </div>

                    </div>


                </div>


                {{-- ACTION --}}

                <div class="form-actions">

                    <a
                        href="{{ route('dashboard.admin.kelola-user.index') }}"
                        class="btn-cancel"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="btn-save"
                    >
                        Simpan Perubahan
                    </button>

                </div>


            </form>

        </div>

    </div>

</div>

@endsection