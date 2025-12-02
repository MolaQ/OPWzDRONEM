<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\Settings\AllSettings;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Middleware\AdminMiddleware;

use App\Livewire\Admin\Members;
use App\Livewire\Admin\Groups;
use App\Livewire\Admin\Posts;
use App\Livewire\Admin\Comments;
use App\Livewire\Admin\BarcodeScanner;
use App\Livewire\PostView;
use App\Livewire\NewsPage;

app('router')->aliasMiddleware('admin', AdminMiddleware::class);

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/news', NewsPage::class)->name('news');
Route::get('/post/{id}', PostView::class)->name('post.view');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', Dashboard::class)->name('admin.dashboard');
    Route::get('/admin/members', Members::class)->name('admin.members');
    Route::get('/admin/groups', Groups::class)->name('admin.groups');
    Route::get('/admin/posts', Posts::class)->name('admin.posts');
    Route::get('/admin/comments', Comments::class)->name('admin.comments');
    Route::get('/admin/scanner', BarcodeScanner::class)->name('admin.scanner');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/all');

    Route::get('settings/all', AllSettings::class)->name('settings.all');
    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
