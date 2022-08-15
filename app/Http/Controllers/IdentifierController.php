<?php

namespace App\Http\Controllers;

use App\Constants\ResponseStatus;
use App\Events\IdentityUpdated;
use App\Models\Identifier;
use App\Models\User;
use Illuminate\Http\Request;

class IdentifierController extends Controller
{
    public function index()
    {
        return response()->json(Identifier::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required']
        ]);

        $identifier = Identifier::create($data);

        return response()->json($identifier);
    }

    public function userIdentifiers(Request $request)
    {
        $request->validate([
            'status_sort' => ['in:desc,asc'],
            'sort' => ['in:desc,asc']
        ]);
        $query = User::has('identifiers')->join('identifier_user', 'users.id', '=', 'identifier_user.user_id');
        if ($request->exists('status')) {
            $query = $query->where('identifier_user.status', $request->status);
        }
        if ($request->exists('status_sort')) {
            $query = $query->orderBy('identifier_user.status', $request->status_sort);
        }
        if ($request->exists('sort')) {
            $query = $query->orderBy('identifier_user.updated_at', $request->sort);
        }

        return response()->json($query->filter($request->only(['email']))->select('users.*')->paginate((int)$request->per_page ?? 10));
    }

    public function rejectIdentifier(Request $request, Identifier $identifier)
    {
        $request->validate(['user_id' => ['required', 'exists:users,id']]);
        $user = User::find($request->user_id);
        if ($user->identifiers()->findOrFail($identifier->id)->identity->status != 1)
            abort(ResponseStatus::BAD_REQUEST, 'Cannot reject');
        $identifier->deleteImages($user);
        $user->identifiers()->updateExistingPivot($identifier->id, [
            'status' => 3,
            'images' => json_encode([])
        ]);

        IdentityUpdated::dispatch($user);
        return response()->json($user->load('identifiers'));
    }

    public function confirmIdentifier(Request $request, Identifier $identifier)
    {
        $request->validate(['user_id' => ['required', 'exists:users,id']]);
        $user = User::find($request->user_id);
        if ($user->identifiers()->findOrFail($identifier->id)->identity->status != 1)
            abort(ResponseStatus::BAD_REQUEST, 'Cannot confirm');
        $user->identifiers()->updateExistingPivot($identifier->id, [
            'status' => 2,
            'confirmed_at' => now()
        ]);

        IdentityUpdated::dispatch($user);
        return response()->json($user->load('identifiers'));
    }
}
