<?php

use App\Http\Middleware\IsBanned;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\RedirectIfInvitation;
use App\Http\Middleware\RedirectAfterInvitationLogin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'isBanned' => IsBanned::class,
            'isAdmin' => IsAdmin::class,
            'redirect.if.invitation' => RedirectIfInvitation::class,
            'redirect.after.invitation.login' => RedirectAfterInvitationLogin::class,
        ]);
    })->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
