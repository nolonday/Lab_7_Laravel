<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Article $article): RedirectResponse
    {
        $comment = $article->comments()->make($request->validated());
        $comment->user()->associate($request->user());
        $comment->save();

        return redirect()
            ->route('articles.show', $article)
            ->with('status', 'Комментарий успешно добавлен.');
    }

    public function update(
        CommentRequest $request,
        Article $article,
        Comment $comment,
    ): RedirectResponse {
        $comment->update($request->validated());

        return redirect()
            ->route('articles.show', $article)
            ->with('status', 'Комментарий успешно обновлён.');
    }

    public function destroy(Article $article, Comment $comment): RedirectResponse
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return redirect()
            ->route('articles.show', $article)
            ->with('status', 'Комментарий успешно удалён.');
    }
}
