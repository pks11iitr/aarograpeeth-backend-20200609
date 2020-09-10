<?php

namespace App\Http\Controllers\Therapist\Auth;

use App\Events\SendOtp;
use App\Models\Customer;
use App\Models\OTPModel;
use App\Models\Therapist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{

    /**
     * Handle a login request to the application with otp.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */

    public function verify(Request $request){
        $request->validate([
            'type'=>'required|string|max:15',
            'mobile'=>'required|string|digits:10|exists:therapists',
            'otp'=>'required|digits:6'
        ]);

        switch($request->type){
            case 'register': return $this->verifyRegister($request);
            case 'login': return $this->verifyLogin($request);
        }

        return [
            'status'=>'failed',
            'message'=>'Request is not valid'
        ];
    }

    protected function verifyRegister(Request $request){
        $user=Therapist::where('mobile', $request->mobile)->first();
        if($user->status==0){
            if(OTPModel::verifyOTP('therapistadmin',$user->id,$request->type,$request->otp)){

                $user->status=1;
                $user->save();

                return [
                    'status'=>'success',
                    'message'=>'OTP has been verified successfully',
                    'token'=>Auth::guard('therapistapi')->fromUser($user)
                ];
            }

            return [
                'status'=>'failed',
                'message'=>'OTP is not correct',
                'token'=>''
            ];

        }
        return [
            'status'=>'failed',
            'message'=>'Request is not valid',
            'token'=>''
        ];
    }


    protected function verifyLogin(Request $request){
        $user=Therapist::where('mobile', $request->mobile)->first();
        if(in_array($user->status, [0,1])){
            if(OTPModel::verifyOTP('therapistadmin',$user->id,$request->type,$request->otp)){

                $user->status=1;
                $user->save();

                return [
                    'status'=>'success',
                    'message'=>'OTP has been verified successfully',
                    'token'=>Auth::guard('therapistapi')->fromUser($user)
                ];
            }

            return [
                'status'=>'failed',
                'message'=>'OTP is not correct',
                'token'=>''
            ];

        }
        return [
            'status'=>'failed',
            'message'=>'Account has been blocked',
            'token'=>''
        ];
    }


    public function resend(Request $request){
        $request->validate([
            'type'=>'required|string|max:15',
            'mobile'=>'required|string|digits:10|exists:customers',
        ]);

        $user=Customer::where('mobile', $request->mobile)->first();
        if(in_array($user->status, [0,1])){
                $otp=OTPModel::createOTP('customer', $user->id, $request->type);
                $msg=str_replace('{{otp}}', $otp, config('sms-templates.'.$request->type));
                event(new SendOtp($user->mobile, $msg));
                return [
                    'status'=>'success',
                    'message'=>'Please verify OTP to continue',
                ];
        }

        return [
            'status'=>'failed',
            'message'=>'Account has been blocked',
        ];

    }

}
