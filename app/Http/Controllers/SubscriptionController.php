<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\ServerUser;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{

    public function store($code)
    {
        $server = Server::where('code', '=', $code)
            ->firstOrFail();

        $check = auth()->user()->subscriptions()->where('code', '=', $code)
            ->exists();
        if ($check) {
            return response()->json([
                'data' => 'you are already joined the server'
            ], 422);
        }
        $server->subscribers()->attach(auth()->id());

        return response()->json([
            'data' => 'joined successfully'
        ]);
    }

    public function destroy($code)
    {
        $server = auth()->user()->subscriptions()
            ->where('code', '=', $code)
            ->firstOrFail();
        auth()->user()->subscriptions()->detach($server->id);
        return response()->json([
            'data' => 'you left the server'
        ]);
    }

    public function index($code, Request $request)
    {
        $server = auth()->user()->subscriptions()
            ->where('code', '=', $code)
            ->firstOrFail();
//        if ($request->has('phone')) {
//            $phone = $request->get('phone');
//            $users = $server->subscribers()
//                ->where('phone', 'like', '%' . $phone . '%')
//                ->get();
//            return response()->json([
//                'data' => $users
//            ]);
//        }
//        if ($request->has('user_id')) {
//            $userId = $request->input('user_id');
//            $users = $server->subscribers()
//                ->where('users.id', '=', $userId)
//                ->get();
//            return response()->json([
//                'data' => $users
//            ]);
//        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $users = $server->subscribers()
                ->where('users.id', '=', $search)
                ->orWhere('users.name', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%')
                ->get();
        } else {
            $users = $server->subscribers;
        }

        return response()->json([
            'data' => $users
        ]);

    }
}
