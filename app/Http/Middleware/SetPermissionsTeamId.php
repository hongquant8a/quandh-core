<?php

namespace App\Http\Middleware;

use App\Modules\Core\Models\Team;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sau auth:sanctum: đồng bộ user sang guard web (Spatie dùng chung guard web cho API),
 * và đặt team_id cho Spatie Permission (tính năng teams).
 */
class SetPermissionsTeamId
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            Auth::guard('web')->setUser($user);
            $team = Team::where('slug', 'default')->first();
            if ($team) {
                setPermissionsTeamId($team->id);
            }
        }

        return $next($request);
    }
}
