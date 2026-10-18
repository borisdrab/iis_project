<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Exists;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $id=Auth::id();
        $table=DB::table($role);
        if(!$table->where('id',$id)->exists())
            {
                abort(403);
            }
        return $next($request);
    }
}
