<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Log as ModelsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    protected $mongo;
    public function __construct()
    {
        $this->mongo = new ModelsLog;
    }

    public function index(){
        DB::enableQueryLog();
        $suppliers = Supplier::where('status', true)->get();
        $queries = DB::getQueryLog();
        $datalog = [
            'msg'   =>  'Listado de proveedores',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries,
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Listado de proveedores',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["suppliers"=>$suppliers]);
    }

    public function store(Request $request){
        $validate = Validator::make($request->all(),[
            'name'  =>  'required|max:30',
            'email' =>  'required|email|unique:suppliers',
            'phone' =>  'required|digits:10'
        ]);

        if($validate->fails()){
           
            return response()->json(['errors'=>$validate->errors()],422);
        }

        $supplier = Supplier::create([
            'name'  =>  $request->name,
            'email' =>  $request->email,
            'phone' =>  $request->phone
        ]);

        $logdata=[
            'msg'   =>  'Proveedor creado correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  $request->method(),
            'ruta' =>   $request->url(),
            'data'  =>  $supplier->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Proveedor creado correctamente',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["msg"=>"Proveedor creado correctamente","supplier"=>$supplier],201);
    }

    public function update(Request $request, int $id){
        $supplier = Supplier::find($id);
        if(!$supplier){
           
            return response()->json(["msg"=>"No se encontro el proveedor"],404);
        }

        $validate = Validator::make($request->all(),[
            'name'  =>  'required|max:30',
            'email' =>  'required|email|unique:suppliers,email,'.$id,
            'phone' =>  'required|digits:10'
        ]);

        if($validate->fails()){
           
            return response()->json(['errors'=>$validate->errors()],422);
        }

        $supplier->name = $request->name;
        $supplier->email = $request->email;
        $supplier->phone = $request->phone;
        $supplier->save();

        $logdata=[
            'msg'   =>  'Proveedor actualizado correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  $request->method(),
            'ruta' =>   $request->url(),
            'data'  =>  $supplier->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Proveedor actualizado correctamente',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["msg"=>"Proveedor actualizado correctamente","supplier"=>$supplier],200);
    }

    public function destroy(Request $request, int $id){
       
        $supplier = Supplier::find($id);
        if(!$supplier){
          
            return response()->json(["msg"=>"No se encontro el proveedor"],404);
        }

        $supplier->status = false;
        $supplier->save();

        $logdata=[
            'msg'   =>  'Proveedor eliminado correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  $request->method(),
            'ruta' =>   $request->url(),
            'data'  =>  $supplier->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Proveedor eliminado correctamente',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["msg"=>"Proveedor eliminado correctamente","supplier"=>$supplier],200);
    }

    public function show(int $id){
        DB::enableQueryLog();
        $supplier = Supplier::find($id);
        $queries = DB::getQueryLog();
        if(!$supplier){
            
            return response()->json(["msg"=>"No se encontro el proveedor"],404);
        }

        $logdata=[
            'msg'   =>  'Proveedor encontrado',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries,
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Proveedor encontrado',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["supplier"=>$supplier],200);
    }

}
