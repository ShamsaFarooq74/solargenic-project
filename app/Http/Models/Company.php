<?php



namespace App\Http\Models;



use Illuminate\Database\Eloquent\Model;



class Company extends Model

{

    protected $fillable = [

        "admin_id","business_type",'company_name','contact_number','address','address_lat','address_long','logo','email'

    ];



    public function user()

    {

        return $this->hasMany(User::class,'company_id');

    }



    public function plant()

    {

        return $this->hasMany(Plant::class,'company_id');

    }

}

