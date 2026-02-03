@extends('admin::layouts.master')
@section('content')
    <div class="page-header">
        <ol class="breadcrumb">
            <li><a href="{{route('admin.home')}}">Trang chủ</a></li>
            <li><a href="{{ route('admin.get.list.transaction') }}">Đơn hàng</a></li>
            <li><a href="">Danh sách</a></li>
        </ol>
    </div>
    <form action="{{ route('admin.get.list.transaction') }}" method="GET">
        <div class="form-group">
            <label for="searchInput">Tìm kiếm theo số điện thoại:</label>
            <input type="text" class="form-control" id="searchInput" name="l" placeholder="Nhập số điện thoại...">
        </div>
        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
    </form>
    <div class="table-responsive">
        <h2>QUẢN LÝ ĐƠN HÀNG <a href="{{route('admin.get.create.article')}}" class="pull-right"><i class="fa-solid fa-circle-plus"></i></a></h2>
        <div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên Khách hàng</th>
                        <th>Địa chỉ</th>
                        <th>Số điện thoại</th>
                        <th>Ghi chú</th>
                        <th>Tổng tiền</th>
                        <th>Ngày mua</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                        <tr>
                            <td>{{$transaction->id}}</td>
                            <td>{{isset($transaction->user->name) ? $transaction->user->name : '[N/A]'}}</td>
                            <td>{{$transaction->tr_address}}</td>
                            <td>{{$transaction->tr_phone}}</td>
                            <td>{{$transaction->tr_note}}</td>
                            <td>{{number_format($transaction->tr_total,0,',','.')}} VND</td>
                            <td>{{$transaction->created_at->format('d-m-Y')}}</td>
                            <td>
                                @if($transaction->tr_status == 1)
                                    <a href="" class="label-success label">Đã xử lý</a>
                                @elseif($transaction->tr_status == 2)
                                    <a href="" class="label label-danger">Đã huỷ</a>
                                
                                @else
                                    <a href="{{route('admin.get.active.transaction', $transaction->id)}}" class="label label-default">Đang chờ</a>

                                @endif
                            </td>
                             <td class="action-cell">
                                <div class="action-buttons">
                                    <a href="{{route('update.transaction',$transaction->id)}}"><i class="fa-solid fa-pen"></i>Chỉnh sửa</a>
                                    <a href="{{route('admin.get.delete.transaction',$transaction->id)}}"><i class="fa-solid fa-trash"></i>Xoá</a>
                                    <a class="js_order_item" data-id="{{$transaction->id}}" href="{{route('admin.get.view.order',$transaction->id)}}"><i class="fa-solid fa-eye"></i></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination-wrap">
                {!! $transactions->links('components.pagination') !!}
            </div>
        </div>
        <div id="myModalOrder" class="modal fade" role="dialog">
            <div class="modal-dialog">
          
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Modal Header</h4>
                </div>
                <div class="modal-body">
                  <p>Some text in the modal.</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
          
            </div>
          </div>
@stop

@section('script')
          <script>
            $(function(){
                $(".js_order_item").click(function(event){
                    event.preventDefault();
                    let $this = $(this);
                    let url = $this.attr('href');
                    $("#myModalOrder").modal('show');
                    console.log(url);
                });
            })
          </script>
@stop