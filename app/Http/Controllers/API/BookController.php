<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{


    public function index()
    {
        $user = auth()->user();



        if ($user->hasRole('superadmin')) {
            return response()->json(Book::all()); // Superadmin can view all books
        } elseif ($user->hasRole('admin')) {
            return response()->json($user->books); // Admin can view their own books
        } elseif ($user->hasRole('user')) {

            return response()->json($user->books); // User can view their own books
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }

    public function store(Request $request)
    {


        $validator = \Validator::make($request->all(), [
            'title' => 'required|string',
            'author' => 'required|string',
            'description' => 'required|string',
            'ISBN' => 'required|string|unique:books',
            'published_year' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $book = Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'description' => $request->description,
            'ISBN' => $request->ISBN,
            'published_year' => $request->published_year,
            'user_id' => auth()->user()->id,
        ]);

        return response()->json('Book added successfully', 201);
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $user = auth()->user();

        // Superadmin can update any book, User can only update their own book
        if (!$user->hasRole('superadmin') && $user->id != $book->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $book->update($request->only(['title', 'author', 'description', 'ISBN', 'published_year']));

        return response()->json($book);
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        $user = auth()->user();

       // Superadmin can update any book, User can only update their own book
        if (!$user->hasRole('superadmin') && $user->id != $book->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $book->delete();

        return response()->json(['message' => 'Book deleted successfully']);
    }
}
