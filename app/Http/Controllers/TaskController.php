<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    public function store(Request $request, $code)
    {
        $inputs = $request->validate([
            'name' => ['required', 'string'],
            'description' => ['max:1024'],
            'before_date' => ['date'],
            'priority' => ['required', 'integer', 'min:1', 'max:5'],
            'status' => ['string', 'in:Not-Started,In-Progress,Completed,Cancelled']
        ]);

        auth()->user()
            ->subscriptions()->where('code', '=', $code)
            ->firstOrFail()->tasks()->create($inputs);

        return response()->json([
            'data' => 'task created successfully'
        ]);
    }

    public function index($code)
    {
        $tasks = auth()->user()->subscriptions()
            ->where('code', '=', $code)
            ->firstOrFail()->tasks()->with('server')->get();

        return response()->json([
            'data' => $tasks
        ]);
    }

    public function show($id)
    {
        $task = auth()->user()
            ->servers()->firstOrFail()
            ->tasks()->findOrFail($id);
        return response()->json(['data' => $task]);
    }

    public function update(Request $request, $id)
    {
        $inputs = $request->validate([
            'name' => ['string'],
            'description' => ['max:1024'],
            'before_date' => ['date'],
            'priority' => ['integer', 'min:1', 'max:5'],
            'status' => ['string', 'in:Not-Started,In-Progress,Completed,Cancelled']
        ]);

        $task = auth()->user()
            ->servers()->firstOrFail()
            ->tasks()->findOrFail($id);

        $task->update($inputs);

        return response()->json([
            'data' => 'updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $task = auth()->user()
            ->servers()->firstOrFail()
            ->tasks()->findOrFail($id);


        $task->delete();


        return response()->json([
            'data' => 'task deleted successfully'
        ]);
    }
}
