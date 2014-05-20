<?php namespace Models;

use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
	protected $guarded = ['id', 'deleted_at', 'created_at', 'updated_at'];
}