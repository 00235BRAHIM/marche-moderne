<?php
use Illuminate\Foundation\Application; use Illuminate\Foundation\Configuration\Middleware; use Illuminate\Foundation\Configuration\Exceptions;
return Application::configure(basePath:dirname(__DIR__))->withRouting(web:__DIR__.'/../routes/web.php',commands:__DIR__.'/../routes/console.php',health:'/up')->withMiddleware(function(Middleware $m){$m->alias(['role'=>App\Http\Middleware\RoleMiddleware::class,'vendor.access'=>App\Http\Middleware\VendorAccessMiddleware::class,'super_admin'=>App\Http\Middleware\SuperAdminMiddleware::class]);})->withExceptions(fn(Exceptions $e)=>null)->create();
