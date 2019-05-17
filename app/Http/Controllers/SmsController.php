<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use ATTSMS\SMS;

class SmsController extends Controller
{
    protected $code, $smsOnay;
    function __construct()
    {
        $this->smsOnay = new \App\SmsOnay();
    }
    public function store(Request $request)
    {
        $code = rand(1000, 9999); //random kod üret
        $request['code'] = $code; //add code in $request body
        $this->smsOnay->store($request); //call store method of model
        return $this->sendSms($request); // send and return its response
    }
    public function verifyContact(Request $request)
    {
        $smsOnay =
            smsOnay::where('contact_number','=', $request->contact_number)
                ->latest() //birden fazla kayıt varsa en son numarayı al
                ->first();
        if($request->code == $smsOnay->code)
        {
            $request["status"] = 'verified';
            $smsOnay->updateModel($request);
            $msg["message"] = "verified";
            return $msg;
        }
        else
        {
            $msg["message"] = "not verified";
            return $msg;
        }
    }
    public function sendSms($request)
{
    try
    {
        $sms = new SMS();
        $sms::setConfig("5336024556", "6637omer", "MANASENERJI");
        $sms::sendSms("$request->contact_number", "$request->code");
//Buradan Sonuç Döndür
    }
    catch (Exception $e)
    {
        echo "Error: " . $e->getMessage();
    }
}
}