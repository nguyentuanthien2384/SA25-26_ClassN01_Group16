@extends('layouts.app')
@section('content')

<style>
.article-page {
    background: #f8f9fa;
    padding: 40px 0;
    min-height: calc(100vh - 200px);
}

.article-title-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 60px 0;
    margin-bottom: 40px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.article-title-section h2 {
    color: #fff;
    font-size: 2.5rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin: 0;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.single-blog {
    background: #fff;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.4s ease;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.single-blog:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.blog-photo {
    position: relative;
    overflow: hidden;
    height: 250px;
    background: #f0f0f0;
}

.blog-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.single-blog:hover .blog-photo img {
    transform: scale(1.1);
}

.blog-photo::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.3) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.single-blog:hover .blog-photo::after {
    opacity: 1;
}

.blog-info {
    padding: 25px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.post-date {
    display: inline-block;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 15px;
}

.post-title {
    margin: 0 0 15px 0;
    font-size: 1.3rem;
    line-height: 1.4;
}

.post-title a {
    color: #2d3748;
    text-decoration: none;
    transition: color 0.3s ease;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.post-title a:hover {
    color: #667eea;
}

.article-description {
    color: #718096;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 20px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex: 1;
}

.read-more-btn {
    display: inline-flex;
    align-items: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff !important;
    padding: 12px 30px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
    align-self: flex-start;
}

.read-more-btn:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.read-more-btn i {
    margin-left: 8px;
    transition: transform 0.3s ease;
}

.read-more-btn:hover i {
    transform: translateX(5px);
}

.article-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
    margin-bottom: 50px;
}

.pagination-wrap {
    margin-top: 60px;
    padding-top: 40px;
    border-top: 2px solid #e2e8f0;
}

.no-articles {
    text-align: center;
    padding: 60px 20px;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.no-articles i {
    font-size: 4rem;
    color: #cbd5e0;
    margin-bottom: 20px;
}

.no-articles h3 {
    color: #4a5568;
    font-size: 1.5rem;
    margin-bottom: 10px;
}

.no-articles p {
    color: #a0aec0;
}

@media (max-width: 768px) {
    .article-title-section h2 {
        font-size: 1.8rem;
    }
    
    .article-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .blog-photo {
        height: 200px;
    }
}
</style>

<!-- ARTICLE PAGE START -->
<div class="article-page">
    <!-- Title Section -->
    <div class="article-title-section">
        <div class="container">
            <div class="text-center">
                <h2>TIN TỨC</h2>
            </div>
        </div>
    </div>

    <!-- Articles Grid -->
    <div class="container">
        @if($articles && $articles->count() > 0)
            <div class="article-grid">
                @foreach($articles as $article)
                <div class="single-blog">
                    <div class="blog-photo">
                        <a href="{{route('get.detail.article',[$article->a_slug, $article->id])}}">
                            <img src="{{$article->a_avatar ?: '/images/default-article.jpg'}}" 
                                 alt="{{$article->a_name}}"
                                 loading="lazy"/>
                        </a>
                    </div>
                    <div class="blog-info">
                        <span class="post-date">
                            <i class="fa fa-calendar"></i>
                            {{$article->created_at ? $article->created_at->format('d-m-Y') : 'N/A'}}
                        </span>
                        <h4 class="post-title">
                            <a href="{{route('get.detail.article',[$article->a_slug, $article->id])}}" 
                               title="{{$article->a_name}}">
                                {{$article->a_name}}
                            </a>
                        </h4>
                        @if($article->a_description)
                            <div class="article-description">
                                {!! strip_tags($article->a_description) !!}
                            </div>
                        @endif
                        <a href="{{route('get.detail.article',[$article->a_slug, $article->id])}}" 
                           class="read-more-btn">
                            Đọc thêm
                            <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pagination-wrap text-center">
                {!! $articles->appends(request()->query())->links('components.pagination') !!}
            </div>
        @else
            <div class="no-articles">
                <i class="fa fa-newspaper-o"></i>
                <h3>Chưa có tin tức</h3>
                <p>Hiện tại chưa có bài viết nào. Vui lòng quay lại sau!</p>
            </div>
        @endif
    </div>
</div>
<!-- ARTICLE PAGE END -->
@stop