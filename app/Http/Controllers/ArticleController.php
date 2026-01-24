<?php

namespace App\Http\Controllers;

use App\Models\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function getArticle(){
        $articles = Article::orderBy('id','DESC')->paginate(6);

        return view('article.index',compact('articles'));
    }
    public function getDetailArticle(Request $request){
        $url = (preg_split('/(-)/i', $request->segment(2)));
        $id = array_pop($url);
        if($id){
            $articless = Article::find($id);
        }
       $viewData = [
        'articless' => $articless
       ];

        return view('article.detail',$viewData);
    }

}
