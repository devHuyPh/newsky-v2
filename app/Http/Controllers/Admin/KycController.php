<?php

namespace App\Http\Controllers\Admin;
use App\Models\KycLog;

use App\Http\Controllers\Controller;
use App\Models\CusTomer;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use App\Models\KycForm;
use App\Models\PendingLog;
use Botble\Setting\Supports\SettingStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KycController extends BaseController
{
    public function identityForm()
    {

        $this->pageTitle(trans('core/dashboard::dashboard.title_identity'));
        $kycForms = new KycForm();
        $data = $kycForms->get();
        $address_v=setting('address_verification');
        $identity_v=setting('identity_verification');
        return view('admin.kyc.indexkyc', compact('data','address_v','identity_v'));
    }

    public function storeIdentityForm(Request $request, BaseHttpResponse $response)
    {

        // $kyc_form = KycForm::new();
        $data = $request->all();

        $formData = json_encode([
            'field_name' => $data['field_name'] ?? [],
            'type' => $data['type'] ?? [],
            'field_length' => $data['field_length'] ?? [],
            'length_type' => $data['length_type'] ?? [],
            'validation' => $data['validation'] ?? [],
        ]);

        $kycForm = new KycForm();
        $kycForm->name = $data['name'] ?? null;
        $kycForm->status = $data['status'] ?? 0;
        $kycForm->form = $formData;
        $create_form = $kycForm->save();

        if (!$create_form) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('Create form error'));
        }

        return $this
            ->httpResponse()
            ->setMessage(__('Created form'));
    }
    public function update(Request $request, BaseHttpResponse $response, SettingStore $settingStore)
    { 
        
        $settingKey=['address_verification','identity_verification'];
        $settingValue=[
            $request->address_v,
            $request->identity_v
        ];
        foreach ($settingKey as $index => $key) {
            setting()->set($key, $settingValue[$index]);
        }

        setting()->save();

        return redirect()->route('kyc.form')->with('success', 'Cài đặt địa chỉ thành công');
    }

    public function updateIdentityForm(Request $request, BaseHttpResponse $response, $id)
    {

        // $kyc_form = KycForm::new();
        $data = $request->all();
        //  dd($data);

        $formData = json_encode([
            'field_name' => $data['field_name_'.$id] ?? [],
            'type' => $data['type_'.$id] ?? [],
            'field_length' => $data['field_length_'.$id] ?? [],
            'length_type' => $data['length_type_'.$id] ?? [],
            'validation' => $data['validation_'.$id] ?? [],
        ]);

        // dd($formData);

        $kycForm = KycForm::find($id);


        if ($kycForm) {
            $update_form = $kycForm->update([
                'name' => $data['name'] ?? null,
                'status' => $data['status'] ?? 0,
                'form' => $formData,
            ]);
        }

        if (!$update_form) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('Update form error'));
        }

        return $this
            ->httpResponse()
            ->setMessage(__('Updated form'));
    }

    public function deleteIdentityForm($id){
        $kycForm = KycForm::find($id);
        $delete_form = $kycForm->delete();

        if(!$delete_form){
            return $this
            ->httpResponse()
            ->setError()
            ->setMessage(__('Delete form error'));
        }

        return redirect()->route('kyc.form')
        ->with('success', __('Deleted form successfully'));
    }

    public function showPending(Request $request)
    {
        $query = $request->input('search');
        $pendings = PendingLog::with('customer')->where('status', 'pending')
            ->when($query, function ($q) use ($query) {
                return $q->where('name', 'like', "%$query%")
                         ->orWhere('verification_type', 'like', "%$query%");
            })
            ->paginate(5);
            $this->pageTitle(trans('core/dashboard::dashboard.title_kyc_pending'));
        return view('admin.kyc.pending_kyc', compact('pendings'));
       
        // $this->pageTitle('Identity Form');

    
    }

    public function logs(Request $request)
    {
        $query = $request->input('search');
        $logs = KycLog::with(['kycPending', 'admin', 'customer'])
            ->when($query, function ($q) use ($query) {
                return $q->where('action', 'like', "%$query%")
                         ->orWhere('note', 'like', "%$query%")
                         ->orWhere('reason', 'like', "%$query%")
                         ->orWhere('customer_name', 'like', "%$query%")
                         ->orWhere('customer_email', 'like', "%$query%")
                         ->orWhere('kyc_pending_name', 'like', "%$query%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.kyc.kyc_log', compact('logs'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:ec_customers,id',
            'kyc_form_id' => 'required|exists:kyc_forms,id',
            'name' => 'required|string|max:255',
            'verification_type' => 'required|string|max:255',
            'data' => 'required|array',
        ]);

        $customer = CusTomer::findOrFail($validated['customer_id']);

        $pending = PendingLog::create([
            'customer_id' => $validated['customer_id'],
            'kyc_form_id' => $validated['kyc_form_id'],
            'name' => $validated['name'],
            'avatar' => $customer->avatar,
            'verification_type' => $validated['verification_type'],
            'data' => $validated['data'],
            'status' => 'pending',
        ]);

        KycLog::create([
            'kyc_pending_id' => $pending->id,
            'kyc_pending_name' => $pending->name,
            'kyc_verification_type' => $pending->verification_type,
            'kyc_status' => $pending->status,
            'admin_id' => null,
            'admin_name' => null,
            'admin_email' => null,
            'customer_id' => $validated['customer_id'],
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'customer_status' => $customer->status ?? 'active',
            'action' => 'submitted',
            'affected_entity' => 'customer',
            'affected_entity_id' => $customer->id,
            'system_notification' => false,
            'data_before' => null,
            'data_after' => $pending->data,
            'note' => 'KYC request submitted by customer.',
            'reason' => null,
            'action_at' => now(),
        ]);

        return redirect()->route('kyc.pending')->with('success', 'KYC request submitted successfully.');
    }
    public function pendingview($id){
        $pending = PendingLog::find($id);
        $this->pageTitle(trans('core/dashboard::dashboard.title_kyc_pending'));
        return view('admin.kyc.pending_view', compact('pending'));
    }
    public function pendingapprove($id){
        $pending = PendingLog::with('customer')->findOrFail($id);
        
        $admin = Auth::user();

        $dataBefore = $pending->data;
        $pending->update([
            'status' => 'approved',
        ]);

        KycLog::create([
            'kyc_pending_id' => $pending->id,
            'kyc_pending_name' => $pending->name,
            'kyc_verification_type' => $pending->verification_type,
            'kyc_status' => $pending->status,
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'admin_email' => $admin->email,
            'customer_id' => $pending->customer_id,
            'customer_name' => $pending->customer->name,
            'customer_email' => $pending->customer->email,
            'customer_phone' => $pending->customer->phone,
            'action' => 'approved',
            'affected_entity' => 'customer',
            'affected_entity_id' => $pending->customer_id,
            'system_notification' => true,
            'data_before' => $dataBefore,
            'data_after' => $pending->data,
            'note' => 'Đã phê duyệt sau khi xác minh tất cả tài liệu.',
            'reason' => null,
            'action_at' => now(),
        ]);

        return redirect()->route('kyc.pending')->with('success', 'KYC request approved successfully.');
    }
    
    public function pendingreject(Request $request,$id){
        $pending = PendingLog::with('customer')->findOrFail($id);
        $admin = Auth::user();

        $validated = $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $dataBefore = $pending->data;
        $pending->update([
            'status' => 'rejected',
        ]);

        KycLog::create([
            'kyc_pending_id' => $pending->id,
            'kyc_pending_name' => $pending->name,
            'kyc_verification_type' => $pending->verification_type,
            'kyc_status' => $pending->status,
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'admin_email' => $admin->email,
            'customer_id' => $pending->customer_id,
            'customer_name' => $pending->customer->name,
            'customer_email' => $pending->customer->email,
            'customer_phone' => $pending->customer->phone,
            'action' => 'rejected',
            'affected_entity' => 'customer',
            'affected_entity_id' => $pending->customer_id,
            'system_notification' => true,
            'data_before' => $dataBefore,
            'data_after' => $pending->data,
            'note' => 'Bị từ chối do tài liệu không hợp lệ.',
            'reason' => $validated['reason'] ?? 'Thiếu tài liệu bắt buộc.',
            'action_at' => now(),
        ]);

        return redirect()->route('kyc.pending')->with('success', 'KYC request rejected successfully.');
    }
   
    public function view($id)
{
    $log = KycLog::with(['admin', 'customer', 'kycPending'])->findOrFail($id);
    return view('admin.kyc.kyc_log_view', compact('log'));
}
    
}
