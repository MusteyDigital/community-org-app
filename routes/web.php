<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\EventItemController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PublicOrganizationController;
use App\Models\Member;
use App\Models\EventItem;
use App\Models\Announcement;
use App\Models\Contribution;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicOrganizationController::class, 'index']);
Route::get('/browse', [PublicOrganizationController::class, 'directory']);
Route::get('/org/{slug}', [PublicOrganizationController::class, 'show']);

Route::get('/dashboard', function () {
    $membership = Member::where('user_id', auth()->id())->where('status', 'approved')->first();
    if (! $membership) {
        return view('dashboard', [
            'noOrganization' => true,
            'isAdmin' => false,
            'memberCount' => 0,
            'upcomingEvents' => collect(),
            'pinnedAnnouncements' => collect(),
            'eventCount' => 0,
            'announcementCount' => 0,
            'pendingMemberCount' => 0,
            'contributionsThisMonth' => 0,
            'contributionsThisYear' => 0,
        ]);
    }
    $orgId = $membership->organization_id;
    $isAdmin = $membership->role === 'admin';
    return view('dashboard', [
        'noOrganization' => false,
        'isAdmin' => $isAdmin,
        'memberCount' => Member::where('organization_id', $orgId)->count(),
        'upcomingEvents' => EventItem::where('organization_id', $orgId)->where('event_date', '>=', now())->orderBy('event_date')->take(3)->get(),
        'pinnedAnnouncements' => Announcement::where('organization_id', $orgId)->where('is_pinned', true)->take(3)->get(),
        'eventCount' => EventItem::where('organization_id', $orgId)->count(),
        'announcementCount' => Announcement::where('organization_id', $orgId)->count(),
        'pendingMemberCount' => $isAdmin ? Member::where('organization_id', $orgId)->where('status', 'pending')->count() : 0,
        'contributionsThisMonth' => $isAdmin ? Contribution::where('organization_id', $orgId)->whereMonth('contributed_at', now()->month)->whereYear('contributed_at', now()->year)->sum('amount') : 0,
        'contributionsThisYear' => $isAdmin ? Contribution::where('organization_id', $orgId)->whereYear('contributed_at', now()->year)->sum('amount') : 0,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('members', MemberController::class)->except(['show']);
    Route::get('/directory', [MemberController::class, 'directory'])->name('members.directory');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::post('/directory/visibility', [MemberController::class, 'updateVisibility'])->name('members.visibility');
    Route::post('/members/{member}/approve', [MemberController::class, 'approve'])->name('members.approve');
    Route::post('/members/{member}/reject', [MemberController::class, 'reject'])->name('members.reject');
    Route::post('/members/{member}/visibility', [MemberController::class, 'adminUpdateVisibility'])->name('members.adminVisibility');
    Route::resource('events', EventItemController::class)->except(['show']);
    Route::resource('announcements', AnnouncementController::class)->except(['show']);
    Route::get('/announcements/trashed', [AnnouncementController::class, 'trashed'])->name('announcements.trashed');
    Route::post('/announcements/{id}/restore', [AnnouncementController::class, 'restore'])->name('announcements.restore');
    Route::delete('/announcements/{id}/force-delete', [AnnouncementController::class, 'forceDelete'])->name('announcements.forceDelete');
    Route::resource('contributions', App\Http\Controllers\ContributionController::class)->except(['show']);
    Route::get('/contributions/{contribution}/receipt', [App\Http\Controllers\ContributionController::class, 'receipt'])->name('contributions.receipt');
    Route::get('/contributions/pay/start', [App\Http\Controllers\PaystackController::class, 'pay'])->name('paystack.pay');
    Route::post('/contributions/pay/initialize', [App\Http\Controllers\PaystackController::class, 'initialize'])->name('paystack.initialize');
    Route::get('/contributions/pay/callback', [App\Http\Controllers\PaystackController::class, 'callback'])->name('paystack.callback');
    Route::get('/organizations/payout', [App\Http\Controllers\OrganizationPayoutController::class, 'edit'])->name('organizations.payout.edit');
    Route::post('/organizations/payout/resolve', [App\Http\Controllers\OrganizationPayoutController::class, 'resolve'])->name('organizations.payout.resolve');
    Route::post('/organizations/payout', [App\Http\Controllers\OrganizationPayoutController::class, 'update'])->name('organizations.payout.update');
    Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations.index');
    Route::get('/organizations/create', [OrganizationController::class, 'create'])->name('organizations.create');
    Route::post('/organizations', [OrganizationController::class, 'store'])->name('organizations.store');
    Route::post('/organizations/{organization}/join', [OrganizationController::class, 'join'])->name('organizations.join');
    Route::post('/organizations/leave', [OrganizationController::class, 'leave'])->name('organizations.leave');
    Route::middleware('super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/organizations', [App\Http\Controllers\Admin\OrganizationApprovalController::class, 'index'])->name('organizations.index');
        Route::post('/organizations/{organization}/approve', [App\Http\Controllers\Admin\OrganizationApprovalController::class, 'approve'])->name('organizations.approve');
        Route::post('/organizations/{organization}/reject', [App\Http\Controllers\Admin\OrganizationApprovalController::class, 'reject'])->name('organizations.reject');
    });
});

Route::get('/auth/google', [App\Http\Controllers\Auth\GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'callback'])->name('google.callback');

require __DIR__.'/auth.php';



