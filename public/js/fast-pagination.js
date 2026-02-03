/**
 * SUPER FAST AJAX PAGINATION
 * Load sản phẩm nhanh như chớp với cache, prefetch, lazy loading
 */

class FastPagination {
    constructor(options) {
        this.container = options.container;
        this.endpoint = options.endpoint;
        this.perPage = options.perPage || 4;
        this.currentPage = 1;
        this.totalPages = 1;
        this.cache = new Map();
        this.abortController = null;
        this.isLoading = false;
        
        this.init();
    }
    
    init() {
        // Load trang đầu tiên
        this.loadPage(1);
        
        // Setup pagination click handlers
        this.setupPaginationHandlers();
        
        // Setup intersection observer cho lazy loading images
        this.setupLazyLoading();
    }
    
    /**
     * Load trang với AJAX - CỰC NHANH
     */
    async loadPage(page, skipAnimation = false) {
        if (this.isLoading) {
            return;
        }
        
        // Check cache trước
        if (this.cache.has(page)) {
            this.renderProducts(this.cache.get(page), skipAnimation);
            this.currentPage = page;
            this.updatePagination();
            return;
        }
        
        this.isLoading = true;
        
        // Show skeleton loading
        if (!skipAnimation) {
            this.showSkeletonLoading();
        }
        
        // Cancel previous request nếu có
        if (this.abortController) {
            this.abortController.abort();
        }
        
        this.abortController = new AbortController();
        
        try {
            const response = await fetch(
                `${this.endpoint}?page=${page}&per_page=${this.perPage}`,
                { 
                    signal: this.abortController.signal,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            );
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const data = await response.json();
            
            // Cache kết quả
            this.cache.set(page, data);
            
            // Update total pages
            this.totalPages = data.last_page;
            this.currentPage = page;
            
            // Render products
            this.renderProducts(data, skipAnimation);
            
            // Update pagination
            this.updatePagination();
            
            // Prefetch trang kế tiếp (load trước để chuyển nhanh hơn)
            this.prefetchNextPage();
            
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Error loading products:', error);
                this.showError();
            }
        } finally {
            this.isLoading = false;
        }
    }
    
