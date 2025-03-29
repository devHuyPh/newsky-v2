@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="card card-primary m-0 shadow-lg h-100" style="border-radius: 0;">
        <div class="card-header bg-gradient-primary text-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fas fa-crown me-2"></i>{{ trans('core/base::layouts.create_new_rank')}}</h5>
            <a href="{{ route('rank.index') }}" class="btn btn-outline-dark btn-sm rounded-pill">
                <i class="fas fa-arrow-left me-2"></i>{{ trans('core/base::layouts.back')}}
            </a>
        </div>

        <div class="card-body p-4 bg-light overflow-auto">
            <form method="post" action="{{ route('rank.store') }}" class="needs-validation h-100 d-flex flex-column" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="row g-3 flex-grow-1">
                    <!-- Basic Info -->
                    <div class="col-12"><h6 class="text-primary fw-bold border-bottom pb-2"><i class="fas fa-info-circle me-2"></i>{{ trans('core/base::layouts.basic_information')}}</h6></div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="rank_name" value="{{ old('rank_name') }}" class="form-control border-primary" id="rankName" placeholder=" " required>
                            <label for="rankName">{{ trans('core/base::layouts.rank_name')}}</label>
                            @error('rank_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="rank_lavel" value="{{ old('rank_lavel') }}" class="form-control border-primary" id="rankLevel" placeholder=" " required>
                            <label for="rankLevel">{{ trans('core/base::layouts.rank_level')}}</label>
                            @error('rank_lavel')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Maintain Rank -->
                    <div class="col-12 mt-3"><h6 class="text-success fw-bold border-bottom pb-2"><i class="fas fa-arrow-up me-2"></i>{{ trans('core/base::layouts.maintain_ranks')}}</h6></div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="number_referrals" value="{{ old('number_referrals') }}" class="form-control border-success" id="minInvest" placeholder=" " step="1" required>
                            <label for="minInvest">{{ trans('core/base::layouts.number_referrals')}}</label>
                            @error('number_referrals')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="total_revenue" value="{{ old('total_revenue') }}" class="form-control border-success" id="minDeposit" placeholder=" " step="1000000" required>
                            <label for="minDeposit">{{ trans('core/base::layouts.total_revenue')}}</label>
                            @error('total_revenue')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Demotion Conditions -->
                    <div class="col-12 mt-3"><h6 class="text-danger fw-bold border-bottom pb-2"><i class="fas fa-arrow-down me-2"></i>{{ trans('core/base::layouts.demotion_conditions')}}</h6></div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="demotion_investment" value="{{ old('demotion_investment') }}" class="form-control border-danger" id="demotionInvestment" placeholder=" " step="1000000" required>
                            <label for="demotionInvestment">{{ trans('core/base::layouts.total_revenue_if_not_achieved')}}</label>
                            @error('demotion_investment')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="demotion_referrals" value="{{ old('demotion_referrals') }}" class="form-control border-danger" id="demotionReferrals" placeholder=" " step="1" required>
                            <label for="demotionReferrals">{{ trans('core/base::layouts.number_of_referrals_if_not_met')}}</label>
                            @error('demotion_referrals')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="demotion_time_months" value="{{ old('demotion_time_months') }}" class="form-control border-danger" id="demotionTime" placeholder=" " step="1" required>
                            <label for="demotionTime">{{ trans('core/base::layouts.demotion_time_months')}}</label>
                            @error('demotion_time_months')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Rewards & Additional -->
                    <div class="col-12 mt-3"><h6 class="text-info fw-bold border-bottom pb-2"><i class="fas fa-gift me-2"></i>{{ trans('core/base::layouts.rewards_and_addtional')}}</h6></div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="percentage_reward" value="{{ old('percentage_reward') }}" class="form-control border-info" id="percentageReward" placeholder=" " step="0.01" required>
                            <label for="percentageReward">{{ trans('core/base::layouts.percentage_reward')}}</label>
                            @error('percentage_reward')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" checked>
                            <label class="form-check-label" for="statusSwitch">
                                <span class="badge bg-success px-2 py-1 text-light">{{ trans('core/base::layouts.active')}}</span>
                            </label>
                            @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <input type="file" name="rank_icon" id="image" class="form-control mb-2" accept="image/*">
                        <img id="image_preview_container" class="img-thumbnail rounded shadow-sm" src="{{ asset('default-image.png') }}" alt="Preview" style="max-height: 150px;">
                        @error('rank_icon')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <textarea name="description" class="form-control border-info" id="description" placeholder=" " style="height: 150px;">{{ old('description') }}</textarea>
                            <label for="description">{{ trans('core/base::layouts.description')}}</label>
                            @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm px-4">
                        <i class="fas fa-save me-2"></i>{{ trans('core/base::layouts.save_now')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        'use strict';
        $(document).ready(function() {
            $('#image').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => $('#image_preview_container').attr('src', e.target.result);
                reader.readAsDataURL(this.files[0]);
            });

            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });

            $('#statusSwitch').change(function() {
                $(this).next('label').find('.badge')
                    .toggleClass('bg-success bg-danger')
                    .text(this.checked ? {{ trans('core/base::layouts.active')}} : {{ trans('core/base::layouts.inactive')}});
            });
        });
    </script>
@endpush

@push('style')
    <style>
        html, body { height: 100%; margin: 0; padding: 0; }
        .bg-gradient-primary { background: linear-gradient(45deg, #007bff, #00b4db); }
        .card { height: 100%; }
        .form-floating > .form-control:focus { box-shadow: 0 0 10px rgba(0, 123, 255, 0.2); }
        .form-check-input:checked { background-color: #28a745; border-color: #28a745; }
        .btn-primary { transition: all 0.3s ease; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4); }
        .border-primary { border-color: #007bff !important; }
        .border-success { border-color: #28a745 !important; }
        .border-danger { border-color: #dc3545 !important; }
        .border-info { border-color: #17a2b8 !important; }
    </style>
@endpush
