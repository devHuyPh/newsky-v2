<?php

namespace App\Http\Controllers\Admin;
use Botble\Setting\Supports\SettingStore;
use Botble\Base\Http\Responses\BaseHttpResponse;
use App\Http\Controllers\Controller;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class ReferralCommissionController extends BaseController
{
    const PATH_WEB='admin.referral.';
    public function index()
    {
        $direct=setting('direct-referral-commission');
        $indirect = setting('indirect-referral-commission');
        return view(self::PATH_WEB.__FUNCTION__,compact('direct', 'indirect'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editreferral()
    {
        $direct=setting('direct-referral-commission');
        $indirect = setting('indirect-referral-commission');
        return view(self::PATH_WEB.__FUNCTION__,compact('direct','indirect'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BaseHttpResponse $response, SettingStore $settingStore)
    { $request->validate([
            'direct' => 'required|max:255',
            'indirect' => 'required',
        ]);
        $settingKey=['direct-referral-commission','indirect-referral-commission'];
        $settingValue=[
            $request->direct,
            $request->indirect
        ];
        foreach ($settingKey as $index => $key) {
            setting()->set($key, $settingValue[$index]);
        }

        setting()->save();

        return redirect()->route('referral.index')->with('success', 'Cài đặt giới thiệu đã được cập nhật thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    
}
