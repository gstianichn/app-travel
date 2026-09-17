<?php

test('login page route loads successfully', function () {
    $this->get('/login')->assertOk();
});

test('password reset request route is registered', function () {
    $this->assertNotNull(route('password.request'));
});
