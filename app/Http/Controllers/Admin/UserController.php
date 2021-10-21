<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\User;
use Spatie\Permission\Models\Role;

use App\Http\Requests\Admin\StoreUsersRequest;
use App\Http\Requests\Admin\UpdateUsersRequest;
use Illuminate\Support\Facades\Gate;

use DataTables;
use Lang;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! Gate::allows('users_manage')) {
            return abort(401);
        }

        if ($request->ajax()) {
            $data = User::select('*');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('Role', function($user){
                            
                        //$btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm"></a>';

                    })
                    ->addColumn('action', function($user){
                        $html ="";
                        $html .="<a class='btn btn-xs btn-primary' href='".route('admin.users.show', $user->id)."'>".trans('global.view')."</a>";
                        
                        $html .="<a class='btn btn-xs btn-info' href='".route('admin.users.edit', $user->id)."'>".trans('global.edit')."</a>";

                        $html .="<form action='".route('admin.users.destroy', $user->id)."' method='POST' onsubmit='return confirm(".trans('global.areYouSure').")' style='display: inline-block;' >";
                            $html .='<input type="hidden" name="_method" value="DELETE">';
                            $html .='<input type="hidden" name="_token" value="'.csrf_token().'">';
                            
                            $html .='<input type="submit" class="btn btn-xs btn-danger" value="'. trans('global.delete').'">';
                        $html .="</form>";

                        return $html;
                        
                    })
                    ->rawColumns(['Role'])
                    ->rawColumns(['action'])
                    ->make(true);
        }

        //abs(number)$users = User::all();

        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('users_manage')) {
            return abort(401);
        }
        $roles = Role::get()->pluck('name', 'name');

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUsersRequest $request)
    {
        if (! Gate::allows('users_manage')) {
            return abort(401);
        }
        $request->merge(['password' => bcrypt($request->password) ]);
        $user = User::create($request->all());
        $roles = $request->input('roles') ? $request->input('roles') : [];
        $user->assignRole($roles);

        return redirect()->route('admin.users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user){
        if (! Gate::allows('users_manage')) {
            return abort(401);
        }

        $user->load('roles');

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if (! Gate::allows('users_manage')) {
            return abort(401);
        }
        $roles = Role::get()->pluck('name', 'name');

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUsersRequest $request, User $user)
    {
        if (! Gate::allows('users_manage')) {
           return abort(401);
       }
       $request->merge(['password' => bcrypt($request->password) ]);
       
       $user->update($request->all());
       $roles = $request->input('roles') ? $request->input('roles') : [];
       $user->syncRoles($roles);

       return redirect()->route('admin.users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if (! Gate::allows('users_manage')) {
            return abort(401);
        }
        $user->delete();

        return redirect()->route('admin.users.index');
    }


    public function Permission(){

    }
}
