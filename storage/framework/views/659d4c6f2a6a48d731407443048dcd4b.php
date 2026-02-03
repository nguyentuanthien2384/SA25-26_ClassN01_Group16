
<?php $__env->startSection('content'); ?>

<style>
.article-detail-page {
    background: #f8f9fa;
    padding: 40px 0 80px;
    min-height: calc(100vh - 200px);
}

.article-breadcrumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 30px 0;
    margin-bottom: 40px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.breadcrumb-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
}

.breadcrumb-list li {
    color: #fff;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
}

.breadcrumb-list li a {
    color: #fff;
    text-decoration: none;
    transition: opacity 0.3s ease;
}

.breadcrumb-list li a:hover {
    opacity: 0.8;
}

.breadcrumb-list li:not(:last-child)::after {
    content: '›';
    margin: 0 10px;
    font-size: 1.2rem;
}

.article-detail-container {
    max-width: 900px;
    margin: 0 auto;
}

.article-detail-card {
    background: #fff;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.article-hero-image {
    position: relative;
    width: 100%;
    height: 500px;
    overflow: hidden;
    background: #f0f0f0;
}

.article-hero-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.article-meta-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%);
    padding: 30px;
}

.article-date {
    display: inline-flex;
    align-items: center;
    background: rgba(255,255,255,0.95);
    color: #667eea;
    padding: 8px 20px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.article-date i {
    margin-right: 8px;
}

.article-content-wrapper {
    padding: 50px;
}

.article-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 30px;
    line-height: 1.3;
}

.article-description {
    font-size: 1.2rem;
    color: #718096;
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 2px solid #e2e8f0;
    font-style: italic;
}

.article-content {
    font-size: 1.05rem;
    line-height: 1.8;
    color: #4a5568;
}

.article-content p {
    margin-bottom: 20px;
}

.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    margin: 30px 0;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.article-content h1,
.article-content h2,
.article-content h3,
.article-content h4 {
    color: #2d3748;
    margin-top: 40px;
    margin-bottom: 20px;
    font-weight: 700;
}

.article-content ul,
.article-content ol {
    margin: 20px 0;
    padding-left: 30px;
}

.article-content li {
    margin-bottom: 10px;
}

.article-content blockquote {
    border-left: 4px solid #667eea;
    padding-left: 20px;
    margin: 30px 0;
    font-style: italic;
    color: #4a5568;
    background: #f7fafc;
    padding: 20px;
    border-radius: 5px;
}

.back-button {
    display: inline-flex;
    align-items: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 12px 30px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    margin-bottom: 30px;
}

.back-button:hover {
    transform: translateX(-5px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: #fff;
}

.back-button i {
    margin-right: 8px;
}

.article-share {
    margin-top: 50px;
    padding-top: 30px;
    border-top: 2px solid #e2e8f0;
    text-align: center;
}

.article-share h5 {
    color: #4a5568;
    margin-bottom: 20px;
    font-weight: 600;
}

.share-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.share-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    color: #fff;
    transition: all 0.3s ease;
    text-decoration: none;
}

.share-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.share-btn.facebook { background: #3b5998; }
.share-btn.twitter { background: #1da1f2; }
.share-btn.linkedin { background: #0077b5; }
.share-btn.email { background: #dd4b39; }

@media (max-width: 768px) {
    .article-hero-image {
        height: 300px;
    }
    
    .article-content-wrapper {
        padding: 30px 20px;
    }
    
    .article-title {
        font-size: 1.8rem;
    }
    
    .article-description {
        font-size: 1rem;
    }
}
</style>

<!-- ARTICLE DETAIL PAGE START -->
<div class="article-detail-page">
    <!-- Breadcrumb -->
    <div class="article-breadcrumb">
        <div class="container">
            <ul class="breadcrumb-list">
                <li><a href="<?php echo e(route('home')); ?>"><i class="fa fa-home"></i> Trang chủ</a></li>
                <li><a href="<?php echo e(route('get.list.article')); ?>">Tin tức</a></li>
                <li><?php echo e($articless->a_name); ?></li>
            </ul>
        </div>
    </div>

    <!-- Article Content -->
    <div class="container">
        <div class="article-detail-container">
            <!-- Back Button -->
            <a href="<?php echo e(route('get.list.article')); ?>" class="back-button">
                <i class="fa fa-arrow-left"></i>
                Quay lại danh sách
            </a>

            <!-- Article Card -->
            <div class="article-detail-card">
                <!-- Hero Image -->
                <div class="article-hero-image">
                    <img src="<?php echo e($articless->a_avatar ?: '/images/default-article.jpg'); ?>" 
                         alt="<?php echo e($articless->a_name); ?>" />
                    <div class="article-meta-overlay">
                        <span class="article-date">
                            <i class="fa fa-calendar"></i>
                            <?php echo e($articless->created_at ? $articless->created_at->format('d/m/Y H:i') : 'N/A'); ?>

                        </span>
                    </div>
                </div>

                <!-- Content -->
                <div class="article-content-wrapper">
                    <h1 class="article-title"><?php echo e($articless->a_name); ?></h1>
                    
                    <?php if($articless->a_description): ?>
                        <div class="article-description">
                            <?php echo strip_tags($articless->a_description); ?>

                        </div>
                    <?php endif; ?>
                    
                    <div class="article-content">
                        <?php echo $articless->a_content; ?>

                    </div>

                    <!-- Share Section -->
                    <div class="article-share">
                        <h5><i class="fa fa-share-alt"></i> Chia sẻ bài viết</h5>
                        <div class="share-buttons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e(urlencode(url()->current())); ?>" 
                               target="_blank" 
                               class="share-btn facebook"
                               title="Chia sẻ trên Facebook">
                                <i class="fa fa-facebook"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo e(urlencode(url()->current())); ?>&text=<?php echo e(urlencode($articless->a_name)); ?>" 
                               target="_blank" 
                               class="share-btn twitter"
                               title="Chia sẻ trên Twitter">
                                <i class="fa fa-twitter"></i>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo e(urlencode(url()->current())); ?>&title=<?php echo e(urlencode($articless->a_name)); ?>" 
                               target="_blank" 
                               class="share-btn linkedin"
                               title="Chia sẻ trên LinkedIn">
                                <i class="fa fa-linkedin"></i>
                            </a>
                            <a href="mailto:?subject=<?php echo e(urlencode($articless->a_name)); ?>&body=<?php echo e(urlencode(url()->current())); ?>" 
                               class="share-btn email"
                               title="Chia sẻ qua Email">
                                <i class="fa fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ARTICLE DETAIL PAGE END -->

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web_Ban_Do_Dien_Tu\resources\views\article\detail.blade.php ENDPATH**/ ?>