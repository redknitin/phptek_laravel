<?php namespace Models;

use Hash;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class User extends Base implements UserInterface, RemindableInterface
{
	use RemindableTrait;
	use SoftDeletingTrait;

	protected $table = 'users';

	protected $dates = ['deleted_at'];

	protected $hidden = ['password'];

	// Query scopes
	public function scopeEnabled($query)
	{
		return $query
			->where('status', '=', 'enabled');
	}

	// Attribute mutators
	public function setPasswordAttribute($value)
	{
		$this->attributes['password'] = Hash::make($value);
	}

	// Laravel methods

	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	public function getAuthPassword()
	{
		return $this->password;
	}

	public function getRememberToken()
	{
		return $this->remember_token;
	}

	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	public function getRememberTokenName()
	{
		return 'remember_token';
	}
}
