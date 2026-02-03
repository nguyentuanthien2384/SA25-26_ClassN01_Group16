<?php

namespace App\Http\Controllers;

use App\Models\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ArticleController extends Controller
{
    /**
     * Hiển thị danh sách tin tức với pagination
     */
    public function getArticle(Request $request)
    {
        // Lấy số trang hiện tại
        $page = $request->input('page', 1);
        $perPage = 9; // 9 bài/trang để hiển thị grid 3x3
        
        // Cache key
        $cacheKey = "articles:list:{$perPage}:{$page}";
        
        // Cache trong 5 phút
        $articles = Cache::remember($cacheKey, 300, function () use ($perPage, $page) {
            return Article::select([
                'id',
                'a_name',
                'a_slug',
                'a_description',
                'a_avatar',
                'a_active',
                'created_at'
            ])
            ->where('a_active', 1) // Chỉ lấy bài viết đang active
            ->orderBy('id', 'DESC')
            ->paginate($perPage, ['*'], 'page', $page);
        });

        return view('article.index', compact('articles'));
    }

    /**
     * Hiển thị chi tiết bài viết
     */
    public function getDetailArticle(Request $request)
    {
        // Parse URL để lấy ID
        $url = (preg_split('/(-)/i', $request->segment(2)));
        $id = array_pop($url);
        
        if (!$id) {
            abort(404, 'Bài viết không tồn tại');
        }

        // Cache chi tiết bài viết
        $cacheKey = "article:detail:{$id}";
        $articless = Cache::remember($cacheKey, 300, function () use ($id) {
            return Article::where([
                'id' => $id,
                'a_active' => 1
            ])->first();
        });

        if (!$articless) {
            abort(404, 'Bài viết không tồn tại hoặc đã bị xóa');
        }

        $viewData = [
            'articless' => $articless
        ];

        return view('article.detail', $viewData);
    }
}
