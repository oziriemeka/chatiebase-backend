<?php

namespace App\Http\Middleware;

use App\Helpers\ErrorStatus;
use App\Models\OrganizationSettings;
use App\Models\User;
use App\Models\UserOrganization;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRegistrationIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(UserOrganization::where('user_id', auth()->user()->id)->exists()){
            return $next($request);
        } else {
            if(auth()->user()->was_invited == "1"){
                return $next($request);
            } else {
               return response()->json([
                    ErrorStatus::PERMISSION_ERROR => "registration is incomplete"
                ], 403);
            }
        }
    }
}
