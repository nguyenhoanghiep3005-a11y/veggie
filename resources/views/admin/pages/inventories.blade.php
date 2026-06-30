@extends('layouts.admin')

@section('title','Quản lý kho')
@section('content')
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Quản lý kho hàng</h3>
            </div>
        </div>
        <div class="clearfix"></div>

        @if(session('success'))
        <div class="alert alert-success">{{session('success')}}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">{{session('error')}}</div>
        @endif
        @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
            <div>{{$error}}</div>
            @endforeach
        </div>
        @endif

        <div class="row">
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Danh sách kho hàng</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li>
                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                    data-target="#modal-add-inventory">
                                    <i class="fa fa-plus"></i> Thêm lô hàng
                                </button>
                            </li>
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" style="text-align:center;">
                                <thead>
                                    <tr>
                                        <th>Mã lô</th>
                                        <th>Sản phẩm</th>
                                        <th>Danh mục</th>
                                        <th>Ngày nhập</th>
                                        <th>HSD</th>
                                        <th>Số lượng</th>
                                        <th>Hư hỏng</th>
                                        <th>Tình trạng</th>
                                        <th>Giá bán</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inventories as $inventory)
                                    <tr>
                                        <td><strong>{{$inventory->lotCode()}}</strong></td>
                                        <td>{{$inventory->product->name}}</td>
                                        <td>{{$inventory->product->category->name}}</td>
                                        <td>{{date('d/m/Y', strtotime($inventory->imported_at))}}</td>
                                        <td>{{date('d/m/Y', strtotime($inventory->expired_at))}}</td>
                                        <td>{{$inventory->quantity_remaining}} / {{$inventory->quantity_imported}}</td>
                                        <td>{{$inventory->quantity_damaged}}</td>
                                        <td>
                                            <span class="{{$inventory->conditionClass()}}">
                                                {{$inventory->conditionLabel()}}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>{{number_format($inventory->sellingPrice(), 0, ',', '.')}}đ</strong>
                                            @if($inventory->adjusted_price !== null && $inventory->adjusted_price < $inventory->product->price)
                                            <br>
                                            <small><del>{{number_format($inventory->product->price, 0, ',', '.')}}đ</del></small>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                                data-target="#modal-inventory-{{$inventory->id}}">
                                                Chi tiết / Điều chỉnh
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-add-inventory" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{route('admin.inventories.store')}}" method="POST" class="form-horizontal form-label-left">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Thêm lô hàng vào kho</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">Sản phẩm</label>
                        <div class="col-md-8 col-sm-8 ">
                            <select name="product_id" class="form-control" required>
                                <option value="">Chọn sản phẩm</option>
                                @foreach($products as $product)
                                <option value="{{$product->id}}">
                                    {{$product->name}} - {{number_format($product->price, 0, ',', '.')}}đ
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">Số lượng nhập</label>
                        <div class="col-md-8 col-sm-8 ">
                            <input type="number" name="quantity_imported" class="form-control" min="1" required>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">Ngày nhập</label>
                        <div class="col-md-8 col-sm-8 ">
                            <input type="date" name="imported_at" class="form-control" value="{{date('Y-m-d')}}" required>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">Hạn sử dụng</label>
                        <div class="col-md-8 col-sm-8 ">
                            <input type="date" name="expired_at" class="form-control" value="{{date('Y-m-d', strtotime('+7 days'))}}" required>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">Giá điều chỉnh</label>
                        <div class="col-md-8 col-sm-8 ">
                            <input type="number" name="adjusted_price" class="form-control" min="0"
                                placeholder="Để trống nếu bán theo giá niêm yết">
                            <small class="form-text text-muted">Nhập giá thấp hơn giá niêm yết nếu muốn bán khuyến mãi.</small>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">Ghi chú</label>
                        <div class="col-md-8 col-sm-8 ">
                            <input type="text" name="note" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success">Thêm vào kho</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($inventories as $inventory)
<div class="modal fade" id="modal-inventory-{{$inventory->id}}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{route('admin.inventories.update')}}" method="POST"
                class="form-horizontal form-label-left inventory-adjust-form"
                data-max-unsold="{{$inventory->maxUnsoldQuantity()}}">
                @csrf
                <input type="hidden" name="inventory_id" value="{{$inventory->id}}">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết / Điều chỉnh lô hàng</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Mã lô:</strong> {{$inventory->lotCode()}}</p>
                            <p><strong>Sản phẩm:</strong> {{$inventory->product->name}}</p>
                            <p><strong>Danh mục:</strong> {{$inventory->product->category->name}}</p>
                            <p><strong>Giá niêm yết:</strong> {{number_format($inventory->product->price, 0, ',', '.')}}đ</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Ngày nhập:</strong> {{date('d/m/Y', strtotime($inventory->imported_at))}}</p>
                            <p><strong>Hạn sử dụng:</strong> {{date('d/m/Y', strtotime($inventory->expired_at))}}</p>
                            <p><strong>Số lượng nhập:</strong> {{$inventory->quantity_imported}}</p>
                            <p><strong>Số lượng còn:</strong> <span class="inventory-remaining-count">{{$inventory->quantity_remaining}}</span></p>
                            <p><strong>Số lượng hư:</strong> <span class="inventory-damaged-count">{{$inventory->quantity_damaged}}</span></p>
                            <p><strong>Giá đang bán:</strong> {{number_format($inventory->sellingPrice(), 0, ',', '.')}}đ</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <p><strong>Chọn mã hàng bị hư:</strong></p>
                            <div class="inventory-code-list">
                                @for($i = 1; $i <= $inventory->quantity_imported; $i++)
                                <label class="badge inventory-code-label {{$inventory->isDamagedItem($i) ? 'is-damaged' : ''}} {{$inventory->isSoldItem($i) ? 'is-sold' : ''}}">
                                    <input type="checkbox" name="damaged_item_numbers[]" value="{{$i}}"
                                        class="inventory-damaged-code"
                                        {{$inventory->isDamagedItem($i) ? 'checked' : ''}}
                                        {{$inventory->isSoldItem($i) ? 'disabled' : ''}}>
                                    {{$inventory->itemCode($i)}}
                                    @if($inventory->isSoldItem($i))
                                    <small>(đã bán)</small>
                                    @endif
                                </label>
                                @endfor
                            </div>
                            <small class="text-danger inventory-damaged-error"></small>
                            <small class="form-text text-muted">
                                Click vào mã hàng bị hư. Hệ thống tự giảm số lượng còn và tăng số lượng hư.
                            </small>
                        </div>
                    </div>

                    <div class="ln_solid"></div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">Giá điều chỉnh</label>
                        <div class="col-md-8 col-sm-8 ">
                            <input type="number" name="adjusted_price" class="form-control" min="0"
                                value="{{$inventory->adjusted_price}}"
                                placeholder="Để trống nếu bán theo giá niêm yết">
                            <small class="form-text text-muted">Lô có giá điều chỉnh thấp hơn giá niêm yết sẽ hiển thị ở sản phẩm khuyến mãi.</small>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">Ghi chú</label>
                        <div class="col-md-8 col-sm-8 ">
                            <input type="text" name="note" class="form-control" value="{{$inventory->note}}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary inventory-save-button">Lưu điều chỉnh</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
