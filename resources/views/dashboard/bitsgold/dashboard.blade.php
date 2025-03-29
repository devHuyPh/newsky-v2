@extends('plugins/marketplace::themes.bitsgold-dashboard.layouts.master')

@section('content')
  <section class="py-5">
    <div class="container">
    <div class="row g-4">
      <div class="col-12 col-md-4">
      <div class="card bg-primary text-white border-0 rounded-3 shadow">
        <div class="card-body d-flex align-items-center">
        <div class="rounded-3 p-3 me-3">
          <i class="fas fa-wallet text-white fs-3"></i>
        </div>
        <div>
          <h6 class="card-title text-white mb-1">@lang('plugins/marketplace::marketplace.total_balance')</h6>
          <h3 class="card-text text-white mb-0">{{number_format($customer->walet_1+$customer->walet_2)}} VND</h3>
        </div>
        </div>
      </div>
      </div>
      <div class="col-12 col-md-4">
      <div class="card bg-primary text-white border-0 rounded-3 shadow">
        <div class="card-body d-flex align-items-center">
        <div class="rounded-3 p-3 me-3">
          <i class="fas fa-wallet text-white fs-3"></i>
        </div>
        <div>
          <h6 class="card-title text-white mb-1">@lang('plugins/marketplace::marketplace.main_wallet')</h6>
          <h3 class="card-text text-white mb-0">{{number_format($customer->walet_1)}} VND</h3>
        </div>
        </div>
      </div>
      </div>
      <div class="col-12 col-md-4">
      <div class="card bg-primary text-white border-0 rounded-3 shadow">
        <div class="card-body d-flex align-items-center">
        <div class="rounded-3 p-3 me-3">
          <i class="fas fa-wallet text-white fs-3"></i>
        </div>
        <div>
          <h6 class="card-title text-white mb-1">@lang('plugins/marketplace::marketplace.profit_wallet')</h6>
          <h3 class="card-text text-white mb-0">{{number_format($customer->walet_2)}} VND</h3>
        </div>
        </div>
      </div>
      </div>
    </div>
    </div>
  </section>
@endsection

@push('footer')
  <script>
    'use strict';

    var BotbleVariables = BotbleVariables || {};
    BotbleVariables.languages = BotbleVariables.languages || {};
    BotbleVariables.languages.reports = {!! json_encode(trans('plugins/ecommerce::reports.ranges'), JSON_HEX_APOS) !!}
  </script>
@endpush
