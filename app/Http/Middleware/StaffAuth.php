<?php

namespace App\\Http\\Middleware;

use Closure;
use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\DB;

class StaffAuth
{
    public function handle(Request $request, Closure $next)
    {
        $id = $request->session()->get('admin_id');
        if (!$id) return response()->json(['success'=>false,'message'=>'Admin login required.'],401);
        $user = DB::table('admins')->where('id',$id)->first();
        if (!$user || !$user->active) {
            $request->session()->invalidate();
            return response()->json(['success'=>false,'message'=>'Your account is not active. Please contact the admin.'],401);
        }
        $request->attributes->set('staff',$user);
        if ($user->must_change_password && !in_array($request->path(), ['api/admin/change-password','api/admin/logout','api/admin/me'], true)) {
            return response()->json(['success'=>false,'code'=>'PASSWORD_CHANGE_REQUIRED','message'=>'Please set a new password first.'],403);
        }
        return $next($request);
    }
}
