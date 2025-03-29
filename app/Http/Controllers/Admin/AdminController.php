<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ranking;
use Botble\Ecommerce\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class AdminController extends BaseController
{
	//form list ranks
	const PATH_VIEW = 'ranks.';
	public function indexranks()
	{
		$data = Ranking::orderBy('sort_by', 'asc')->paginate(5);
		$dayofsharing = setting('day_of_sharing');
		return view(self::PATH_VIEW . __FUNCTION__, compact('data', 'dayofsharing'));
	}

	//form add ranks
	public function addranks()
	{
		return view(self::PATH_VIEW . __FUNCTION__);
	}

	//logic add ranks
	public function storeranks(Request $request)
	{

		$rules = [
			// 'rank_name' => 'required',
			// 'rank_lavel' => 'required',
			'rank_icon' => 'required'
		];
		// dd(1);
		$this->validate($request, $rules);

		$rank = new Ranking();
		// dd($rank);
		$rank->rank_name = $request->rank_name;
		// dd($rank->rank_name);
		$rank->rank_lavel = $request->rank_lavel;
		$rank->number_referrals = isset($request->number_referrals) ? $request->number_referrals : 0;
		$rank->total_revenue = isset($request->total_revenue) ? $request->total_revenue : 0;
		$rank->min_earning = isset($request->min_earning) ? $request->min_earning : 0;
		$rank->description = $request->description;
		$rank->demotion_investment = isset($request->demotion_investment) ? $request->demotion_investment : 0;
		$rank->demotion_referrals = isset($request->demotion_referrals) ? $request->demotion_referrals : 0;
		$rank->demotion_time_months = isset($request->demotion_time_months) ? $request->demotion_time_months : 0;
		$rank->percentage_reward = isset($request->percentage_reward) ? $request->percentage_reward : 0;
		$rank->status = isset($request->status) ? 1 : 0;

		if ($request->hasFile('rank_icon')) {
			try {
				$file = $request->file('rank_icon');
				$fileName = time() . '.' . $file->getClientOriginalExtension();
				$file->move(public_path('uploads/rank'), $fileName);
				$rank->rank_icon = 'uploads/rank/' . $fileName;
			} catch (\Exception $exp) {
				return back()->with('error', 'Image could not be uploaded.');
			}
		}
		// dd($rank->rank_icon);

		// dd($rank);
		$rank->save();
		return redirect()->route('rank.index')->with('success', 'Ranking create successfully');

	}

	public function editranks($id)
	{
		// dd(1);
		$data = Ranking::find($id);
		// dd($data);
		return view(self::PATH_VIEW . __FUNCTION__, compact('data'));
	}

	public function updateranks(Request $request, $id)
	{
		// dd(1);
		// Quy tắc validation
		$rules = [
			'rank_name' => 'required|string|max:255',
			'rank_lavel' => 'required|string|max:255',
			'rank_icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Chỉ kiểm tra khi có file
		];

		$this->validate($request, $rules);

		// Tìm bản ghi Ranking
		$rank = Ranking::findOrFail($id);


		// Cập nhật các trường cơ bản
		$rank->rank_name = $request->rank_name;
		$rank->rank_lavel = $request->rank_lavel;
		$rank->number_referrals = $request->number_referrals ?? 0;
		$rank->total_revenue = $request->total_revenue ?? 0;
		$rank->min_earning = $request->min_earning ?? 0;
		$rank->description = $request->description;
		$rank->status = $request->status ? 1 : 0; // Đơn giản hóa cú pháp
		$rank->demotion_investment = $request->demotion_investment ?? 0;
		$rank->demotion_referrals = $request->demotion_referrals ?? 0;
		$rank->demotion_time_months = $request->demotion_time_months ?? 0;
		$rank->percentage_reward = $request->percentage_reward ?? 0;
		// Xử lý ảnh rank_icon
		if ($request->hasFile('rank_icon')) {
			try {
				$file = $request->file('rank_icon');
				$fileName = time() . '.' . $file->getClientOriginalExtension();
				$file->move(public_path('uploads/rank'), $fileName);
				// Xóa ảnh cũ nếu tồn tại (tùy chọn)
				if ($rank->rank_icon && file_exists(public_path($rank->rank_icon))) {
					unlink(public_path($rank->rank_icon));
				}
				$rank->rank_icon = 'uploads/rank/' . $fileName;
			} catch (\Exception $exp) {
				return back()->with('error', 'Không thể tải lên ảnh: ' . $exp->getMessage());
			}
		}
		// Nếu không có ảnh mới, $rank->rank_icon giữ nguyên giá trị cũ từ database
		// dd($rank);
		// Lưu thay đổi
		$rank->save();

		return redirect()->route('rank.index')->with('success', 'Cập nhật xếp hạng thành công');
	}
	public function deleteranks($id)
	{
		try {
			$rank = Ranking::findOrFail($id);
			// dd($rank);
			$rank->delete();
			return redirect()->back()->with('success', 'Xóa xếp hạng thành công');
		} catch (\Exception $e) {
			return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa: ' . $e->getMessage());
		}
	}

	public function sortBadges(Request $request)
	{
		$data = $request->all();
		foreach ($data['sort'] as $key => $value) {

			Ranking::where('id', $value)->update([
				'sort_by' => $key + 1
			]);
		}

	}


	public function updateDayOfSharing(Request $request)
	{
		try {
			$request->validate([
				'value' => [
					'required',
					'max:29',
					'min:1'
				]
			]);

			// Định nghĩa các key và value cần cập nhật
			$settingKey = ['day_of_sharing']; // Chỉ một cột trong trường hợp này
			$settingValue = [$request->value];

			// Cập nhật từng cặp key-value
			foreach ($settingKey as $index => $key) {
				setting()->set($key, $settingValue[$index]);
			}

			// Lưu vào database
			setting()->save();

			return response()->json(['success' => true]);
		} catch (\Exception $e) {
			return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}



}
