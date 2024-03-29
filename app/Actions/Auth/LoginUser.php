<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Cookie;
use App\Models\User;
use App\Traits\SyncUserPreferences;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginUser extends Action
{
   use SyncUserPreferences;

   protected $request;
   public function __construct(Request $request)
   {
      $this->request = $request;
   }
   protected function validate()
   {
      $val = Validator::make($this->request->all(), [
         'email' => 'required|email|exists:users,email',
         'password' => 'required|string',
      ]);
      return $this->valResult($val);
   }
   protected function isCredencialsCorrect($record)
   {
      if (!isset($record)) return false;
      if (Hash::check($this->request->password, $record->password)) {
         return true;
      } else {
         return false;
      }
   }
   protected function generateToken($record)
   {
      $token = $record->createToken('Personal Access Token')->accessToken;
      return $token;
   }
   protected function syncUserData($user)
   {
      if (!isset($user)) return;
      $cookie_pin = $this->request->cookie("basic_access");
      $cookie = Cookie::where('cookie_value', $cookie_pin)->first();
      if (!isset($cookie)) return;
      DB::transaction(function () use ($user, $cookie) {
         $this->syncUserCurrency($cookie->id, $user->id);
         $this->syncUserCountry($cookie->id, $user->id);
         $this->syncUserCart($cookie->id, $user->id);
         $this->syncUserRecentlyViewed($cookie->id, $user->id);
         $this->syncUserWishList($cookie->id, $user->id);
         $this->syncSearchHistory($cookie->id, $user->id);
      });
   }
   protected function getTokenCookie($token)
   {
      return cookie(
         '_token', //name
         $token, //value
         6 * 30 * 24 * 60, //minutes
         "/", //path,
         null, //domain,
         null, //secure,
         true, //httponly
         false, //raw
         null, //same site
      );
   }
   public function execute()
   {
      try {
         $val = $this->validate();
         if ($val['status'] != "success") return $this->resp($val);
         $record = User::where('email', $this->request->email)->first();
         if ($this->isCredencialsCorrect($record)) {
            $token = $this->generateToken($record);
            $this->syncUserData($record);
            $cookie = $this->getTokenCookie($token);
            return $this->successWithData([
               'token' => $token,
               'user_type' => $record->user_type,
            ])->withCookie($cookie);
         } else {
            return $this->validationError('Incorrect email or password entered');
         }
      } catch (\Exception $e) {
         return $this->internalError($e->getMessage());
      }
   }
}
