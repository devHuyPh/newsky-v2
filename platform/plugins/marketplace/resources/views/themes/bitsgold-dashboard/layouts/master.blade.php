<!DOCTYPE html>
<html {!! Theme::htmlAttributes() !!}>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="format-detection" content="telephone=no">
  <meta name="apple-mobile-web-app-capable" content="yes">

  @if ($favicon = theme_option('favicon'))
    {{ Html::favicon(RvMedia::getImageUrl($favicon)) }}
  @endif

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ucfirst(Request::segment(2))}} | Marketing</title>

  <style>
    :root {
      --primary-font: '{{ theme_option('primary_font', 'Muli') }}', sans-serif;
      --primary-color:
        {{ theme_option('primary_color', '#fab528') }}
      ;
    }

    /* Ensure the section takes full width */
    .refferal-link {
      padding: 15px;
    }

    /* Style the input group for better mobile display */
    .input-group {
      flex-wrap: nowrap;
    }

    .input-group .form-control {
      font-size: 14px;
    }

    .input-group .btn {
      font-size: 14px;
      white-space: nowrap;
    }

    /* Style the table for mobile */
    .table-responsive {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .table {
      width: 100%;
      min-width: 600px;
      /* Ensure the table is wide enough to scroll on mobile */
    }

    .table th,
    .table td {
      font-size: 14px;
      padding: 8px;
    }

    /* Style the dropdown for mobile */
    .form-select {
      width: 100%;
      font-size: 16px;
    }

    /* Hide vertical tabs on mobile and show dropdown */
    @media (max-width: 767.98px) {
      .d-none.d-md-block {
        display: none !important;
      }

      .d-block.d-md-none {
        display: block !important;
      }

      /* Adjust table font size for smaller screens */
      .table th,
      .table td {
        font-size: 12px;
        padding: 6px;
      }

      /* Ensure the input group button doesn't shrink too much */
      .input-group .btn {
        font-size: 12px;
        padding: 6px 10px;
      }
    }

    /* Ensure the table header stands out */
    .table thead th {
      position: sticky;
      top: 0;
      z-index: 1;
    }
    @import url(https://fonts.googleapis.com/css?family=Roboto:300,400,700&display=swap);

body {
    font-family: "Roboto", sans-serif;
    background: #EFF1F3;
    min-height: 100vh;
    position: relative;
}

.section-50 {
    padding: 50px 0;
}

.m-b-50 {
    margin-bottom: 50px;
}

.dark-link {
    color: #333;
}

.heading-line {
    position: relative;
    padding-bottom: 5px;
}

.heading-line:after {
    content: "";
    height: 4px;
    width: 75px;
    background-color: #29B6F6;
    position: absolute;
    bottom: 0;
    left: 0;
}

.notification-ui_dd-content {
    margin-bottom: 30px;
}

.notification-list {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: justify;
    -ms-flex-pack: justify;
    justify-content: space-between;
    padding: 20px;
    margin-bottom: 7px;
    background: #fff;
    -webkit-box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
}

.notification-list--unread {
    border-left: 2px solid #29B6F6;
}

.notification-list .notification-list_content {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
}

.notification-list .notification-list_content .notification-list_img img {
    height: 48px;
    width: 48px;
    border-radius: 50px;
    margin-right: 20px;
}

.notification-list .notification-list_content .notification-list_detail p {
    margin-bottom: 5px;
    line-height: 1.2;
}

.notification-list .notification-list_feature-img img {
    height: 48px;
    width: 48px;
    border-radius: 5px;
    margin-left: 20px;
}
  </style>
 


  @yield('header', view(MarketplaceHelper::viewPath('bitsgold-dashboard.layouts.header')))

  <script>
    window.siteUrl = "{{ BaseHelper::getHomepageUrl() }}";
  </script>

  <script type="text/javascript">
    'use strict';
    window.trans = Object.assign(window.trans || {}, JSON.parse('{!! addslashes(json_encode(trans('plugins/marketplace::marketplace'))) !!}'));

    var BotbleVariables = BotbleVariables || {};
    BotbleVariables.languages = {
      tables: {!! json_encode(trans('core/base::tables'), JSON_HEX_APOS) !!},
      notices_msg: {!! json_encode(trans('core/base::notices'), JSON_HEX_APOS) !!},
      pagination: {!! json_encode(trans('pagination'), JSON_HEX_APOS) !!},
      system: {
        character_remain: '{{ trans('core/base::forms.character_remain') }}'
      }
    };

    var RV_MEDIA_URL = {
      'media_upload_from_editor': '{{ route('marketplace.vendor.upload-from-editor') }}'
    };
  </script>

  @stack('header')
</head>

<body @if (session('locale_direction', 'ltr') == 'rtl') dir="rtl" @endif>

  @yield('body', view(MarketplaceHelper::viewPath('bitsgold-dashboard.layouts.body')))

  @stack('pre-footer')

  @if (
  session()->has('status') ||
  session()->has('success_msg') ||
  session()->has('error_msg') ||
  (isset($errors) && $errors->count() > 0) ||
  isset($error_msg)
  )
    <script type="text/javascript">
    'use strict';
    window.noticeMessages = [];
    @if (session()->has('success_msg'))
    noticeMessages.push({
      'type': 'success',
      'message': "{!! addslashes(session('success_msg')) !!}"
    });
  @endif
    @if (session()->has('status'))
    noticeMessages.push({
      'type': 'success',
      'message': "{!! addslashes(session('status')) !!}"
    });
  @endif
    @if (session()->has('error_msg'))
    noticeMessages.push({
      'type': 'error',
      'message': "{!! addslashes(session('error_msg')) !!}"
    });
  @endif
    @if (isset($error_msg))
    noticeMessages.push({
      'type': 'error',
      'message': "{!! addslashes($error_msg) !!}"
    });
  @endif
    @if (isset($errors))
    @foreach ($errors->all() as $error)
    noticeMessages.push({
      'type': 'error',
      'message': "{!! addslashes($error) !!}"
    });
  @endforeach
  @endif
    </script>
  @endif

  {!! Assets::renderFooter() !!}
  @yield('footer', view(MarketplaceHelper::viewPath('bitsgold-dashboard.layouts.footer')))

  @stack('scripts')
  @stack('footer')
  {{-- {!! apply_filters(THEME_FRONT_FOOTER, null) !!} --}}
</body>

</html>
