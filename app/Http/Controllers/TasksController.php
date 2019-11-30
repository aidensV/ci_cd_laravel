<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        $editableTask = null;

        if ( request('id') &&  request('action') == 'edit'){
            $editableTask = Task::find(request('id'));
        }
        foreach ($tasks as $key => $value) {
          if ($value->created_at >= \Carbon\Carbon::now()) {
            $date_id = $value->created_at;
          }else{
            $boringLanguage = 'id';
            $translator = \Carbon\Translator::get($boringLanguage);
            $date_id = \Carbon\Carbon::createFromTimeStamp(strtotime($value->created_at))->locale($boringLanguage)->diffForHumans();
          }
          // dd($value->created_at);
          // dd( \Carbon\Carbon::now());

          dd($date_id);

        }
        return view('tasks.index', compact('tasks','editableTask'));
    }
    public function store()
    {
        request()->validate([
            'name'        => 'required|max:255',
            'description' => 'required|max:255',
        ]);

        Task::create(request()->only('name', 'description'));
        return back();
    }
    public function update(Task $task)
    {
        $taskData = request()->validate([
            'name'          => 'required|max:255',
            'description'   => 'required|max:255',
        ]);
        $task->update($taskData);

        return redirect('/tasks');
    }
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect('/tasks');
    }
}
