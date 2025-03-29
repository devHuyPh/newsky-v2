@extends('plugins/marketplace::themes.bitsgold-dashboard.layouts.master')

@section('content')
<div class="container-wrapper">
    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-header bg-light border-bottom">
            <h4 class="mb-0">@lang('plugins/marketplace::marketplace.kyc_verification')</h4>
        </div>

        <div class="card-body">
            <!-- Thông tin người dùng -->
            <div class="mb-5">
                <!--@lang('plugins/marketplace::marketplace.username')-->

                <h5 class="border-bottom pb-2 mb-3">@lang('plugins/marketplace::marketplace.your_infomation')</h5>
                <div class="row align-items-center">
                    <div class="col-md-3 text-center">
                        <div class="avatar-wrapper">
                            @if(Auth::guard('customer')->user()->avatar)
                                <img src="{{ asset('storage/'.Auth::guard('customer')->user()->avatar) }}" alt="{{ Auth::guard('customer')->user()->name }}" class="avatar-img">
                            @else
                                <div class="avatar-placeholder">
                                    {{ strtoupper(substr(Auth::guard('customer')->user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="info-item">
                            <strong>{{ trans('core/base::layouts.name')}}:</strong> {{ Auth::guard('customer')->user()->name }}
                        </div>
                        <div class="info-item">
                            <strong>{{ trans('core/base::layouts.email')}}:</strong> {{ Auth::guard('customer')->user()->email }}
                        </div>
                        <div class="info-item">
                            <strong>{{ trans('core/base::layouts.phone')}}:</strong> {{ Auth::guard('customer')->user()->phone ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trạng thái KYC hiện tại -->
            <div class="mb-5">
                <h5 class="border-bottom pb-2 mb-3">{{ trans('core/base::layouts.kyc_status')}}</h5>
                @if($kycPending)
                    <div class="kyc-status">
                        <div class="mb-3">
                            <strong>{{ trans('core/base::layouts.status')}}:</strong>
                            <span class="badge {{ $kycPending->status == 'approved' ? 'bg-success' : ($kycPending->status == 'pending' ? 'bg-warning' : 'bg-danger') }} text-white">
                                {{ ucfirst($kycPending->status) }}
                            </span>
                        </div>
                        <div class="mb-3">
                            <strong>{{ trans('core/base::layouts.verification_type')}}:</strong> {{ $kycPending->verification_type }}
                        </div>
                        <div class="mb-3">
                            <strong>@lang('plugins/marketplace::marketplace.submitted_at'):</strong> {{ optional($kycPending->created_at)->format('d-m-Y H:i') ?? 'N/A' }}
                        </div>
                        @if($kycPending->status == 'rejected' && $kycPending->logs->where('action', 'rejected')->first())
                            <div class="mb-3">
                                <strong>{{ trans('core/base::layouts.rejection_reason') }}:</strong>
                                <span class="text-danger">{{ $kycPending->logs->where('action', 'rejected')->first()->reason ?? 'N/A' }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-muted">{{ trans('core/base::layouts.you_have_not_submitted_a_kyc_request_yet') }}.</p>
                @endif
            </div>

            <!-- Form gửi KYC (hiển thị nếu chưa có KYC hoặc KYC bị từ chối) -->
            @if(!$kycPending || $kycPending->status == 'rejected')
                <div class="mb-5">
                    <h5 class="border-bottom pb-2 mb-3">@lang('plugins/marketplace::marketplace.submit_new_kyc_request')</h5>

                    <!-- Hiển thị thông báo -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Form KYC -->
                    {{-- {{ route('customer.kyc.submit') }} --}}
                    <form action="{{route('kyc.submit')}}" method="POST" enctype="multipart/form-data" id="kycForm">
                        @csrf

                        <!-- Chọn loại biểu mẫu KYC -->
                        <div class="mb-4">
                            <label for="kyc_form_id" class="form-label">@lang('plugins/marketplace::marketplace.kyc_form_type') <span class="text-danger">*</span></label>
                            <select name="kyc_form_id" id="kyc_form_id" class="form-select @error('kyc_form_id') is-invalid @enderror" required>
                                <option value="">@lang('plugins/marketplace::marketplace.select_a_kyc_form_type')</option>
                                @foreach ($kycForms as $form)
                                    <option value="{{ $form->id }}" {{ old('kyc_form_id') == $form->id ? 'selected' : '' }}>
                                        {{ $form->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kyc_form_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Các trường động sẽ được thêm vào đây -->
                        <div id="dynamicFields"></div>

                        <!-- Nút gửi -->
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                                    <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z"/>
                                </svg>
                                @lang('plugins/marketplace::marketplace.submit_kyc')
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const kycFormSelect = document.getElementById('kyc_form_id');
    const dynamicFieldsContainer = document.getElementById('dynamicFields');

    // Dữ liệu kyc_forms từ server
    const kycForms = @json($kycForms);

    // Thông tin người dùng để tự động điền
    const userInfo = {
        full_name: @json(Auth::guard('customer')->user()->name),
        phone: @json(Auth::guard('customer')->user()->phone ?? ''),
    };

    kycFormSelect.addEventListener('change', function () {
        const selectedFormId = this.value;
        dynamicFieldsContainer.innerHTML = ''; // Xóa các trường hiện tại

        if (selectedFormId) {
            const selectedForm = kycForms.find(form => form.id == selectedFormId);
            if (selectedForm) {
                const formData = JSON.parse(selectedForm.form);

                // Tạo các trường động
                formData.field_name.forEach((fieldName, index) => {
                    const fieldType = formData.type[index];
                    const fieldValidation = formData.validation[index];
                    const fieldLength = formData.field_length[index];
                    const isRequired = fieldValidation.includes('required');

                    // Chuẩn hóa tên trường để sử dụng trong name attribute
                    const fieldKey = fieldName.toLowerCase().replace(/\s+/g, '_');

                    // Giá trị mặc định
                    let defaultValue = '';
                    if (fieldKey === 'full_name') {
                        defaultValue = userInfo.full_name;
                    } else if (fieldKey === 'phone') {
                        defaultValue = userInfo.phone;
                    }

                    // Tạo HTML cho trường
                    const fieldHtml = `
                        <div class="mb-4">
                            <label for="${fieldKey}" class="form-label">${fieldName} ${isRequired ? '<span class="text-danger">*</span>' : ''}</label>
                            ${
                                fieldType === 'file'
                                    ? `<input type="file" name="data[${fieldKey}]" id="${fieldKey}" class="form-control" accept="image/*" ${isRequired ? 'required' : ''}>`
                                    : `<input type="text" name="data[${fieldKey}]" id="${fieldKey}" class="form-control" maxlength="${fieldLength}" ${isRequired ? 'required' : ''} value="${defaultValue}">`
                            }
                        </div>
                    `;

                    dynamicFieldsContainer.insertAdjacentHTML('beforeend', fieldHtml);
                });
            }
        }
    });

    // Kích hoạt sự kiện change ngay khi tải trang nếu đã có giá trị được chọn
    if (kycFormSelect.value) {
        kycFormSelect.dispatchEvent(new Event('change'));
    }
});
</script>

<style>
    .container-wrapper {
        padding: 20px;
        background-color: #f5f7fa;
        min-height: 100vh;
    }

    .card {
        background-color: #fff;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .card-header {
        padding: 20px;
        background-color: #f8f9fa;
    }

    .card-body {
        padding: 30px;
    }

    /* Tùy chỉnh thông tin người dùng */
    .info-item {
        margin-bottom: 15px;
        font-size: 14px;
        color: #374151;
    }

    .info-item strong {
        display: inline-block;
        width: 100px;
        font-weight: 600;
        color: #1f2937;
    }

    /* Tùy chỉnh avatar */
    .avatar-wrapper {
        width: 100px;
        height: 100px;
        margin: 0 auto;
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .avatar-img:hover {
        border-color: #007bff;
        transform: scale(1.05);
    }

    .avatar-placeholder {
        width: 100%;
        height: 100%;
        background-color: #e2e8f0;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 40px;
        font-weight: 600;
        border: 3px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .avatar-placeholder:hover {
        border-color: #007bff;
        transform: scale(1.05);
    }

    /* Tùy chỉnh trạng thái KYC */
    .kyc-status {
        font-size: 14px;
        color: #374151;
    }

    .kyc-status strong {
        display: inline-block;
        width: 150px;
        font-weight: 600;
        color: #1f2937;
    }

    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        text-transform: capitalize;
        transition: all 0.3s ease;
    }

    .badge.bg-success {
        background-color: #10b981 !important;
    }

    .badge.bg-warning {
        background-color: #f59e0b !important;
        color: #fff !important;
    }

    .badge.bg-danger {
        background-color: #ef4444 !important;
    }

    .text-danger {
        color: #ef4444 !important;
    }

    .text-muted {
        color: #6b7280 !important;
    }

    /* Tùy chỉnh form */
    .form-label {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 10px 15px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        outline: none;
    }

    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #ef4444;
    }

    .invalid-feedback {
        font-size: 12px;
        color: #ef4444;
    }

    /* Tùy chỉnh nút Submit */
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 123, 255, 0.2);
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    .btn-primary svg {
        margin-right: 5px;
    }

    /* Tùy chỉnh thông báo */
    .alert {
        border-radius: 8px;
        font-size: 14px;
    }

    .alert-success {
        background-color: #d1fae5;
        color: #065f46;
    }

    .alert-danger {
        background-color: #fee2e2;
        color: #991b1b;
    }

    /* Tùy chỉnh tiêu đề phần */
    h5 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container-wrapper {
            padding: 10px;
        }

        .card-header {
            padding: 15px;
        }

        .card-body {
            padding: 20px;
        }

        .avatar-wrapper {
            width: 80px;
            height: 80px;
        }

        .avatar-placeholder {
            font-size: 30px;
        }

        .info-item, .kyc-status {
            font-size: 12px;
        }

        .info-item strong, .kyc-status strong {
            width: 80px;
        }

        .form-control, .form-select {
            font-size: 12px;
            padding: 8px 12px;
        }

        .btn-primary {
            padding: 6px 12px;
            font-size: 12px;
        }
    }
</style>
@endsection

@push('footer')
  <script>
    'use strict';

    var BotbleVariables = BotbleVariables || {};
    BotbleVariables.languages = BotbleVariables.languages || {};
    BotbleVariables.languages.reports = {!! json_encode(trans('plugins/ecommerce::reports.ranges'), JSON_HEX_APOS) !!}
  </script>
@endpush