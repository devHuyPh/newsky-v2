@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="card card-primary m-0 m-md-4 my-4 m-md-0 shadow">
        <div class="card-body">
            <form method="post" action="{{route('address.update')}}" class="form-row align-items-center">
                @csrf
                @method('put')
                <div class="d-flex justify-content-between align-items-center">
                    <label class="form-check form-switch">{{ trans('core/base::layouts.address_verification')}}
                        <input name="address_v" type="hidden" value="0" />
                        <input class="form-check-input" name="address_v" type="checkbox" value="1" 
                        id="is_featured" {{ $address_v == 1 ? 'checked' : '' }} />
                    </label>
                    <label class="form-check form-switch">{{ trans('core/base::layouts.identity_verification')}}
                        <input name="identity_v" type="hidden" value="0" />
                        <input class="form-check-input" name="identity_v" type="checkbox" value="1" id="is_featured" {{$identity_v==1 ? 'checked' : ''}}/>
                    </label>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block btn-rounded mx-2 mt-4">
                            <span>Save Changes</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card has-actions has-filter">
        <div class="card-header">
            <div class="w-100 justify-content-between d-flex flex-wrap align-items-center gap-1">
                <div class="d-flex flex-wrap flex-md-nowrap align-items-center gap-1">
                    <div class="dropdown d-inline-block">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            {{ trans('core/base::layouts.bulk_actions')}}
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="http://127.0.0.1:8000/admin/tables/bulk-actions"
                                data-trigger-bulk-action="data-trigger-bulk-action" data-method="POST"
                                data-table-target="Botble\Ecommerce\Tables\ProductTable"
                                data-target="Botble\Table\BulkActions\DeleteBulkAction"
                                data-confirmation-modal-title="Confirm to perform this action"
                                data-confirmation-modal-message="Are you sure you want to do this action? This cannot be undone."
                                data-confirmation-modal-button="Delete" data-confirmation-modal-cancel-button="Cancel">
                                {{ trans('core/base::layouts.deactive')}}
                            </a>
                            <a class="dropdown-item" href="http://127.0.0.1:8000/admin/tables/bulk-actions"
                                data-trigger-bulk-action="data-trigger-bulk-action" data-method="POST"
                                data-table-target="Botble\Ecommerce\Tables\ProductTable"
                                data-target="Botble\Table\BulkActions\DeleteBulkAction"
                                data-confirmation-modal-title="Confirm to perform this action"
                                data-confirmation-modal-message="Are you sure you want to do this action? This cannot be undone."
                                data-confirmation-modal-button="Delete" data-confirmation-modal-cancel-button="Cancel">
                                {{ trans('core/base::layouts.delete')}}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <div class="d-inline-block">
                        <button class="btn buttons-collection dropdown-toggle action-item btn-primary"
                            data-bs-toggle="modal" data-bs-target="#createModal">
                            <svg class="icon svg-icon-ti-ti-plus" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg> {{ trans('core/base::layouts.create')}}
                        </button>
                    </div>
                    <button class="btn" type="button" data-bb-toggle="dt-buttons" data-bb-target=".buttons-reload"
                        tabindex="0" aria-controls="botble-ecommerce-tables-product-table">
                        <svg class="icon icon-left svg-icon-ti-ti-refresh" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                            <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                        </svg> {{ trans('core/base::layouts.reload')}}
                    </button>
                </div>
            </div>
        </div>

        <div class="card-table">
            <div class="table-responsive table-has-actions table-has-filter">
                <table class="table card-table table-vcenter table-striped table-hover"
                    id="botble-ecommerce-tables-product-table">
                    <thead>
                        <tr>
                            <th title="Checkbox"><input class="form-check-input m-0 align-middle table-check-all"
                                    data-set=".dataTable .checkboxes" name type="checkbox"></th>
                            <th title="ID" width="5" class="text-center no-column-visibility column-key-0">ID</th>
                            <th class="text-start column-key-1">{{ trans('core/base::layouts.identity_type') }}</th>
                            <th class="text-start column-key-2">{{ trans('core/base::layouts.status') }}</th>
                            <th class="text-center no-column-visibility text-nowrap sorting_disabled" rowspan="1"
                                colspan="1">{{ trans('core/base::layouts.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!$data)
                            <tr class="odd">
                                <td valign="top" colspan="8" class="dataTables_empty text-center">trans('core/base::layouts.no_data_found')</td>
                            </tr>
                        @else
                            @foreach ($data as $item)
                                <tr class="even">
                                    <td class="w-1 text-start no-column-visibility dtr-control"><input
                                            class="form-check-input m-0 align-middle checkboxes" type="checkbox" name="id[]"
                                            value="{{$item->id}}"></td>
                                    <td class="text-center no-column-visibility column-key-0 sorting_1">{{$item->id}}</td>
                                    <td class="text-start column-key-1">{{$item->name}}</td>
                                    <td class="text-start column-key-2">
                                        @if ($item->status == 1)
                                            <span class="badge bg-success text-success-fg">{{ trans('core/base::layouts.active')}}</span>
                                        @else
                                            <span class="badge bg-danger text-danger-fg">{{ trans('core/base::layouts.deactive')}}</span>
                                        @endif
                                    </td>
                                    <td class="text-center no-column-visibility text-nowrap">
                                        <div class="table-actions">
                                            <a href="#" class="btn btn-sm btn-icon btn-primary"
                                                data-bs-toggle="modal" data-bs-target="#updateFiled_{{$item->id}}">
                                                <svg class="icon svg-icon-ti-ti-edit" data-bs-toggle="tooltip"
                                                    data-bs-title="Edit" xmlns="http://www.w3.org/2000/svg" width="24"
                                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                                                    <path d="M16 5l3 3"></path>
                                                </svg>
                                                <span class="sr-only">{{ trans('core/base::layouts.edit')}}</span>
                                            </a>
                                            <form action="{{ route('kyc.form.delete', $item->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm btn-icon delete-btn"
                                                    data-url="{{ route('kyc.form.delete', $item->id) }}">
                                                    <svg class="icon svg-icon-ti-ti-trash" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M4 7l16 0" />
                                                        <path d="M10 11l0 6" />
                                                        <path d="M14 11l0 6" />
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                    </svg>
                                                    <span class="sr-only">{{ trans('core/base::layouts.delete')}}</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Include modals -->
    @include('admin.kyc.createkyc')
    @foreach ($data as $item)
        @include('admin.kyc.updatekyc', ['item' => $item])
    @endforeach

    <style>
    /* Tùy chỉnh nút hành động */
    .table-actions {
        display: flex;
        gap: 5px;
        justify-content: center;
    }

    .table-actions .btn {
        border-radius: 4px; /* Góc bo tròn nhẹ hơn, giống hình ảnh */
        transition: all 0.3s ease;
    }

    .table-actions .btn:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .table-actions .btn-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px; /* Kích cỡ cố định */
        height: 32px;
        padding: 0; /* Loại bỏ padding */
        border: none; /* Loại bỏ viền */
    }

    /* Điều chỉnh kích cỡ và màu SVG icon */
    .table-actions .btn-icon svg {
        width: 20px;
        height: 20px;
        stroke: #ffffff; /* Đặt màu icon thành trắng */
    }

    /* Specific styles for the Edit button */
    .table-actions .btn-primary.btn-icon {
        background-color: #007bff;
        border-color: #007bff;
    }

    .table-actions .btn-primary.btn-icon:hover {
        background-color: #0056b3;
        border-color: #004085;
    }

    /* Specific styles for the Delete button */
    .table-actions .btn-danger.btn-icon {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .table-actions .btn-danger.btn-icon:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .table-actions .btn-icon {
            width: 28px;
            height: 28px;
        }

        .table-actions .btn-icon svg {
            width: 18px;
            height: 18px;
        }
    }
    </style>
@endsection

@push('footer')
    <script src="{{ asset('vendor/core/core/js-validation/js/create_kyc.js') }}"></script>
    <script src="{{ asset('vendor/core/core/js-validation/js/update_kyc.js') }}"></script>
    <script src="{{ asset('vendor/core/core/js-validation/js/delete_kyc.js') }}"></script>
@endpush