<?php

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Resources\Json\JsonResource;

arch('debug helpers and unsafe PHP functions never reach production')
    ->preset()
    ->php();

arch('insecure functions such as eval, md5 and rand are not used')
    ->preset()
    ->security();

arch('config is read through config() so cached config keeps working')
    ->expect('env')
    ->not->toBeUsed();

arch('controllers share the base controller and the Controller suffix')
    ->expect('App\Http\Controllers')
    ->classes()
    ->toExtend(Controller::class)
    ->toHaveSuffix('Controller')
    ->ignoring(Controller::class);

arch('API controllers return responses through the ApiResponser trait')
    ->expect('App\Http\Controllers\Api')
    ->toUseTrait(ApiResponser::class);

arch('controllers stay thin and leave queries to services')
    ->expect('App\Http\Controllers')
    ->not->toUse(DB::class);

arch('input is validated by FormRequests, not by the controller')
    ->expect('App\Http\Requests')
    ->classes()
    ->toExtend(FormRequest::class)
    ->toHaveSuffix('Request')
    ->toHaveMethod('rules');

arch('services hold business logic and stay independent of the HTTP request')
    ->expect('App\Services')
    ->classes()
    ->toHaveSuffix('Service')
    ->not->toUse(Request::class);

arch('models are Eloquent models that define casts in the casts() method')
    ->expect('App\Models')
    ->classes()
    ->toExtend(Model::class)
    ->toHaveMethod('casts')
    ->not->toHaveSuffix('Model');

arch('enums are string backed so the stored value is readable')
    ->expect('App\Enums')
    ->toBeStringBackedEnums();

arch('API resources carry a SchemaName so Scramble documents them')
    ->expect('App\Http\Resources')
    ->classes()
    ->toExtend(JsonResource::class)
    ->toHaveAttribute(SchemaName::class)
    ->toHaveMethod('toArray');

arch('notifications are queued so requests are not slowed down by sending')
    ->expect('App\Notifications')
    ->classes()
    ->toExtend(Notification::class)
    ->toImplement(ShouldQueue::class);

arch('mail is sent through notifications, never directly from controllers or services')
    ->expect(Mail::class)
    ->not->toBeUsedIn(['App\Http', 'App\Services']);

arch('traits folder only contains traits')
    ->expect('App\Traits')
    ->toBeTraits();

arch('custom exceptions are throwable')
    ->expect('App\Exceptions')
    ->toImplement(Throwable::class);

arch('middleware implement handle()')
    ->expect('App\Http\Middleware')
    ->classes()
    ->toHaveMethod('handle');

arch('artisan commands extend the base command and implement handle()')
    ->expect('App\Console\Commands')
    ->classes()
    ->toExtend(Command::class)
    ->toHaveMethod('handle');
