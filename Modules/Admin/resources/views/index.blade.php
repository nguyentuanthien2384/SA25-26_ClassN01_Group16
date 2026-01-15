@extends('admin::layouts.master')
@section('content')
<h1 class="page-header">Trang tổng quan</h1>
<div class="row">
    <!-- Có thể giữ lại các phần khác nếu cần, đã xóa phần biểu đồ doanh thu -->
</div>
<div class="row">
    <div class="col-sm-6">
        <h2>Danh sách đơn hàng mới</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên Khách hàng</th>
                    <th>Số điện thoại</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactionNews as $transaction)
                    <tr>
                        <td>{{$transaction->id}}</td>
                        <td>{{isset($transaction->user->name) ? $transaction->user->name : '[N/A]'}}</td>
                        <td>{{$transaction->tr_phone}}</td>
                        <td>{{number_format($transaction->tr_total,0,',','.')}} VND</td>
                        <td>
                            @if($transaction->tr_status == 1)
                            <a href="" class="label-success label">Đã xử lý</a>
                        @elseif($transaction->tr_status == 2)
                            <a href="" class="label label-danger">Đã huỷ</a>
                        
                        @else
                            <a href="{{route('admin.get.active.transaction', $transaction->id)}}" class="label label-default">Đang chờ</a>

                        @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            {!!$transactionNews->links()!!}
        </table>
    </div>
    <div class="col-sm-6">
        <h2 class="sub-header">Danh sách đánh giá mới nhất</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên đánh giá</th>
                        <th>Sản phẩm</th>
                        <th>Nội dung</th>
                        <th>Rating</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($ratings))
                        @foreach ($ratings as $rating)
                        <tr>
                            <td>{{$rating->id}}</td>
                            <td>{{ optional($rating->user)->name ?? '[N/A]' }}</td>
                            <td>{{ optional($rating->product)->pro_name ?? '[N/A]' }}</td>
                            <td>{{$rating->ra_content}}</td>
                            <td>{{$rating->ra_number}}</td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
 
