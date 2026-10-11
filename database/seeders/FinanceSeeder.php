<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\FinanceService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        $finance = User::where('role', User::ROLE_FINANCE)->first() ?? User::where('role', User::ROLE_ADMIN)->first();
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $sales = User::where('role', User::ROLE_SALES)->first() ?? $admin;

        $financeService = app(FinanceService::class);

        // 1. Faktur dari Pesanan B2B SO-202610-0001 (Kafe Senja) dengan pembayaran sebagian (Cicilan)
        $order1 = Order::where('order_number', 'SO-202610-0001')->first();
        if ($order1 && !Invoice::where('order_id', $order1->id)->exists()) {
            $inv1 = Invoice::create([
                'invoice_number' => 'INV-' . date('Ym') . '-0001',
                'order_id' => $order1->id,
                'customer_id' => $order1->customer_id,
                'user_id' => $finance->id,
                'invoice_date' => $order1->order_date,
                'due_date' => $order1->due_date ?? Carbon::parse($order1->order_date)->addDays(14)->toDateString(),
                'total_amount' => $order1->total_amount,
                'paid_amount' => 500000,
                'remaining_amount' => $order1->total_amount - 500000,
                'status' => Invoice::STATUS_PARTIAL,
                'notes' => 'Faktur pesanan reguler Kafe Senja Nusantara',
            ]);

            $order1->update([
                'paid_amount' => 500000,
                'payment_status' => Order::PAYMENT_STATUS_PARTIAL,
            ]);

            Payment::create([
                'payment_number' => 'PAY-' . date('Ym') . '-0001',
                'invoice_id' => $inv1->id,
                'order_id' => $order1->id,
                'customer_id' => $order1->customer_id,
                'user_id' => $finance->id,
                'payment_date' => Carbon::parse($order1->order_date)->addDay()->toDateString(),
                'payment_method' => Payment::METHOD_TRANSFER_BCA,
                'amount' => 500000,
                'reference_number' => 'TRF-BCA-202610-0988',
                'notes' => 'Pembayaran cicilan termin ke-1 via transfer BCA',
            ]);
        }

        // 2. Faktur Tempo Melewati Jatuh Tempo (Overdue) - Toko Berkah Kopi
        $toko = Customer::where('customer_type', Customer::TYPE_TOKO)->first();
        $variant = ProductVariant::first();

        if ($toko && $variant && !Invoice::where('invoice_number', 'INV-202609-0002')->exists()) {
            $orderDateOld = now()->subDays(25)->toDateString();
            $dueDateOld = now()->subDays(11)->toDateString();
            $totalOverdue = 1250000;

            $orderOverdue = Order::create([
                'order_number' => 'SO-202609-0002',
                'order_type' => Order::TYPE_B2B,
                'customer_id' => $toko->id,
                'user_id' => $sales->id,
                'sales_user_id' => $toko->sales_user_id ?? $sales->id,
                'order_date' => $orderDateOld,
                'payment_type' => Order::PAYMENT_CREDIT,
                'due_date' => $dueDateOld,
                'status' => Order::STATUS_CONFIRMED,
                'payment_status' => Order::PAYMENT_STATUS_UNPAID,
                'subtotal' => $totalOverdue,
                'discount' => 0,
                'shipping_cost' => 0,
                'total_amount' => $totalOverdue,
                'paid_amount' => 0,
                'shipping_address' => $toko->shipping_address ?? $toko->address,
                'recipient_name' => $toko->pic_name,
                'recipient_phone' => $toko->phone,
                'stock_reduced' => true,
                'confirmed_at' => now()->subDays(25),
                'confirmed_by_user_id' => $admin->id,
                'notes' => 'Pesanan tempo bulan lalu - simulasi overdue',
            ]);

            $orderOverdue->items()->create([
                'product_variant_id' => $variant->id,
                'quantity' => 15,
                'unit_price' => $variant->selling_price,
                'cost_price' => $variant->cost_price,
                'subtotal' => $totalOverdue,
            ]);

            Invoice::create([
                'invoice_number' => 'INV-202609-0002',
                'order_id' => $orderOverdue->id,
                'customer_id' => $toko->id,
                'user_id' => $finance->id,
                'invoice_date' => $orderDateOld,
                'due_date' => $dueDateOld,
                'total_amount' => $totalOverdue,
                'paid_amount' => 0,
                'remaining_amount' => $totalOverdue,
                'status' => Invoice::STATUS_UNPAID,
                'notes' => 'Tagihan melewati tempo jatuh tempo (Perlu peringatan & penagihan).',
            ]);
        }

        // 3. Faktur Lunas Penuh (Paid Full) - Pelanggan Retail / Tunai
        $reseller = Customer::where('customer_type', Customer::TYPE_RESELLER)->first();
        if ($reseller && $variant && !Invoice::where('invoice_number', 'INV-' . date('Ym') . '-0003')->exists()) {
            $totalLunas = 850000;
            $orderLunas = Order::create([
                'order_number' => 'SO-' . date('Ym') . '-0003',
                'order_type' => Order::TYPE_B2B,
                'customer_id' => $reseller->id,
                'user_id' => $sales->id,
                'sales_user_id' => $reseller->sales_user_id ?? $sales->id,
                'order_date' => now()->subDay()->toDateString(),
                'payment_type' => Order::PAYMENT_CASH,
                'due_date' => now()->subDay()->toDateString(),
                'status' => Order::STATUS_CONFIRMED,
                'payment_status' => Order::PAYMENT_STATUS_PAID,
                'subtotal' => $totalLunas,
                'discount' => 0,
                'shipping_cost' => 0,
                'total_amount' => $totalLunas,
                'paid_amount' => $totalLunas,
                'shipping_address' => $reseller->shipping_address ?? $reseller->address,
                'recipient_name' => $reseller->pic_name,
                'recipient_phone' => $reseller->phone,
                'stock_reduced' => true,
                'confirmed_at' => now()->subDay(),
                'confirmed_by_user_id' => $admin->id,
                'notes' => 'Pesanan tunai reseller langsung lunas.',
            ]);

            $orderLunas->items()->create([
                'product_variant_id' => $variant->id,
                'quantity' => 10,
                'unit_price' => $variant->selling_price,
                'cost_price' => $variant->cost_price,
                'subtotal' => $totalLunas,
            ]);

            $invLunas = Invoice::create([
                'invoice_number' => 'INV-' . date('Ym') . '-0003',
                'order_id' => $orderLunas->id,
                'customer_id' => $reseller->id,
                'user_id' => $finance->id,
                'invoice_date' => now()->subDay()->toDateString(),
                'due_date' => now()->subDay()->toDateString(),
                'total_amount' => $totalLunas,
                'paid_amount' => $totalLunas,
                'remaining_amount' => 0,
                'status' => Invoice::STATUS_PAID,
                'notes' => 'Faktur lunas dibayar tunai di kasir.',
            ]);

            Payment::create([
                'payment_number' => 'PAY-' . date('Ym') . '-0002',
                'invoice_id' => $invLunas->id,
                'order_id' => $orderLunas->id,
                'customer_id' => $reseller->id,
                'user_id' => $finance->id,
                'payment_date' => now()->subDay()->toDateString(),
                'payment_method' => Payment::METHOD_CASH,
                'amount' => $totalLunas,
                'reference_number' => 'KASIR-CASH-0021',
                'notes' => 'Pelunasan kontan kasir toko',
            ]);
        }
    }
}
