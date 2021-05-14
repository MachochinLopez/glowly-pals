<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\UsersDataTable;
use App\User;
use App\Teacher;
use App\TeacherStatu;
use App\Role;
use App\Permission;
use App\ModuleType;
use App\PermissionType;
use App\PermissionUser;
use App\Traits\UploadTrait;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
	use UploadTrait;

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('permission');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		//Consultar permiso para botón de agregar
		$allowAdd = User::find(Auth::id())->hasPermission('users.create');

		return (new UsersDataTable())->render('users.index', compact('allowAdd'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//Trae todos los roles de usuario.
		$role_id = Role::orderBy('created_at','desc')->get()->pluck('name', 'id');
		//Trae todos los statuses de maestro
		$teacher_status_id = TeacherStatu::pluck('description', 'id');
		
		return view('users.create', compact('role_id', 'teacher_status_id'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$request->validate([
			'username' 	=> 'required',
			'email' 	=> 'required|unique:users,email',
			'password' 	=> 'confirmed|min:6',
			'role_id' 	=> 'required',
			'picture' 	=> 'image'
		]);

		//validar passwords iguales

		$user = new User();
		$data = $request->all();
		$data["password"] = Hash::make($data["password"]);
		$user->fill($data);

        if ($request->has('picture')) {
            // Get image file
            $image = $request->file('picture');

            // Make a image name based on user name and current timestamp
            $name = str_slug($request->input('picture')).'_'.time();
            // Define folder path
            $folder = '/uploads/images/users/';

            // Make a file path where image will be stored [ folder path + file name + file     extension]
            $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();

            // Upload image
            $this->uploadOne($image, $folder, 'public', $name);

            // Set image path in database to filePath
            $user->picture = $filePath;
        }
        else{
        	$user->picture = asset('/dist/img/avatar.jpg');
        }

		$user->save();

		//Si se crea un usuario con rol de maestro.
		if($request->role_id == 2){
			$request->validate([
				'name' 		 => 'required',
				'paternal_surname' 	=> 'required',
				'maternal_surname' 	=> 'required',
				'birthdate' 	 => 'required',
				'start_date' 	 => 'required',
				'payroll_number' => 'required|unique:teachers,payroll_number',
				'start_date' 	 => 'required',
			]);

			//Crea el nuevo maestro con los datos del formulario 

			$teacher = new Teacher();			
			$data = $request->all();
			$teacher->fill($data);
			$teacher->teacher_status_id = 1;
			$teacher->user_id = $user->id;
			
			$originalDate = $request->birthdate;
            $teacher->birthdate = date("Y-m-d", strtotime($originalDate));
        
            $originalDate = $request->start_date;
            $teacher->start_date = date("Y-m-d", strtotime($originalDate));
			
			$teacher->created_at	 = date("Y-m-d H:i:s");
	        $teacher->updated_at 	 = date("Y-m-d H:i:s");
	        $teacher->created_by	 = Auth::id();
	        $teacher->updated_by	 = Auth::id();
	        $teacher->save();
	        
			Session::flash('message', __('users.Successfully created user'));
			return redirect()->route('users.index');
		}

		Session::flash('message', __('users.Successfully created user'));
		return redirect()->route('users.showextra', ['id' => $user->id]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$user = User::with('role')->find($id);
		return view('users.show', compact('user'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		$user = User::find($id);
		//Trae los roles de usuario.
		$role_id = Role::orderBy('created_at','desc')->get()->pluck('name', 'id');
		//Trae todos los statuses de maestro
		$teacher_status_id = TeacherStatu::pluck('description', 'id');

		$view = view('users.edit', compact('user','role_id', 'teacher_status_id'));

		if($user->role_id == 2){
			$teacher = Teacher::where('user_id', $user->id)->first();
			$view = view('users.edit', compact('user','role_id', 'teacher_status_id', 'teacher'));
		}

		return $view;
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		$user = User::find($id);
		
		$request->validate([
			'username' => 'required',
			'email' => 'required|unique:users,email,'.$id,
			'role_id' => 'required',
            'picture' => 'image',
		]);

		//Si se crea un usuario con rol de maestro.
		if($request->role_id == 2){
			$teacher = Teacher::where('user_id', $user->id)->first();

			$request->validate([
				'name' 		 	 => 'required',
				'paternal_surname' 	=> 'required',
				'maternal_surname' 	=> 'required',
				'teacher_status_id' => 'required',
				'birthdate' 	 => 'required',
				'payroll_number' => 'required',
				'start_date' 	 => 'required',
			]);

			//Actualiza el maestro con los datos del formulario 

			$data = $request->all();
			$teacher->fill($data);
			
			$originalDate = $request->birthdate;
            $teacher->birthdate = date("Y-m-d", strtotime($originalDate));
        
            $originalDate = $request->start_date;
            $teacher->start_date = date("Y-m-d", strtotime($originalDate));
			
			$teacher->created_at = date("Y-m-d H:i:s");
	        $teacher->updated_at = date("Y-m-d H:i:s");
	        $teacher->created_by = Auth::id();
	        $teacher->updated_by = Auth::id();
			$teacher->user_id = $user->id;
	        $teacher->save();
		}

		if ($user->role_id!=$request->all()['role_id']){
			
			$newRole = Role::find($request->all()['role_id']);
			$newPermissions = $newRole->permissions()->pluck('permission_id');			
			$user->extraPermissions()->detach($newPermissions);
		}

        $oldPicture = $user->picture;
        $oldPicturePath = public_path() . $user->picture;

		$data = $request->all();
		$user->fill($data);
		
		//Guarda la nueva contraseña.
		if($request->has('password')){
		    $user->password = Hash::make($data["password"]);
		}
		
		$user->picture = $oldPicture;

        if ($request->has('picture')) {
        	$request->validate([
                'picture' => 'image',
            ]);

             // Get image file
            $image = $request->file('picture');

            //Delete previous image
            File::delete($oldPicturePath);

            // Make a image name based on user name and current timestamp
            $name = str_slug($request->input('picture')).'_'.time();
            // Define folder path
            $folder = '/uploads/images/users/';
            // Make a file path where image will be stored [ folder path + file name + file     extension]

            $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();

            // Upload image
            $this->uploadOne($image, $folder, 'public', $name);
            // Set image path in database to filePath
            $user->picture = $filePath;
        }

		$user->save();
		if($request->role_id == 2){
    		Session::flash('message', __('users.Successfully updated user'));
			$route = redirect()->route('users.index');
		}

		return redirect()->route('users.showextra', ['id' => $id]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$user = User::find($id);
		$user->delete();

		$oldPicture = $user->picture;
        $oldPicturePath = public_path() . $user->picture;

		//Delete previous image
        File::delete($oldPicturePath);

        Session::flash('message', __('users.Successfully deleted user'));
		return redirect()->route('users.index');
	}

	/**
	 * Muestra todos los permisos extra fuera del rol que tiene el usuario.
	 * 
	 */
	public function showextra($id)
	{
		//encuentra el id del usuario
		$user = User::find($id);
		
		//Muestra los modulos que existen
		$module_types = Moduletype::orderBy('name','asc')->get();
		
		//se declara permisos
		$permissions = [];
		//para cada modulo consulta los permisos de ese modulo
		foreach ($module_types  as $currentModuleType){
			//Consulta los tipo de  permisos.
			$currentPermissionTypes= PermissionType::where('moduleType_id', $currentModuleType->id)->orderBy('name','asc')->get();	
			//para cada tipo de permiso 
			foreach ($currentPermissionTypes  as $currentPermissionType){
				
				$currentPermissions = Permission::where('permissionType_id', $currentPermissionType->id)->orderBy('name','asc')->get()->toArray();
				// consulta los permisos
				foreach ($currentPermissions as $currentPermission){
					
					$currentPermission['hasPermission'] =0; // lo puedo modificar y no está activo
					if ($user->hasPermissionByRole($currentPermission['name'])){
						$currentPermission['hasPermission'] =1;  //es permiso de rol. Mo lo puedo modificar y esta activo
					} else if ($user->hasPermissionByExtra($currentPermission['name'])){
						$currentPermission['hasPermission'] =2;  // lo puedo modificar y no está activo
					}
				
					
					$permissions[$currentModuleType->name][$currentPermissionType->name][] =$currentPermission;
				}
			}
		}
		
	
	
		$module_types=$module_types->pluck('name', 'id');
		return view('users.showextra', compact('user','module_types','permissions'));
	
	}
	/**
	 * Actualizo todos los permisos extra del usuario.
	 */
	public function updateextra(Request $request, $id)
	{
		
		$user = User::find($id);
		//remuevo todos los permisos extra
		$user->extraPermissions()->detach();
		//asigno los todos los nuevos permisos.
		if(isset($request->all()['chbxPermission'])){
			$user->extraPermissions()->attach(Permission::whereIn('id', $request->all()['chbxPermission'])->get());
		}

    	Session::flash('message', __('users.Successfully updated user'));
		return redirect()->route('users.index');
	
	}
}
