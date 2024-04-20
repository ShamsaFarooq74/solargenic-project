<?php


namespace App\Http\Controllers\Admin\Settings;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsController extends Controller
{

    public function index() {
        $permissions = Permission::all(); //Get all permissions

        return view('admin.settings.permissions')->with('permissions', $permissions);
    }


    public function create() {
        $roles = Role::get(); //Get all roles

        return view('admin.settings.create-permission')->with('roles', $roles);
    }


    public function store(Request $request) {
        $this->validate($request, [
            'name'=>'required|max:40',
        ]);

        $name = $request['name'];
        $permission = new Permission();
        $permission->name = $name;

        $roles = $request['roles'];

        $permission->save();

        if (!empty($request['roles'])) { //If one or more role is selected
            foreach ($roles as $role) {
                $r = Role::where('id', '=', $role)->firstOrFail(); //Match input role to db record

                $permission = Permission::where('name', '=', $name)->first(); //Match input //permission to db record
                $r->givePermissionTo($permission);
            }
        }

        Session::flash('message', 'Permission added successfully');
        Session::flash('alert-class', 'alert-success');
        return redirect('admin/permissions');
    }

    public function show($id) {
        return redirect('permissions');
    }


    public function edit($id) {
        $permission = Permission::findOrFail($id);

        return view('admin.settings.edit-permission', compact('permission'));
    }


    public function update(Request $request, $id) {
        $permission = Permission::findOrFail($id);
        $this->validate($request, [
            'name'=>'required|max:40',
        ]);
        $input = $request->all();
        $permission->fill($input)->save();

        Session::flash('message', 'Permission updated successfully');
        Session::flash('alert-class', 'alert-success');
        return redirect('admin/permissions');

    }


    public function destroy($id) {
        $permission = Permission::findOrFail($id);

        //Make it impossible to delete this specific permission
        if ($permission->name == "Administer roles & permissions") {
            Session::flash('message', 'Cannot delete this Permission!');
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/permissions');
        }

        $permission->delete();

        Session::flash('message', 'Permission deleted successfully');
        Session::flash('alert-class', 'alert-success');
        return redirect('admin/permissions');

    }
}
