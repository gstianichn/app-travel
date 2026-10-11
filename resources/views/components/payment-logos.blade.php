{{--
    Logos of the payment channels Midtrans handles for TravelKu, as white tiles.
    Files live in public/images/payments. Optional: $only = ['bca', ...] to show a subset, $class for the list.
--}}
@php
    $paymentLogos = [
        'bca' => 'BCA',
        'mandiri' => 'Mandiri',
        'bni' => 'BNI',
        'bri' => 'BRI',
        'bsi' => 'BSI',
        'seabank' => 'SeaBank',
        'qris' => 'QRIS',
        'gopay' => 'GoPay',
        'shopeepay' => 'ShopeePay',
    ];

    if (! empty($only)) {
        $paymentLogos = array_intersect_key($paymentLogos, array_flip($only));
    }
@endphp

<ul class="{{ $class ?? 'pay-logos' }}">
    @foreach ($paymentLogos as $code => $label)
        <li>
            <img src="{{ asset('images/payments/'.$code.'.svg') }}" alt="{{ $label }}" loading="lazy" decoding="async">
        </li>
    @endforeach
</ul>
