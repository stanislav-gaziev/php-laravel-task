<?php

use Illuminate\Support\Facades\Route;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $books = Book::get(['id', 'name', 'price'])->toArray();
    $booksForSelectForm = [0 => ''];

    foreach ($books as $book) {
        $booksForSelectForm[$book['id']] = "{$book['name']} ({$book['price']})";
    }

    $authors = Author::get(['id', 'full_name'])->toArray();
    $authorsForSelectForm = [0 => ''];

    foreach ($authors as $author) {
        $authorsForSelectForm[$author['id']] = "{$author['full_name']}";
    }

    return view('index', compact('booksForSelectForm', 'authorsForSelectForm'));
});

Route::get('ajax/book', function (Request $request) {
    $book = Book::findOrFail($request->input('id'));
    $authors = $book->authors->toArray();
    $authorsForView = [];

    foreach ($authors as $author) {
        $authorsForView[] = $author['full_name'];
    }

    return response()->json($authorsForView);
})->name('ajax.book');

Route::get('ajax/author', function (Request $request) {
    $author = Author::findOrFail($request->input('id'));
    $sum = $author->books->sum('price');

    return response()->json($sum);
})->name('ajax.author');

Route::get('ajax/list', function (Request $request) {
    $booksWithAuthor = DB::table('author_book')->groupBy('book_id')->pluck('book_id')->toArray();
    $booksWithoutAuthor = Book::whereNotIn('id', $booksWithAuthor)->get();
    $booksForView = [];

    foreach ($booksWithoutAuthor as $book) {
        $booksForView[] = "{$book['name']} ({$book['price']})";
    }

    return response()->json($booksForView);
})->name('ajax.list');
