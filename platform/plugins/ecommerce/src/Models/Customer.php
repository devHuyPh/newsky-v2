<?php

namespace Botble\Ecommerce\Models;

use App\Models\CustomerNotification;
use App\Models\Ranking;
use App\Models\TotalRevenue;
use Botble\Base\Facades\MacroableModels;
use Botble\Base\Models\BaseModel;
use Botble\Base\Models\BaseQueryBuilder;
use Botble\Base\Supports\Avatar;
use Botble\Ecommerce\Enums\CustomerStatusEnum;
use Botble\Ecommerce\Enums\DiscountTypeEnum;
use Botble\Ecommerce\Notifications\ConfirmEmailNotification;
use Botble\Ecommerce\Notifications\ResetPasswordNotification;
use Botble\Media\Facades\RvMedia;
use Botble\Payment\Models\Payment;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class Customer extends BaseModel implements
  AuthenticatableContract,
  AuthorizableContract,
  CanResetPasswordContract
{
  use Authenticatable;
  use Authorizable;
  use CanResetPassword;
  use MustVerifyEmail;
  use HasApiTokens;
  use Notifiable;

  protected $table = 'ec_customers';

  protected $fillable = [
    'name',
    'uuid_code',
    'email',
    'referrals_id',
    'uuid_code',
    'password',
    'avatar',
    'phone',
    'status',
    'private_notes',
    'referral_ids',
    'rank_id',
    'total_dowline',
    'walet_1',
    'walet_2',
    'total_revenue_id',
    'rank_assigned_at',
    'total_dowline_on_rank',
    'total_dowline_month',
    'is_admin_active'
  ];

  protected $hidden = [
    'password',
    'remember_token',
  ];

  protected $casts = [
    'status' => CustomerStatusEnum::class,
    'dob' => 'date',
  ];

  public function sendPasswordResetNotification($token): void
  {
    $this->notify(new ResetPasswordNotification($token));
  }

  public function sendEmailVerificationNotification(): void
  {
    $this->notify(new ConfirmEmailNotification());
  }

  public function orders(): HasMany
  {
    return $this->hasMany(Order::class, 'user_id', 'id');
  }

  public function rank(): BelongsTo
  {
    return $this->belongsTo(Ranking::class, 'rank_id', 'id');
  }

  public function referrer(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'referral_ids', 'id');
  }

  public function getTotalRevenue(): BelongsTo
  {
    return $this->belongsTo(TotalRevenue::class, 'total_revenue_id');
  }

  public function referrers(): HasMany
  {
    return $this->hasMany(Customer::class, 'referral_ids', 'id');
  }

  public function directReferralsCount()
  {
    return $this->hasMany(Customer::class, 'referral_ids', 'id')->count();
  }

  public function notifications()
  {
    return $this->hasMany(CustomerNotification::class, 'customer_id', 'id')->orderBy('created_at', 'desc');
  }

  public function notificationCount()
  {
      return $this->hasMany(CustomerNotification::class, 'customer_id', 'id')->count();
  }

  public function directReferralsCountAt(): Attribute
  {
      return Attribute::make(
          get: fn () => (int) $this->hasMany(Customer::class, 'referral_ids', 'id')->count(),
      );
  }

  public function updateTotalRevenue()
  {
    // Lấy total_revenue cao nhất mà user có thể đạt được
    $total_revenue = TotalRevenue::where('amount', '<=', $this->total_dowline)
      ->orderByDesc('amount')
      ->first();

    if ($total_revenue) {
      $this->total_revenue_id = $total_revenue->id;
      $this->save();
    }
  }


  public function updateRank()
  {
    // Lấy rank cao nhất mà user có thể đạt được
    $rank = Ranking::where('number_referrals', '<=', $this->directReferralsCount())
      ->where('total_revenue', '<=', $this->total_dowline)
      ->orderByDesc('number_referrals')
      ->first();

    if ($rank) { // && $this->is_default != 0
      if($rank->id != $this->rank_id){
        $this->rank_assigned_at = Carbon::now();
        $this->total_dowline_on_rank = null;

        CustomerNotification::create([
          'title' => 'Thông báo nâng hạng',
          'dessription' => 'Bạn đã đạt đủ điều kiện thăng hạng: ' . $rank->rank_name . '. Với tổng thu nhập tuyến dưới: ' . format_price($this->total_dowline) .' và số thành viên trực tiếp: '. $rank->number_referrals ,
          'customer_id' => $this->id,
          'url' => '/bitsgold/dashboard'
        ]);
      }
      $this->rank_id = $rank->id;
      $this->save();
    }

  }


  public function completedOrders(): HasMany
  {
    return $this->orders()->whereNotNull('completed_at');
  }

  public function addresses(): HasMany
  {
    return $this
      ->hasMany(Address::class, 'customer_id', 'id')
      ->when(is_plugin_active('location'), function (HasMany|BaseQueryBuilder $query) {
        return $query->with(['locationCountry', 'locationState', 'locationCity']);
      });
  }

  public function payments(): HasMany
  {
    return $this->hasMany(Payment::class, 'customer_id', 'id');
  }

  public function discounts(): BelongsToMany
  {
    return $this->belongsToMany(Discount::class, 'ec_discount_customers', 'customer_id', 'id');
  }

  public function wishlist(): HasMany
  {
    return $this->hasMany(Wishlist::class, 'customer_id');
  }

  protected static function booted(): void
  {
    self::deleted(function (Customer $customer): void {
      $customer->discounts()->detach();
      $customer->usedCoupons()->detach();
      $customer->orders()->update(['user_id' => 0]);
      $customer->addresses()->delete();
      $customer->wishlist()->delete();
      $customer->reviews()->each(fn(Review $review) => $review->delete());
    });

    static::deleted(function (Customer $customer): void {
      $folder = Storage::path($customer->upload_folder);
      if (File::isDirectory($folder) && Str::endsWith($customer->upload_folder, '/' . $customer->id)) {
        File::deleteDirectory($folder);
      }
    });
  }

  public function __get($key)
  {
    if (class_exists('MacroableModels')) {
      $method = 'get' . Str::studly($key) . 'Attribute';
      if (MacroableModels::modelHasMacro(get_class($this), $method)) {
        return call_user_func([$this, $method]);
      }
    }

    return parent::__get($key);
  }

  public function reviews(): HasMany
  {
    return $this->hasMany(Review::class, 'customer_id');
  }

  public function promotions(): BelongsToMany
  {
    return $this
      ->belongsToMany(Discount::class, 'ec_discount_customers', 'customer_id')
      ->where('type', DiscountTypeEnum::PROMOTION)
      ->where('start_date', '<=', Carbon::now())
      ->where('target', 'customer')
      ->where(function ($query) {
        return $query
          ->whereNull('end_date')
          ->orWhere('end_date', '>=', Carbon::now());
      })
      ->where('product_quantity', 1);
  }

  public function viewedProducts(): BelongsToMany
  {
    return $this->belongsToMany(Product::class, 'ec_customer_recently_viewed_products');
  }

  public function usedCoupons(): BelongsToMany
  {
    return $this->belongsToMany(Discount::class, 'ec_customer_used_coupons');
  }

  public function deletionRequest(): HasOne
  {
    return $this->hasOne(CustomerDeletionRequest::class, 'customer_id');
  }

  protected function avatarUrl(): Attribute
  {
    return Attribute::get(function () {
      if ($this->avatar) {
        return RvMedia::getImageUrl($this->avatar, 'thumb');
      }

      try {
        return (new Avatar())->create(Str::ucfirst($this->name))->toBase64();
      } catch (Exception) {
        return RvMedia::getDefaultImage();
      }
    });
  }

  protected function uploadFolder(): Attribute
  {
    return Attribute::get(function () {
      $folder = $this->id ? 'customers/' . $this->id : 'customers';

      return apply_filters('ecommerce_customer_upload_folder', $folder, $this);
    });
  }

  public $allusers = [];

  function getAllLevelUser($user_id)
  {
    $this->allusers = [];
    return $this->referralUsers([$user_id], 1, false); // true: get all level after it
  }

  function getOnlyLevelUserAfterIt($user_id)
  {
    $this->allusers = [];
    return $this->referralUsers([$user_id], 1, true); // true: get all level after it
  }

  public function referralUsers($id, $currentLevel = 1, $afterIt = false)
  {
    $users = $this->getUsers($id);
    if ($users['status']) {
      $this->allusers[$currentLevel] = $users['user']; //$this->allusers[1]
      if ($afterIt && $currentLevel >= 1) {
        return $this->allusers;
      }
      $currentLevel++;

      if (!empty($users['ids']) && count($users['ids']) > 0) {
        $this->referralUsers($users['ids'], $currentLevel, $afterIt);
      }
    }
    return $this->allusers;
  }

  public function getUsers($id)
  {
    $data = [];
    if (!empty($id) && is_array($id)) {

      $data['user'] = Customer::whereIn('referral_ids', $id)->get(['id', 'name', 'phone', 'email', 'referral_ids','rank_id', 'created_at']);

      if ($data['user']->count() > 0) {
        $data['status'] = true;
        $data['ids'] = $data['user']->pluck('id')->toArray();
        return $data;
      }
    }
    $data['status'] = false;
    return $data;
  }

}
