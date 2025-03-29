@if(
    is_plugin_active('announcement')
    && ! \ArchiElite\Announcement\AnnouncementHelper::isHideOnMobile()
    && \ArchiElite\Announcement\Models\Announcement::query()->available()->exists()
)
    <style>
        @media (max-width: 767px) {
            .tp-header-transparent {
                top: 3.5rem;
            }

            /* .tp-header-action-btn {
                display: none !important; 
            } */

            .tp-header-action-item a[href*="compare"] {
                display: none !important;
            }

            .logo__mobile {
                width: 42px;
            }

        }
        .offcanvas {
            z-index: 9999 !important; /* Đặt z-index cao nhất */
        }

        #ghn-error-alert, #notify-success-alert {
            /* display: none; Xóa !important */
            position: fixed;
            top: 20px;
            right: 20px;
            width: 400px;
            z-index: 9999;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }

        .notify-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 400px;
            display: none;
            animation: slideDown 0.5s ease-out;
        }

        .hidden-important {
            animation: fadeOut 1s ease-out;
            opacity: 0;
        }

    </style>
@endif

<header>
    {!! Theme::partial('header.top', ['colorMode' => 'light', 'headerTopClass' => 'container-fluid pl-45 pr-45', 'showUserMenu' => true]) !!}
    <div
        id="header-sticky"
        @class(['tp-header-area tp-header-sticky has-dark-logo tp-header-height', 'header-main' => ! Theme::get('hasSlider'), 'tp-header-style-transparent-white tp-header-transparent' => Theme::get('hasSlider')])
        {!! Theme::partial('header.sticky-data') !!}
    >
        <div class="tp-header-bottom-3 pl-35 pr-35" style="background-color: {{ $headerMainBackgroundColor }}; color: {{ $headerMainTextColor }}">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-xl-2 col-lg-2 col-6">
                        {!! Theme::partial('header.logo', ['hasLogoLight' => true]) !!}
                    </div>
                    <div class="col-xl-8 col-lg-8 d-none d-lg-block">
                        <div class="main-menu menu-style-3 p-relative d-flex align-items-center justify-content-center">
                            <nav class="tp-main-menu-content">
                                {!! Menu::renderMenuLocation('main-menu', ['view' => 'main-menu']) !!}
                            </nav>
                        </div>
                        @if(is_plugin_active('ecommerce'))
                            <div class="tp-category-menu-wrapper d-none">
                                <nav class="tp-category-menu-content">
                                    {!! Theme::partial('header.categories-dropdown') !!}
                                </nav>
                            </div>
                        @endif
                    </div>
                    <div class="col-xl-2 col-lg-2 col-6">
                        @php
                            $customerCheck = auth('customer')->check();
                            $customer = auth('customer')->user();
                        @endphp
                        @if ($customerCheck && $customer)
                        {{-- @dd($customer->notificationCount()) --}}
                        <div class="row row-cols-2 row-cols-md-2 gx-0 align-items-center">

                            <div class="col order-2 order-md-1">
                                {!! Theme::partial('header.actions', ['class' => 'justify-content-end ml-50', 'showSearchButton' => true]) !!}
                            </div>
                            <div class="col order-1 order-md-2 text-end text-md-start ps-md-3">
                                <div class="tp-header-action-item">
                                    <button type="button" class="tp-header-action-btn" 
                                        data-bs-toggle="offcanvas" 
                                        data-bs-target="#notificationRight"
                                        aria-controls="notificationRight">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22" viewBox="0 0 21 22" fill="none"  
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"  
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-bell">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                                            <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                                        </svg>
                                        <span class="tp-header-action-badge">
                                            {{$customer->notificationCount()}}
                                        </span>
                                    </button>
                                </div>
                                
                                <div class="offcanvas offcanvas-end" tabindex="-1" id="notificationRight" aria-labelledby="offcanvasRightLabel">
                                    <div class="offcanvas-header mt-5">
                                        <h4 id="offcanvasRightLabel">Thông báo</h4>
                                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                    </div>
                                    <hr>
                                    <div class="offcanvas-body">
                                        @if (!$customer->notifications->isEmpty())
                                            @foreach ($customer->notifications as $notification)
                                                <a href="{{$notification->url}}" class="notification text-start">
                                                    <h6>{{$notification->title}}</h6>
                                                    <p>{{$notification->created_at->format('d-m-Y H:i')}}</p>
                                                    <p>{{$notification->dessription}}</p>
                                                </a>
                                                <hr>
                                            @endforeach
                                        @else
                                            <p>Không có thông báo mới.</p>
                                        @endif
                                    </div>
                                </div>
                                
                                
                            </div>

                        
                        </div>
                        @else
                        <div class="row row-cols-2 row-cols-md-2 gx-0 align-items-center">
                            <div class="col order-2 order-md-1">
                                {!! Theme::partial('header.actions', ['class' => 'justify-content-end ml-50', 'showSearchButton' => true]) !!}
                            </div>
                            <div class="col order-1 order-md-2 text-end text-md-start ps-md-3">
                                <div class="tp-header-action-item">
                                    <a class="tp-header-action-btn" 
                                    href="{{route('customer.login')}}">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="21"  height="22"  viewBox="0 0 21 22"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
               
                    </div>
                    
                    {{-- ---------- --}}
                </div>
            </div>
        </div>
    </div>
</header>


