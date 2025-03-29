@php
    Theme::set('breadcrumbHeight', 100);
    Theme::set('pageTitle', __('Register'));
@endphp

{!! $form->bannerDirection('horizontal')->renderForm() !!}

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Lấy username từ URL
        const url = window.location.pathname;
        const username = url.split('/').pop(); // Lấy phần cuối của URL (johndoe)
        // Tìm TextField và gán giá trị
        const referralField = document.getElementById('registerReferralID'); // Sửa id để khớp với HTML
        if (referralField && username && username !== 'register') {
            referralField.value = username;
        } else if (referralField) {
            referralField.value = 'N/A'; // Giá trị mặc định nếu không có username
        }
    });
</script>
