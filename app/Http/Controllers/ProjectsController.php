<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\Task;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProjectsController extends Controller
{
    public function addProject() {
        $project = new Project;
        $project->name = 'New project';
        $project->user_id = $this->userid();
        $project->save();

        $data = collect([
            'project' => collect([
                'pid'   => $project->id,
                'pname' => $project->name])
            ]);

        return response()->json($data, 200);
    }

    public function delProject(Request $request) {
        $where = [
            'project_id' => $request['pid'],
            'user_id'   => $this->userid()
        ];
        $where2 = [
            'id' => $request['pid'],
            'user_id'   => $this->userid()
        ];

        Task::where($where)->delete();
        Project::where($where2)->delete();
        $data = collect($request);

        return response()->json($data, 200);
    }

    public function editProject(Request $request) {
        $where = [
            'id' => $request['pid'],
            'user_id'   => $this->userid()
        ];
        $project = Project::where($where)->firstOrFail();
        $project->name = $request['pname'];
        $project->save();

        return response()->json($project->name, 200);
    }

    public function userid(){
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['User Not Found'], 401);
        };
        
        return $user->id;
    }
}
