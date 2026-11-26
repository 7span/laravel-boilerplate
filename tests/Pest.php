<?php

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Feature tests are bound to the application TestCase so they boot the framework.
| Unit tests stay on PHPUnit's plain TestCase. Add ->use(RefreshDatabase::class)
| here once tests start touching the database.
|
*/

pest()->extend(TestCase::class)->in('Feature');
