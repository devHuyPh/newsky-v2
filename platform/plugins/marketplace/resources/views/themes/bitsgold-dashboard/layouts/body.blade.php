@php
  $customer = auth('customer')->user();
  if (!empty($customer->rank_id)) {
      $ranking = \App\Models\Ranking::findOrFail($customer->rank_id);
  }
@endphp

<header class="header--mobile">
  <div class="header__left">
    <button class="ps-drawer-toggle">
      <x-core::icon name="ti ti-menu-2" />
    </button>
  </div>
  <div class="header__center">
    @if (!empty($customer->avatar))
      <img src="{{ asset($customer->avatar) }}">
    @endif
  </div>
  <div class="header__right">
    <a class="header__site-link" href="{{ route('customer.logout') }}">
      <x-core::icon name="ti ti-logout" />
    </a>
  </div>
</header>
<aside class="ps-drawer--mobile">
  <div class="ps-drawer__header">
    <h4 class="fs-3 mb-0">Menu</h4>
    <button class="ps-drawer__close">
      <x-core::icon name="ti ti-x" />
    </button>
  </div>
  <div class="ps-drawer__content">
    @include(MarketplaceHelper::viewPath('bitsgold-dashboard.layouts.menu'))
  </div>
</aside>
<div class="ps-site-overlay"></div>
<main class="ps-main">
  <div class="ps-main__sidebar pt-0">
    <div class="ps-sidebar">
      <!-- Bootstrap Responsive Sidebar with Mobile Optimizations -->
      <div class="card shadow-sm border-0">
        <!-- User Profile Section - Optimized for Mobile -->
        <div class="card-header bg-primary bg-gradient text-white">
          <div class="d-flex flex-column flex-md-row align-items-center">
            <div class="mb-3 mb-md-0 text-center text-md-start">
              <img src="{{ asset('storage/' . $customer->avatar) }}" class="rounded-circle" width="60"
                height="60">
            </div>
            <div class="ms-md-3 text-center text-md-start">
              <h5 class="mb-0">{{ __('Hello') }}, {{ $customer->name }}</h5>
              <small
                class="text-white-50">{{ __('Joined on :date', ['date' => $customer->created_at->translatedFormat('M d, Y')]) }}</small>
            </div>
          </div>
        </div>

        <!-- Rank Section - Optimized for Mobile -->
        <div class="card-body border-bottom">
          <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
            <div class="d-flex align-items-center mb-2 mb-md-0">
              <img src="{{ asset($ranking->rank_icon ?? '') }}" width="40" alt="{{ $ranking->rank_name ?? '' }}"
                class="me-2">
              <div>
                <h4 class="mb-0 text-primary">{{ $ranking->rank_name ?? '' }}</h4>
              </div>
            </div>
          <span class="badge border-success text-black border-1 rounded-pill shadow-lg position-relative d-inline-block" style="
    background: #6bdf13 linear-gradient(263deg, #dbdab8, #0a583c00);
">
    {{ __('Active') }}
   
</span>
          </div>
        </div>
      </div>
      <div class="ps-sidebar__content">
        <div class="ps-sidebar__center">
          @include(MarketplaceHelper::viewPath('bitsgold-dashboard.layouts.menu'))
        </div>
        <div class="ps-sidebar__footer">
          <div class="ps-copyright">
            @php $logo = theme_option('logo_vendor_dashboard', theme_option('logo')); @endphp

            {{-- @if ($logo) --}}
            {{-- <a href="{{ BaseHelper::getHomepageUrl() }}" title="{{ $siteTitle = theme_option('site_title') }}"> --}}
            {{-- <img src="{{ RvMedia::getImageUrl($logo) }}" alt="{{ $siteTitle }}" style="max-height: 40px;"> --}}
            {{-- </a> --}}
            {{-- @endif --}}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="ps-main__wrapper" id="bitsgold-dashboard">
    <header class="d-flex justify-content-between align-items-center mb-3">
      <div class="container d-flex">
        <div class="col-xl-3">
          <h3 class="fs-1 mb-0 text-truncate me-3">{{ __(ucfirst(Request::segment(2))) }}</h3>
        </div>
        <div class="col-xl-9">
          <div class="d-flex align-items-center justify-content-end">
            @if (is_plugin_active('language'))
              {!! apply_filters(
                  'marketplace_vendor_dashboard_language_switcher',
                  view(MarketplaceHelper::viewPath('bitsgold-dashboard.partials.language-switcher'))->render(),
              ) !!}
            @endif

            <div class="d-none d-md-inline-block">
              <a href="{{ BaseHelper::getHomepageUrl() }}" target="_blank" class="text-uppercase d-block">
                <h2>
                  <x-core::icon name="ti ti-home" class="text-primary" />
                </h2>
              </a>
            </div>
          </div>
        </div>
      </div>
    </header>
    <div id="app">
      @yield('content')
    </div>
  </div>
</main>
