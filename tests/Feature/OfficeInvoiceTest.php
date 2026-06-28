<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\tables;
use App\Models\User;
use Tests\TestCase;

class OfficeInvoiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guest users are redirected to login page.
     */
    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get('/office-invoices');
        $response->assertRedirect('/login');
    }

    /**
     * Test the office invoices page loads successfully for authenticated users.
     */
    public function test_office_invoices_page_loads_for_auth_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/office-invoices');

        $response->assertStatus(200);
        $response->assertViewIs('office-invoices');
        $response->assertViewHas('invoices');
    }

    /**
     * Test a new office invoice can be stored successfully, automatic fields are set.
     */
    public function test_store_office_invoice_successfully(): void
    {
        $user = User::factory()->create([
            'name' => 'المستخدم المختبر'
        ]);

        $invoiceData = [
            'invoice' => 'INV-TEST-99',
            'code' => 'TOP-TEST-99',
            'receiver' => 'المستلم التجريبي',
            'qty' => 10,
            'price' => 150.50,
            'type' => 'طباعة',
        ];

        $response = $this->actingAs($user)->post('/office-invoices', $invoiceData);

        $response->assertRedirect(route('office-invoices.index'));
        $response->assertSessionHas('success', 'تم إضافة الفاتورة بنجاح!');

        $this->assertDatabaseHas('tables', [
            'invoice' => 'INV-TEST-99',
            'code' => 'TOP-TEST-99',
            'sender' => 'المستخدم المختبر', // Saved automatically from user name
            'receiver' => 'المستلم التجريبي',
            'qty' => 10,
            'price' => 150.50,
            'type' => 'طباعة',
            'date' => now()->toDateString(), // Saved automatically as current date
        ]);
    }

    /**
     * Test validation rules when storing a new office invoice.
     */
    public function test_store_office_invoice_validation_fails(): void
    {
        $user = User::factory()->create();

        // Empty request
        $response = $this->actingAs($user)->post('/office-invoices', []);

        $response->assertSessionHasErrors(['invoice', 'code', 'receiver', 'qty', 'price', 'type']);
    }

    /**
     * Test the printable invoice receipt can be displayed.
     */
    public function test_printable_invoice_receipt_loads(): void
    {
        $user = User::factory()->create();
        $invoice = tables::create([
            'invoice' => 'INV-TEST-88',
            'code' => 'TOP-TEST-88',
            'sender' => 'المسلم',
            'receiver' => 'المستلم',
            'qty' => 5,
            'price' => 200,
            'type' => 'ليزر',
            'date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->get("/invoice-receipt/{$invoice->id}");

        $response->assertStatus(200);
        $response->assertViewIs('invoice-receipt');
        $response->assertViewHas('invoice');
        $response->assertSee('INV-TEST-88');
        $response->assertSee('1,000.00'); // total qty * price = 5 * 200 = 1000
    }
}
