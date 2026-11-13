<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\SubtestController;
use App\Http\Controllers\Admin\TryoutController as AdminTryoutController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\PracticeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\TryoutController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');

    Route::get('auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
    Route::get('auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('verify-email', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('progress', [ProgressController::class, 'index'])->name('progress.index');
    Route::get('practice', [PracticeController::class, 'index'])->name('practice.index');
    Route::post('practice/questions/{question}/check', [PracticeController::class, 'check'])->name('practice.check');
    Route::get('tryouts', [TryoutController::class, 'index'])->name('tryouts.index');
    Route::get('tryouts/{tryout}', [TryoutController::class, 'show'])->name('tryouts.show');

    Route::post('tryouts/{tryout}/start', [ExamController::class, 'start'])->name('exam.start');
    Route::get('attempts/{attempt}', [ExamController::class, 'show'])->name('exam.show');
    Route::post('attempts/{attempt}/answer', [ExamController::class, 'saveAnswer'])->name('exam.answer');
    Route::post('attempts/{attempt}/submit', [ExamController::class, 'submit'])->name('exam.submit');

    Route::get('attempts/{attempt}/result', [ResultController::class, 'show'])->name('results.show');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('questions', [QuestionController::class, 'index'])->name('questions.index');
    Route::get('questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::post('questions', [QuestionController::class, 'store'])->name('questions.store');
    Route::delete('questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');
    Route::get('questions/import', [QuestionController::class, 'importForm'])->name('questions.import');
    Route::post('questions/import', [QuestionController::class, 'import'])->name('questions.import.store');
    Route::get('questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
    Route::post('questions/{question}/image', [QuestionController::class, 'uploadImage'])->name('questions.image');
    Route::post('questions/{question}/revise', [QuestionController::class, 'revise'])->name('questions.revise');

    Route::get('subtests', [SubtestController::class, 'index'])->name('subtests.index');
    Route::patch('subtests/{subtest}', [SubtestController::class, 'update'])->name('subtests.update');

    Route::get('tryouts', [AdminTryoutController::class, 'index'])->name('tryouts.index');
    Route::post('tryouts', [AdminTryoutController::class, 'store'])->name('tryouts.store');
    Route::get('tryouts/{tryout}', [AdminTryoutController::class, 'edit'])->name('tryouts.edit');
    Route::patch('tryouts/{tryout}', [AdminTryoutController::class, 'update'])->name('tryouts.update');
    Route::delete('tryouts/{tryout}', [AdminTryoutController::class, 'destroy'])->name('tryouts.destroy');
    Route::post('tryouts/{tryout}/questions', [AdminTryoutController::class, 'syncQuestions'])->name('tryouts.questions');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::patch('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
});
