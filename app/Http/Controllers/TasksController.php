<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use App\Project;
use App\Task;
use Illuminate\Support\Carbon;

class TasksController extends Controller
{
    public function get()
    {
        return response()->json($this->tasks(), 200);
    }
    
    public function addTask(Request $request) {
        $task = new Task;
        $task->name = $request['name'];
        $task->user_id = $this->userid();
        $task->project_id = $request['pid'];
        $task->save();

        $data = [
            
        ];
        $data = collect([
            'tasks' => collect([
                'pid'   => $request['pid'],
                'pname' => $request['pname'],
                'id'    => $task->id,
                'name'  => $task->name,
                'status'=> $task->status,
                'order' => $task->order,
                'deadline'=> $task->deadline])
            ]);

        return response()->json($data, 200);
    }

    public function editTask(Request $request)
    {
        if ($request['date'] != null) {
            $date = Carbon::parse($request['date'])->toDateTimeString();
        } 
        else {
            $date = null;
        }
        $where = [
            'id' => $request['id'],
            'user_id'   => $this->userid()
        ];
        $task = Task::where($where)->firstOrFail();

        $task->name = $request['name'];
        $task->deadline = $date;
        $task->save();
        
        $data = collect([
            'pid' => $task->project_id,
            'id' => $task->id,
            'name' => $task->name,
            'date' => $task->deadline
        ]);
        return response()->json($data, 200);
    }
    public function delTask(Request $request) {
        $where = [
            'id' => $request['id'],
            'user_id'   => $this->userid()
        ];

        Task::where($where)->delete();
       
        return response()->json($request['id'], 200);
    }
    public function chkTask(Request $request){
        $where = [
            'id' => $request['pid'],
            'user_id'   => $this->userid()
        ];
        $task = Task::where($where)->firstOrFail();
        $task->status = $request['status'];
        $task->save();
        return response()->json(['Successfull!'], 200);
    }

    public function update(Request $request){
        foreach ($request->all() as $task){
            $id = $task['id'];
            $order = $task['order'];
            DB::table('tasks')
            ->where(['id'=> $id, 'user_id' => $this->userid()])
            ->update(['order' => $order]);            
        }
        return response("Updated successfull", 200);
    }

    private function projects()
    {
        $projects = Project::where('user_id', $this->userid())->get();
        return $projects;
    }

    private function tasks()
    {
        $tasks = Task::where('user_id', $this->userid())->orderBy('order')->get();
        $projects = $this->projects();
        $return = collect();

        foreach ($projects as $project) {
            $pid = $project->id;
            $pname = $project->name;
            $filtered = $tasks->where('project_id', $pid)->values();
            $return->add(['project' => collect(['pid' => $pid, 'pname' => $pname]), 'tasks' => $filtered]);
        }
        return $return->all();
    }

    public function userid(){
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['User Not Found'], 401);
        }
        $userId = $user->id;
        return $userId;
    }
}
