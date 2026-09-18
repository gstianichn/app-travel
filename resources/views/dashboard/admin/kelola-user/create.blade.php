@extends('layouts.admin')

@section('title', 'Tambah User')

@section('page-title', 'Tambah User')

@section('page-subtitle', 'Tambahkan pengguna baru ke dalam sistem Travel.')

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

        .form-group.full {
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
                Tambah User Baru
            </h1>

            <p>
                Isi informasi berikut untuk menambahkan pengguna baru.
            </p>

        </div>


        <div class="form-body">

            <form
                action="{{ route('dashboard.admin.kelola-user.store') }}"
                method="POST"
            >

                @csrf


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
                            value="{{ old('name') }}"
                            class="form-input"
                            placeholder="Masukkan nama lengkap"
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
                            value="{{ old('email') }}"
                            class="form-input"
                            placeholder="contoh@email.com"
                            required
                        >

                        @error('email')
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

                            <option value="">
                                Pilih Role
                            </option>

                            <option
                                value="admin"
                                {{ old('role') == 'admin' ? 'selected' : '' }}
                            >
                                Admin Utama
                            </option>

                            <option
                                value="staff"
                                {{ old('role') == 'staff' ? 'selected' : '' }}
                            >
                                Staff
                            </option>

                            <option
                                value="cs"
                                {{ old('role') == 'cs' ? 'selected' : '' }}
                            >
                                Customer Service
                            </option>

                            <option
                                value="driver"
                                {{ old('role') == 'driver' ? 'selected' : '' }}
                            >
                                Driver
                            </option>

                            <option
                                value="user"
                                {{ old('role') == 'user' ? 'selected' : '' }}
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


                    {{-- PASSWORD --}}

                    <div class="form-group">

                        <label class="form-label">
                            Password
                            <span class="required">*</span>
                        </label>

                        <input
                            type="password"
                            name="password"
                            class="form-input"
                            placeholder="Masukkan password"
                            required
                        >

                        <span class="form-help">
                            Minimal 8 karakter.
                        </span>

                        @error('password')
                            <span class="field-error">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- KONFIRMASI PASSWORD --}}

                    <div class="form-group">

                        <label class="form-label">
                            Konfirmasi Password
                            <span class="required">*</span>
                        </label>

                        <input
                            type="password"
                            name="password_confirmation"
                            class="form-input"
                            placeholder="Ulangi password"
                            required
                        >

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
                        Simpan User
                    </button>

                </div>


            </form>

        </div>

    </div>

</div>

@endsection