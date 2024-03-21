<?php

namespace App\Http\Controllers;

use App\Models\Log as ModelsLog;
use App\Models\Payment_Method;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller
{
    protected $mongo;
    public function __construct()
    {
        $this->mongo = new ModelsLog;
    }

    public function index(){
        DB::enableQueryLog();
        $payment_methods = Payment_Method::where('status', true)->get();
        $queries = DB::getQueryLog();
        $datalog = [
            'msg'   =>  'Listado de metodos de pago',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries,
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Listado de metodos de pago',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["payment_methods"=>$payment_methods]);
    }

    public function store(Request $request){
        $validate = Validator::make($request->all(),[
            'method'  =>  'required|max:30'
        ]);

        if($validate->fails()){
           
            return response()->json(['errors'=>$validate->errors()],422);
        }

        $payment_method = Payment_Method::create([
            'method'  =>  $request->method
        ]);

        $logdata=[
            'msg'   =>  'Metodo de pago registrado correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  $request->method(),
            'ruta' =>   $request->url(),
            'data'  =>  $payment_method->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Metodo de pago registrado correctamente',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["msg"=>"Metodo de pago registrado correctamente","payment_method"=>$payment_method]);
    }

    public function update(Request $request, int $id){
        $payment_method = Payment_Method::find($id);
        if(!$payment_method){
           
            return response()->json(["msg"=>"Metodo de pago no encontrado"],404);
        }

        $validate = Validator::make($request->all(),[
            'method'  =>  'required|max:30'
        ]);

        if($validate->fails()){
          
            return response()->json(['errors'=>$validate->errors()],422);
        }

        $payment_method->method = $request->method;
        $payment_method->save();

        $logdata=[
            'msg'   =>  'Metodo de pago actualizado correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  $request->method(),
            'ruta' =>   $request->url(),
            'data'  =>  $payment_method->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Metodo de pago actualizado correctamente',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["msg"=>"Metodo de pago actualizado correctamente","payment_method"=>$payment_method]);
    }

    public function destroy(Request $request, int $id){
        $payment_method = Payment_Method::find($id);
        if(!$payment_method){
           
            return response()->json(["msg"=>"Metodo de pago no encontrado"],404);
        }

        $payment_method->status = false;
        $payment_method->save();

        $logdata=[
            'msg'   =>  'Metodo de pago eliminado correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  $request->method(),
            'ruta' =>   $request->url(),
            'data'  =>  $payment_method->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Metodo de pago eliminado correctamente',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["msg"=>"Metodo de pago eliminado correctamente","payment_method"=>$payment_method]);
    }

    public function show (int $id){
        DB::enableQueryLog();
        $payment_method = Payment_Method::find($id);
        $queries = DB::getQueryLog();
        if(!$payment_method){
           
            return response()->json(["msg"=>"Metodo de pago no encontrado"],404);
        }

        $logdata=[
            'msg'   =>  'Metodo de pago encontrado',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries,
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Metodo de pago encontrado',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["payment_method"=>$payment_method]);
    }
}
