<?php

namespace App\Http\Controllers;

use App\Models\Developer;
use App\Models\Log as ModelsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DeveloperController extends Controller
{
    protected $mongo;
    public function __construct()
    {
        $this->mongo = new ModelsLog;
    }

    public function index(){
        DB::enableQueryLog();
        $developers = Developer::where('status', true)->get();
        $queries = DB::getQueryLog();
        $datalog = [
            'msg'   =>  'Listado de desarrolladores',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries,
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Listado de desarrolladores',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["developers"=>$developers]);
    }

    public function store(Request $request){
        $validate = Validator::make($request->all(),[
            'name'  =>  'required|max:30',
            'email' =>  'required|email|unique:developers',
            'phone' =>  'required|digits:10'
        ]);

        if($validate->fails()){
            $logdata=[
                'msg'   =>  'No pudo registrarse correctamente',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  $request->method(),
                'ruta' =>   $request->url(),
                'data'  =>  $validate->errors()->toArray(),
                'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
            ];
            Log::alert('No pudo registrarse correctamente',$logdata);
            $this->mongo->Log = $logdata;
            $this->mongo->save();
            return response()->json(['errors'=>$validate->errors()],422);
        }

        $developer = Developer::create([
            'name'  =>  $request->name,
            'email' =>  $request->email,
            'phone' =>  $request->phone
        ]);

        $logdata=[
            'msg'   =>  'Desarrollador registrado correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  $request->method(),
            'ruta' =>   $request->url(),
            'data'  =>  $developer->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Desarrollador registrado correctamente',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["msg"=>"Desarrollador registrado correctamente","developer"=>$developer]);
    }

    public function show(int $id){
        DB::enableQueryLog();
        $developer = Developer::find($id);
        $queries = DB::getQueryLog();
        if(!$developer){
            
            return response()->json(["msg"=>"Desarrollador no encontrado"],404);
        }
        $datalog = [
            'msg'   =>  'Desarrollador encontrado',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries,
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Desarrollador encontrado',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["developer"=>$developer]);
    }

    public function destroy(int $id){
        $developer = Developer::find($id);
        if(!$developer){
          
            return response()->json(['msg'=>'Desarrollador no encontrado'],404);
        }

        $developer->status = false;
        $developer->save();

        $logdata=[
            'msg'   =>  'Desarrollador eliminado correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $developer->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Desarrollador eliminado correctamente',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["msg"=>"Desarrollador eliminado correctamente","developer"=>$developer],200);
    }

    public function update (int $id){
        $developer = Developer::find($id);
        if(!$developer){
           
            return response()->json(['msg'=>'Desarrollador no encontrado'],404);
        }

        $validate = Validator::make(request()->all(),[
            'name'  =>  'required|max:30',
            'email' =>  'required|email|unique:developers,email,'.$id,
            'phone' =>  'required|digits:10'
        ]);

        if($validate->fails()){
            $logdata=[
                'msg'   =>  'No pudo actualizarse correctamente',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  request()->method(),
                'ruta' =>   request()->url(),
                'data'  =>  $validate->errors()->toArray(),
                'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
            ];
            Log::alert('No pudo actualizarse correctamente',$logdata);
            $this->mongo->Log = $logdata;
            $this->mongo->save();
            return response()->json(['errors'=>$validate->errors()],422);
        }

        $developer->name = request()->name;
        $developer->email = request()->email;
        $developer->phone = request()->phone;
        $developer->save();

        $logdata=[
            'msg'   =>  'Desarrollador actualizado correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $developer->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Desarrollador actualizado correctamente',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["msg"=>"Desarrollador actualizado correctamente","developer"=>$developer]);
    }
}
