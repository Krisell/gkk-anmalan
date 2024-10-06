<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class EndToEndTestingController extends Controller
{
    public function __construct()
    {
        if (app()->environment() !== 'testing' && ! config('app.testing-non-testing-environment')) {
            abort(404);
        }
    }

    public function login(Request $request)
    {
        $attributes = $request->input('attributes', []);

        $user = empty($attributes)
            ? null
            : User::where($attributes)->first();

        if (! $user) {
            $user = User::factory(1)->create($attributes)[0];
        }

        /** @var User $user */
        auth()->login($user);

        return $user;
    }

    public function factory(Request $request)
    {
        $models = $this->model()::factory()
            ->times(\intval($request->input('times', 1)))
            ->create($request->input('attributes'));

        return $models->count() > 1
            ? $models
            : $models->first();
    }

    public function first()
    {
        return $this->model()::where(request('attributes'))->first();
    }

    public function update()
    {
        return $this->model()::where(request('attributes'))->update(request('values'));
    }

    public function php(Request $request)
    {
        $code = $request->input('command').';';

        if (! \str_contains($code, 'return')) {
            $code = 'return '.$code;
        }

        // Extra guard to not eval passed code even if this function is called directly (for instance
        // as a result of a deserialization code injection). We should also work towards replacing this
        // eval with the specific features that we actually use (like the other functions in this class).
        if (app()->environment() !== 'testing') {
            abort(404);
        }

        return response()->json(['result' => eval($code)]);
    }

    private function model()
    {
        if (\class_exists(request('model'))) {
            return request('model');
        }

        if (\class_exists('App\\Models\\'.\ucfirst(request('model')))) {
            return 'App\\Models\\'.\ucfirst(request('model'));
        }

        throw new ModelNotFoundException(request('model'));
    }
}
