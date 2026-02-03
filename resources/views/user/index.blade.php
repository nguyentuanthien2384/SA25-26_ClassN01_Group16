@extends('user.layout')
@section('content')
<h1 class="page-header">Trang tổng quan</h1>
<div class="js-ajax-section" data-section="user-transactions">
<div class="row">
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading">Tổng đơn hàng</div>
            <div class="panel-body">
                <h3 style="margin:0;">{{ $totalTransaction }}</h3>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-success">
            <div class="panel-heading">Đơn hàng đã xử lý</div>
            <div class="panel-body">
                <h3 style="margin:0;">{{ $totalTransactionDone }}</h3>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-warning">
            <div class="panel-heading">Đơn hàng chưa xử lý</div>
            <div class="panel-body">
                <h3 style="margin:0;">{{ $totalTransaction - $totalTransactionDone }}</h3>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <h2>Danh sách đơn hàng của bạn</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Địa chỉ</th>
                    <th>Số điện thoại</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thời gian</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                    <tr>
                        <td>{{$transaction->id}}</td>
                        <td>{{isset($transaction->user->address) ? $transaction->user->address : '[N/A]'}}</td>
                        <td>{{$transaction->tr_phone}}</td>
                        <td>{{number_format($transaction->tr_total,0,',','.')}} VND</td>
                        <td>
                            @if($transaction->tr_status == 1)
                                <a href="" class="label-success label">Đã xử lý</a>
                            
                            @else
                                <a href="#" class="label label-default">Đang chờ</a>
                        
                            
                            @endif
                        </td>
                        <td>{{$transaction->created_at}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pagination-wrap text-center">
            {!! $transactions->appends(request()->query())->links('components.pagination') !!}
        </div>
    </div>
</div>
</div>
{{-- <div class="row">
    <div class="col-sm-6">
        <h2 class="sub-header">Danh sách liên hệ mới nhất</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên liên hệ</th>
                        <th>Số điện thoại</th>
                        <th>Emaill</th>
                        <th>Tiêu đề</th>
                        <th>Nội dung</th>
                    </tr>
                </thead>
                {{-- <tbody>
                    @if (isset($contacts))
                        @foreach ($contacts as $contact)
                        <tr>
                            <td>{{$contact->id}}</td>
                            <td>{{$contact->con_name}}</td>
                            <td>{{$contact->con_phone}}</td>
                            <td>{{$contact->con_email}}</td>
                            <td>{{$contact->con_title}}</td>
                            <td>{{$contact->con_message}}</td>
                        </tr>  
                        @endforeach
                    @endif
                </tbody> --}}
            </table>
        </div>
    </div>
    {{-- <div class="col-sm-6">
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
                            <td>{{$rating->user->name}}</td>
                            <td>{{ optional($rating->product)->pro_name }}</td>
                            <td> {{$rating->ra_content}}</td>
                            <td>{{$rating->ra_number}}</td>
                        </tr>  
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div> --}}

@stop