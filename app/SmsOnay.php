<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsOnay extends Model
{
    const EXPIRATION_TIME = 15; // minutes
    protected $fillable = [
        'user_id','contact_number','code','status'
    ];
    public function store($request)
    {
        $code = rand(1000, 9999); //generate random code
        $request['code'] = $code; //add code in $request body
        $this->fill($request->all());
        $sms = $this->save();
        return response()->json($sms, 200);
    }
    public function updateModel($request)
    {
        $this->update($request->all());
        return $this;
    }
    public function sendCode($request) {
        try
        {
            $sms = new ATTSMS\SMS();
            $sms::setConfig("5336024556", "6637omer", "MANASENERJI");
            $sms::sendSms("$request->contact_number", "$request->code");
//Buradan Sonuç Döndür
        }
        catch (Exception $e)
        {
            echo "Error: " . $e->getMessage();
        }
}
    public function verifyContact(Request $request)
    {
        $smsOnay =
            smsOnay::where('contact_number','=', $request->contact_number)
                ->latest() //show the latest if there are multiple
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
    public function isValid() {
        return !$this->isUsed() && !$this->isExpired();
    }
    public function isUsed() {
        return $this->used;
    }
    public function isExpired() {
        return $this->created_at->diffInMinutes(Carbon::now()) > static::EXPIRATION_TIME;
    }
}
