{{--
    Password input with a show/hide button.
    Expects: $id, $name, $label, $autocomplete; optional $hint, $dataAttribute, $describedBy.
--}}
@php
    $errorId = $id.'-error';
    $describedBy = trim(($describedBy ?? '').' '.($errors->has($name) ? $errorId : ''));
@endphp

<div class="au-field">
    <label for="{{ $id }}">{{ $label }}</label>
    @isset($hint)
        <span class="au-hint" id="{{ $id }}-hint">{{ $hint }}</span>
    @endisset
    <div class="au-input-wrap">
        <input
            type="password"
            id="{{ $id }}"
            name="{{ $name }}"
            autocomplete="{{ $autocomplete }}"
            required
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @error($name) aria-invalid="true" @enderror
            {{ $dataAttribute ?? '' }}
        >
        <button type="button" class="au-reveal" data-reveal aria-controls="{{ $id }}" aria-pressed="false" aria-label="Tampilkan password">
            <svg data-eye-open width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z" /><circle cx="12" cy="12" r="3" /></svg>
            <svg data-eye-closed hidden width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3l18 18M10.6 5.1A10.6 10.6 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3 3.8M6.6 6.6C3.8 8.3 2 12 2 12s3.6 7 10 7a9.8 9.8 0 0 0 5.4-1.6M9.9 9.9a3 3 0 0 0 4.2 4.2" /></svg>
        </button>
    </div>
    @error($name)
        <p class="au-field-error" id="{{ $errorId }}">{{ $message }}</p>
    @enderror
</div>
