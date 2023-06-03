<?php

namespace App\Http\Controllers;

use App\Models\PartnerUser;
use App\Models\User;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PartnerUserController extends BaseController
{
    /**
     * @throws \Exception
     */
    public function pageAction($user_uid)
    {
        if (empty($userData = Redis::get($user_uid))) {
            throw new \Exception('user not found');
        }

        $userData = json_decode($userData, true);

        Validator::make($userData, [
            'id' => 'required|integer|exists:users,id',
            'access' => 'required|string|in:view,write',
        ])->validate();

        $user = PartnerUser::where(['id' => $userData['id']])->first();

        return response($user->getRoom($userData['access']));
    }
}
