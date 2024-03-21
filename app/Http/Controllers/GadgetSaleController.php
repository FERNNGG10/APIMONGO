<?php

namespace App\Http\Controllers;

use App\Models\Gadget_Inventory;
use App\Models\Gadget_Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Log as ModelsLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GadgetSaleController extends Controller
{
    protected $mongo;
    public function __construct()
    {
        $this->mongo = new ModelsLog;
    }

    public function index(){
        DB::enableQueryLog();
        $sales = Gadget_Sale::with('gadget','user','paymentMethod')->get()->map(function($sale){
            return [
                'sale' => $sale->gadget->name,
                'user' => $sale->user->name,
                'payment_method' => $sale->paymentMethod->method,
            ];
        });
        $queries = DB::getQueryLog();
        $datalog = [
            'msg'   =>  'Listado de gadget sales',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries
        ];
        Log::info('Listado de gadget sales',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["gadget_sales"=>$sales]);
    }

    public function store(Request $request){
        $validate = Validator::make($request->all(),[
            'gadget_id'  =>  'required|exists:gadgets,id|numeric',
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
        $gadget_inventory=Gadget_Inventory::where('gadget_id',$request->gadget_id)->first();
        if(!$gadget_inventory->gadget->status){
          
            return response()->json(['errors'=>'Gadget no disponible'],422);
        }
        if($gadget_inventory->stock<$request->quantity){
            
            return response()->json(['errors'=>'No hay suficiente stock'],409);
        }
        $total = $gadget_inventory->price * $request->quantity;
        $gadget_sale = Gadget_Sale::create([
            'gadget_id' => $request->gadget_id,
            'payment_method_id' => $request->payment_method_id,
            'quantity' => $request->quantity,
            'total' => $total,
            'user_id' => auth()->user()->id
        ]);
        $gadget_inventory->stock = $gadget_inventory->stock - $request->quantity;
        $gadget_inventory->save();
        $datalog = [
            'msg'   =>  'Gadget vendido correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $gadget_sale->toArray()
        ];
        Log::info('Gadget vendido correctamente',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["msg"=>"Compra hecha correctamente",'gadget_sale'=>$gadget_sale]);


    }

    public function show($id){
        $gadget_sale = Gadget_Sale::with('gadget','user','paymentMethod')->find($id);
        if(!$gadget_sale){
       
            return response()->json(['errors'=>'Gadget sale no encontrado'],404);
        }
        $datalog = [
            'msg'   =>  'Detalle de gadget sale',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $gadget_sale->toArray()
        ];
        Log::info('Detalle de gadget sale',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["gadget_sale"=>$gadget_sale]);
    }
}
