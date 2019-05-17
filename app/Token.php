<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use ATTSMS\SMS;

class Token extends Model {

    const EXPIRATION_TIME = 15; // minutes

    protected $fillable = [
        'code',
        'user_id',
        'used'
    ];

    public function __construct(array $attributes = []) {
        if (!isset($attributes['code'])) {
            $attributes['code'] = $this->generateCode();
        }
        parent::__construct($attributes);
    }

    /**
     * Generate a six digits code
     *
     * @param int $codeLength
     * @return string
     */
    public function generateCode($codeLength = 4) {
        $min = pow(10, $codeLength);
        $max = $min * 10 - 1;
        $code = mt_rand($min, $max);

        return $code;
    }

    /**
     * User tokens relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Send code to user
     *
     * @return bool
     * @throws \Exception
     */
    public function sendCode() {
        if (!$this->user) {
            throw new \Exception("No user attached to this token.");
        }

        if (!$this->code) {
            $this->code = $this->generateCode();
        }
        try {
            try
            {
                $sms = new SMS();
                $sms::setConfig("5336024556", "6637omer", "MANASENERJI");
                $sms::sendSms($this->user->getPhoneNumber(), $this->code);
//Buradan Sonuç Döndür
            }
            catch (Exception $e)
            {
                echo "Error: " . $e->getMessage();
            }
        } catch (\Exception $ex) {
            return false; //enable to send SMS
        }

        return true;
    }

    /**
     * True if the token is not used nor expired
     *
     * @return bool
     */
    public function isValid() {
        return !$this->isUsed() && !$this->isExpired();
    }

    /**
     * Is the current token used
     *
     * @return bool
     */
    public function isUsed() {
        return $this->used;
    }

    /**
     * Is the current token expired
     *
     * @return bool
     */
    public function isExpired() {
        return $this->created_at->diffInMinutes(Carbon::now()) > static::EXPIRATION_TIME;
    }

}
