<?php
use Illuminate\\Support\\Facades\\Route;
use App\\Http\\Controllers\\ApiController as API;
use App\\Http\\Middleware\\StaffAuth;
Route::get('/', fn () => redirect('/index.html'));
Route::get('/healthz', [API::class, 'health']);
Route::prefix('api')->group(function () {
    Route::get('/result/options', [API::class, 'publicOptions']);
    Route::get('/result/search', [API::class, 'publicSearch']);
    Route::get('/result/marksheet.pdf', [API::class, 'publicPdf']);
    Route::get('/verify/{id}/{sig}', [API::class, 'health']);
    Route::prefix('admin')->group(function () {
        Route::post('/login', [API::class, 'login'])->middleware('throttle:10,15');
        Route::get('/me', [API::class, 'me']);
        Route::post('/logout', [API::class, 'logout']);
        Route::post('/change-password', [API::class, 'changePassword'])->middleware(StaffAuth::class);
        Route::middleware(StaffAuth::class)->group(function () {
            Route::get('/classes', [API::class, 'classes']); Route::post('/classes', [API::class, 'storeClass']); Route::delete('/classes/{id}', [API::class, 'deleteClass']);
            Route::get('/subjects', [API::class, 'subjects']); Route::post('/subjects', [API::class, 'storeSubject']); Route::delete('/subjects/{id}', [API::class, 'deleteSubject']);
            Route::get('/exams', [API::class, 'exams']); Route::post('/exams', [API::class, 'storeExam']); Route::delete('/exams/{id}', [API::class, 'deleteExam']);
            Route::get('/results', [API::class, 'results']); Route::post('/results/individual', [API::class, 'storeResult']); Route::get('/results/{id}', [API::class, 'showResult'])->whereNumber('id');
            Route::put('/results/{id}', [API::class, 'updateResult'])->whereNumber('id'); Route::delete('/results/{id}', [API::class, 'deleteResult'])->whereNumber('id');
            Route::put('/results/{id}/publish', [API::class, 'publishResult'])->whereNumber('id'); Route::put('/results/{id}/unpublish', [API::class, 'unpublishResult'])->whereNumber('id');
            Route::delete('/results/bulk', [API::class, 'bulkDelete']); Route::post('/results/publish-bulk', [API::class, 'publishBulk']); Route::get('/dashboard-stats', [API::class, 'dashboardStats']);
            Route::post('/bulk-preview', [API::class, 'bulkPreview']); Route::post('/bulk-import', [API::class, 'bulkImport']); Route::get('/download-excel-demo', [API::class, 'excelTemplate']);
            Route::get('/reports/options', [API::class, 'reportOptions']); Route::get('/reports/merit', [API::class, 'merit']); Route::get('/reports/tabulation', [API::class, 'tabulation']);
            Route::get('/reports/statistics', [API::class, 'statistics']); Route::get('/reports/marksheets.pdf', [API::class, 'classPdf']);
            Route::get('/export/results.csv', [API::class, 'exportCsv']); Route::get('/export/results.xlsx', [API::class, 'exportXlsx']); Route::get('/backup', [API::class, 'backup']);
            Route::get('/users', [API::class, 'users']); Route::post('/users', [API::class, 'storeUser']); Route::put('/users/{id}', [API::class, 'updateUser'])->whereNumber('id');
            Route::post('/users/{id}/reset-password', [API::class, 'resetPassword'])->whereNumber('id'); Route::get('/audit', [API::class, 'auditLog']);
        });
    });
});
