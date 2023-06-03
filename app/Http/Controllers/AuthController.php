<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Project;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rules;


class AuthController extends Controller
{
    public function signinAction(Request $request)
    {
        if (auth()->attempt($request->all())) {
            return response($this->getUserInfo(auth()->user()), Response::HTTP_OK);
        }

        return response([
            'message' => 'This User does not exist'
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function signupAction(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'active' => true,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $project = Project::create([
            'name' => $request->name,
        ]);

        $user->projectsUser()->create([
            'project_id' => $project->id,
        ]);

        $user->refresh();

        foreach ($user->projectsUser as $projectUser) {
            $projectUser->groups()->attach(Group::where('name', 'ProjectAdmin')->first()->id);
            return response($this->getUserInfo($user));
        }

        $user->refresh();
        return response($this->getUserInfo($user));
    }

    /**
     * @param $user
     * @return array
     */
    private function getUserInfo($user)
    {
        return [
            'user' => $this->getUserData($user),
            'groups' => $user->getGroupNames(),
            'projects' => $this->getUserProjects($user),
            'access_token' => $user->createToken('authToken', $this->getScopes($user))->accessToken
        ];
    }

    /**
     * @param $user
     * @return array
     */
    private function getUserData($user): array
    {
        return [
            'name' => $user->name,
        ];
    }

    /**
     * @param $user
     * @return array
     */
    private function getScopes($user): array
    {
        if ($user->isSuperUser()) {
            return ['superuser'];
        }

        if ($user->isProjectAdmin()) {
            return ['project_admin'];
        }

        if ($user->isProjectManager()) {
            return ['project_manager'];
        }

        return [];
    }

    /**
     * @param $user
     * @return array
     */
    private function getUserProjects($user)
    {
        $projects = [];
        $projectsUser = $user->projectsUser;

        foreach ($projectsUser as $projectUser) {
            $projects[] = (array_merge($projectUser->project->toArray(), ['group' => array_column($projectUser->groups->toArray(), 'name')]));
        }

        return $projects;
    }
}
