<?php

namespace App\Http\Controllers;

use App\Models\Gadget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Log as ModelsLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GadgetController extends Controller
{
    protected $mongo;
    public function __construct()
    {
        $this->mongo = new ModelsLog;
    }

    public function index(){
        DB::enableQueryLog();
        $gadgets = Gadget::with('gadget_inventory','supplier')->where('status',true)->get()->map(function($gadget){
            return [
                'id'  =>  $gadget->id,
                'gadget' => $gadget->name,
                'description' => $gadget->description,
                'stock' => $gadget->gadget_inventory->stock,
                'price' => $gadget->gadget_inventory->price,
                'supplier' => $gadget->supplier->name
            ];
        });
        $queries = DB::getQueryLog();
        $datalog = [
            'msg'   =>  'Listado de gadgets',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries,
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Listado de gadgets',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["gadgets"=>$gadgets]);
    }

    public function store(Request $request){
        $validate = Validator::make($request->all(),[
            'name'  =>  'required|max:30',
            'description' =>  'required|max:50',
            'supplier_id' =>  'required|exists:suppliers,id|numeric',
            'stock' =>  'required|numeric|between:1,999999',
            'price' =>  'required|numeric|between:1,999999.99'
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
        $gadget = Gadget::create([
            'name'  =>  $request->name,
            'description' =>  $request->description,
            'supplier_id' =>  $request->supplier_id
        ]);
        $gadget->gadget_inventory()->create([
            'stock' => $request->stock,
            'price' => $request->price
        ]);
        $datalog = [
            'msg'   =>  'Gadget creado correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $gadget->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Gadget creado correctamente',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["msg"=>"Gadget creada correctamente",'gadget'=>$gadget],201);
    }

    public function show($id){
        DB::enableQueryLog();
        $gadget = Gadget::with('gadget_inventory','supplier')->find($id);
        $queries = DB::getQueryLog();
        if($gadget){
            $datalog = [
                'msg'   =>  'Detalle de gadget',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  request()->method(),
                'ruta' =>   request()->url(),
                'data'  =>  $queries
            ];
            Log::info('Detalle de gadget',$datalog);
            $this->mongo->Log = $datalog;
            $this->mongo->save();
            return response()->json(['gadget'=>$gadget]);
        }
        
        return response()->json(['msg'=>'Gadget no encontrado'],404);
    }

    public function update(Request $request, $id){
        $validate = Validator::make($request->all(),[
            'name'  =>  'required|max:30',
            'description' =>  'required|max:50',
            'supplier_id' =>  'required|exists:suppliers,id|numeric',
            'stock' =>  'required|numeric|between:1,999999',
            'price' =>  'required|numeric|between:1,999999.99'
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
            return response()->json(['errors'=>$validate->errors()],400);
        }
        $gadget = Gadget::find($id);
        if($gadget){
            $gadget->update([
                'name'  =>  $request->name,
                'description' =>  $request->description,
                'supplier_id' =>  $request->supplier_id
            ]);
            $gadget->gadget_inventory()->update([
                'stock' => $request->stock,
                'price' => $request->price
            ]);
            $datalog = [
                'msg'   =>  'Gadget actualizado correctamente',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  request()->method(),
                'ruta' =>   request()->url(),
                'data'  =>  $gadget->toArray()
            ];
            Log::info('Gadget actualizado correctamente',$datalog);
            $this->mongo->Log = $datalog;
            $this->mongo->save();
            return response()->json(['gadget'=>$gadget]);
        }
       
        return response()->json(['msg'=>'Gadget no encontrado'],404);
    }

    public function destroy($id){
        $gadget = Gadget::find($id);
        if($gadget){
            $gadget->update(['status'=>false]);
            $datalog = [
                'msg'   =>  'Gadget eliminado correctamente',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  request()->method(),
                'ruta' =>   request()->url(),
                'data'  =>  $gadget->toArray(),
                'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
            ];
            Log::info('Gadget eliminado correctamente',$datalog);
            $this->mongo->Log = $datalog;
            $this->mongo->save();
            return response()->json(['msg'=>'Gadget eliminado correctamente']);
        }
       
        return response()->json(['msg'=>'Gadget no encontrado'],404);
    }


}
