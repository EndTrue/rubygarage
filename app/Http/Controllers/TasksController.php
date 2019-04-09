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
        return response()->json([
            'projects'  => $this->projects(),
            'tasks'     => $this->tasks()
        ]);
    }    
    
    private function query()
    {
        $userid = $this->userid();
        $tasks = DB::table('projects')
        ->join('tasks', 'projects.id', '=', 'tasks.project_id')
        ->select('projects.id as pid', 'projects.name as pname', 'tasks.id', 'tasks.name', 'tasks.status', 'tasks.order', 'tasks.deadline')
        ->where('projects.user_id', '=', $userid)
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
        return $tasks;
    }

    public function userid(){
        $user = JWTAuth::parseToken()->authenticate();
        $userid = $user->id;
        return $userid;
    }


    
}
