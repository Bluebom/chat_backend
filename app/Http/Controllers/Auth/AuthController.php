<?php

namespace App\Http\Controllers\Auth;


use App\Http\Requests\Auth\AuthRequest;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Laravel\Sanctum\NewAccessToken;

/**
 * Class AuthController
 *
 * @author Franklin Henrique
 * @package App\Http\Controllers
 */
class AuthController
{

    /**
     * Estrutura para criação e edição de usuário.
     *
     * @param array $data
     * @return array
     */
    public function structure(array $data): array
    {
        return [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ];
    }

    /**
     * Registro do usuário
     *
     * @param AuthRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function register(AuthRequest $request)
    {
        /** @var \App\Models\User $user Guarda as informações do usuário recém-criado.  */
        $user = User::create($request->all());

        /** @var NewAccessToken $token Guarda o token de sessão desse usuário. */
        $token = $user->createToken('main')->plainTextToken;

        return response(['user' => $user, 'token' => $token]);
    }


}
