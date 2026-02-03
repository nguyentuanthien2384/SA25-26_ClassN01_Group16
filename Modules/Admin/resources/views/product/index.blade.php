
@extends('admin::layouts.master')
@section('content')
    <div class="page-header">
        <ol class="breadcrumb">
            <li><a href="{{route('admin.home')}}">Trang chủ</a></li>
            <li><a href="">Sản phẩm</a></li>
            <li><a href="">Danh sách</a></li>
        </ol>
    </div>
    <dov class="row">
        <div class="col-sm-12">
            <form class="form-inline" action="" style="margin-bottom: 20px">
                <div class="form-group">
                    <input type="text" class="form-control" name="name" placeholder="Tên sản phẩm..." value="{{Request::get('name')}}">
                </div>
                <div class="form-group">
                    <select name="cate" id="" class="form-control" >
                        <option value="">Danh mục</option>
                        @if(isset($categorys))
                            @foreach($categorys as $category )
                            <option value="{{$category->id}}"{{\Request::get('cate') == $category->id ? "selected = 'selected" : ""}}>{{$category->c_name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
            </form>
        </div>
    </dov>
    <div class="table-responsive">
        <h2>QUẢN LÝ SẢN PHẨM <a href="{{route('admin.get.create.product')}}" class="pull-right"><i class="fa-solid fa-circle-plus"></i></a></h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th style="width:200px">Tên sản phẩm</th>
                    <th style="width :70px">Loại sản phẩm</th>
                    <th>Giá bán</th>
                    <th>Giá Khuyễn mãi</th>
                    <th>Hình ảnh</th>
                    <th style="width :50px">Số lượng</th>
                    <th>Trạng thái</th>
                    <th>Nổi bật</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @if( isset($products))
                    @foreach($products as $product)
                    @php
                    $totalReviews = $product->pro_total_number;
                     $totalStars = $product->pro_total;
                    $averageRating = $totalReviews > 0 ? ($totalReviews /  $totalStars) : 0;
                    $roundedNumber = round($averageRating * 2) / 2;
                    @endphp
                    <tr>
                        <td>{{$product->id}}</td>
                        <td>
                            {{$product->pro_name}}
                            <div class="average-rating" style="color:rgb(221, 172, 12)">
                                @for ($i = 1; $i <= 5; $i++)
                              @if ($i <= $roundedNumber)
                                 <i class="fas fa-star"></i>
                                @elseif ($i - 0.5 == $averageRating)
                                <i class="fas fa-star-half-alt"></i>
                            @else
                            <i class="far fa-star"></i> 
                            @endif
                                @endfor
                                <span style="color: black">{{ $roundedNumber }}</span>
                            </div>
                        </td>
                        <td>{{isset($product->category->c_name) ? $product->category->c_name :'[N\A]'}}</td>
                        <td>{{isset($product->pro_price) ? $product->pro_price :'[N\A]'}}</td>
                        <td>{{isset($product->pro_sale) ? $product->pro_sale :'[N\A]'}}</td>
                        <td>
                            <img src="{{isset($product->pro_image) ? $product->pro_image :'[N\A]'}}" alt="" style="max-width:60px;height: auto;">
                        </td>
                        <td>{{isset($product->quantity) ? $product->quantity :'[N\A]'}}</td>
                        <td>
                            <a href="{{route('admin.get.action.product',['active',$product->id])}}"class="label {{$product->getStatus($product->pro_active)['class']}}">{{$product->getStatus($product->pro_active)['name']}}</a>
                        </td>
                        <td>
                            <a href="{{route('admin.get.action.product',['hot',$product->id])}}"class="label {{$product->getHot($product->pro_hot)['class']}}">{{$product->getHot($product->pro_hot)['name']}}</a>
                        </td>
                        <td class="action-cell">
                            <div class="action-buttons">
                                <a href="{{route('admin.get.edit.product',$product->id)}}"><i class="fa-solid fa-pen"></i>Chỉnh sửa</a>
                                <a href="{{route('admin.get.action.product',['delete',$product->id])}}"><i class="fa-solid fa-trash"></i>Xoá</a>
                            </div>
                        </td>
                    </tr>                
                    @endforeach
                    @endif
            </tbody>
        </table>
        <div class="pagination-wrap">
            {!! $products->links('components.pagination') !!}
        </div>
    </div>
@stop