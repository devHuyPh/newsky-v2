@extends(MarketplaceHelper::viewPath('vendor-dashboard.layouts.master'))



@push('footer')
  <script>
    'use strict';

    var BotbleVariables = BotbleVariables || {};
    BotbleVariables.languages = BotbleVariables.languages || {};
    BotbleVariables.languages.reports = {!! json_encode(trans('plugins/ecommerce::reports.ranges'), JSON_HEX_APOS) !!}
  </script>
@endpush
