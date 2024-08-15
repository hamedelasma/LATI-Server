<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserTask;
use Illuminate\Http\Request;

class UserTaskController extends Controller
{

    public function store(Request $request)
    {
        $inputs = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'task_id' => ['required', 'exists:tasks,id']
        ]);

        $server = auth()->user()->servers()->firstOrFail();

        if (!$server->subscribers()->where('users.id', '=', $inputs['user_id'])->exists()) {
            return response()->json([
                'data' => 'user not found on the server'
            ]);
        }
        if (!$server->tasks()->where('tasks.id', '=', $inputs['task_id'])->exists()) {
            return response()->json([
                'data' => 'task not found on the server'
            ]);
        }
        User::find($inputs['user_id'])->tasks()->attach($inputs['task_id']);

        return response()->json([
            'data' => 'task assigned successfully'
        ]);

    }

    public function destroy(Request $request)
    {
        $inputs = $request->validate([
            'user_id' => ['required'],
            'task_id' => ['required']
        ]);

        $server = auth()->user()->servers()->firstOrFail();

        $server->tasks()->findOrFail($inputs['task_id'])
            ->users()->findOrFail($inputs['user_id'])
            ->tasks()->detach($inputs['task_id']);

//        UserTask::where('user_id', '=', $inputs['user_id'])
//            ->where('task_id', '=', $inputs['task_id'])
//            ->firstOrFail()->delete();

        return response()->json([
            'data' => 'task revoked for this user'
        ]);

    }
}
