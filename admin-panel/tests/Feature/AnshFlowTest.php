<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use App\Models\Product;
use App\Models\DeliveryPerson;
use App\Models\DeliveryPartnerLocality;
use App\Models\City;
use App\Models\Locality;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

class AnshFlowTest extends TestCase
{
    use RefreshDatabase;

    // ── Real actors matching Ansh's setup ─────────────────────────────────────
    private User         $admin;
    private User         $anshSales;      // anshy8726@gmail.com  role:sales
    private User         $anshDelivery;   // anshyadav30@gmail.com role:delivery
    private DeliveryPerson $deliveryPerson;
    private Store        $store;
    private Product      $product;
    private City         $city;
    private Locality     $locality;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        // Disable role middleware so actingAs() is enough for auth
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckRole::class,
        ]);
        $this->seedTestActors();
    }

    private function seedTestActors(): void
    {
        // ── City & Locality (matching Ansh's Delhi setup) ──────────────────
        $this->city = City::create(['name' => 'Delhi', 'state' => 'Delhi', 'status' => true]);

        $this->locality = Locality::create([
            'city_id' => $this->city->id,
            'name'    => 'Delhi - Central',
            'status'  => true,
        ]);

        // ── Store: swastik general store with GPS ──────────────────────────
        $this->store = Store::create([
            'store_name'  => 'swastik general store',
            'address'     => 'Ganga Nagar',
            'phone'       => '9876543210',
            'manager'     => 'Swastik Owner',
            'city_id'     => $this->city->id,
            'locality_id' => $this->locality->id,
            'latitude'    => 28.6063200,
            'longitude'   => 77.2090000,
            'status'      => true,
        ]);

        // ── Product ────────────────────────────────────────────────────────
        $this->product = Product::create([
            'name'           => 'Test Product',
            'sku'            => 'SKU-ANSH-001',
            'brand'          => 'General',
            'category'       => 'FMCG',
            'purchase_price' => 80.00,
            'sale_price'     => 110.00,
            'mrp'            => 130.00,
            'margin'         => 30.00,
            'status'         => 'Active',
        ]);

        // ── Admin ──────────────────────────────────────────────────────────
        $this->admin = User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@test.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'status'   => true,
        ]);

        // ── Ansh as Sales Person ───────────────────────────────────────────
        $this->anshSales = User::create([
            'name'     => 'ANSH YADAV',
            'email'    => 'anshy8726@gmail.com',
            'password' => Hash::make('password'),
            'role'     => 'sales',
            'status'   => true,
        ]);

        // ── Delivery Person record (Ankita style) ──────────────────────────
        $this->deliveryPerson = DeliveryPerson::create([
            'name'    => 'Ankita Upadhyay',
            'phone'   => '8726377978',
            'email'   => 'anshyadav30@gmail.com',
            'vehicle' => 'bike',
            'status'  => true,
        ]);

        // Assign delivery person to locality
        DeliveryPartnerLocality::create([
            'delivery_partner_id' => $this->deliveryPerson->id,
            'city_id'             => $this->city->id,
            'locality_id'         => $this->locality->id,
        ]);

        // ── Ansh as Delivery User (linked by email to delivery person) ─────
        $this->anshDelivery = User::create([
            'name'     => 'ANSH YADAV',
            'email'    => 'anshyadav30@gmail.com',
            'password' => Hash::make('password'),
            'role'     => 'delivery',
            'phone'    => '8726377978',
            'status'   => true,
        ]);
    }

    // =========================================================================
    // GROUP 1 — ADMIN PANEL TESTS
    // =========================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_access_orders_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/orders');
        $response->assertStatus(200);
        $response->assertSee('Order');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_gets_locality_filtered_delivery_agents(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/orders/api/delivery-agents?locality_id=' . $this->locality->id);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Ankita Upadhyay', 'phone' => '8726377978']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_gets_all_agents_when_no_locality_match(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/orders/api/delivery-agents');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Ankita Upadhyay']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_assign_delivery_agent_to_order(): void
    {
        $order = $this->makeOrder();

        $response = $this->actingAs($this->admin)
            ->postJson("/orders/{$order->id}/assign-delivery", [
                'delivery_person_id' => $this->deliveryPerson->id,
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonFragment(['name' => 'Ankita Upadhyay']);

        $this->assertDatabaseHas('orders', [
            'id'                          => $order->id,
            'assigned_delivery_person_id' => $this->deliveryPerson->id,
            'assigned_delivery'           => $this->anshDelivery->id,
            'status'                      => 'Assigned',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_order_show_displays_store_gps_and_assign_panel(): void
    {
        $order = $this->makeOrder();

        $response = $this->actingAs($this->admin)->get("/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertSee('Assign Delivery Agent')
            ->assertSee('Delivery Store Location')
            ->assertSee('Ganga Nagar')
            ->assertSee('maps.google.com');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_assign_rejects_invalid_delivery_person(): void
    {
        $order = $this->makeOrder();

        $this->actingAs($this->admin)
            ->postJson("/orders/{$order->id}/assign-delivery", ['delivery_person_id' => 99999])
            ->assertStatus(422);
    }

    // =========================================================================
    // GROUP 2 — SALES PANEL (ANSH as Sales Person)
    // =========================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function ansh_sales_can_access_dashboard(): void
    {
        $this->actingAs($this->anshSales)
            ->get('/sale/panel/dashboard')
            ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function ansh_sales_sees_his_order_in_transactions(): void
    {
        $order = $this->makeOrder();

        $this->actingAs($this->anshSales)
            ->get('/sale/panel/transactions')
            ->assertStatus(200)
            ->assertSee($order->order_number);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function ansh_sales_sees_no_delivery_badge_before_assignment(): void
    {
        $order = $this->makeOrder();

        $this->actingAs($this->anshSales)
            ->get('/sale/panel/transactions')
            ->assertStatus(200)
            ->assertSee($order->order_number)
            ->assertDontSee('Delivery Agent');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function ansh_sales_sees_purple_delivery_card_after_assignment(): void
    {
        $order = $this->makeAssignedOrder();

        $response = $this->actingAs($this->anshSales)
            ->get('/sale/panel/transactions');

        $response->assertStatus(200)
            ->assertSee('Delivery Agent')
            ->assertSee('Ankita Upadhyay')
            ->assertSee('Get Directions');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function ansh_sales_sees_store_map_link_in_delivery_card(): void
    {
        $order = $this->makeAssignedOrder();

        $this->actingAs($this->anshSales)
            ->get('/sale/panel/transactions')
            ->assertStatus(200)
            ->assertSee('google.com/maps')  // covers both maps.google.com and google.com/maps/dir
            ->assertSee('swastik general store');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function ansh_sales_sees_store_address_in_delivery_card(): void
    {
        $order = $this->makeAssignedOrder();

        $this->actingAs($this->anshSales)
            ->get('/sale/panel/transactions')
            ->assertStatus(200)
            ->assertSee('Ganga Nagar');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function ansh_sales_cannot_assign_delivery_agent(): void
    {
        $order = $this->makeOrder();

        // Sales user gets 403 from controller's isAdmin() check
        $this->actingAs($this->anshSales)
            ->postJson("/orders/{$order->id}/assign-delivery", [
                'delivery_person_id' => $this->deliveryPerson->id,
            ])
            ->assertStatus(403);

        // Order must remain unassigned
        $this->assertDatabaseMissing('orders', [
            'id'                          => $order->id,
            'assigned_delivery_person_id' => $this->deliveryPerson->id,
        ]);
    }

    // =========================================================================
    // GROUP 3 — DELIVERY PANEL (ANSH as Delivery Person)
    // =========================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function ansh_delivery_can_login(): void
    {
        $this->post('/delivery-panel/login', [
            'email'    => 'anshyadav30@gmail.com',
            'password' => 'password',
        ])->assertRedirect('/delivery-panel/dashboard');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function ansh_delivery_sees_assigned_order_in_my_orders(): void
    {
        $order = $this->makeAssignedOrder();

        $this->actingAs($this->anshDelivery)
            ->get('/delivery-panel/my-orders')
            ->assertStatus(200)
            ->assertSee($order->order_number);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function ansh_delivery_sees_full_location_card_on_order_details(): void
    {
        $order = $this->makeAssignedOrder();

        $response = $this->actingAs($this->anshDelivery)
            ->get("/delivery-panel/order-details/{$order->id}");

        $response->assertStatus(200)
            ->assertSee('Delivery Location')
            ->assertSee('swastik general store')
            ->assertSee('Ganga Nagar')
            ->assertSee('Get Directions')
            ->assertSee('Call Store')
            ->assertSee('maps.google.com');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function ansh_delivery_sees_gps_coordinates_on_order_details(): void
    {
        $order = $this->makeAssignedOrder();

        $this->actingAs($this->anshDelivery)
            ->get("/delivery-panel/order-details/{$order->id}")
            ->assertStatus(200)
            ->assertSee('28.6063200')
            ->assertSee('GPS Coordinates');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function ansh_delivery_sees_sales_person_name_on_order(): void
    {
        $order = $this->makeAssignedOrder();

        $this->actingAs($this->anshDelivery)
            ->get("/delivery-panel/order-details/{$order->id}")
            ->assertStatus(200)
            ->assertSee('Order Issued By')
            ->assertSee('ANSH YADAV');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function ansh_delivery_can_mark_order_picked(): void
    {
        $order = $this->makeAssignedOrder();

        $this->actingAs($this->anshDelivery)
            ->post("/delivery-panel/orders/{$order->id}/status", [
                '_token' => csrf_token(),
                'status' => 'Picked',
            ])->assertRedirect();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'Picked']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function ansh_delivery_can_mark_order_out_for_delivery(): void
    {
        $order = $this->makeAssignedOrder();
        $order->update(['status' => 'Picked']);

        $this->actingAs($this->anshDelivery)
            ->post("/delivery-panel/orders/{$order->id}/status", [
                '_token' => csrf_token(),
                'status' => 'Out for Delivery',
            ])->assertRedirect();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'Out for Delivery']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function ansh_delivery_can_mark_delivered_with_gps_and_photo(): void
    {
        $order = $this->makeAssignedOrder();
        $order->update(['status' => 'Out for Delivery']);

        $photo = UploadedFile::fake()->image('proof.jpg', 640, 480);

        $response = $this->actingAs($this->anshDelivery)
            ->post("/delivery-panel/orders/{$order->id}/status", [
                '_token'         => csrf_token(),
                'status'         => 'Delivered',
                'delivery_lat'   => '28.6063200',
                'delivery_lng'   => '77.2090000',
                'delivery_photo' => $photo,
            ]);
        $response->assertRedirect();

        $order->refresh();
        $this->assertEquals('Delivered', $order->status);
        // GPS saved (decimal column stores as string)
        $this->assertNotNull($order->delivery_lat);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function ansh_delivery_can_mark_order_failed(): void
    {
        $order = $this->makeAssignedOrder();
        $order->update(['status' => 'Out for Delivery']);

        $this->actingAs($this->anshDelivery)
            ->post("/delivery-panel/orders/{$order->id}/status", [
                '_token'         => csrf_token(),
                'status'         => 'Failed',
                'failure_reason' => 'Owner Not Available',
            ])->assertRedirect();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'Failed']);
    }

    // =========================================================================
    // GROUP 4 — ATTENDANCE (Check-In requires Location + Photo)
    // =========================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function check_in_without_location_and_photo_marks_absent(): void
    {
        $this->actingAs($this->anshDelivery)
            ->post('/delivery-panel/attendance/mark', [
                '_token'      => csrf_token(),
                'status'      => 'Present',
                'action_type' => 'check_in',
            ])->assertRedirect();

        $this->assertDatabaseHas('attendances', ['status' => 'Absent']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function check_in_with_only_location_no_photo_marks_absent(): void
    {
        $this->actingAs($this->anshDelivery)
            ->post('/delivery-panel/attendance/mark', [
                '_token'      => csrf_token(),
                'status'      => 'Present',
                'action_type' => 'check_in',
                'latitude'    => '28.6139',
                'longitude'   => '77.2090',
            ])->assertRedirect();

        $this->assertDatabaseHas('attendances', ['status' => 'Absent']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function check_in_with_only_photo_no_location_marks_absent(): void
    {
        $photo = UploadedFile::fake()->image('checkin.jpg');

        $this->actingAs($this->anshDelivery)
            ->post('/delivery-panel/attendance/mark', [
                '_token'           => csrf_token(),
                'status'           => 'Present',
                'action_type'      => 'check_in',
                'attendance_image' => $photo,
            ])->assertRedirect();

        $this->assertDatabaseHas('attendances', ['status' => 'Absent']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function check_in_with_location_and_photo_marks_present(): void
    {
        $photo = UploadedFile::fake()->image('checkin.jpg', 400, 400);

        $this->actingAs($this->anshDelivery)
            ->post('/delivery-panel/attendance/mark', [
                '_token'           => csrf_token(),
                'status'           => 'Present',
                'action_type'      => 'check_in',
                'latitude'         => '28.6139',
                'longitude'        => '77.2090',
                'attendance_image' => $photo,
            ])->assertRedirect();

        $this->assertDatabaseHas('attendances', ['status' => 'Present']);
    }

    // =========================================================================
    // GROUP 5 — COMPLETE END-TO-END FLOW (Ansh's full journey)
    // =========================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function complete_ansh_flow_sales_to_delivery(): void
    {
        // STEP 1: Ansh (sales) creates order for swastik store
        $order = Order::create([
            'order_number'   => 'ORD-ANSH-' . time(),
            'store_id'       => $this->store->id,
            'customer_name'  => 'Swastik',
            'customer_phone' => '9000000001',
            'total_amount'   => 330.00,
            'amount'         => 330.00,
            'status'         => 'Pending',
            'created_by'     => $this->anshSales->id,
            'order_date'     => now()->toDateString(),
        ]);
        OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $this->product->id,
            'product_name' => 'Test Product',
            'quantity'     => 3,
            'unit_price'   => 110.00,
            'subtotal'     => 330.00,
            'total'        => 330.00,
        ]);
        $this->assertEquals('Pending', $order->fresh()->status);

        // STEP 2: Ansh (sales) sees order — no delivery badge yet
        $this->actingAs($this->anshSales)
            ->get('/sale/panel/transactions')
            ->assertSee($order->order_number)
            ->assertDontSee('Delivery Agent');

        // STEP 3: Admin sees locality-filtered agents for swastik store
        $this->actingAs($this->admin)
            ->getJson('/orders/api/delivery-agents?locality_id=' . $this->locality->id)
            ->assertJsonFragment(['name' => 'Ankita Upadhyay']);

        // STEP 4: Admin assigns Ankita to the order
        $this->actingAs($this->admin)
            ->postJson("/orders/{$order->id}/assign-delivery", [
                'delivery_person_id' => $this->deliveryPerson->id,
            ])
            ->assertJson(['success' => true]);

        $order->refresh();
        $this->assertEquals('Assigned', $order->status);
        $this->assertEquals($this->deliveryPerson->id, $order->assigned_delivery_person_id);
        $this->assertEquals($this->anshDelivery->id, $order->assigned_delivery);

        // STEP 5: Ansh (sales) now sees purple delivery card with full info
        $salesView = $this->actingAs($this->anshSales)
            ->get('/sale/panel/transactions');
        $salesView->assertSee('Delivery Agent')
            ->assertSee('Ankita Upadhyay')
            ->assertSee('swastik general store')
            ->assertSee('Ganga Nagar')
            ->assertSee('Get Directions');

        // STEP 6: Ansh (delivery) sees order in My Orders
        $this->actingAs($this->anshDelivery)
            ->get('/delivery-panel/my-orders')
            ->assertSee($order->order_number);

        // STEP 7: Ansh (delivery) opens order details — sees full location card
        $detailView = $this->actingAs($this->anshDelivery)
            ->get("/delivery-panel/order-details/{$order->id}");
        $detailView->assertSee('Delivery Location')
            ->assertSee('swastik general store')
            ->assertSee('Ganga Nagar')
            ->assertSee('Get Directions')
            ->assertSee('Call Store')
            ->assertSee('GPS Coordinates')
            ->assertSee('ANSH YADAV');

        // STEP 8: Ansh (delivery) marks Picked
        $this->actingAs($this->anshDelivery)
            ->post("/delivery-panel/orders/{$order->id}/status", [
                '_token' => csrf_token(), 'status' => 'Picked',
            ])->assertRedirect();
        $this->assertEquals('Picked', $order->fresh()->status);

        // STEP 9: Ansh (delivery) marks Out for Delivery
        $this->actingAs($this->anshDelivery)
            ->post("/delivery-panel/orders/{$order->id}/status", [
                '_token' => csrf_token(), 'status' => 'Out for Delivery',
            ])->assertRedirect();
        $this->assertEquals('Out for Delivery', $order->fresh()->status);

        // STEP 10: Ansh (delivery) marks Delivered with GPS proof + photo
        $photo = UploadedFile::fake()->image('delivery_proof.jpg', 640, 480);
        $this->actingAs($this->anshDelivery)
            ->post("/delivery-panel/orders/{$order->id}/status", [
                '_token'         => csrf_token(),
                'status'         => 'Delivered',
                'delivery_lat'   => '28.6063200',
                'delivery_lng'   => '77.2090000',
                'delivery_photo' => $photo,
            ])->assertRedirect();

        $order->refresh();
        $this->assertEquals('Delivered', $order->status);
        // GPS coordinates saved
        $this->assertNotNull($order->delivery_lat);

        // STEP 11: Ansh (sales) sees order as Delivered
        $this->actingAs($this->anshSales)
            ->get('/sale/panel/transactions')
            ->assertSee('Delivered');

        // STEP 12: Admin sees order as Delivered with agent name
        $this->actingAs($this->admin)
            ->get("/orders/{$order->id}")
            ->assertSee('Delivered')
            ->assertSee('Ankita Upadhyay');
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    private function makeOrder(): Order
    {
        return Order::create([
            'order_number'   => 'ORD-TEST-' . uniqid(),
            'store_id'       => $this->store->id,
            'customer_name'  => 'Swastik',
            'customer_phone' => '9000000000',
            'total_amount'   => 330.00,
            'amount'         => 330.00,
            'status'         => 'Pending',
            'created_by'     => $this->anshSales->id,
            'order_date'     => now()->toDateString(),
        ]);
    }

    private function makeAssignedOrder(): Order
    {
        $order = $this->makeOrder();
        $order->update([
            'status'                      => 'Assigned',
            'assigned_delivery_person_id' => $this->deliveryPerson->id,
            'assigned_delivery'           => $this->anshDelivery->id,
        ]);
        return $order;
    }
}
