@extends('layouts.auth')

@section('title', 'Masuk petugas')

@section('variant', 'staff')

@php
    $staffRoles = [
        'admin_utama' => ['Admin utama', 'Rute, armada, staf, dan laporan'],
        'staff' => ['Staf pool', 'Validasi tiket dan boarding'],
        'cs' => ['Customer service', 'Chat dan bantuan penumpang'],
        'driver' => ['Driver', 'Jadwal, penumpang, status jalan'],
    ];
@endphp

@section('content')
    <h1 class="au-title">Masuk sebagai petugas</h1>
    <p class="au-lead">Untuk admin, staf pool, customer service, dan driver. Kode akses dibuat oleh admin utama.</p>

    <form method="POST" action="{{ route('login.process') }}" class="au-form" data-auth-form>
        @csrf

        <fieldset class="au-roles" @error('role') aria-invalid="true" aria-describedby="role-error" @enderror>
            <legend class="au-legend">Masuk sebagai</legend>
            <div class="au-role-grid">
                @foreach ($staffRoles as $value => [$roleName, $roleDescription])
                    <label class="au-role">
                        <input type="radio" name="role" value="{{ $value }}" required @checked(old('role') === $value)>
                        <b>{{ $roleName }}</b>
                        <span>{{ $roleDescription }}</span>
                    </label>
                @endforeach
            </div>
            @error('role')
                <p class="au-field-error" id="role-error">{{ $message }}</p>
            @enderror
        </fieldset>

        <div class="au-field">
            <label for="email">Email kerja</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="nama@travelku.test"
                autocomplete="username"
                required
                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
            >
            @error('email')
                <p class="au-field-error" id="email-error">{{ $message }}</p>
            @enderror
        </div>

        @include('auth.partials.password-field', [
            'id' => 'password',
            'name' => 'password',
            'label' => 'Password',
            'autocomplete' => 'current-password',
        ])

        @include('auth.partials.password-field', [
            'id' => 'access_code',
            'name' => 'access_code',
            'label' => 'Kode akses',
            'autocomplete' => 'off',
        ])

        <label class="au-check">
            <input type="checkbox" name="remember" @checked(old('remember'))>
            Ingat saya di perangkat ini
        </label>

        <button type="submit" class="au-btn" data-label="Masuk" data-busy-label="Memeriksa akses...">
            <span class="au-btn-spinner" aria-hidden="true"></span>
            <span data-label>Masuk</span>
        </button>
    </form>

    <div class="au-switch">
        <p>Lupa password atau kode akses? Hubungi admin utama.</p>
    </div>
@endsection
