@extends('admin::layouts.master')
@section('content')
<h1 class="page-header">Trang tổng quan</h1>
<div class="row">
    <div class="col-md-6">
        <h3>Biểu đồ doanh thu</h3>
        <div id="revenue-chart" style="height: 320px; background:#fff;"></div>
    </div>
    <div class="col-md-6">
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
                        <td>{{ optional($transaction->user)->name ?? '[N/A]' }}</td>
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
</div>
<div class="row">
    <div class="col-sm-6">
        <h2>Danh sách liên hệ mới nhất</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Họ tên</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th>Tiêu đề</th>
                    <th>Nội dung</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contacts as $contact)
                    <tr>
                        <td>{{ $contact->id }}</td>
                        <td>{{ $contact->con_name }}</td>
                        <td>{{ $contact->con_phone }}</td>
                        <td>{{ $contact->con_email }}</td>
                        <td>{{ $contact->con_title }}</td>
                        <td>{{ $contact->con_message }}</td>
                    </tr>
                @endforeach
            </tbody>
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

@section('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script>
        (function () {
            var dataMoney = @json($dataMoney);
            Highcharts.chart('revenue-chart', {
                chart: { type: 'column' },
                title: { text: 'Biểu đồ doanh thu' },
                xAxis: { type: 'category' },
                yAxis: { title: { text: 'VND' } },
                series: [{
                    name: 'Giá trị',
                    data: dataMoney
                }],
                credits: { enabled: false }
            });
        })();
    </script>
@endsection
 
