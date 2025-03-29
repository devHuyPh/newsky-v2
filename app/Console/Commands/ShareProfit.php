<?php

namespace App\Console\Commands;

use App\Models\CustomerNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Botble\Ecommerce\Models\Customer;
use App\Models\Ranking;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\RewardHistory;

class ShareProfit extends Command
{
    protected $signature = 'share:profit';
    protected $description = 'Chia tổng doanh thu theo rank';

    public function handle()
    {
        $today = (int) Carbon::now()->format('d');
        $shareOfDate = (int) setting('share_of_date');
        $currentMonth = Carbon::now()->format('Y-m');

        if ($today !== $shareOfDate) {
            $this->info('Hôm nay không phải ngày chia lợi nhuận!');
            return;
        }

        $alreadyShared = RewardHistory::where('date_reward', 'like', "$currentMonth%")->exists();
    
        if ($alreadyShared) {
            $this->info("Lợi nhuận đã được chia trong tháng này! Không thực hiện lại.");
            return;
        }

        $mainCustomer = Customer::find(1);
        if (!$mainCustomer) {
            $this->error('Không tìm thấy customer ID = 1');
            return;
        }

        $totalFunds = $mainCustomer->total_dowline_month;
        $this->info($totalFunds);
        if ($totalFunds <= 0) {
            $this->info('Không có số tiền để chia!');
            return;
        }

        $ranks = Ranking::orderBy('rank_lavel', 'desc')->get();
        $rankUserCounts = [];
        $cumulativeUserCounts = [];
        
        $totalUsers = 0;
        foreach ($ranks as $rank) {
            $rankUserCounts[$rank->id] = Customer::where('rank_id', $rank->id)->count();
            $totalUsers += $rankUserCounts[$rank->id];
            $cumulativeUserCounts[$rank->id] = $totalUsers;
        }

        if ($totalUsers == 0) {
            $this->info('Không có user nào để chia tiền!');
            return;
        }

        DB::beginTransaction();
        try {
            $accumulatedRanks = [];
        
            foreach ($ranks as $rank) {
                if ($rankUserCounts[$rank->id] == 0) {
                    continue;
                }
        
                // Tính phần trăm số tiền của rank này
                $rankPercentage = $rank->percentage_reward / 100;
                $amountToShare = $totalFunds * $rankPercentage;
        
                // Tổng số người nhận tính từ rank này trở lên
                $usersAtOrAboveRank = 0;
                foreach ($ranks as $higherRank) {
                    if ($higherRank->rank_lavel >= $rank->rank_lavel) {
                        $usersAtOrAboveRank += $rankUserCounts[$higherRank->id];
                    }
                }
        
                if ($usersAtOrAboveRank > 0) {
                    $amountPerUser = $amountToShare / $usersAtOrAboveRank;
        
                    // Lưu lại tổng số tiền mà từng rank sẽ nhận được khi cộng dồn
                    foreach ($ranks as $higherRank) {
                        if ($higherRank->rank_lavel >= $rank->rank_lavel && $rankUserCounts[$higherRank->id] > 0) {
                            if (!isset($accumulatedRanks[$higherRank->id])) {
                                $accumulatedRanks[$higherRank->id] = 0;
                            }
                            $accumulatedRanks[$higherRank->id] += $amountPerUser;
        
                            $this->info("Rank {$rank->rank_name} chia {$amountPerUser} cho {$rankUserCounts[$higherRank->id]} users có rank >= {$rank->rank_name}");
                            Log::info("Rank {$rank->rank_name} chia {$amountPerUser} cho {$rankUserCounts[$higherRank->id]} users có rank >= {$rank->rank_name}");
                        }
                    }
                }
            }
        
            // Cập nhật số tiền cuối cùng vào ví của từng rank
            foreach ($accumulatedRanks as $rankId => $totalAmountPerUser) {
                // Cập nhật số dư trong bảng customers
                Customer::where('rank_id', $rankId)->update([
                    'walet_1' => DB::raw("COALESCE(walet_1, 0) + {$totalAmountPerUser}")
                ]);
            
                // Lấy danh sách customer có rank_id hiện tại
                $customers = Customer::where('rank_id', $rankId)->get();
            
                foreach ($customers as $customer) {
                    RewardHistory::create([
                        'customer_id' => $customer->id,
                        'rank_id' => $rankId,
                        'reward' => $totalAmountPerUser,
                        'date_reward' =>  Carbon::now(),
                    ]);

                    CustomerNotification::create([
                        'title' => 'Thông báo thu nhập đồng chia',
                        'dessription' => 'Bạn đã nhận được ' . format_price($totalAmountPerUser) . 
                                        ' từ hệ thống chia lợi nhuận rank '. $customer->rank->rank_name . '. Tổng số dư trong ví hiện tại: ' . 
                                        format_price($customer->walet_1),
                        'customer_id' => $customer->id,
                        'url' => '/bitsgold/dashboard'
                    ]);
                }
            
                $this->info("Rank ID {$rankId} thực nhận: {$totalAmountPerUser}/người");
                Log::info("Rank ID {$rankId} thực nhận: {$totalAmountPerUser}/người");
            }
        
            DB::commit();
            $this->info('Chia lợi nhuận thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi chia lợi nhuận: ' . $e->getMessage());
            $this->error('Có lỗi xảy ra khi chia lợi nhuận!' . $e->getMessage());
        }
    }
}
