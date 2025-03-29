<?php

namespace App\Http\Controllers\Client;

use Botble\Ecommerce\Http\Controllers\BaseController;

use App\Models\User;
use Auth;
use Botble\Ecommerce\Models\Customer;
use Illuminate\Http\Request;
use Botble\Base\Supports\Language;

class BitsgoldController extends BaseController
{
  /**
   * Display a listing of the resource.
   */
  public const PATH_VIEW = 'dashboard.bitsgold.';
  public function dashboard()
  {
    $customer = Customer::findOrFail(auth('customer')->user()->id);

    return view(self::PATH_VIEW . 'dashboard', compact('customer'));
  }
  public function plan()
  {
    return redirect('/products');
  }
  public function invest_history()
  {

  }
  public function add_fund()
  {

  }
  public function transaction()
  {

  }
  public function referral()
  {
    $title = 'My Referral';
    $user = auth('customer')->user();
    if (!empty($user['id'])) {
      $customer = Customer::findOrFail($user['id']);
      $referrals = $customer->getAllLevelUser($user['id']);
      return view(self::PATH_VIEW . 'referral', compact('title', 'referrals', 'user'));

    }
  }
  public function referral_bonus()
  {
  }
}
