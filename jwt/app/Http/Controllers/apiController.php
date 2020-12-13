<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\users;
use App\Models\lists;
use App\Models\session;

class apiController extends Controller
{
    public function checkUser()
    {
        $getdata = json_decode(file_get_contents("php://input"));
        $username = $getdata->username;
        $password = $getdata->password;
        //$username = $request->get('username');
        //$password = $request->get('password');
        $id = users::where('username', $username)->where('password', $password)->value('id');
        if (empty($id))
        {
            $data = '{"userid":null, "username":null, "JWT":null }';
        }
        else
        {
            $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
            $payload = json_encode(['user_id' => $id,'username'=> $username,'uniqueNumber'=> uniqid()]);
            $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
            $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
            $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'abC123!', true);
            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
            $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
            $sessionid = session::where('session', $jwt)->value('id');
            if(empty($sessionid))
            {
                $session1 = new session();
                $session1->session = $jwt;
                $session1->userid = $id;
                $session1->username = $username;
                $session1->save();
            }
            $data = '{"userid":'.$id.',"username":"'.$username.'","JWT":"'.$jwt.'"}';
        }
        return $data;
    }
    public function getData(request $request)
    {
        $header = $request->header('auth');
        $sessionid = session::where('session', $header)->value('id');
        if(!empty($sessionid))
        {
            $data = lists::select('id','name','title')->get();
            $data1 = json_encode($data,true);
            $data = '{"item":'.$data1.'}';
        }
        else{
            $data = '{"item":null }';
        }
        return $data;
    }
}
