<?php

test('the application returns a successful response', function (): void {
    $this->get('/')->assertStatus(200);
});
