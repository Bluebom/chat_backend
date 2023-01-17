<?php

namespace App\Http\Controllers\Auth;


use App\Http\Requests\AuthUser\AuthLoginRequest;
use App\Http\Requests\AuthUser\AuthRegisterRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
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
     * @var Guard|StatefulGuard
     */
    private $auth;

    public function __construct()
    {
        $this->auth = Auth::guard('web');
    }

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
     * Registro do usuário.
     *
     * @param AuthRegisterRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function register(AuthRegisterRequest $request)
    {
        /** @var User $user Guarda as informações do usuário recém-criado. */
        $user = User::create($this->structure($request->all()));

        /** @var NewAccessToken $token Guarda o token de sessão desse usuário. */
        $token = $user->createToken('main')->plainTextToken;

        return response(['user' => $user, 'token' => $token]);
    }

    /**
     * Acesso do usuário.
     *
     * @param AuthLoginRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function login(AuthLoginRequest $request)
    {
        if (!$this->auth->attempt($request->all(), false))
            return response([
                'error' => 'Dados de usuário e/ou senha incorretos.'
            ], 422);

        /** @var User $user Instância de user */
        $user = $this->auth->user();

        $token = $user->createToken('main')->plainTextToken;

        return response(['user' => $user, 'token' => $token]);
    }

    /**
     * Invalida token de acesso ao sistema.
     *
     * @return Application|ResponseFactory|Response
     */
    public function loggout()
    {
        if(Auth::user()->currentAccessToken()->delete)
            $status = true;

        return response(['status' => $status??false]);
    }

}
