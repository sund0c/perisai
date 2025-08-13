<?php
namespace App\Services;
use DB;
use Log;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use App\Helpers\PublicHelper;
use App\Models\UserWarehouse;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User\DetailUserResource;

class UserService
{
    public function getAll()
    {
        return User::query()->with('created_user','tokens')->get();
    }

    public function datatable($users = [])
    {
        $user = Auth::user();
        $permissions = [
            'user_edit' => $user->can('user_edit'),
        ];
        return DataTables::of($users)
            ->addColumn('action', function ($user)use($permissions){
                $action_menu = [];
                if ($permissions['user_edit']) {
                    $action_menu[] = [
                        'label' => 'Edit Pengguna',
                        'class' => 'btn_edit',
                        'url' => route('user.edit', $user->id),
                    ];
                }
                return view('components.button-table')
                    ->with('id',$user->id)
                    ->with('actions',$action_menu);
            })->rawColumns(['action'])->make(true);
    }

    public function create($data = [])
    {
        DB::beginTransaction();
        try {
            $data['password'] = bcrypt('selender');
            $user = User::create($data);
            DB::commit();
            return $user;
        } catch (\Throwable $th) {
            Log::error($th);
            DB::rollback();
            return false;
        }
    }



    public function isAlreadyExist($sso_user)
    {
        $sso_roles = session('defaultRole');
        if($sso_roles->role_code == "super-admin"){
            $role = "admin";
        }else{
            $role = $sso_roles->role_code;
        }
        $user = User::where('sso_user_id',$sso_user->id)->first();
        if ($user) {
            User::where('sso_user_id', $sso_user->id)->update([
                "email" => $sso_user->email,
                "role" => $role,
            ]);
            return true;
        }
        $user = User::where('email', $sso_user->email)->first();
        if ($user) {
            User::where('email', $sso_user->email)->update([
                'sso_user_id' => $sso_user->id,
                "email" => $sso_user->email,
                "role" => $role,
            ]);
            return true;
        }
        $user_data = (array)$sso_user;
        $user_data['sso_user_id'] = $sso_user->id;
        $user_data['password'] = bcrypt($sso_user->id);
        $user_data['email_verified_at'] = now();
        $user_data['role'] = $role;
        $user = User::create($user_data);
        if (!Auth::check()) {
            Auth::loginUsingId($user->id);
        }
        return true;
    }

    public function whereSsoUserId($sso_user_id)
    {
        $user = User::where('sso_user_id', $sso_user_id)->first();
        if (empty($user)) {
            abort(301);
        }
        return $user;
    }

    /**
     * Handle informasi akun yang login
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userPublicLogin()
    {
        $user = Auth::user();
        $user->load('detail');
        return ServiceApi::success(new DetailUserResource($user), 'Akun user login', 200);
    }

    /**
     * Handle update password akun publik login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
    {
        try {
            $user = Auth::user();
            if (!Hash::check($request->password_old, $user->password)) {
                return ServiceApi::error('Password lama tidak cocok', 400);
            }

            $user->update([
                'password' => Hash::make($request->password_new)
            ]);

            return ServiceApi::success([], 'Password berhasil diupdate', 200);
        } catch (\Exception $e) {
            return ServiceApi::failed('Terjadi Suatu kesalahan', 500);
        }
    }

    /**
     * Handle update profile akun publik login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $emailExisting = User::where('email', $request->email)->where('id', '!=', $user->id)->first();
        if($emailExisting) {
            return ServiceApi::failed([], 'Email sudah digunakan pada akun lain', 400);
        }

        $noHp = convertNomor($request->telepon);
        $phoneExisting = User::where('telepon', $noHp)->where('id', '!=', $user->id)->first();
        if($phoneExisting) {
            return ServiceApi::failed([], 'Nomor telepon sudah digunakan pada akun lain', 400);
        }

        DB::beginTransaction();
        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'telepon' => $noHp,
            ]);
            UserDetail::updateOrCreate([
                'user_id' => $user->id
            ],[
                'provinsi_id' => $request->provinsi_id,
                'kabkot_id' => $request->kabkot_id,
                'kecamatan_id' => $request->kecamatan_id,
                'desa_id' => $request->desa_id,
                'date_of_birth' => Carbon::createFromFormat('d-m-Y', $request->date_of_birth)->format('Y-m-d'),
                'address' => $request->address,
                'work' => $request->work,
            ]);

            DB::commit();
            return ServiceApi::success([], 'Profile berhasil diupdate', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return ServiceApi::failed('Terjadi Suatu kesalahan', 500);
        }
    }

    /**
     * Handle update bahasa akun publik login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLanguage(Request $request)
    {
        try {
            $user = Auth::user();
            UserDetail::updateOrCreate([
                'user_id' => $user->id
            ],[
                'language' => $request->language
            ]);
            return ServiceApi::success([], 'Bahasa berhasil diupdate', 200);
        } catch (\Exception $e) {
            return ServiceApi::failed('Terjadi Suatu kesalahan', 500);
        }
    }

    /**
     * Handle update tema akun publik login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTheme(Request $request)
    {
        try {
            $user = Auth::user();
            UserDetail::updateOrCreate([
                'user_id' => $user->id
            ],[
                'theme' => $request->theme
            ]);
            return ServiceApi::success([], 'Theme berhasil diupdate', 200);
        } catch (\Exception $e) {
            return ServiceApi::failed('Terjadi Suatu kesalahan', 500);
        }
    }

    /**
     * Handle update photo akun publik login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePhoto(Request $request)
    {
        try {
            $user = Auth::user();
            UserDetail::updateOrCreate([
                'user_id' => $user->id
            ],[
                'photo' => PublicHelper::uploadFile('user/photo', $request->file('photo'))
            ]);
            return ServiceApi::success([
                'photo' => $user->detail->url_photo
            ], 'Photo berhasil diupdate', 200);
        } catch (\Exception $e) {
            return ServiceApi::failed('Terjadi Suatu kesalahan', 500);
        }
    }
}
