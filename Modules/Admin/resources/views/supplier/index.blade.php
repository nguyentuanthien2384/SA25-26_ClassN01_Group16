
@extends('admin::layouts.master')
@section('content')
    <div class="page-header">
        <ol class="breadcrumb">
            <li><a href="{{route('admin.home')}}">Trang chủ</a></li>
            <li><a href="">Sản phẩm</a></li>
            <li><a href="">Danh sách</a></li>
        </ol>
    </div>
   
    <div class="table-responsive">
        <h2>QUẢN LÝ NHÀ CUNG CẤP <a href="{{route('admin.get.create.supplier')}}" class="pull-right"><i class="fa-solid fa-circle-plus"></i></a></h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên nhà cung cấp</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @if( isset($suppliers))
                    @foreach($suppliers as $supplier)
                    <tr>
                        <td>{{$supplier->id}}</td>
                        <td>{{isset($supplier->s_name) ? $supplier->s_name :'[N\A]'}}</td>
                        <td class="action-cell">
                            <div class="action-buttons">
                                <a href="{{route('admin.get.delete.supplier',$supplier->id)}}"><i class="fa-solid fa-trash"></i>Xoá</a>
                            </div>
                        </td>
                    </tr>                
                    @endforeach
                    @endif
            </tbody>
        </table>
        <div class="pagination-wrap">
            {!! $suppliers->links('components.pagination') !!}
        </div>
@stop