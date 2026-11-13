<x-mail::message>
# Permintaan Atur Ulang Password

Halo **{{ $name }}**,

Kami menerima permintaan untuk mengatur ulang password akun TravelKu yang terhubung dengan email **{{ $email }}**.

Gunakan kode verifikasi (OTP) berikut untuk melanjutkan proses reset password:

<x-mail::panel>
<div style="text-align: center; font-size: 32px; font-weight: 800; letter-spacing: 6px; color: #0C7950; font-family: monospace;">
{{ $otp }}
</div>
</x-mail::panel>

> [!WARNING]
> **Penting**: Kode ini hanya berlaku selama **{{ $expiresInMinutes }} menit**. Jangan membagikan kode verifikasi ini kepada siapa pun, termasuk pihak yang mengatasnamakan TravelKu.

Jika Anda tidak pernah meminta untuk mengatur ulang kata sandi, abaikan email ini. Akun Anda tetap aman dan kata sandi Anda tidak akan berubah.

Salam hangat,<br>
**{{ config('app.name', 'TravelKu') }}**
</x-mail::message>

