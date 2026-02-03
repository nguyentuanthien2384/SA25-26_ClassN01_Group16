@extends('admin::layouts.master')
@section('content')
    <div class="page-header">
        <ol class="breadcrumb">
            <li><a href="{{route('admin.home')}}">Trang chủ</a></li>
            <li><a href="">Bài viết</a></li>
            <li><a href="">Danh sách</a></li>
        </ol>
    </div>
    <div class="table-responsive">
        <h2>QUẢN LÝ BÀI VIẾT <a href="{{route('admin.get.create.article')}}" class="pull-right"><i class="fa-solid fa-circle-plus"></i></a></h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th style="width: 250px">Tên bài viết</th>
                    <th style=" width: 300px ">Mô tả</th>
                    <th>Hình ảnh</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @if( isset($articles))
                    @foreach($articles as $article)
                    <tr>
                        <td>{{$article->id}}</td>
                        <td>
                            {{$article->a_name}}
                        </td>
                        <td>{{$article->a_description}}</td>
                        <td>
                            <img src="{{isset($article->a_avatar) ? $article->a_avatar :'[N\A]'}}" alt="" style="max-width:60px;height: auto;">
                        </td>
                        <td>
                            <a href="{{route('admin.get.action.article',['active',$article->id])}}"class="label {{$article->getStatus($article->a_active)['class']}}">{{$article->getStatus($article->a_active)['name']}}</a>
                        </td>
                        <td>{{$article->created_at->format('d-m-Y')}}</td>
                
                        <td class="action-cell">
                            <div class="action-buttons">
                                <a href="{{route('admin.get.edit.article',$article->id)}}"><i class="fa-solid fa-pen"></i>Chỉnh sửa</a>
                                <a href="{{route('admin.get.action.article',['delete',$article->id])}}"><i class="fa-solid fa-trash"></i>Xoá</a>
                            </div>
                        </td>
                    </tr>                
                    @endforeach
                    @endif
            </tbody>
        </table>
@stop