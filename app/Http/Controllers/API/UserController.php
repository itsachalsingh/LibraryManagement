<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    //

    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('superadmin')) {
            return response()->json(User::with('books')->get()); // Superadmin can view all books
        } elseif ($user->hasRole('admin')) {
            return response()->json(User::with('books')->get()); // Admin can view their own books
        } elseif ($user->hasRole('user')) {

            return response()->json('Only Super Admin and Admin Can all the User List'); // User can view their own books
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $adminuser = auth()->user();

        if ( !$adminuser->hasRole('superadmin') ) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user->update($request->only(['title', 'author', 'description', 'ISBN', 'published_year']));

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $adminuser = auth()->user();

        if ( !$adminuser->hasRole('superadmin') ) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
