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
use App\Livewire\Admin\GroupDetail;
use App\Livewire\Admin\QrCodes;
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

Route::middleware(['auth'])->group(function () {
    // Main dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Content management
    Route::get('/dashboard/members', Members::class)->name('admin.members');
    Route::get('/dashboard/members/{id}', MemberDetail::class)->name('admin.member.detail');
    Route::get('/dashboard/instructors/{id}', MemberDetail::class)->name('admin.instructor.detail');
    Route::get('/dashboard/groups', Groups::class)->name('admin.groups');
    Route::get('/dashboard/groups/{id}', GroupDetail::class)->name('admin.group.detail');
    Route::get('/dashboard/posts', Posts::class)->name('admin.posts');
    Route::get('/dashboard/comments', Comments::class)->name('admin.comments');

    // Equipment management
    Route::get('/dashboard/rentals', Rentals::class)->name('admin.rentals');
    Route::get('/dashboard/returns', Returns::class)->name('admin.returns');
    Route::get('/dashboard/equipment', Equipment::class)->name('admin.equipment');
    Route::get('/dashboard/equipment/{id}', EquipmentDetail::class)->name('admin.equipment.detail');
    Route::get('/dashboard/equipment-sets', EquipmentSets::class)->name('admin.equipment-sets');
    Route::get('/dashboard/equipment-sets/{id}', EquipmentSetDetail::class)->name('admin.equipment-set.detail');
    Route::get('/dashboard/barcodes', QrCodes::class)->name('admin.qr-codes');

    // Courses
    Route::get('/dashboard/courses', Courses::class)->name('admin.courses');
    Route::get('/dashboard/course-materials', CourseMaterials::class)->name('admin.course-materials');
    Route::get('/dashboard/awards', Awards::class)->name('admin.awards');

    // Admin tools
    Route::get('/dashboard/roles', Roles::class)->name('admin.roles');
    Route::get('/dashboard/permissions', Permissions::class)->name('admin.permissions');
    Route::get('/dashboard/search', GlobalSearch::class)->name('admin.search');

    // Settings
    Route::get('/settings', AllSettings::class)->name('settings.all');
    Route::get('/settings/profile', Profile::class)->name('profile.edit');
    Route::get('/settings/password', Password::class)->name('user-password.edit');
    Route::get('/settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('/settings/two-factor', TwoFactor::class)
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
