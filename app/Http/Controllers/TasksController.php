<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\CreateTaskRequest;
use Illuminate\Support\Facades\DB;

class TasksController extends Controller
{
    private $queryvar;
    public function __construct()
    {
        $this->queryvar = $this->query();
    }  

    public function get(){
        return response()->json($this->tasks(), 200);
    }
    
    public function update(Request $request){
        foreach ($request->all() as $task){
            $id = $task['id'];
            $order = $task['order'];
            DB::table('tasks')
            ->where(['id'=> $id, ])
            ->update(['order' => $order]);            
        }
        return response("Updated successfrul", 200);
    }

    private function query()
    {
        $userid = $this->userid();
        $tasks = DB::table('projects')
        ->join('tasks', 'projects.id', '=', 'tasks.project_id')
        ->select('projects.id as pid', 'projects.name as pname', 'tasks.id', 'tasks.name', 'tasks.status', 'tasks.order', 'tasks.deadline')
        ->where('projects.user_id', '=', $userid)
        ->orderBy('order')
        ->get();
        
        return $tasks;
    }

    private function projects(){
        // $projects = $this->queryvar->pluck('pname', 'pid');
        $projects = $this->queryvar->unique('pid')->all();
        return $projects;
    }

    private function tasks(){
        $tasks = $this->queryvar;
        $projects = $this->projects();
        $return = collect();

        foreach ($projects as $project) {
            $pid = $project->pid;
            $pname = $project->pname;
            $filtered = $tasks->where('pid', $pid)->values();
            $return->add(['project' => collect(['pid' => $pid, 'pname' => $pname]), 'tasks' => $filtered]);
        }
        return $return->all();
    }

    public function userid(){
        // $user = JWTAuth::parseToken()->authenticate();
        // $userid = $user->id;
        return 1;
    }
}
