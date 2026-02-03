@extends('admin::layouts.master')
@section('content')
    <div class="page-header">
        <ol class="breadcrumb">
            <li><a href="{{route('admin.home')}}">Trang chủ</a></li>
            <li><a href="">Đánh giá</a></li>
            <li><a href="">Danh sách</a></li>
        </ol>
    </div>
    <div class="table-responsive">
        <h2>QUẢN LÝ ĐÁNH GIÁ </h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên đánh giá</th>
                    <th>Số điện thoại</th>
                    <th>Sản phẩm</th>
                    <th>Nội dung</th>
                    <th>Rating</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($ratings))
                    @foreach ($ratings as $rating)
                    <tr>
                        <td>{{$rating->id}}</td>
                        <td>{{ optional($rating->user)->name ?? '[N/A]' }}</td>
                        <td>{{ optional($rating->user)->phone ?? '[N/A]' }}</td>
                        <td>{{ optional($rating->product)->pro_name ?? '[N/A]' }}</td>
                        <td>{{$rating->ra_content}}</td>
                        <td>{{$rating->ra_number}}</td>
                        <td class="action-cell">
                            <div class="action-buttons">
                                <a href="{{route('admin.delete.rating',$rating->id)}}"><i class="fa-solid fa-trash"></i>Xoá</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <div class="pagination-wrap">
            {!! $ratings->links('components.pagination') !!}
        </div>
@stop