<?php

use Illuminate\Support\Facades\Route;

function route_class(): string
{
    return str_replace('.', '-', Route::currentRouteName());
}
