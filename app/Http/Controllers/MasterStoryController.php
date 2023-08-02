<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Story;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MasterStoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('role_or_permission:Super Admin|master_article.list', ['only' => ['index', 'show']]);
        $this->middleware('role_or_permission:Super Admin|master_article.create', ['only' => ['create', 'store']]);
        $this->middleware('role_or_permission:Super Admin|master_article.edit', ['only' => ['edit', 'update']]);
        $this->middleware('role_or_permission:Super Admin|master_article.delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $data = [
            'title' => 'Master Data',
            'subtitle' => 'List Article'
        ];

        $stories = Story::select()->where('user_id', $user->id)->latest()->get();

        foreach ($stories as $story) {
            $category = Category::where('id', $story->category_id)->first();
            $story['category_name'] = $category->name;
        }

        if ($request->ajax()) {
            return DataTables::of($stories)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="/master-articles/' . $row->slug . '" class="edit btn btn-icon btn-primary mx-1" id="detail"><svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.9999 15.6004C13.6895 15.6004 14.3508 15.3265 14.8384 14.8389C15.326 14.3513 15.5999 13.69 15.5999 13.0004C15.5999 12.3108 15.326 11.6495 14.8384 11.1619C14.3508 10.6743 13.6895 10.4004 12.9999 10.4004C12.3103 10.4004 11.649 10.6743 11.1614 11.1619C10.6738 11.6495 10.3999 12.3108 10.3999 13.0004C10.3999 13.69 10.6738 14.3513 11.1614 14.8389C11.649 15.3265 12.3103 15.6004 12.9999 15.6004Z" fill="#ffffff"/><path fill-rule="evenodd" clip-rule="evenodd" d="M0.595459 13.0004C2.25166 7.72629 7.17866 3.90039 13.0001 3.90039C18.8215 3.90039 23.7485 7.72629 25.4047 13.0004C23.7485 18.2745 18.8215 22.1004 13.0001 22.1004C7.17866 22.1004 2.25166 18.2745 0.595459 13.0004ZM18.2001 13.0004C18.2001 14.3795 17.6522 15.7022 16.677 16.6773C15.7018 17.6525 14.3792 18.2004 13.0001 18.2004C11.6209 18.2004 10.2983 17.6525 9.3231 16.6773C8.34791 15.7022 7.80006 14.3795 7.80006 13.0004C7.80006 11.6213 8.34791 10.2986 9.3231 9.32344C10.2983 8.34825 11.6209 7.80039 13.0001 7.80039C14.3792 7.80039 15.7018 8.34825 16.677 9.32344C17.6522 10.2986 18.2001 11.6213 18.2001 13.0004Z" fill="#ffffff"/></svg></a><a href="/master-articles/' . $row->slug . '/edit" class="edit btn btn-icon btn-success mx-auto" id="edit"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a> <form id="form_delete_data" style="display:inline" class="" action="/departments/delete/' . $row->id . '" method="post" title="Delete"><button title="Delete" type="submit"  class="btn btn-icon btn-danger mx-auto" onclick="sweetConfirm(' . $row->id . ')"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button><input type="hidden" name="_method" value="delete" /><input type="hidden" name="_token" value="' . csrf_token() . '"></form>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('stories.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'title' => 'Master Data',
            'subtitle' => 'Create Article',
            'categories' => Category::all()
        ];

        return view('stories.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'required|unique:stories',
            'category_id' => 'required',
            'image' => 'image|file|max:1024',
            'body' => 'required'
        ]);

        if ($request->file('image')) {
            $validatedData['image'] = $request->file('image')->store('story-images');
        }

        $validatedData['user_id'] = $user->id;
        $validatedData['excerpt'] = Str::limit(strip_tags($request->body), 200);

        Story::create($validatedData);

        return redirect('/master-articles')->with('success', 'New post has been added!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Story $story, $slug)
    {
        $story = Story::where('slug', $slug)->first();
        $user = User::where('id', Auth::user()->id)->first();

        if (!Gate::allows('story', $story) && !$user->hasRole('Super Admin')) {
            abort(403);
        }
        $data = [
            'title' => 'Master Data',
            'subtitle' => 'Detail Article',
            'story' => $story
        ];

        return view('stories.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Story $story, $slug)
    {
        $story = Story::where('slug', $slug)->first();
        $user = User::where('id', Auth::user()->id)->first();

        if (!Gate::allows('story', $story) && !$user->hasRole('Super Admin')) {
            abort(403);
        }

        $data = [
            'title' => 'Master Data',
            'subtitle' => 'Edit Article',
            'story' => $story,
            'categories' => Category::all()
        ];

        return view('stories.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Story $story, $slug)
    {
        $story = Story::where('slug', $slug)->first();
        $user = User::where('id', Auth::user()->id)->first();

        if (!Gate::allows('story', $story) && !$user->hasRole('Super Admin')) {
            abort(403);
        }

        $rules = [
            'title' => 'required|max:255',
            'category_id' => 'required',
            'image' => 'image|file|max:1024',
            'body' => 'required'
        ];

        if ($request->slug != $story->slug) {
            $rules['slug'] = 'required|unique:stories';
        }

        $validatedData = $request->validate($rules);

        if ($request->file('image')) {
            if ($request->oldImage) {
                Storage::delete($request->oldImage);
            }
            $validatedData['image'] = $request->file('image')->store('story-images');
        }

        $validatedData['user_id'] = auth()->user()->id;
        $validatedData['excerpt'] = Str::limit(strip_tags($request->body), 200);

        Story::where('id', $story->id)
            ->update($validatedData);

        return redirect('/master-articles')->with('success', 'Article has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Story $story, $id)
    {
        $story = Story::where('id', $id)->first();
        $user = User::where('id', Auth::user()->id)->first();

        if (!Gate::allows('story', $story) && !$user->hasRole('Super Admin')) {
            abort(403);
        }

        Story::destroy($story->id);
        Storage::delete($story->image);

        return response()->json([
            "statusCode" => 200,
            "status" => 'Success',
            "message" => 'Article successfull deleted!'
        ], 200);
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Story::class, 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }
}
