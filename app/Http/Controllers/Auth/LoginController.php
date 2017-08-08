<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\FetchUserThreads;
use App\Repositories\UserRepository;
use App\User;
use Google_Service_Gmail;
use Google_Service_Plus;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

/**
 */
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    protected $userRepository;
    protected $request;

    /**
     * Create a new controller instance.
     * @param UserRepository $userRepository
     * @param Request $request
     * @param Guard $auth
     */
    public function __construct(UserRepository $userRepository, Request $request, Guard $auth)
    {
        $this->auth = $auth;
        $this->request = $request;
        $this->userRepository = $userRepository;
        $this->middleware('guest')->except('logout');
    }

    public function login()
    {
        return view('auth.login');
    }


    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @param $service
     * @return Response
     */
    public function redirectToProvider($service)
    {
        if (!$this->isValidService($service)) {
            return redirect(null, 404);
        }
        return Socialite::driver($service)
            ->setScopes([
                Google_Service_Plus::USERINFO_PROFILE,
                Google_Service_Plus::USERINFO_EMAIL,
                Google_Service_Gmail::GMAIL_READONLY
            ])->redirect();
    }

    private function isValidService($service)
    {
        return in_array($service, ['google']);
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @param User $authUser
     * @param Request $request
     * @param $service
     * @return Response
     */
    public function handleProviderCallback(User $authUser, Request $request, $service)
    {
        if (!$this->isValidService($service)) {
            return redirect(null, 404);
        }
        $serviceUser = Socialite::driver($service)->user();

        if (is_null($serviceUser)) {
            return redirect(null, 404);
        }

        $dbUser = $this->userRepository->findByEmailOrCreate($serviceUser);

        $accessToken = $dbUser->setToken($serviceUser->token, $serviceUser->expiresIn);

        $this->auth->login($dbUser, true);

        dispatch(new FetchUserThreads($dbUser->id, $accessToken->getOriginal()));

        return $this->sendLoginResponse($request);
    }

    public function redirectTo()
    {
        return "/filters";
    }

    protected function getCode()
    {
        return $this->request->input('code');
    }

}
