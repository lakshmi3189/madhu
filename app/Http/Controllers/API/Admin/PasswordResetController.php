<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Admin\PasswordReset;
use App\Models\Admin\User;
use Illuminate\Support\Facades\Validator;
use Exception;


/*
Created By : Lakshmi kumari 
Created On : 10-Apr-2023 
Code Status : Open 
*/

class PasswordResetController extends Controller
{
    /**
     * | Send password in email 
     */
    public function sendResetPasswordEmail(Request $req)
    {
        //Description: Sending passeord reset link to email
        $email = $req->email;
        $validator = Validator::make($req->all(), [
            'email' => 'required|email'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            //Check user's email exist or not
            $user = User::where('email', $email)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'Email not exist',
                    'status' => 'failed'
                ]);
            }

            //Generate token
            $token = Str::random(60);

            //Saving data to present reset table
            PasswordReset::create([
                'email' => $email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
            // dump("http://127.0.0.1:8000/api/resetPassword".$token);

            //Send email with password reset view
            Mail::send('reset', ['token' => $token], function (Message $message) use ($email) {
                $message->subject('Reset your password');
                $message->to($email);
            });
            return responseMsgs(true, "Password reset email sent... check your email", [], "", "API-2.1", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API-2.1", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Reset password
     */
    public function resetPassword(Request $req, $token)
    {
        $validator = Validator::make($req->all(), [
            'password' => 'required|confirmed'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            //Delete token older than 1 minute
            $formatted = Carbon::now()->subMinutes(1)->toDateTimeString();
            PasswordReset::where('created_at', '<=', $formatted)->delete();
            $passwordReset = PasswordReset::where('token', $token)->first();
            if (!$passwordReset)
                return responseMsgs(false, $validator->errors(), []);
            // return response()->json(['code' => '400','message' => 'Token is invalid or expired','status' => 'failed']);
            $user = User::where('email', $passwordReset->email)->first();
            $user->password = Hash::make($req->password);
            $user->save;

            //delete the token after resetting password
            PasswordReset::where('email', $user->email)->delete();
            return responseMsgs(true, "Password reset successfully", [], "", "API-2.2", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API-2.2", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
