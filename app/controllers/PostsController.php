<?php

use Models\Post;

class PostsController extends \BaseController
{
	public function index()
	{
		$posts = Post::orderBy('id', 'desc')
			->paginate(20);

		if ($posts->count() === 0) return Redirect::to('admin');

		return View::make('admin.posts.index', compact('posts'));
	}

	public function create()
	{
		return View::make('admin.posts.create');
	}

	public function store()
	{
		$input = Input::only(['name', 'body', 'tags', 'status']);

		$validator = Validator::make($input, [
			'name' => 'required|min:4',
			'status' => 'required',
		]);

		if ($validator->fails())
		{
			return Redirect::back()
				->with('error', implode('<br>', $validator->messages()->all()));
		}

		$post = Post::create($input);

		return Redirect::to('admin/posts')
				->with('message', 'Post saved! &mdash; <a href="/admin/posts/' . $post->id . '/edit">edit again</a>');
	}

	public function show($slug)
	{
		$post = Post::withSlug($slug)
			->first();

		$posts = Models\Post::published()
			->where('id', '!=', $post->id)
			->orderBy('id', 'desc')
			->take(5)
			->get();

		return View::make('post', compact('post', 'posts'));
	}

	public function edit($id)
	{
		if (Input::has('return')) Session::set('after_edit_return_to_post', true);

		$post = Post::find($id);

		return View::make('admin.posts.edit', compact('post'));
	}

	public function update($id)
	{
		$returnToPost = (Session::has('after_edit_return_to_post'));

		$validator = Validator::make(Input::all(), [
			'name' => 'required|min:4',
			'status' => 'required',
		]);

		if ($validator->fails())
		{
			return Redirect::back()
				->with('error', implode('<br>', $validator->messages()->all()));
		}

		$post = Post::find($id);

		$post->name = Input::get('name');
		$post->body = Input::get('body');
		$post->tags = Input::get('tags');
		$post->status = Input::get('status');

		$post->save();

		if ($returnToPost)
		{
			Session::forget('after_edit_return_to_post');

			return Redirect::to($post->slug);
		}

		else
		{
			return Redirect::to('admin/posts')
				->with('message', 'Post saved! &mdash; <a href="/admin/posts/' . $post->id . '/edit">edit again</a>');
		}
	}

	public function destroy($id)
	{
		Post::destroy($id);

		return Redirect::to('admin/posts')
			->with('message', 'Post deleted!');
	}
}