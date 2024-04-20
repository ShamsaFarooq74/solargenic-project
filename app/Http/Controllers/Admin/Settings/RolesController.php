<?php


namespace App\Http\Controllers\Admin\Settings;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{

    public function index() {
        $roles = Role::all();//Get all roles

        return view('admin.settings.roles')->with('roles', $roles);
    }


    public function create() {
        $permissions = Permission::all();//Get all permissions

        return view('admin.settings.create-role', ['permissions'=>$permissions]);
    }


    public function store(Request $request) {
        //Validate name and permissions field
        $this->validate($request, [
                'name'=>'required|unique:roles|max:10',
                'permissions' =>'required',
            ]
        );

        $name = $request['name'];
        $role = new Role();
        $role->name = $name;

        $permissions = $request['permissions'];

        $role->save();
        //Looping thru selected permissions
        foreach ($permissions as $permission) {
            $p = Permission::where('id', '=', $permission)->firstOrFail();
            //Fetch the newly created role and assign permission
            $role = Role::where('name', '=', $name)->first();
            $role->givePermissionTo($p);
        }

        Session::flash('message', 'Role added successfully');
        Session::flash('alert-class', 'alert-success');
        return redirect('admin/roles-&-permissions');
    }


    public function show($id) {
        return redirect('roles');
    }


    public function edit($id) {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();

        return view('admin.settings.edit-roles', compact('role', 'permissions'));
    }


    public function update(Request $request, $id) {

        $role = Role::findOrFail($id);//Get role with the given id
        //Validate name and permission fields
        $this->validate($request, [
            'name'=>'required|max:10|unique:roles,name,'.$id,
            'permissions' =>'required',
        ]);

        $input = $request->except(['permissions']);
        $permissions = $request['permissions'];
        $role->fill($input)->save();

        $p_all = Permission::all();//Get all permissions

        foreach ($p_all as $p) {
            $role->revokePermissionTo($p); //Remove all permissions associated with role
        }

        foreach ($permissions as $permission) {
            $p = Permission::where('id', '=', $permission)->firstOrFail(); //Get corresponding form //permission in db
            $role->givePermissionTo($p);  //Assign permission to role
        }

        Session::flash('message', 'Role updated successfully');
        Session::flash('alert-class', 'alert-success');
        return redirect('admin/roles-&-permissions');
    }


    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        Session::flash('message', 'Role deleted successfully');
        Session::flash('alert-class', 'alert-success');
        return redirect('admin/roles-&-permissions');

    }
}
