<?php

namespace App\Http\Controllers;

use App\Mail\ActiveMail;
use App\Models\Log as ModelsLog;
use App\Models\Payment_Method;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $mongo;
    public function __construct()
    {
        $this->mongo = new ModelsLog;
    }

    public function index(){
        DB::enableQueryLog();
        $users = User::all();
        $queries = DB::getQueryLog();
        $datalog = [
            'msg'   =>  'Listado de usuarios',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries,
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Listado de usuarios',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["users"=>$users]);
    }

    public function store(Request $request){
        $validate = Validator::make($request->all(),[
            'name'  =>  'required|max:30',
            'email' =>  'required|email|unique:users',
            'password'  =>  'required|min:8|confirmed',
            'rol_id'    =>  'exists:roles,id|numeric',
           
        ]);

        if($validate->fails()){
            $logdata=[
                'msg'   =>  'No pudo regisrarse correctamente',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  $request->method(),
                'ruta' =>   $request->url(),
                'data'  =>  $validate->errors()->toArray(),
                'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
            ];
            Log::alert('No pudo regisrarse correctamente',$logdata);
            $this->mongo->Log = $logdata;
            $this->mongo->save();
            return response()->json(['errors'=>$validate->errors()],422);
        }
        $code = Crypt::encrypt(rand(100000,999999));

        $user = User::create([
            'name'  =>  $request->name,
            'email' =>  $request->email,
            'password'   =>  Hash::make($request->password),
            'code'  =>  $code,
            'rol_id'    =>  $request->rol_id??2,
           
        ]);
        $signed_route = URL::temporarySignedRoute(
            'activate',
            now()->addMinutes(15),
            ['user'=>$user->id]
        );
        Mail::to($user->email)->send(new ActiveMail($signed_route));

        $logdata=[
            'msg'   =>  'Usuario registrado correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  $request->method(),
            'ruta' =>   $request->url(),
            'data'  =>  $user->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Usuario registrado correctamente',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["msg"=>"Usuario registrado correctamente","user"=>$user],201);
    }

    public function show(int $id){
        DB::enableQueryLog();
        $user = User::find($id);
        $queries = DB::getQueryLog();
        if($user){
            $datalog = [
                'msg'   =>  'Usuario encontrado',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  request()->method(),
                'ruta' =>   request()->url(),
                'data'  =>  $queries
            ];
            Log::info('Usuario encontrado',$datalog);
            $this->mongo->Log = $datalog;
            $this->mongo->save();
            return response()->json(["user"=>$user]);
        }
       
        return response()->json(["msg"=>"Usuario no encontrado"],404);
    }

    public function update(Request $request, int $id){
        $user = User::find($id);
        if(!$user){
           
            return response()->json(["msg"=>"Usuario no encontrado"],404);
        }

        $validate = Validator::make($request->all(),[
            'name'  =>  'required|max:30',
            'email' =>  'required|email|unique:users,email,'.$id,
            'status'    =>  'boolean',
            'rol_id'    =>  'exists:roles,id|numeric',
            'password' => 'nullable|min:8'
        ]);

        if($validate->fails()){
            $logdata=[
                'msg'   =>  'No pudo actualizarse correctamente',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  $request->method(),
                'ruta' =>   $request->url(),
                'data'  =>  $validate->errors()->toArray(),
                'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
            ];
            Log::alert('No pudo actualizarse correctamente',$logdata);
            $this->mongo->Log = $logdata;
            $this->mongo->save();
            return response()->json(['errors'=>$validate->errors()],422);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->rol_id = $request->rol_id??2;
        $user->status = $request->status??false;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $logdata=[
            'msg'   =>  'Usuario actualizado correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  $request->method(),
            'ruta' =>   $request->url(),
            'data'  =>  $user->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Usuario actualizado correctamente',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["msg"=>"Usuario actualizado correctamente","user"=>$user]);
    }

    public function destroy(int $id){
        $user = User::find($id);
        if(!$user){
            
            return response()->json(["msg"=>"Usuario no encontrado"],404);
        }

        $user->status = false;
        $user->save();
        $logdata=[
            'msg'   =>  'Usuario eliminado correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $user->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Usuario eliminado correctamente',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["msg"=>"Usuario eliminado correctamente"]);
    }

    public function roles(){
        $roles = Role::all();
        return response()->json(["roles"=>$roles]);
    }
}
