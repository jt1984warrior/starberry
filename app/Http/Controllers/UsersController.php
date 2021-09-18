<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users;
use DataTables;
use DB;
use App\Http\Requests\StoreUsers;
use Session;
use View;
use Image;
use Storage;
class UsersController extends Controller
{
    public function index(Request $request){
        $externaldata=$this->curlApi();
        try{
          $result=$externaldata->results;
          //dd($result);
          $users = Users::count();
          if($users == 0){
            foreach($result as $key=>$value){
            
          
                $response['name']=$value->name;
                $response['height']=$value->height;
                $response['skin_color']=$value->skin_color;
                $response['hair_color']=$value->hair_color;
                $response['films']=json_encode($value->films,true);
                $data[] = $response;
              
            }
            Users::insert($data);
         }
        }
        catch (Exception $e) {
          $errorMessage = $e->getMessage();
        }
$data = array();
        return view('users',$data);
    }

    public function curlApi(){
        $curl = curl_init();
        $url='https://swapi.dev/api/people/';
        $headers = array(
            "Content-Type: application/json"
        );
        curl_setopt_array($curl, 
        array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYHOST =>  0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",	
            CURLOPT_HTTPHEADER => $headers,
        ));
        
        $response = curl_exec($curl);

        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "e:e";
        } else {
            $response = json_decode($response);
            return $response;
        }
      }

    public function getUserData(Request $request){

        $userData = Users::all();

       // DB::table('users')->pluck('name', 'id');

        $userData = DB::table('people')
        
            ->select('people.*')
            ->groupBy('people.id')
            ->orderBy('people.id','DESC')
            ->get();
           // dd(  $userData);

        return Datatables::of($userData)->addColumn('action', function($row){
       
            $btn = '';
            $btn = $btn.' <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#userModal">
            Edit
          </button>';
            $btn = $btn.'<a href="javascript:void(0)" class="edit btn btn-danger btn-sm" onclick="deleteRow('.$row->id.')">Delete</a>';

             return $btn;
     })
   

     ->rawColumns(['action'])
     ->make();
    }

    public function store(StoreUsers $request){

        try{
        $input = $request->all();
        Users::create($input);
        
Session::flash('success', 'People created successfully!');
        } catch(Throwable $e){
            Session::flash('error', 'Something went wrong!');
        }

       
       return View::make('flash-messages');
    }
    public function destroy($id){

        DB::table('people')->where('id', $id)->delete();
        Session::flash('success', 'People deleted successfully!');
        return View::make('flash-messages');
    }
}
