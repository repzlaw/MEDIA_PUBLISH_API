<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\TaskNoteController;
use App\Http\Controllers\MessagingController;
use App\Http\Controllers\ContentIdeaController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TaskConversationController;
require __DIR__ . '/admin.php';

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/logout',[AuthController::class,'logout'])->name('logout');

    //get online users
    Route::get('online-user', [UserController::class, 'onlineStatus']);

    //notifications routes
    Route::prefix('/notifications')->name('notification.')->group(function(){
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/{notification}', [NotificationController::class,'show'])->name('show');
        Route::post('/change-status', [NotificationController::class,'changeStatus'])->name('change-status');
    });

    //task routes
    Route::prefix('/tasks')->name('task.')->group(function () {
        Route::get('/', [TaskController::class,'index'])->name('all');
        Route::post('/', [TaskController::class,'store'])->name('store')->middleware(['admin']);
        Route::get('/{task}', [TaskController::class,'show'])->name('show');
        Route::post('/update', [TaskController::class,'update'])->name('update')->middleware(['admin']);
        Route::post('/submit-task', [TaskController::class,'submitTask'])->name('submit-task');
        Route::post('/submit-review', [TaskController::class,'submitReview'])->name('submit-review');
        Route::post('/search-user', [TaskController::class, 'searchUser'])->name('search');
        Route::post('/cancel', [TaskController::class, 'cancelTask'])->name('cancel');
        Route::get('/acknowledge/{task}', [TaskController::class, 'acknowledgeTask'])->name('acknowledge');
        Route::get('/copy/{task}', [TaskController::class, 'copyTask'])->name('copy')->middleware(['admin']);
        Route::post('/save-feedback', [FeedbackController::class, 'store'])->name('feedback');
        Route::get('/notes/{task}', [TaskNoteController::class, 'index'])->name('view-notes');
        Route::post('/save-notes', [TaskNoteController::class, 'store'])->name('store-notes');
        Route::post('/share-notes', [TaskNoteController::class, 'shareNotes'])->name('share-notes');

        //conversations routes
        Route::prefix('/conversations')->name('conversation.')->group(function(){
            Route::get('/{task_id}', [TaskConversationController::class, 'index'])->name('all');
            Route::post('/', [TaskConversationController::class,'store'])->name('store');
        });
    });

    //content ideas routes
    Route::prefix('/content-ideas')->middleware(['verified'])->name('content-ideas.')->group(function () {
        Route::post('/share', [ContentIdeaController::class, 'share'])->name('share');
        Route::post('/update', [ContentIdeaController::class, 'update'])->name('update');
        Route::get('/shared-ideas', [ContentIdeaController::class, 'sharedIdeas']);
        
    });

    Route::apiResources([
        '/content-ideas'=> ContentIdeaController::class,
        '/messagings'=> MessagingController::class,

    ]);
});





Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact work@tvz.com'], 404);
});