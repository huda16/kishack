<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('role_or_permission:Super Admin|role_permission.list', ['only' => ['index', 'show']]);
        $this->middleware('role_or_permission:Super Admin|role_permission.create', ['only' => ['create', 'store']]);
        $this->middleware('role_or_permission:Super Admin|role_permission.edit', ['only' => ['edit', 'update']]);
        $this->middleware('role_or_permission:Super Admin|role_permission.delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $permissions = Permission::get();
        $data = [
            'title' => 'Roles',
            'subtitle' => 'List Roles',
            'permissions' => $permissions
        ];

        $roles = Role::latest()->get();

        if ($request->ajax()) {
            return DataTables::of($roles)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<button title="Edit" type="button" data-toggle="modal" data-target="#modal-form" data-id="' . $row->id . '" class="edit btn btn-icon btn-success mx-auto" id="edit"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button> <form id="form_delete_data" style="display:inline" class="" action="/departments/delete/' . $row->id . '" method="post" title="Delete"><button title="Delete" type="submit"  class="btn btn-icon btn-danger mx-auto" onclick="sweetConfirm(' . $row->id . ')"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button><input type="hidden" name="_method" value="delete" /><input type="hidden" name="_token" value="' . csrf_token() . '"></form>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('roles.index', $data);
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
        $data = $request->only('name', 'permission_id');
        $validatedData = Validator::make($data, [
            'name' => 'required|max:255|unique:roles'
        ]);
        if ($validatedData->fails()) {
            return response()->json([
                "statusCode" => 400,
                "error" => "Bad Request",
                "message" => $validatedData->errors()->toArray()
            ], 400);
        }

        $permission_id = $request->permission_id;

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        foreach ($permission_id as $key) {
            $permission = Permission::where('id', $key)->first();
            $role->givePermissionTo($permission);
        }

        Artisan::call('cache:clear');

        return response()->json([
            "statusCode" => 201,
            "status" => 'Created',
            "message" => 'Role with Permissions successfull added!'
        ], 201);
        $data = $request->only('name', 'permission_id');
        $validatedData = Validator::make($data, [
            'name' => 'required|max:255|unique:roles'
        ]);
        if ($validatedData->fails()) {
            return response()->json([
                "statusCode" => 400,
                "error" => "Bad Request",
                "message" => $validatedData->errors()->toArray()
            ], 400);
        }

        $permission_id = $request->permission_id;

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        foreach ($permission_id as $key) {
            $permission = Permission::where('id', $key)->first();
            $role->givePermissionTo($permission);
        }

        Artisan::call('cache:clear');

        return response()->json([
            "statusCode" => 201,
            "status" => 'Created',
            "message" => 'Role with Permissions successfull added!'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $data = Role::where('id', $role->id)->first();
        if (!$data) {
            return response()->json([
                "statusCode" => 404,
                "error" => "Not Found",
                "message" => "Not Found"
            ], 404);
        }

        $data->getAllPermissions();

        return response()->json(["statusCode" => 200, "status" => "Success", "data" => $data], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $role = Role::where('id', $role->id)->first();
        if (!$role) {
            return response()->json([
                "statusCode" => 404,
                "error" => "Not Found",
                "message" => "Not Found"
            ], 404);
        }

        $data = $request->only('name', 'permission_id');

        if ($request->name != $role->name) {
            $validatedData = Validator::make($data, [
                'name' => 'required|max:191|unique:roles'
            ]);

            if ($validatedData->fails()) {
                return response()->json([
                    "statusCode" => 400,
                    "error" => "Bad Request",
                    "message" => $validatedData->errors()->toArray()
                ], 400);
            }
        }

        $name = [];
        foreach ($request->permission_id as $item) {
            $permission = Permission::where('id', $item)->first();
            if ($permission) {
                $name[] = $permission->name;
            }
        }

        $role->syncPermissions($name);
        Role::where('id', $role->id)->update(['name' => $request->name]);

        return response()->json([
            "statusCode" => 200,
            "status" => 'Success',
            "message" => 'Role with Permissions successfull updated!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $delete = Role::destroy($role->id);
        if ($delete > 0) {
            return response()->json([
                "statusCode" => 200,
                "status" => 'Success',
                "message" => 'Role successfull deleted!'
            ], 200);
        } else {
            return response()->json([
                "statusCode" => 404,
                "error" => "Not Found",
                "message" => "Not Found"
            ], 404);
        }
    }
}
