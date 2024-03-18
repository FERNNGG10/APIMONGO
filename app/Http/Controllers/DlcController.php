<?php

namespace App\Http\Controllers;

use App\Models\Dlc;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Log as ModelsLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DlcController extends Controller
{
    protected $mongo;
    public function __construct()
    {
        $this->mongo = new ModelsLog;
    }

    public function index(){
        DB::enableQueryLog();
        $dlcs = Dlc::with('game')->get()->map(function($dlc){
            return [
                'id'  =>  $dlc->id,
                'dlc' => $dlc->name,
                'game' => $dlc->game->name,
            ];
        });
        $queries = DB::getQueryLog();
        $datalog = [
            'msg'   =>  'Listado de dlcs',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries,
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Listado de dlcs',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["dlcs"=>$dlcs]);
    }

    public function store(Request $request){
        $validate = Validator::make($request->all(),[
            'name'  =>  'required|max:30',
            'game_id' =>  'required|exists:games,id|numeric',
        ]);
        if($validate->fails()){
            $datalog = [
                'msg'   =>  'Error en validacion de datos',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  request()->method(),
                'ruta' =>   request()->url(),
                'data'  =>  $validate->errors()->toArray(),
                'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
            ];
            Log::alert('Error en validacion de datos',$datalog);
            $this->mongo->Log = $datalog;
            $this->mongo->save();
            return response()->json(['errors'=>$validate->errors()],422);
        }
        $dlc = Dlc::create($request->all());
        $datalog = [
            'msg'   =>  'Dlc creado correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $dlc->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Dlc creado correctamente',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["msg"=>"Dlc creada correctamente","dlc"=>$dlc]);
    }

    public function show($id){
        DB::enableQueryLog();
        $dlc = Dlc::with('game')->find($id);
        $queries = DB::getQueryLog();
        if(!$dlc){
          
            return response()->json(['msg'=>'Dlc no encontrado'],404);
        }
        $datalog = [
            'msg'   =>  'Dlc encontrado',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries,
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Dlc encontrado',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["dlc"=>$dlc]);
    }

    public function update(Request $request, $id){
        $validate = Validator::make($request->all(),[
            'name'  =>  'required|max:30',
            'game_id' =>  'required|exists:games,id|numeric',
        ]);
        if($validate->fails()){
            $datalog = [
                'msg'   =>  'Error en validacion de datos',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  request()->method(),
                'ruta' =>   request()->url(),
                'data'  =>  $validate->errors()->toArray(),
                'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
            ];
            Log::alert('Error en validacion de datos',$datalog);
            $this->mongo->Log = $datalog;
            $this->mongo->save();
            return response()->json(['errors'=>$validate->errors()],422);
        }
        $dlc = Dlc::find($id);
        if(!$dlc){
           
            return response()->json(['msg'=>'Dlc no encontrado'],404);
        }
        $dlc->update($request->all());
        $datalog = [
            'msg'   =>  'Dlc actualizado correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $dlc->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Dlc actualizado correctamente',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["dlc"=>$dlc]);
    }

    public function destroy($id){
        $dlc = Dlc::find($id);
        if(!$dlc){
          
            return response()->json(['msg'=>'Dlc no encontrado'],404);
        }
        $dlc->delete();
        $datalog = [
            'msg'   =>  'Dlc eliminado correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $dlc->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Dlc eliminado correctamente',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["dlc"=>$dlc]);
    }

    public function games(){
        return response()->json(["games"=>Game::all(),200]);
    }
}
