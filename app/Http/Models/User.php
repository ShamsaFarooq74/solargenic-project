<?php



namespace App\Http\Models;



use Illuminate\Database\Eloquent\Model;



class User extends Model

{

    protected $table = "users";

    protected $fillable = [

        'name','username','email','password','phone','profile_pic','is_active',

        'is_admin','roles','created_at','updated_at'

    ];



    public function plants()

    {

        return $this->belongsToMany(Plant::class);

    }



    public function company()

    {

        return $this->belongsTo(Company::class);

    }

    public function role()

    {

        return $this->hasOne(Role::class, 'id', 'roles');

    }

    public function plant_user()

    {

        return $this->hasMany(PlantUser::class, 'user_id');

    }

    public function user_companies()

    {

        return $this->hasMany(UserCompany::class, 'user_id');

    }

}

