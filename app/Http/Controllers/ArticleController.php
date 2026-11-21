<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ArticleController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Article::class);

        return view('articles.index', [
            'articles' => Article::query()
                ->latest('published_at')
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Article::class);

        return view('articles.create', [
            'article' => new Article,
        ]);
    }

    public function store(ArticleRequest $request): RedirectResponse
    {
        Article::create($request->validated());

        return redirect()
            ->route('articles.index')
            ->with('status', 'Новость успешно создана.');
    }

    public function show(Article $article): View
    {
        Gate::authorize('view', $article);
        Gate::authorize('viewAny', Comment::class);

        return view('articles.show', [
            'article' => $article,
            'comments' => $article->comments()->with('user')->latest()->get(),
        ]);
    }

    public function edit(Article $article): View
    {
        Gate::authorize('update', $article);

        return view('articles.edit', [
            'article' => $article,
        ]);
    }

    public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        $article->update($request->validated());

        return redirect()
            ->route('articles.index')
            ->with('status', 'Новость успешно обновлена.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        Gate::authorize('delete', $article);

        $article->delete();

        return redirect()
            ->route('articles.index')
            ->with('status', 'Новость успешно удалена.');
    }
}
