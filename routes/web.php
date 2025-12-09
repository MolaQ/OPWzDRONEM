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
use App\Livewire\Admin\GlobalSearch;
use App\Livewire\Admin\Rentals;
use App\Livewire\Admin\Returns;
use App\Livewire\Admin\Equipment;
use App\Livewire\Admin\EquipmentSets;
use App\Livewire\Admin\EquipmentDetail;
use App\Livewire\Admin\EquipmentSetDetail;
use App\Livewire\Admin\MemberDetail;
use App\Livewire\Admin\Courses;
use App\Livewire\Admin\CourseMaterials;
use App\Livewire\Admin\Awards;
use App\Livewire\Admin\Roles;
use App\Livewire\Admin\Permissions;
use App\Livewire\StudentDashboard;
use App\Livewire\TeacherOverview;
use App\Livewire\PostView;
use App\Livewire\NewsPage;

app('router')->aliasMiddleware('admin', AdminMiddleware::class);

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/news', NewsPage::class)->name('news');
Route::get('/post/{id}', PostView::class)->name('post.view');

Route::middleware(['auth'])->group(function () {
    Route::get('/student/achievements', StudentDashboard::class)->name('student.achievements');
    Route::get('/teacher/overview', TeacherOverview::class)->name('teacher.overview');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', Dashboard::class)->name('admin.dashboard');
    Route::get('/admin/members', Members::class)->name('admin.members');
    Route::get('/admin/members/{id}', MemberDetail::class)->name('admin.member.detail');
    Route::get('/admin/groups', Groups::class)->name('admin.groups');
    Route::get('/admin/posts', Posts::class)->name('admin.posts');
    Route::get('/admin/comments', Comments::class)->name('admin.comments');
    Route::get('/admin/search', GlobalSearch::class)->name('admin.search');
    Route::get('/admin/rentals', Rentals::class)->name('admin.rentals');
    Route::get('/admin/returns', Returns::class)->name('admin.returns');
    Route::get('/admin/equipment', Equipment::class)->name('admin.equipment');
    Route::get('/admin/equipment/{id}', EquipmentDetail::class)->name('admin.equipment.detail');
    Route::get('/admin/equipment-sets', EquipmentSets::class)->name('admin.equipment-sets');
    Route::get('/admin/equipment-sets/{id}', EquipmentSetDetail::class)->name('admin.equipment-set.detail');
    Route::get('/admin/courses', Courses::class)->name('admin.courses');
    Route::get('/admin/course-materials', CourseMaterials::class)->name('admin.course-materials');
    Route::get('/admin/awards', Awards::class)->name('admin.awards');
    Route::get('/admin/roles', Roles::class)->name('admin.roles');
    Route::get('/admin/permissions', Permissions::class)->name('admin.permissions');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/settings', AllSettings::class)->name('settings.all');
    Route::get('/admin/settings/profile', Profile::class)->name('profile.edit');
    Route::get('/admin/settings/password', Password::class)->name('user-password.edit');
    Route::get('/admin/settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('/admin/settings/two-factor', TwoFactor::class)
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
