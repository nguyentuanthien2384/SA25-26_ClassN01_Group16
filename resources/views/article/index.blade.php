@extends('layouts.app')
@section('content')

<!-- HEADING-BANNER END -->
<!-- ELEMENTS-TAB START -->
<div class="elements-tab pb-80">            
    <!-- BLGO-AREA START -->
    <div class="blog-area blog-2 pt-30">
        <div class="container">
            <div class="tab-content row">
                <div class="tab-pane active" id="elements-preview-3">
                    <!-- Section-title start -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="section-title text-center">
                                <h2 class="title-border">TIN TỨC</h2>
                            </div>
                        </div>
                    </div>
                    <!-- Section-title end -->
                    <div class="row">
                        <!-- Single-blog start -->
                        @foreach($articles as $article)
                        <div class="col-xl-4 col-md-6">
                            <div class="single-blog mt-30">
                                <div class="blog-photo">
                                    <a href="{{route('get.detail.article',[$article->a_slug, $article->id])}}"><img src="{{$article->a_avatar}}" alt="" style="width: auto;height: 200px;"/></a>
                                </div>
                                <div class="blog-info"> 
                                    <div class="post-meta fix">
                                        <div class="post-year floatleft">
                                            <p class="text-uppercase text-dark-red mb-0">{{$article->created_at->format('d-m-Y')}}</p>
                                            <h4 class="post-title"><a href="{{route('get.detail.article',[$article->a_slug, $article->id])}}" tabindex="0">{{$article->a_name}}</a></h4>
                                        </div>
                                    </div>
                                    {{-- {!!$article->a_description!!} --}}
                                    <a href="{{route('get.detail.article',[$article->a_slug, $article->id])}}" class="button-2 text-dark-red">ĐỌc thêm</a>
                                </div>
                            </div>
                        </div>
                        
                        @endforeach
                        <div class="pagination-wrap text-center">
                            {!! $articles->appends(request()->query())->links('components.pagination') !!}
                        </div>
                    </div>
                </div>                             
            </div> 
        </div>
    </div>
    <!-- BLGO-AREA END -->
</div>
@stop