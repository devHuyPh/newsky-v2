@extends('plugins/marketplace::themes.bitsgold-dashboard.layouts.master')
@php
  $url = request()->getSchemeAndHttpHost() . '/register' . '/' . $user['uuid_code'];

  // Mã hóa URL để sử dụng trong API
  $encodedUrl = urlencode($url);
  // QRCode Monkey API
  $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={$encodedUrl}&download=true";
@endphp
@section('content')
  <section class="referral-section py-5">
    <div class="container">
      <!-- Referral Link Box -->
      <div class="row">
        <div class="col-12">
          <div class="card text-white shadow-lg rounded-3 p-4">
            <h4 class="text-success fw-bold mb-4">@lang('plugins/marketplace::marketplace.introduction_link')</h4>
            <div class="input-group mb-4">
              <input type="text" value="{{ $url ?? '' }}"
                class="form-control bg-black text-white border-0 rounded-start" id="sponsorURL" readonly />
              <button class="btn btn-success fw-bold rounded-end" id="copyBoard" onclick="copyFunction()">
                <i class="fa fa-copy m-auto"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- QR Code Section -->
      <div class="row mt-4">
        <div class="col-12 text-center">
          <img src="{{ $qrCodeUrl }}" alt="QR Code" class="img-fluid rounded-3" style="max-width: 200px;">
          <div class="mt-3">
            <a href="{{ $qrCodeUrl }}" download class="btn btn-success fw-bold rounded-3">
              <i class="fa fa-download m-auto"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- Level Tabs and Table -->
      <div class="row mt-5">
        <div class="col-12">
          <div class="d-flex flex-column flex-md-row">
            <!-- Tabs for Levels (Vertical on Desktop, Dropdown on Mobile) -->
            <div class="col-md-2 mb-3 mb-md-0" style="padding-right: 4px;padding-top: 50px;">
              <!-- Desktop View: Vertical Tabs -->
              <div class="d-none d-md-block">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                  @foreach ($referrals as $key => $value)
                    <a style="height: 45px;" class="nav-link {{ $key == 1 ? 'active' : '' }} bg-white text-black rounded-3 mb-2 border text-center py-3 text-center"
                      id="v-pills-level{{ $key }}-tab" data-bs-toggle="pill"
                      href="#v-pills-level{{ $key }}" role="tab"
                      aria-controls="v-pills-level{{ $key }}" aria-selected="{{ $key == 1 ? 'true' : 'false' }}">
                    {{ trans('plugins/marketplace::marketplace.Level') }} {{ $key }}
                    </a>
                  @endforeach
                </div>
              </div>
              <!-- Mobile View: Dropdown -->
              @if(!empty($referrals))
              <div class="d-block d-md-none">
                <select class="form-select text-black rounded-3 mb-3 border" id="levelSelect"
                  onchange="showTab(this.value)">
                  @foreach ($referrals as $key => $value)
                    <option value="v-pills-level{{ $key }}" {{ $key == 1 ? 'selected' : '' }}>
                      {{__('plugins/marketplace::marketplace.Level')}} {{ $key }}
                    </option>
                  @endforeach
                </select>
              </div>
              @endif
            </div>
            <!-- Tab Content -->
            <div class="col-md-10">
              <div class="tab-content" id="v-pills-tabContent">
                @foreach ($referrals as $key => $value)

                  <div class="tab-pane fade {{ $key == 1 ? 'show active' : '' }}" id="v-pills-level{{ $key }}"
                    role="tabpanel" aria-labelledby="v-pills-level{{ $key }}-tab">
                    <div class="table-responsive">
                      <table class="table table-hover bg-white">
                        <thead class=" text-white bg-black">
                          <tr>
                            <th scope="col" class="px-4 py-3">@lang('plugins/marketplace::marketplace.username')</th>
                            <th scope="col" class="px-4 py-3">@lang('plugins/marketplace::marketplace.email')</th>
                            <th scope="col" class="px-4 py-3">@lang('plugins/marketplace::marketplace.phonenumber')</th>
                            <th scope="col" class="px-4 py-3">@lang('plugins/marketplace::marketplace.datejoined')</th>
                          </tr>
                        </thead>
                        <tbody class="text-dark">
                          @if (count($value) > 0)
                            @foreach ($value as $index => $item)
                              @php
                                $rank = null;
                                if(!empty($item->rank_id)) {
                                  $rank = \App\Models\Ranking::findOrFail($item->rank_id);
                                }
                              @endphp
                              <tr>
                                <td data-label="Username" class="px-2 py-3">
                                {{ $item->name }}<br/>
                                 <span class="text-center "><img src="{{ asset($rank?->rank_icon) }}" width="28px" height="28px" class="rounded-circle"
                                      alt="{{ $rank?->rank_name }}"><span class="mx-2 text-primary">{{ $rank?->rank_name }}</span></span>
                                </td>
                                <td data-label="Email" class="px-2 py-3">{{ $item->email }}</td>
                                <td data-label="Số điện thoại" class="px-2 py-3">{{ $item->phone ?? '-' }}</td>
                                <td data-label="Ngày tham gia" class="px-2 py-3">{{ $item->created_at }}</td>
                              </tr>
                            @endforeach
                          @else
                            <tr>
                              <td colspan="4" class="text-center px-2 py-3">Không có dữ liệu</td>
                            </tr>
                          @endif
                        </tbody>
                      </table>
                    </div>
                  </div>
                @endforeach
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
    function showTab(tabId) {
      // Hide all tab panes
      document.querySelectorAll('.tab-pane').forEach(function(pane) {
        pane.classList.remove('show', 'active');
      });

      // Show the selected tab pane
      document.getElementById(tabId).classList.add('show', 'active');
    }

    function copyFunction() {
      const url = document.getElementById('sponsorURL').value;

      // Lấy button element
      const copyButton = document.getElementById('copyBoard');

      // Sao chép vào clipboard
      navigator.clipboard.writeText(url)
        .then(() => {
          copyButton.innerHTML = '<i class="fa fa-copy text-dark m-auto"></i>';
        })
        .catch(err => {
          console.error('Lỗi khi sao chép: ', err);
          alert('Không thể sao chép liên kết!');
        });
    }
  </script>
@push('footer')
    <script>
        'use strict';

        var BotbleVariables = BotbleVariables || {};
        BotbleVariables.languages = BotbleVariables.languages || {};
        BotbleVariables.languages.reports = {
            !!json_encode(trans('plugins/ecommerce::reports.ranges'), JSON_HEX_APOS) !!
        }
    </script>
@endpush
