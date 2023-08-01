<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('role_or_permission:Super Admin|user.list', ['only' => ['index', 'show']]);
        $this->middleware('role_or_permission:Super Admin|user.create', ['only' => ['create', 'store']]);
        $this->middleware('role_or_permission:Super Admin|user.edit', ['only' => ['edit', 'update']]);
        $this->middleware('role_or_permission:Super Admin|user.delete', ['only' => ['destroy']]);
    }

    public function login()
    {
        if (Auth::user()) {
            return redirect()->intended('/');
        }

        return view('users.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roles = Role::all();
        $data = [
            'title' => 'User Management',
            'subtitle' => 'List Users',
            'roles' => $roles
        ];

        $users = User::get();
        foreach ($users as $user) {
            $role = Role::where('id', $user->role_id)->first();
            $user['role_name'] = $role->name;
        }

        if ($request->ajax()) {
            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<button title="Edit" type="button" data-toggle="modal" data-target="#modal-detail" data-id="' . $row->id . '" class="edit btn btn-icon btn-primary mx-1" id="detail"><svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.9999 15.6004C13.6895 15.6004 14.3508 15.3265 14.8384 14.8389C15.326 14.3513 15.5999 13.69 15.5999 13.0004C15.5999 12.3108 15.326 11.6495 14.8384 11.1619C14.3508 10.6743 13.6895 10.4004 12.9999 10.4004C12.3103 10.4004 11.649 10.6743 11.1614 11.1619C10.6738 11.6495 10.3999 12.3108 10.3999 13.0004C10.3999 13.69 10.6738 14.3513 11.1614 14.8389C11.649 15.3265 12.3103 15.6004 12.9999 15.6004Z" fill="#ffffff"/><path fill-rule="evenodd" clip-rule="evenodd" d="M0.595459 13.0004C2.25166 7.72629 7.17866 3.90039 13.0001 3.90039C18.8215 3.90039 23.7485 7.72629 25.4047 13.0004C23.7485 18.2745 18.8215 22.1004 13.0001 22.1004C7.17866 22.1004 2.25166 18.2745 0.595459 13.0004ZM18.2001 13.0004C18.2001 14.3795 17.6522 15.7022 16.677 16.6773C15.7018 17.6525 14.3792 18.2004 13.0001 18.2004C11.6209 18.2004 10.2983 17.6525 9.3231 16.6773C8.34791 15.7022 7.80006 14.3795 7.80006 13.0004C7.80006 11.6213 8.34791 10.2986 9.3231 9.32344C10.2983 8.34825 11.6209 7.80039 13.0001 7.80039C14.3792 7.80039 15.7018 8.34825 16.677 9.32344C17.6522 10.2986 18.2001 11.6213 18.2001 13.0004Z" fill="#ffffff"/></svg></button><button title="Edit" type="button" data-toggle="modal" data-target="#modal-form" data-id="' . $row->id . '" class="edit btn btn-icon btn-success mx-auto" id="edit"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button> <form id="form_delete_data" style="display:inline" class="" action="/departments/delete/' . $row->id . '" method="post" title="Delete"><button title="Delete" type="submit"  class="btn btn-icon btn-danger mx-auto" onclick="sweetConfirm(' . $row->id . ')"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button><input type="hidden" name="_method" value="delete" /><input type="hidden" name="_token" value="' . csrf_token() . '"></form>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('users.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->only('username', 'email', 'password', 'role_id');

        $validatedData = Validator::make($data, [
            'username' => 'required|max:255',
            'email' => 'required|max:255|unique:users,email',
            // 'image' => 'image|file|max:1024',
            'password' => 'required|max:255',
            'role_id' => 'required'
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                "statusCode" => 400,
                "error" => "Bad Request",
                "message" => $validatedData->errors()->toArray()
            ], 400);
        }

        $data['password'] = bcrypt($data['password']);
        // $data['image'] = null;
        // if ($request->file('image')) {
        //     $data['image'] = Storage::disk('local')->put('post-images', $request->file('image'));
        // }
        $user = User::create($data);
        $role = Role::where('id', $data['role_id'])->first();
        $user->assignRole($role->name);

        return response()->json([
            "statusCode" => 201,
            "status" => 'Created',
            "message" => 'User successfull added!'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $role = Role::where('id', $user->role_id)->first();
        $user['role_name'] = $role->name;
        return response()->json(["statusCode" => 200, "status" => "Success", "data" => $user], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->only('username', 'email', 'password', 'role_id');

        $rules = [
            'username' => 'required|max:255',
            // 'image' => 'image|file|max:1024',
            'password' => 'required|max:255',
            'role_id' => 'required'
        ];

        if ($request->email != $user->email) {
            $rules['email'] = 'required|max:255|unique:users,email';
        }

        $validatedData = Validator::make($data, $rules);

        if ($validatedData->fails()) {
            return response()->json([
                "statusCode" => 400,
                "error" => "Bad Request",
                "message" => $validatedData->errors()->toArray()
            ], 400);
        }

        $data['password'] = bcrypt($data['password']);
        // $data['image'] = null;
        // if ($request->file('image')) {
        //     $data['image'] = Storage::disk('local')->put('post-images', $request->file('image'));
        // }
        $result = User::where('id', $user->id)->update($data);
        if ($request->role_id != $user->role_id) {
            $roleOld = Role::where('id', $user->role_id)->first();
            $result->revokeRole($roleOld->name);
            $role = Role::where('id', $data['role_id'])->first();
            $result->assignRole($role->name);
        }

        return response()->json([
            "statusCode" => 200,
            "status" => 'Success',
            "message" => 'User successfull updated!'
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        User::destroy($user->id);

        return response()->json([
            "statusCode" => 200,
            "status" => 'Success',
            "message" => 'User successfull deleted!'
        ], 200);
    }
}
