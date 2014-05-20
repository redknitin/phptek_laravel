<?php namespace Models;

use Carbon;
use Str;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Post extends Base
{
	use SoftDeletingTrait;

	protected $table = 'posts';

	protected $dates = ['deleted_at'];

	protected $appends = ['image'];

	// Query scopes
	public function scopePublished($query)
	{
		return $query->where('status', 'published');
	}

	public function scopeWithSlug($query, $slug = '')
	{
		return $query->where('slug', $slug);
	}

	public function scopeWithTag($query, $tag = '')
	{
		return $query->where('tags', 'like', '%,' . $tag . ',%');
	}

	// Attribute mutators
	public function setTagsAttribute($value)
	{
		if (is_string($value)) $value = explode(',', $value);

		$value = array_map(function($value)
		{
			return mb_strtolower(trim($value));
		}, $value);

		$value = implode(',', $value);

		$this->attributes['tags'] = ( ! empty($value)) ? ','.$value.',' : null;
	}

	public function getTagsAttribute($value)
	{
		return ( ! empty($value)) ? explode(',', trim($value, ',')) : [];
	}

	public function setNameAttribute($value)
	{
		$this->attributes['name'] = $value;

		// Create unique slug from name when creating
		if (empty($this->attributes['id']))
		{
			$this->attributes['slug'] = Str::slug($value);

			if (self::where('slug', $this->attributes['slug'])->count())
			{
				$this->attributes['slug'] .= '-' . Carbon::now()->toDateString();
			}
		}
	}

	public function setBodyAttribute($value)
	{
		$this->attributes['body'] = html_entity_decode($value);
	}

	public function getImageAttribute()
	{
		$imgMatches = [];

		preg_match(
			'|(?Umi-s)src="(.*)"|',
			$this->attributes['body'],
			$imgMatches
		);

		return ( ! empty($imgMatches)) ? $imgMatches[1] : null;
	}
}