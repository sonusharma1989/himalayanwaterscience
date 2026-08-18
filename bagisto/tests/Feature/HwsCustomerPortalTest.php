<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Webkul\Customer\Models\Customer;
use Webkul\Sales\Models\Order;
use Hws\FieldService\Listeners\CreateLeadFromOrder;
use Hws\FieldService\Models\SiteSurvey;

class HwsCustomerPortalTest extends TestCase
{
    use DatabaseTransactions;

    public function test_bulk_quote_creates_trackable_sales_lead(): void
    {
        $response = $this->postJson(route('hws.customer-requests.store'), [
            'request_type'   => 'bulk_quote',
            'customer_name'  => 'Portal Test Customer',
            'customer_phone' => '9999999999',
            'customer_email' => 'portal-test@example.test',
            'product'        => 'RO membrane system',
            'quantity'       => '10 units',
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('hws_site_surveys', [
            'customer_email' => 'portal-test@example.test',
            'request_type'   => 'bulk_quote',
            'source'         => 'Website',
        ]);
    }

    public function test_service_catalog_and_detail_pages_are_available(): void
    {
        $this->get(route('hws.services.index'))
            ->assertOk()
            ->assertSee('Our core services')
            ->assertSee('Water Treatment Plants (WTP)')
            ->assertSee('Annual Maintenance Contracts (AMC)');

        $this->get(route('hws.services.show', 'sewage-treatment-plants'))
            ->assertOk()
            ->assertSee('Sewage Treatment Plants (STP)')
            ->assertSee('Get a technical quote')
            ->assertSee('Required capacity');

        $this->get(route('hws.vision'))
            ->assertOk()
            ->assertSee('Reliable water infrastructure');
    }

    public function test_service_page_quote_is_created_as_a_sales_lead(): void
    {
        $response = $this->postJson(route('hws.customer-requests.store'), [
            'request_type'    => 'bulk_quote',
            'customer_name'   => 'STP Project Customer',
            'customer_phone'  => '9999999997',
            'customer_email'  => 'stp-project@example.test',
            'customer_address'=> 'Noida, Uttar Pradesh',
            'product'         => 'Sewage Treatment Plants (STP)',
            'quantity'        => '50 KLD',
            'notes'           => 'Residential society project',
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $lead = SiteSurvey::where('customer_email', 'stp-project@example.test')->firstOrFail();

        $this->assertSame('bulk_quote', $lead->request_type);
        $this->assertSame('Sewage Treatment Plants (STP)', $lead->request_details['product']);
        $this->assertSame('50 KLD', $lead->request_details['quantity']);
    }

    public function test_service_request_is_visible_in_customer_tracking_hub(): void
    {
        $customer = Customer::create([
            'first_name'        => 'Portal',
            'last_name'         => 'Customer',
            'email'             => 'portal-customer@example.test',
            'phone'             => '9999999998',
            'password'          => Hash::make('password123'),
            'customer_group_id' => 2,
            'status'            => 1,
            'is_verified'       => 1,
        ]);

        $this->actingAs($customer, 'customer');

        $this->postJson(route('hws.customer-requests.store'), [
            'request_type'     => 'installation',
            'customer_name'    => $customer->name,
            'customer_phone'   => $customer->phone,
            'customer_email'   => $customer->email,
            'customer_address' => 'Test Site, Dehradun',
            'notes'            => 'Install and commission system',
        ])->assertOk();

        $this->get(route('hws.customer.account.tracking'))
            ->assertOk()
            ->assertSee('My Tracking Hub')
            ->assertSee('Installation');
    }

    public function test_guest_checkout_creates_one_customer_and_one_lead(): void
    {
        $order = Order::create([
            'increment_id'        => 'TEST-PORTAL-001',
            'status'              => 'pending',
            'is_guest'            => 1,
            'is_gift'             => 0,
            'customer_email'      => 'checkout-portal@example.test',
            'customer_first_name' => 'Checkout',
            'customer_last_name'  => 'Customer',
            'order_currency_code' => 'INR',
            'grand_total'         => 12500,
        ]);

        $listener = app(CreateLeadFromOrder::class);
        $listener->handle($order);
        $listener->handle($order->fresh());

        $customer = Customer::where('email', 'checkout-portal@example.test')->first();

        $this->assertNotNull($customer);
        $this->assertSame($customer->id, $order->fresh()->customer_id);
        $this->assertSame(1, SiteSurvey::where('order_id', $order->id)->count());
        $this->assertDatabaseHas('hws_site_surveys', [
            'order_id'       => $order->id,
            'customer_id'    => $customer->id,
            'request_type'   => 'checkout',
            'reference_no'   => 'ORD-TEST-PORTAL-001',
        ]);
    }
}
