<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::join('organization','organization.id','=','users.org_id')
            ->select('users.id', 'users.name', 'users.email','organization.org_name', 'users.is_active')->get();
            return DataTables::of($data)
                ->make(true);
        }
        return view('backend.users.index');
    }
    public function toggleStatus($id, $status)
    {
        $user = User::findOrFail($id);
        $user->is_active = $status;
        $user->save();

        return response()->json(['success' => true, 'message' => 'User status updated successfully.']);
    }
}
