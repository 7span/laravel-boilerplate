<?php

test('globals')
    ->expect(['dd', 'dump', 'die'])
    ->not->toBeUsed();

// test('models should be not used in the controllers')
//     ->expect('App\Models')
//     ->not->toBeUsedIn('App\Http\Controllers');

test('env variable should be used in the config folder only')
    ->expect('env')
    ->not->toBeUsed()
    ->ignoring('config');

test('validator class should be not used in controllers')
    ->expect('App\Http\Controllers')
    ->not->toUse('Illuminate\Validation\Validator');

test('mail should be sent via queue only')
    ->expect('Illuminate\Support\Facades\Mail')
    ->toOnlyBeUsedIn('App\Jobs');
