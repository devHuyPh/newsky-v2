{!! apply_filters(RENDER_PRODUCTS_IN_CHECKOUT_PAGE, $products) !!}
<input type="text" hidden name="" id="from_city" value="{{setting('ecommerce_store_city')}}">
<input type="text" hidden name="" id="from_ward" value="{{setting('ecommerce_store_ward')}}">

{{-- @dd($sessionCheckoutData['discountPercentageDowline']) --}}
@forEach($products as $product)
    <input type="text" hidden name="" id="{{$product->store_id}}_store_id" value="{{$product->store_id}}">
    <input type="text" hidden name="" id="{{$product->id}}_name" value="{{$product->name}}">
    <input type="text" hidden name="" id="{{$product->id}}_quantity">
    <input type="text" hidden name="" id="{{$product->id}}_length" value="{{$product->length}}">
    <input type="text" hidden name="" id="{{$product->id}}_width" value="{{$product->wide}}">
    <input type="text" hidden name="" id="{{$product->id}}_height" value="{{$product->height}}">
    <input type="text" hidden name="" id="{{$product->id}}_weight" value="{{$product->weight}}">
@endforeach
<div class="mt-2 p-2">
    <div class="row">
        <div class="col-6">
            <p>{{ __('Subtotal') }}:</p>
        </div>
        <div class="col-6">
            <p class="price-text sub-total-text text-end">
                {{ format_price(Cart::instance('cart')->rawSubTotal()) }}
            </p>
        </div>
    </div>
    @if (EcommerceHelper::isTaxEnabled())
        <div class="row">
            <div class="col-6">
                <p>{{ __('Tax') }} @if (Cart::instance('cart')->rawTax())
                    (<small>{{ Cart::instance('cart')->taxClassesName() }}</small>)
                @endif</p>
            </div>
            <div class="col-6 float-end">
                <p class="price-text tax-price-text">
                    {{ format_price(Cart::instance('cart')->rawTax()) }}
                </p>
            </div>
        </div>
    @endif
    @if (session('applied_coupon_code'))
        <div class="row coupon-information">
            <div class="col-6">
                <p>{{ __('Coupon code') }}:</p>
            </div>
            <div class="col-6">
                <p class="price-text coupon-code-text">
                    {{ session('applied_coupon_code') }}
                </p>
            </div>
        </div>
    @endif
    @if ($couponDiscountAmount > 0)
        <div class="row price discount-amount">
            <div class="col-6">
                <p>{{ __('Coupon code discount amount') }}:</p>
            </div>
            <div class="col-6">
                <p id="couponDiscountAmount" class="price-text total-discount-amount-text" data-code="{{$couponDiscountAmount}}">
                    - {{ format_price($couponDiscountAmount) }}
                </p>
            </div>
        </div>
    @endif
    @if ($promotionDiscountAmount > 0)
        <div class="row">
            <div class="col-6">
                <p>{{ __('Promotion discount amount') }}:</p>
            </div>
            <div class="col-6">
                <p class="price-text">
                    {{ format_price($promotionDiscountAmount) }}
                </p>
            </div>
        </div>
    @endif
    @if(setting('shipping_ghn_status') == 1)
        <div class="row">
            <div class="col-6">
                <p>{{ __('Shipping fee') }}:</p>
            </div>
            <div class="col-6 float-end">
                <p id="shipping_amount" class="price-text shipping-price-text"></p>
            </div>
        </div>
    {{-- @else
        @if (!empty($shipping) && Arr::get($sessionCheckoutData, 'is_available_shipping', true))
            <div class="row">
                <div class="col-6">
                    <p>{{ __('Shipping fee') }}:</p>
                </div>
                <div class="col-6 float-end">
                    <p class="price-text shipping-price-text">{{ format_price($shippingAmount) }}</p>
                </div>
            </div>
        @endif --}}
    @endif
    <div id="p_discount_percentage_dowline" style="display: none;">
        <div class="row">
            <div class="col-6">
                <p>{{ __('Chiết khấu') }}:</p>
            </div>
            <div class="col-6 float-end">
                <p id="discountper" class="price-text shipping-price-text"></p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <p><strong>{{ __('Total') }}</strong>:</p>
        </div>
        <div class="col-6 float-end">
            <p id="total_amount" class="total-text raw-total-text" data-price="{{ format_price($rawTotal, null, true) }}">
                {{ format_price($orderAmount) }}
            </p>
            
        </div>
    </div>
    <form id="checkout_form">
        <input name="token" hidden id="token_checkout" value="">
        <input name="shipping_amount_inp" hidden id="shipping_amount_inp" value="">
        <input name="total_amount_ipn" hidden id="total_amount_ipn" value="">
        <input name="couponValue" hidden id="couponValue" value="">
        <input name="couponType" hidden id="couponType" value="">
        
        <input name="" hidden id="discount_percentage_dowline" value="{{session('percent_'.session('order_token'))}}">
        <input name="" hidden id="discount_value" value="">
    </form>
</div>