    /**
     * Render sản phẩm với animation mượt mà
     */
    renderProducts(data, skipAnimation = false) {
        const container = document.querySelector(this.container);
        if (!container) return;
        
        if (skipAnimation) {
            container.innerHTML = this.buildProductsHTML(data.data);
            this.setupLazyLoading();
            return;
        }
        
        // Fade out animation
        container.style.opacity = '0';
        container.style.transition = 'opacity 0.2s ease-in-out';
        
        setTimeout(() => {
            container.innerHTML = this.buildProductsHTML(data.data);
            this.setupLazyLoading();
            
            // Fade in animation
            requestAnimationFrame(() => {
                container.style.opacity = '1';
            });
            
            // Smooth scroll về đầu container
            container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 200);
    }
    
    /**
     * Build HTML cho sản phẩm
     */
    buildProductsHTML(products) {
        if (!products || products.length === 0) {
            return '<div class="col-12 text-center py-5"><p>Không có sản phẩm</p></div>';
        }
        
        return products.map(product => `
            <div class="col-md-3 col-sm-6 col-6 mb-4 product-item">
                <div class="product-card">
                    ${product.pro_hot == 1 ? '<span class="badge badge-danger">Giảm 14%</span>' : '<span class="badge badge-info">Trả góp 0%</span>'}
                    <a href="/product/${product.pro_slug}-${product.id}.html">
                        <img 
                            data-src="/uploads/product/${product.pro_avatar}" 
                            class="lazy-image product-image"
                            alt="${product.pro_name}"
                            style="width: 100%; height: 200px; object-fit: cover; background: #f0f0f0;"
                        >
                    </a>
                    <div class="product-info p-3">
                        <h5 class="product-title">
                            <a href="/product/${product.pro_slug}-${product.id}.html">${product.pro_name}</a>
                        </h5>
                        <div class="product-price">
                            ${product.pro_sale ? 
                                `<span class="price-new">${this.formatPrice(product.pro_sale)}</span>
                                 <span class="price-old">${this.formatPrice(product.pro_price)}</span>` :
                                `<span class="price-new">${this.formatPrice(product.pro_price)}</span>`
                            }
                        </div>
                        <p class="product-promo">Trả góp 0% - Ođi phụ thu - Ođ trà trước - ký hạn đến 6 tháng</p>
                        <div class="product-rating">
                            <span class="stars">⭐ 0</span>
                            <button class="btn-wishlist">❤ Yêu thích</button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }
    
    /**
     * Format giá tiền
     */
    formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    }
    
    /**
     * Show skeleton loading (giống Shopee)
     */
    showSkeletonLoading() {
        const container = document.querySelector(this.container);
        if (!container) return;
        
        const skeletons = Array(this.perPage).fill(0).map(() => `
            <div class="col-md-3 col-sm-6 col-6 mb-4">
                <div class="skeleton-card">
                    <div class="skeleton-image"></div>
                    <div class="skeleton-title"></div>
                    <div class="skeleton-price"></div>
                    <div class="skeleton-text"></div>
                </div>
            </div>
        `).join('');
        
        container.style.opacity = '0.6';
        container.innerHTML = skeletons;
    }
    
    /**
     * Setup pagination click handlers
     */
    setupPaginationHandlers() {
        // Delegate event cho pagination
        document.addEventListener('click', (e) => {
            const paginationLink = e.target.closest('.pagination-link');
            if (!paginationLink) return;
            
            e.preventDefault();
            const page = parseInt(paginationLink.dataset.page);
            
            if (page && page !== this.currentPage && page >= 1 && page <= this.totalPages) {
                this.loadPage(page);
            }
        });
    }
    
    /**
     * Update pagination HTML
     */
    updatePagination() {
        const paginationContainer = document.querySelector(this.container + '-pagination');
        if (!paginationContainer) return;
        
        let html = '<div class="pagination-wrapper">';
        
        // Previous button
        html += `<button class="pagination-btn pagination-link ${this.currentPage === 1 ? 'disabled' : ''}" 
                    data-page="${this.currentPage - 1}" 
                    ${this.currentPage === 1 ? 'disabled' : ''}>
                    <
                 </button>`;
        
        // Page numbers
        const pages = this.getPageNumbers();
        pages.forEach(page => {
            if (page === '...') {
                html += '<span class="pagination-dots">...</span>';
            } else {
                html += `<button class="pagination-btn pagination-link ${page === this.currentPage ? 'active' : ''}" 
                            data-page="${page}">
                            ${page}
                         </button>`;
            }
        });
        
        // Next button
        html += `<button class="pagination-btn pagination-link ${this.currentPage === this.totalPages ? 'disabled' : ''}" 
                    data-page="${this.currentPage + 1}"
                    ${this.currentPage === this.totalPages ? 'disabled' : ''}>
                    >
                 </button>`;
        
        html += '</div>';
        
        paginationContainer.innerHTML = html;
    }
    
    /**
     * Get page numbers với ellipsis (1 2 3 ... 18)
     */
    getPageNumbers() {
        const pages = [];
        const maxVisible = 5;
        
        if (this.totalPages <= maxVisible) {
            for (let i = 1; i <= this.totalPages; i++) {
                pages.push(i);
            }
        } else {
            if (this.currentPage <= 3) {
                for (let i = 1; i <= 4; i++) {
                    pages.push(i);
                }
                pages.push('...');
                pages.push(this.totalPages);
            } else if (this.currentPage >= this.totalPages - 2) {
                pages.push(1);
                pages.push('...');
                for (let i = this.totalPages - 3; i <= this.totalPages; i++) {
                    pages.push(i);
                }
            } else {
                pages.push(1);
                pages.push('...');
                pages.push(this.currentPage - 1);
                pages.push(this.currentPage);
                pages.push(this.currentPage + 1);
                pages.push('...');
                pages.push(this.totalPages);
            }
        }
        
        return pages;
    }
    
    /**
     * Prefetch trang kế tiếp (load trước để tăng tốc)
     */
    prefetchNextPage() {
        const nextPage = this.currentPage + 1;
        if (nextPage <= this.totalPages && !this.cache.has(nextPage)) {
            // Load im lặng không hiện UI
            setTimeout(() => {
                fetch(`${this.endpoint}?page=${nextPage}&per_page=${this.perPage}`, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    this.cache.set(nextPage, data);
                })
                .catch(() => {});
            }, 500);
        }
    }
    
    /**
     * Setup lazy loading cho images
     */
    setupLazyLoading() {
        const lazyImages = document.querySelectorAll(this.container + ' .lazy-image');
        
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy-image');
                    img.classList.add('lazy-loaded');
                    observer.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => imageObserver.observe(img));
    }
    
    /**
     * Show error message
     */
    showError() {
        const container = document.querySelector(this.container);
        if (!container) return;
        
        container.innerHTML = `
            <div class="col-12 text-center py-5">
                <p class="text-danger">Có lỗi xảy ra khi tải sản phẩm. Vui lòng thử lại.</p>
                <button class="btn btn-primary" onclick="location.reload()">Tải lại trang</button>
            </div>
        `;
    }
}

// Export để sử dụng
window.FastPagination = FastPagination;
