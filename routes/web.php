<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\MyOrdersController;
use App\Http\Controllers\Guest\GuestCartController;
use App\Http\Controllers\Guest\GuestCheckoutController;
use App\Models\CartItem;
use Illuminate\Support\Facades\Route;

// === Public Route ===
Route::get('/', [HomeController::class, 'index'])->name('home');

// === Dashboard redirect ===
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

// === Profile Routes ===
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// === Register Routes ===
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

// === ADMIN ROUTES ===
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // CRUD Books
    Route::resource('/books', BookController::class);

    // CRUD Categories 
    Route::resource('/categories', CategoryController::class);

    // CRUD Users
    Route::resource('/users', UserController::class);

    // Orders
    Route::resource('/orders', \App\Http\Controllers\Admin\OrderController::class)
        ->only(['index', 'show']);

    // Custom Order Actions
    Route::post('/orders/{order}/confirm', [\App\Http\Controllers\Admin\OrderController::class, 'confirm'])
        ->name('orders.confirm');
    Route::post('/orders/{order}/reject', [\App\Http\Controllers\Admin\OrderController::class, 'reject'])
        ->name('orders.reject');

    // Reviews Monitoring
    Route::resource('/reviews', ReviewController::class)->only(['index', 'show']);
});

// === USER ROUTES ===
Route::middleware(['auth', 'role:customer'])->prefix('user')->name('user.')->group(function () {
    // Cart Management
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{book}', [CartController::class, 'store'])->name('cart.store');
    Route::put('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

    // My Orders
    Route::get('/orders', [MyOrdersController::class, 'index'])->name('user.orders.index');
    Route::patch('/orders/{order}/cancel', [MyOrdersController::class, 'cancel'])->name('orders.cancel');
    Route::patch('/orders/{order}/complete', [MyOrdersController::class, 'complete'])->name('orders.complete');
    Route::post('/orders/{order}/upload-proof', [MyOrdersController::class, 'storeProof'])->name('orders.store-proof');

    // Reviews
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::post('/{orderItem}', [\App\Http\Controllers\User\ReviewController::class, 'store'])->name('store');
        Route::put('/{review}', [\App\Http\Controllers\User\ReviewController::class, 'update'])->name('update');
        Route::delete('/{review}', [\App\Http\Controllers\User\ReviewController::class, 'destroy'])->name('destroy');
    });

    Route::post('/cart/migrate', [CartController::class, 'migrateFromLocal'])->name('cart.migrate');
});

// === GUEST ROUTES (untuk pengguna tanpa login) ===
Route::prefix('guest')->name('guest.')->group(function () {
    // Guest Cart
    Route::get('/cart', [GuestCartController::class, 'index'])->name('cart.index');

    // Guest Checkout
    Route::get('/checkout', [GuestCheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [GuestCheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{token}', [GuestCheckoutController::class, 'success'])->name('checkout.success');
});

Route::middleware('auth:sanctum')->get('/cart/count', function (Request $request) {
    $count = CartItem::where('user_id', $request->user()->id)->sum('quantity');
    return response()->json(['count' => $count]);
});

require __DIR__ . '/auth.php';
