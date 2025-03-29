<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Botble\Ecommerce\Models\Customer;
use App\Models\Ranking;
use Carbon\Carbon;

class CheckRank extends Command
{
    protected $signature = 'check:rank';
    protected $description = 'Kiểm tra và cập nhật rank của customer';

    public function handle()
    {
        $customers = Customer::all();
        $now = now(); // Lấy thời gian hiện tại

        foreach ($customers as $customer) {
            $rankAssignedAt = $customer->rank_assigned_at;

            // Nếu customer chưa có rank hoặc chưa ghi nhận thời gian lên rank thì bỏ qua
            if (!$customer->rank_id || !$rankAssignedAt) {
                continue;
            }

            // Tìm rank hiện tại của user
            $currentRank = Ranking::find($customer->rank_id);


            // Nếu không tìm thấy rank, bỏ qua
            if (!$currentRank) {
                continue;
            }
            // $this->info($customer);
            // $this->info($currentRank);

            // Kiểm tra điều kiện xuống cấp (demotion)
            $demotionTimeMonths = (int) $currentRank->demotion_time_months;
            $demotionInvestment = (int) $currentRank->demotion_investment;
            $demotionReferrals = (int) $currentRank->demotion_referrals;

            // Thời gian hạ cấp = rank_assigned_at + demotion_time_months
            $demotionDeadline = Carbon::parse($rankAssignedAt)->copy()->addMonths($demotionTimeMonths);
            $this->info($demotionDeadline);
            $this->info($now);

            // Kiểm tra nếu đã tới thời hạn xuống cấp
            if ($now->greaterThanOrEqualTo($demotionDeadline)) {
                // Tính số referrals sau khi lên rank
                $newReferralsCount = Customer::where('referral_ids', $customer->id)
                    ->where('created_at', '>=', $rankAssignedAt)
                    ->count();

                $this->info($newReferralsCount);

                // Nếu số referrals hoặc tổng doanh thu không đạt yêu cầu, tìm rank thấp hơn
                if ($newReferralsCount < $demotionReferrals || $customer->total_dowline_on_rank < $demotionInvestment) {
                    $this->info('có rank cần hạ');
                    $lowerRank = Ranking::where('rank_lavel', '<', $currentRank->rank_lavel)
                        ->orderByDesc('rank_lavel')
                        ->first();

                    if ($lowerRank) {
                        $this->info($lowerRank);
                        Log::info("Hạ cấp Customer ID: {$customer->id} từ Rank {$customer->rank_id} → {$lowerRank->id}");
                        $customer->update([
                            'rank_id' => $lowerRank->id,
                            'rank_assigned_at' => $now, // Cập nhật thời gian hạ cấp
                            'total_dowline_on_rank' => $customer->total_dowline, // Cập nhật tổng doanh thu mới
                        ]);
                    }else{
                        $this->info('hạ về null');
                        Log::info("Hạ cấp Customer ID: {$customer->id} từ Rank {$customer->rank_id} → {null}");
                        $customer->update([
                            'rank_id' => null,
                            'rank_assigned_at' => null, // Cập nhật thời gian hạ cấp
                            'total_dowline_on_rank' => $customer->total_dowline, // Cập nhật tổng doanh thu mới
                        ]);
                    }
                }
            }
        }

        $this->info('Hoàn thành kiểm tra & hạ cấp rank!');
    }
}
