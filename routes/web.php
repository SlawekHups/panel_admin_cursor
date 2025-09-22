<?php

use App\Http\Controllers\Auth\AcceptInvitationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Invitation routes
Route::get('/invite/accept/{token}', [AcceptInvitationController::class, 'show'])
    ->name('invite.accept');
Route::post('/invite/accept/{token}', [AcceptInvitationController::class, 'store']);
