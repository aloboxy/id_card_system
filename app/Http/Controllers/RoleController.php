<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    function __construct()
    {
         // We'll add middleware later once we have permissions seeded
         $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
         $this->middleware('permission:role-create', ['only' => ['create','store']]);
         $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Role::select('*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $showUrl = route('roles.show', $row->id);
                    $editUrl = route('roles.edit', $row->id);
                    $deleteUrl = route('roles.destroy', $row->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    $actionBtn = '<div class="flex items-center space-x-2">
                            <a href="'.$showUrl.'" class="text-indigo-600 hover:text-indigo-900" title="Show">
                                Show
                            </a>';
                    
                    if(auth()->user()->can('role-edit')){
                        $actionBtn .= '<a href="'.$editUrl.'" class="text-blue-600 hover:text-blue-900" title="Edit">
                                Edit
                            </a>';
                    }

                    if(auth()->user()->can('role-delete')){
                        $actionBtn .= '<form action="'.$deleteUrl.'" method="POST" style="display:inline" onsubmit="return confirm(\'Are you sure you want to delete this role?\');">
                                '.$csrf.'
                                '.$method.'
                                <button type="submit" class="text-red-600 hover:text-red-900 bg-transparent border-0 cursor-pointer p-0">Delete</button>
                            </form>';
                    }
                    
                    $actionBtn .= '</div>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('roles.index');
    }

    public function create()
    {
        $permission = Permission::get();
        return view('roles.create',compact('permission'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);
    
        $role = Role::create(['name' => $request->input('name')]);
        $permissions = array_map('intval', $request->input('permission'));
        $role->syncPermissions($permissions);
    
        return redirect()->route('roles.index')
                        ->with('success','Role created successfully');
    }

    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
    
        return view('roles.show',compact('role','rolePermissions'));
    }

    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
    
        return view('roles.edit',compact('role','permission','rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);
    
        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();
    
        $permissions = array_map('intval', $request->input('permission'));
        $role->syncPermissions($permissions);
    
        return redirect()->route('roles.index')
                        ->with('success','Role updated successfully');
    }

    public function destroy($id)
    {
        DB::table("roles")->where('id',$id)->delete();
        return redirect()->route('roles.index')
                        ->with('success','Role deleted successfully');
    }
}
