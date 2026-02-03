@extends('admin::layouts.master')
@section('content')
    <div class="page-header">
        <ol class="breadcrumb">
            <li><a href="{{route('admin.home')}}">Trang chủ</a></li>
            <li><a href="">Liên hệ</a></li>
            <li><a href="">Danh sách</a></li>
        </ol>
    </div>
    <div class="table-responsive">
        <h2>QUẢN LÝ Liên hệ </h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên liên hệ</th>
                    <th>Số điện thoại</th>
                    <th>Emaill</th>
                    <th>Tiêu đề</th>
                    <th>Nội dung</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($contacts))
                    @foreach ($contacts as $contact)
                    <tr>
                        <td>{{$contact->id}}</td>
                        <td>{{$contact->con_name}}</td>
                        <td>{{$contact->con_phone}}</td>
                        <td>{{$contact->con_email}}</td>
                        <td>{{$contact->con_title}}</td>
                        <td>{{$contact->con_message}}</td>
                        
                        <td class="action-cell">
                            <div class="action-buttons">
                                <a href="{{route('admin.get.delete.contact',$contact->id)}}" onclick="return confirm('Bạn có chắc muốn xoá liên hệ này?')"><i class="fa-solid fa-trash"></i>Xoá</a>
                            </div>
                        </td>
                    </tr>  
                    @endforeach
                @endif
            </tbody>
        </table>
@stop