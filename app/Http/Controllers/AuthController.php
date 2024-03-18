<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Mail\ActiveMail;
use App\Mail\CodeMail;
use App\Models\Log as ModelsLog;
use App\Models\User;
use App\Rules\EmailRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $mongo;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register','activate']]);
        $this->mongo = new ModelsLog();
    }

    public function register(Request $request){
       
       $validate = Validator::make($request->all(),[
            'name'  =>  'required|max:30',
            'email' =>  ['required','email','unique:users',new EmailRule],
            'password'  =>  'required|min:8|confirmed'
       ]);

       if($validate->fails()){
            return response()->json(['errors'=>$validate->errors()],422);
       }

       $code = Crypt::encrypt(rand(100000,999999));
        $user=User::create([
            'name'  =>  $request->name,
            'email' =>  $request->email,
            'password'   =>  Hash::make($request->password),
            'code'  =>  $code,
            'rol_id'    =>  2
        ]);
        $signed_route = URL::temporarySignedRoute(
            'activate',
            now()->addMinutes(15),
            ['user'=>$user->id]
        );
        Mail::to($user->email)->send(new ActiveMail($signed_route));

        $logdata = [
            'msg'   =>  'Usuario registrado correctamente',
            'user_id'  =>  $user->id,
            'verbo' =>  $request->method(),
            'ruta' =>   $request->url(),
            'data'  =>  $request->except(['password','password_confirmation']),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];

        Log::info('Usuario registrado correctamente',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json([
            'msg'   =>  "Registro exitoso, mandamos un correo para activar tu cuenta",
            'data'  =>  $user
        ],201);
    }

    public function activate(User $user,Request $request){
        if(!$user){
         
            return response()->json("La cuenta no existe");
        }
        $user->status=true;
        $user->save();
        $logdata=[
            'msg'   =>  'Usuario activo su cuenta',
            'user_id'  =>  $user->id,
            'verbo' =>  $request->method(),
            'ruta' =>   $request->url(),
            'data'  =>  true,
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Usuario activo su cuenta',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json("Tu cuenta a sido activada",200);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {   
        $credentials = request(['email', 'password']);
        $user = User::where('email',request('email'))->first();
       
        if (! $token = auth()->attempt($credentials)) {
           
            return response()->json('Unauthorized', 401);
        }
        else{
            if($request->has('code')) {
                if($this->verify_code($request,$user)){
                    $logdata = [
                        'msg'   =>  'Logued',
                        'user_id'  =>  $user->id,
                        'verbo' =>  $request->method(),
                        'ruta' =>   $request->url(),
                        'data'  =>  $request->except(['password','code']),
                        'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
                    ];
                    Log::info('Usuario logueado correctamente',$logdata);
                    $this->mongo->Log = $logdata;
                    $this->mongo->save();

                    return $this->respondWithToken($token);
                }
                else{
                    $logdata = [
                        'msg'   =>  'Usuario ingreso un codigo incorrecto',
                        'user_id'  =>  $user->id,
                        'verbo' =>  $request->method(),
                        'ruta' =>   $request->url(),
                        'data'  =>  $request->except(['password','code']),
                        'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
                    ];
            
                    Log::alert('Usuario ingreso un codigo incorrecto',$logdata);
                    $this->mongo->Log = $logdata;
                    $this->mongo->save();
                    return response()->json("Codigo incorrecto",401);
                }
            }
            else {
                $code = Crypt::decrypt($user->code);
                Mail::to($user->email)->send(new CodeMail($code));
                $logdata = [
                    'msg'   =>  'Se mando codigo',
                    'user_id'  =>  $user->id,
                    'verbo' =>  $request->method(),
                    'ruta' =>   $request->url(),
                    'data'  =>  $request->except(['password','code']),
                    'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
                ];
        
                Log::info('Se mando correo con codigo',$logdata);
                $this->mongo->Log = $logdata;
                $this->mongo->save();
                return response()->json(["msg"=>"Ingresa el codigo, revisa tu correo"],200);
            }
        }
    }

    public function verify_code(Request $request,User $user){
      
        $codei = $request->input('code');
        $code = Crypt::decrypt($user->code);
        if($code == $codei ){
            $new_code = Crypt::encrypt(rand(100000,999999));
            $user->code = $new_code;
            $user->save();
            return true;
        }
        return false;
        
    }
    
    public function rolid()
    {
        return response()->json(auth()->user()->rol_id);
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() *60*24
        ]);
    }
}
