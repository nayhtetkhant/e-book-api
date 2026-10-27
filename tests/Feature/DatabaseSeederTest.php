<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\OrderSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('seeds usable accounts and consistent commerce data', function () {
    $this->seed();

    expect(User::count())->toBe(4)
        ->and(Product::count())->toBe(10)
        ->and(Order::count())->toBe(3)
        ->and(Payment::count())->toBe(3);

    $admin = User::where('email', 'admin@example.com')->firstOrFail();
    expect($admin->role)->toBe(UserRole::ADMIN)
        ->and(Hash::check('password', $admin->password))->toBeTrue();

    foreach (Order::with('products', 'user.products')->get() as $order) {
        $payment = Payment::where('order_id', $order->id)->sole();
        expect($order->total_amount)->toBe($order->products->sum('pivot.amount'))
            ->and($payment->stripe_session_id)->toBeNull();

        foreach ($order->products as $product) {
            expect($product->pivot->amount)->toBe($product->price * $product->pivot->quantity);
        }

        $expectedStatus = match ($order->status) {
            OrderStatus::COMPLETED => PaymentStatus::SUCCESS,
            OrderStatus::PENDING => PaymentStatus::PENDING,
            OrderStatus::FAILED => PaymentStatus::FAILED,
        };
        expect($payment->status)->toBe($expectedStatus)
            ->and($order->user->products->modelKeys())->toBe(
                $order->status === OrderStatus::COMPLETED ? $order->products->modelKeys() : []
            );
    }
});

it('can be rerun without duplicating records or overwriting edits', function () {
    User::factory()->create();
    Product::factory()->create();
    $this->seed();
    $reader = User::where('email', 'reader@example.com')->firstOrFail();
    $reader->update(['name' => 'Updated Reader', 'password' => 'changed-password']);
    $product = Product::where('title', 'Laravel in Practice')->firstOrFail();
    $product->update(['price' => 40, 'file_path' => 'books/real-upload.pdf']);
    $order = Order::where('user_id', $reader->id)->sole();
    $order->status = OrderStatus::FAILED;
    $order->save();
    $snapshot = collect(['users', 'products', 'orders', 'order_items', 'payments', 'product_users'])
        ->mapWithKeys(fn (string $table): array => [$table => DB::table($table)->orderBy('id')->get()->toJson()]);

    $this->seed();

    foreach ($snapshot as $table => $rows) {
        expect(DB::table($table)->orderBy('id')->get()->toJson())->toBe($rows);
    }
});

it('does not insert demo data outside development environments', function (string $seeder) {
    $this->app->instance('env', 'production');

    $this->seed($seeder);

    expect(User::count())->toBe(0)
        ->and(Product::count())->toBe(0)
        ->and(Order::count())->toBe(0)
        ->and(Payment::count())->toBe(0);
})->with([DatabaseSeeder::class, UserSeeder::class, ProductSeeder::class, OrderSeeder::class]);

it('can seed orders directly on an empty database', function () {
    $this->seed(OrderSeeder::class);
    $this->seed(OrderSeeder::class);

    expect(User::count())->toBe(4)
        ->and(Product::count())->toBe(10)
        ->and(Order::count())->toBe(3)
        ->and(Payment::count())->toBe(3)
        ->and(DB::table('order_items')->count())->toBe(4)
        ->and(DB::table('product_users')->count())->toBe(2);
});
