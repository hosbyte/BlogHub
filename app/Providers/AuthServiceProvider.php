<?php

namespace App\Providers;

use App\Models\Post;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Post::class => PostPolicy::class,
        
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Gate برای نویسنده بودن
        Gate::define('is_author' , function ($user)
        {
            return $user->role_id == 2;
        });

        // Gate برای ادمین بودن
        Gate::define('is_admin' , function ($user)
        {
            return $user->role_id == 1;
        });

        // Gate برای ایجاد مقاله
        Gate::define('create_post' , function ($user)
        {
            return in_array($user->role_id, [1 , 2]);
        });

        // Gate برای ویرایش مقاله (مالک یا ادمین)
        Gate::define('update_post' , function ($user , $post)
        {
            return $user->id === $post->user->id || $user->role_id == 1;
        });

        // Gate برای حذف مقاله
        Gate::define('delete_post' , function ($user , $post)
        {
            return $user->id === $post->user->id || $user->role_id == 1;
        });
    }
}
