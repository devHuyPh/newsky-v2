@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="card m-0 shadow-lg h-100" style="border-radius: 0;">
        <div class="row card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center px-4 flex-wrap">
                <h5 class="mb-0 fw-bold text-dark me-5 fs-3">{{ trans('core/base::layouts.rank_list') }}</h5>
                <div class="d-flex align-items-center gap-12 flex-wrap">
                    <div class="d-flex align-items-center gap-12 me-5" style="max-width: 250px">
                        <h5 class="mb-0 text-dark">
                            {{ trans('core/base::layouts.sharing_day') }}
                        </h5>
                        <div class="input-group" style="max-width: 220px; overflow: hidden;">
                            <input type="number" class="form-control dayofsharing-input" max="31" min="0"
                                placeholder="Day of Sharing" id="global-dayofsharing" value="{{ $dayofsharing }}"
                                style=" border: none; font-size: 0.9rem; padding: 0.5rem;"
                                oninput="if(this.value > 31) this.value = 31; if(this.value < 0) this.value = 1;">
                            <button class="btn btn-success update-all-dayofsharing" type="button"
                                style="padding: 0.5rem 0.75rem;">
                                <i class="fas fa-save" style="font-size: 1rem;"></i>
                            </button>
                        </div>
                    </div>
                    <a href="{{ route('rank.add') }}" class="btn btn-primary mt-2 mt-md-0">
                        <i class="fas fa-plus me-2"></i>{{ trans('core/base::layouts.add_new') }}
                    </a>
                </div>
            </div>
        </div>



        <div class="card-body p-0 bg-light overflow-auto">
            <div class="table-responsive h-100">
                <table class="table table-hover table-striped align-middle m-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="py-3 px-4">{{ trans('core/base::layouts.rank_name') }}</th>
                            <th class="py-3 px-4">{{ trans('core/base::layouts.level') }}</th>
                            <th class="py-3 px-4 d-none d-lg-table-cell">{{ trans('core/base::layouts.icon') }}</th>
                            <th class="py-3 px-4 d-none d-md-table-cell">
                                {{ trans('core/base::layouts.upgrade_conditions') }}
                            </th>
                            <th class="py-3 px-4 d-none d-md-table-cell">
                                {{ trans('core/base::layouts.demotion_conditions') }}
                            </th>
                            <th class="py-3 px-4">{{ trans('core/base::layouts.reward_percentage') }}</th>
                            <th class="py-3 px-4 d-none d-lg-table-cell">{{ trans('core/base::layouts.details') }}</th>
                            <th class="py-3 px-4">{{ trans('core/base::layouts.status') }}</th>
                            <th class="py-3 px-4">{{ trans('core/base::layouts.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                            <tr data-id="{{ $item->id }}">
                                <td class="px-4 py-3 fw-semibold">{{ $item->rank_name }}</td>
                                <td class="px-4 py-3"><span
                                        class="badge bg-primary text-light px-2 py-1">{{ $item->rank_lavel }}</span>
                                </td>
                                <td class="px-4 py-3 d-none d-lg-table-cell">
                                    <img src="{{ asset($item->rank_icon) }}" alt="Not Found"
                                        class="rounded-circle border shadow-sm"
                                        style="width: 40px; height: 40px; object-fit: cover;">
                                </td>
                                <td class="px-4 py-3 text-success d-none d-md-table-cell">
                                    <strong>{{ trans('core/base::layouts.referrals') }}</strong>
                                    {{ $item->number_referrals }}</br>
                                    <strong>{{ trans('core/base::layouts.total_revenue') }}</strong>
                                    {{ number_format($item->total_revenue, 0, ',', '.') }} VND
                                </td>
                                <td class="px-4 py-3 text-danger d-none d-md-table-cell">
                                    <strong>{{ trans('core/base::layouts.months') }}</strong>
                                    {{ $item->demotion_time_months }}</br>
                                    <strong>{{ trans('core/base::layouts.referrals') }}</strong>
                                    {{ $item->demotion_referrals }}</br>
                                    <strong>{{ trans('core/base::layouts.investments') }}</strong>
                                    {{ number_format($item->demotion_investment, 0, ',', '.') }} VND</br>
                                </td>
                                <td class="px-4 py-3 fw-semibold">{{ $item->percentage_reward }}%</td>
                                <td class="px-4 py-3 details-column d-none d-lg-table-cell">
                                    @if ($item->description)
                                        {{ Str::limit($item->description, 42) }}
                                    @else
                                        {{ $item->description }}
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="badge {{ $item->status ? 'bg-success text-light' : 'bg-danger text-light' }} px-2 py-1">
                                        {{ $item->status ? trans('core/base::layouts.active') : trans('core/base::layouts.inactive') }}
                                    </span>
                                </td>
                                <td class="px-2 py-3 justify-content-center align-items-center">
                                    <a href="{{ route('rank.edit', $item->id) }}" class="btn btn-primary me-2 mb-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-danger delete-btn" data-bs-toggle="modal"
                                        data-bs-target="#delete-modal" data-route="{{ route('rank.delete', $item->id) }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i
                                        class="fas fa-exclamation-circle me-2"></i>{{ trans('core/base::layouts.no_data_founds') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>




        <div>
            <div class="card m-0 shadow-lg h-100" style="border-radius: 0;">
                <div class="row card-header bg-gradient-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center px-4 flex-wrap">
                        <h5 class="mb-0 fw-bold text-dark me-5 fs-3">Danh sách hạng người dùng</h5>
                        <div class="d-flex align-items-center gap-12 flex-wrap">
                            <button class="btn btn-primary mt-2 mt-md-0" data-bs-toggle="modal"
                                data-bs-target="#addRankModal">
                                <i class="fas fa-plus me-2"></i>{{ trans('core/base::layouts.add_new') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0 bg-light overflow-auto">
                    <div class="table-responsive h-100">
                        <table class="table table-hover table-striped align-middle m-0">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th class="py-3 px-4">{{ trans('core/base::layouts.user_name') }}</th>
                                    <th class="py-3 px-4">{{ trans('core/base::layouts.rank_name') }}</th>
                                    <th class="py-3 px-4">{{ trans('core/base::layouts.level') }}</th>
                                    <th class="py-3 px-4 d-none d-lg-table-cell">{{ trans('core/base::layouts.icon_rank') }}</th>
                                    <th class="py-3 px-4">{{ trans('core/base::layouts.total_doawline') }}</th>
                                    <th class="py-3 px-4">{{ trans('core/base::layouts.walet1') }}</th>
                                    <th class="py-3 px-4">{{ trans('core/base::layouts.status_admin') }}</th>
                                    <th class="py-3 px-4">{{ trans('core/base::layouts.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    <tr data-id="{{ $customer->id }}">
                                        <td class="px-4 py-3 fw-semibold">{{ $customer->name }}</td>
                                        <td class="px-4 py-3">{{ $customer->rank->rank_name ?? 'Chưa có hạng' }}</td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="badge bg-primary text-light px-2 py-1">{{ $customer->rank->rank_lavel ?? 'N/A' }}</span>
                                        </td>
                                        <td class="px-4 py-3 d-none d-lg-table-cell">
                                            @if ($customer->rank && $customer->rank->rank_icon)
                                                <img src="{{ asset($customer->rank->rank_icon) }}" alt="Not Found"
                                                    class="rounded-circle border shadow-sm"
                                                    style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <span>Không có</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">{{ number_format($customer->total_dowline, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3">{{ number_format($customer->walet_1, 0, ',', '.') }} VND
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="badge bg-success text-light px-2 py-1">Hoạt động</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <button class="btn btn-primary btn-sm edit-rank-btn" data-bs-toggle="modal"
                                                data-bs-target="#editRankModal" data-id="{{ $customer->id }}"
                                                data-rank-id="{{ $customer->rank_id ?? '' }}">
                                                <i class="fas fa-edit"></i> Sửa
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="fas fa-exclamation-circle me-2"></i>Không có dữ liệu
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="p-3">
                    {{ $customers->links() }}
                </div>
            </div>

            <td class="px-2 py-3 justify-content-center align-items-center">
                                    <a href="{{ route('rank.edit', $item->id) }}" class="btn btn-primary me-2 mb-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-danger delete-btn" data-bs-toggle="modal"
                                        data-bs-target="#delete-modal" data-route="{{ route('rank.delete', $item->id) }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>

            <!-- Modal Thêm mới -->
            <div class="modal fade" id="addRankModal" tabindex="-1" aria-labelledby="addRankModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow-lg">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title fw-bold" id="addRankModalLabel">Thêm mới người dùng vào danh sách</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form id="addRankForm" method="POST" action="{{ route('customer.store.rank') }}">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group mb-3">
                                    <label for="customer_id" class="fw-semibold">Chọn người dùng</label>
                                    <select name="customer_id" id="customer_id" class="form-control" required>
                                        <option value="">-- Chọn người dùng --</option>
                                        @foreach ($allCustomers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="rank_id_add" class="fw-semibold">Chọn hạng</label>
                                    <select name="rank_id" id="rank_id_add" class="form-control">
                                        <option value="">Không gán hạng</option>
                                        @foreach ($ranks as $rank)
                                            <option value="{{ $rank->id }}">{{ $rank->rank_name }} (Cấp
                                                {{ $rank->rank_lavel }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary"
                                    data-bs-dismiss="modal">Đóng</button>
                                <button type="submit" class="btn btn-primary">Lưu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Sửa hạng -->
            <div class="modal fade" id="editRankModal" tabindex="-1" aria-labelledby="editRankModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow-lg">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title fw-bold" id="editRankModalLabel">Sửa hạng</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form id="editRankForm" method="POST">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="customer_id" id="customer_id">
                                <div class="form-group mb-3">
                                    <label for="rank_id" class="fw-semibold">Chọn hạng</label>
                                    <select name="rank_id" id="rank_id" class="form-control">
                                        <option value="">Không gán hạng</option>
                                        @foreach ($ranks as $rank)
                                            <option value="{{ $rank->id }}">{{ $rank->rank_name }} (Cấp
                                                {{ $rank->rank_lavel }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary"
                                    data-bs-dismiss="modal">Đóng</button>
                                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Script để điền dữ liệu vào modal sửa -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const editButtons = document.querySelectorAll('.edit-rank-btn');
                    editButtons.forEach(button => {
                        button.addEventListener('click', function() {
                            const customerId = this.getAttribute('data-id');
                            const rankId = this.getAttribute('data-rank-id');
                            const form = document.getElementById('editRankForm');

                            document.getElementById('customer_id').value = customerId;
                            document.getElementById('rank_id').value = rankId || '';
                            form.action = '{{ url('/admin/customers') }}/' + customerId + '/update-rank';
                        });
                    });
                });
            </script>
        </div>

    </div>



    <!-- Delete Modal -->
    <div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title fw-bold" id="deleteModalLabel">
                        {{ trans('core/base::layouts.confirm_deletion') }}
                    </h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">{{ trans('core/base::layouts.are_you_sure_you_want_to_delete_this_rank') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">{{ trans('core/base::layouts.close') }}</button>
                    <form id="delete-form" method="post" action="" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ trans('core/base::layouts.yes') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .bg-gradient-primary {
            background: linear-gradient(45deg, #007bff, #00b4db);
        }

        .card {
            height: 100%;
        }

        .table thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background-color: #343a40;
        }

        .table tr:hover {
            background-color: #f8f9fa;
        }

        .btn-group .btn {
            padding: 0.25rem 0.5rem;
        }

        .badge {
            font-size: 0.85em;
        }

        .table td,
        .table th {
            vertical-align: middle;
            white-space: nowrap;
        }

        .details-column {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .card-header .d-flex {
                flex-direction: column;
                align-items: flex-start;
            }

            .input-group {
                max-width: 100%;
                margin-top: 10px;
            }

            .btn-primary {
                width: 100%;
                margin-top: 10px;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .table td,
            .table th {
                font-size: 0.9rem;
            }

            .details-column {
                max-width: 100px;
            }
        }

        @media (max-width: 576px) {
            .table thead {
                display: none;
                /* Ẩn tiêu đề trên mobile */
            }

            .table tbody tr {
                display: block;
                margin-bottom: 15px;
                border: 1px solid #ddd;
                padding: 10px;
            }

            .table tbody td {
                display: block;
                text-align: left;
                padding: 5px 0;
                white-space: normal;
            }

            .table tbody td:before {
                content: attr(data-label);
                font-weight: bold;
                display: inline-block;
                width: 50%;
            }

            .table td[data-label="Actions"] {
                display: flex;
                justify-content: space-between;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        $(document).ready(function() {
            $('.delete-btn').on('click', function() {
                const route = $(this).data('route');
                $('#delete-form').attr('action', route);
            });

            // Gán data-label cho các ô td để hiển thị trên mobile
            $('tbody tr').each(function() {
                $(this).find('td').each(function(index) {
                    const labels = ['Rank Name', 'Level', 'Icon', 'Upgrade Conditions',
                        'Demotion Conditions', 'Reward Percentage', 'Details', 'Status',
                        'Actions'
                    ];
                    $(this).attr('data-label', labels[index]);
                });
            });

            // Xử lý cập nhật dayofsharing qua AJAX
            $('.update-all-dayofsharing').on('click', function() {
                let newValue = $('#global-dayofsharing').val();

                if (!newValue) {
                    alert('Please select a date for Day of Sharing');
                    return;
                }

                if (confirm('Are you sure you want to update Day of Sharing?')) {
                    $.ajax({
                        url: '{{ route('admin.ranks.update.dayofsharing') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            value: newValue
                        },
                        beforeSend: function() {
                            $('.update-all-dayofsharing').prop('disabled', true);
                            $('.update-all-dayofsharing').html(
                                '<i class="fas fa-spinner fa-spin"></i>');
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('Day of sharing updated successfully!');
                            }
                        },
                        error: function(xhr) {
                            alert('Error updating day of sharing: ' + xhr.responseJSON.message);
                        },
                        complete: function() {
                            $('.update-all-dayofsharing').prop('disabled', false);
                            $('.update-all-dayofsharing').html('<i class="fas fa-save"></i>');
                        }
                    });
                }
            });
        });
    </script>
@endpush
