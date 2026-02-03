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
            <tbody id="transactions-body">
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
        </table>
        <div class="pagination-wrap">
            {!! $transactionNews->links('components.pagination') !!}
        </div>
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
            <tbody id="contacts-body">
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
                <tbody id="ratings-body">
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
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script>
        (function () {
            var dataMoney = @json($dataMoney);
            window.revenueChart = Highcharts.chart('revenue-chart', {
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
    <script>
        (function () {
            var key = "{{ env('PUSHER_APP_KEY') }}";
            var cluster = "{{ env('PUSHER_APP_CLUSTER', 'mt1') }}";
            if (!key) return;

            var pusher = new Pusher(key, {
                cluster: cluster,
                forceTLS: true
            });
            var channel = pusher.subscribe('dashboard');
            channel.bind('dashboard.updated', function () {
                fetch("{{ route('admin.dashboard.data') }}", {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (window.revenueChart) {
                        window.revenueChart.series[0].setData(data.dataMoney, true);
                    }

                    var transactionsHtml = '';
                    data.transactions.forEach(function (item) {
                        var statusHtml = item.status_url
                            ? '<a href="' + item.status_url + '" class="label ' + item.status_class + '">Đang chờ</a>'
                            : '<a href="" class="label ' + item.status_class + '">' + item.status_text + '</a>';
                        transactionsHtml += '<tr>'
                            + '<td>' + item.id + '</td>'
                            + '<td>' + item.name + '</td>'
                            + '<td>' + (item.phone || '') + '</td>'
                            + '<td>' + item.total + '</td>'
                            + '<td>' + statusHtml + '</td>'
                            + '</tr>';
                    });
                    document.getElementById('transactions-body').innerHTML = transactionsHtml;

                    var contactsHtml = '';
                    data.contacts.forEach(function (item) {
                        contactsHtml += '<tr>'
                            + '<td>' + item.id + '</td>'
                            + '<td>' + (item.name || '') + '</td>'
                            + '<td>' + (item.phone || '') + '</td>'
                            + '<td>' + (item.email || '') + '</td>'
                            + '<td>' + (item.title || '') + '</td>'
                            + '<td>' + (item.message || '') + '</td>'
                            + '</tr>';
                    });
                    document.getElementById('contacts-body').innerHTML = contactsHtml;

                    var ratingsHtml = '';
                    data.ratings.forEach(function (item) {
                        ratingsHtml += '<tr>'
                            + '<td>' + item.id + '</td>'
                            + '<td>' + item.name + '</td>'
                            + '<td>' + item.product + '</td>'
                            + '<td>' + (item.content || '') + '</td>'
                            + '<td>' + item.number + '</td>'
                            + '</tr>';
                    });
                    document.getElementById('ratings-body').innerHTML = ratingsHtml;
                });
            });
        })();
    </script>
@endsection
 
