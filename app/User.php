<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'usuario';
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'nombre', 'apellido', 'usuario', 'correo', 'clave', 'role_id', 'estado_id'
    ];

    public function posts () {
        return $this->hasMany('App\Models\Posts', 'usuario_id', 'id');
    }

    public function role () {
        return $this->belongsTo('App\Models\Role', 'role_id', 'id');
    }

    public function status () {
        return $this->belongsTo('App\Models\UserStatus', 'estado_id', 'id');
    }

    /**
     * Boost the model 
     */
    public static function boot() {
        parent::boot();

        static::creating(function($user){
            $user->verifyme_token = str_random(40);
        });
    }

    /**
     * verifies the user email
     */
    public function hasVerified() {

        $this->estado_id = 2;
        $this->verifyme_token = null;

        $this->save();
    }

    public function getAuthPassword() {
        return $this->clave;
    }

    public function getEmailForPasswordReset(){
        return $this->correo;
    }

    public function getType() {
        if($this->role_id == 4)
            return "Administrador";
        if($this->role_id == 3)
            return "Redactor";
        if($this->role_id == 2 || $this->role_id == 1)
            return "Estandar";
    }
    
    public function isNormal() {
        if($this->role_id == 2 || $this->role_id == 1)
            return true;

        return false;
    }

    public function isWriter() {
        if($this->role_id == 3)
            return true;

        return false;
    }

    public function isAdmin() {
        if($this->role_id == 4)
            return true;

        return false;
    }

    public function isActive() {
        if($this->estado == 1)
            return true;

        return false;
    }

    public function isDeleted() {
        if($this->deleted_at == null)
            return false;
        
        return true;
    }
}
