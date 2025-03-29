@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="container-fluid p-0 m-0 vh-100 d-flex flex-column">
        <div class="card m-0 border-0 shadow flex-grow-1">
            <div class="card-header bg-primary text-white py-3">
                <div class="container">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-white fw-bold">{{ trans('core/base::layouts.edit_commission_percentage')}}</h5>
                    </div>
                </div>
            </div>
            <div class="card-body p-4 bg-light">
                <form method="POST" action="{{route('referralcommission.update')}}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="direct" class="form-label fw-semibold">{{ trans('core/base::layouts.direct_referral_commission')}} (%)</label>
                        <input type="text" class="form-control @error('direct') is-invalid @enderror" id="direct" name="direct" value="{{ old('direct', $direct) }}" required>
                        @error('direct')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="indirect" class="form-label fw-semibold">{{ trans('core/base::layouts.indirect_referral_commission')}} (%)</label>
                        <input type="text" class="form-control @error('indirect') is-invalid @enderror" id="indirect" name="indirect" value="{{ old('indirect', $indirect) }}" required>
                        @error('indirect')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <span class="commission-note">{{ trans('core/base::layouts.indirect_referral_commissions_from_direct_referral_commissions')}}</span>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('referral.index') }}" class="btn btn-outline-secondary">{{ trans('core/base::layouts.cancel')}}</a>
                        <button type="submit" class="btn btn-primary">{{ trans('core/base::layouts.save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/jquery-ui.min.css') }}">
@endpush

@push('style')
    <style>
        .card {
            border-radius: 0;
            background-color: #ffffff;
        }
        .form-control {
            border-radius: 5px;
        }
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        .btn {
            border-radius: 5px;
        }
        .commission-note {
            display: block;
            margin-top: -0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.9em;
            color: #6c757d; /* Màu xám nhạt */
            font-style: italic;
            padding-left: 0.5rem;
        }
    </style>
@endpush

@push('js')
    <script src="{{ asset('assets/global/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/jquery-ui.min.js') }}"></script>
@endpush