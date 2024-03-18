<?php

namespace App\Http\Controllers;

use App\Models\Console_Inventory;
use App\Models\Console_Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Log as ModelsLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class ConsoleSaleController extends Controller
{
    protected $mongo;
    public function __construct()
    {
        $this->mongo = new ModelsLog;
    }

    public function index(){
        DB::enableQueryLog();
        $sales = Console_Sale::with('console','user','paymentMethod')->get()->map(function($sale){
            return [
                'sale' => $sale->console->name,
                'user' => $sale->user->name,
                'payment_method' => $sale->paymentMethod->method,
            ];
        });
        $queries = DB::getQueryLog();
        $datalog = [
            'msg'   =>  'Listado de console sales',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries
        ];
        Log::info('Listado de console sales',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["console_sales"=>$sales]);
    }

    public function store(Request $request){
        $validate = Validator::make($request->all(),[
            'console_id'  =>  'required|exists:consoles,id|numeric',
            'payment_method_id' =>  'required|exists:payment_methods,id|numeric',
            'quantity' =>  'required|numeric|min:1',

        ]);
        if($validate->fails()){
            $datalog = [
                'msg'   =>  'Error en validacion de datos',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  request()->method(),
                'ruta' =>   request()->url(),
                'data'  =>  $validate->errors()->toArray()
            ];
            Log::alert('Error en validacion de datos',$datalog);
            $this->mongo->Log = $datalog;
            $this->mongo->save();
            return response()->json(['errors'=>$validate->errors()],422);
        }
       $console_inventory=Console_Inventory::where('console_id',$request->console_id)->first();

       if(!$console_inventory->console->status){
        $datalog = [
            'msg'   =>  'La consola no esta disponible',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  null
        ];
        Log::alert('La consola no esta disponible',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(['errors'=>'La consola no esta disponible'],409);
       }

       if($console_inventory->stock<$request->quantity){
        $datalog = [
            'msg'   =>  'No hay suficiente stock',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  null
        ];
        Log::alert('No hay suficiente stock',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(['errors'=>'No hay suficiente stock'],422);
       }

       $total = $console_inventory->price * $request->quantity;
        $sale = Console_Sale::create([
              'console_id' => $request->console_id,
              'payment_method_id' => $request->payment_method_id,
              'quantity' => $request->quantity,
              'total' => $total,
              'user_id' => auth()->user()->id
         ]);
        $console_inventory->stock = $console_inventory->stock - $request->quantity;
        $console_inventory->save();
        $datalog = [
            'msg'   =>  'Venta creada',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $sale->toArray()
        ];
        Log::info('Compra hecha correctamente',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["msg"=>"Compra hecha correctamente","sale"=>$sale],201);
      
       
    }

    public function show($id){
        DB::enableQueryLog();
        $sale = Console_Sale::with('console','user','paymentMethod')->find($id);
        $queries = DB::getQueryLog();
        if($sale){
            $datalog = [
                'msg'   =>  'Venta encontrada',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  request()->method(),
                'ruta' =>   request()->url(),
                'data'  =>  $queries
            ];
            Log::info('Venta encontrada',$datalog);
            $this->mongo->Log = $datalog;
            $this->mongo->save();
            return response()->json(["sale"=>$sale]);
        }
        
        $datalog = [
            'msg'   =>  'Venta no encontrada',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries
        ];
        Log::alert('Venta no encontrada',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["msg"=>"Venta no encontrada"],404);
    }

    
}
