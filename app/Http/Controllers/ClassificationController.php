<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use App\Models\Log as ModelsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClassificationController extends Controller
{
    protected $mongo;
    public function __construct()
    {
        $this->mongo = new ModelsLog;
    }

    public function index(){
        DB::enableQueryLog();
        $classifications = Classification::where('status', true)->get();
        $queries = DB::getQueryLog();
        $datalog = [
            'msg'   =>  'Listado de clasificaciones',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries,
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Listado de clasificaciones',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["classifications"=>$classifications]);
    }

    public function store(Request $request){
        $validate = Validator::make($request->all(),[
            'classification'  =>  'required|max:10'
        ]);

        if($validate->fails()){
            
            return response()->json(['errors'=>$validate->errors()],422);
        }

        $classification = Classification::create([
            'classification'  =>  $request->classification
        ]);

        $logdata=[
            'msg'   =>  'Clasificacion creada correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  $request->method(),
            'ruta' =>   $request->url(),
            'data'  =>  $classification,
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Clasificacion creada correctamente',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["msg"=>"Clasificacion creada correctamente","classification"=>$classification],201);
    }

    public function update(Request $request, $id){
        $classification = Classification::find($id);
        if(!$classification){
            return response()->json(['msg'=>'No se encontro la clasificacion'],404);
        }

        $validate = Validator::make($request->all(),[
            'classification'  =>  'required|max:10'
        ]);

        if($validate->fails()){
          
            return response()->json(['errors'=>$validate->errors()],422);
        }

        $classification->classification = $request->classification;
        $classification->save();

        $logdata=[
            'msg'   =>  'Clasificacion actualizada correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  $request->method(),
            'ruta' =>   $request->url(),
            'data'  =>  $classification->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Clasificacion actualizada correctamente',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["msg"=>"Clasificacion actualizada correctamente","classification"=>$classification],200);
    }

    public function destroy($id){
        $classification = Classification::find($id);
        if(!$classification){
        
            return response()->json(['msg'=>'No se encontro la clasificacion'],404);
        }

        $classification->status = false;
        $classification->save();

        $logdata=[
            'msg'   =>  'Clasificacion eliminada correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $classification->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Clasificacion eliminada correctamente',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["msg"=>"Clasificacion eliminada correctamente","classification"=>$classification],200);
    }

    public function show (int $id){
        DB::enableQueryLog();
        $classification = Classification::find($id);
        $queries = DB::getQueryLog();
        if(!$classification){
           
            return response()->json(['msg'=>'No se encontro la clasificacion'],404);
        }

        $logdata=[
            'msg'   =>  'Clasificacion encontrada',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries,
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Clasificacion encontrada',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["classification"=>$classification],200);
    }
}
