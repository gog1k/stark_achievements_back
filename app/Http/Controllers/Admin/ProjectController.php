<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class ProjectController extends Controller
{
    public function getListAction(): Response
    {
        if (auth()->user()->isSuperUser()) {
            $response = Project
                ::withCount('users');
        } else {
            $response = Project
                ::whereIn('id', auth()->user()->projectsAllowedForAdministrationIds());
        }

        $response = $response->paginate(10);

        return response([
            'items' => $response->items(),
            'pagination' => [
                'currentPage' => $response->currentPage(),
                'perPage' => $response->perPage(),
                'total' => $response->total(),
            ]
        ]);
    }

    /**
     * @throws \Exception
     */
    public function getApiKeyAction(Request $request): Response
    {
        $request->validate([
            'project_id' => 'required|integer',
            'login' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $user = User::where([
            'name' => $request->login
        ])->firstOrFail();

        if (auth()->user()->id !== $user->id) {
            throw new \Exception('Incorrect user');
        }

        if (!Hash::check($request->password, $user->password)) {
            throw new \Exception('Params incorrect');
        }

        $projectUser = $user->projectsUser()->where(['project_id' => $request->project_id])->firstOrFail();

        $projectUser->project->save();

        return response($projectUser->project->api_key);
    }

    public function allowListAction(): Response
    {
        if (auth()->user()->isSuperUser()) {
            $response = Project::query();
        } else {
            $response = Project
                ::whereIn('id', auth()->user()->projectsAllowedForAdministrationIds());
        }

        $response = $response->get()->pluck('name', 'id');

        return response($response);
    }

    public function getAction(int $id): Response
    {
        return response(
            Project::where(['id' => $id])->first()
        );
    }

    public function createAction(Request $request): Response
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'callback_url' => 'url',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'callback_url' => $request->callback_url,
        ]);

        return response($project);
    }

    public function updateAction(Request $request): Response
    {
        $request->validate([
            'id' => 'required|integer|exists:projects,id',
            'name' => 'string|max:255',
            'callback_url' => 'url',
        ]);

        $project = Project::findOrFail($request->id);

        if ($request->name) {
            $project->name = $request->name;
        }

        if ($request->callback_url) {
            $project->callback_url = $request->callback_url;
        }

        $project->save();

        return response($project);
    }
}
