<?php

namespace App\Modules\Core\Middleware;

use App\Modules\Core\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sau auth:sanctum: đồng bộ user sang guard web (Spatie dùng chung guard web cho API),
 * và đặt organization_id cho Spatie Permission (tính năng teams).
 */
class SetPermissionsTeamId
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            Auth::guard('web')->setUser($user);
            $organization = Organization::where('slug', 'default')->first();
            if ($organization) {
                setPermissionsTeamId($organization->id);
            }
        }

        return $next($request);
    }
}
