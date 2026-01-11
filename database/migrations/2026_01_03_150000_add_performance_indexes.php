<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tambah Database Indexes untuk Performa Go-Live
 * 
 * Index ini ditambahkan untuk kolom yang sering digunakan dalam:
 * - Filter (WHERE clause)
 * - Sort (ORDER BY)
 * - JOIN antar tabel
 * - Grouping (GROUP BY)
 * 
 * Referensi: Checklist Go-Live Laravel - Database Optimization
 */
return new class extends Migration
{
    public function up(): void
    {
        // ========================================
        // SALES TABLE
        // ========================================
        Schema::table('sales', function (Blueprint $table) {
            // Filter tanggal (sangat sering dipakai di laporan & DataTable)
            $table->index('date', 'idx_sales_date');
            
            // Filter status transaksi
            $table->index('status', 'idx_sales_status');
            
            // Filter status pembayaran
            $table->index('payment_status', 'idx_sales_payment_status');
            
            // Filter per kasir
            $table->index('user_id', 'idx_sales_user_id');
            
            // Filter customer
            $table->index('customer_id', 'idx_sales_customer_id');
            
            // Composite index untuk filter umum: tanggal + status
            $table->index(['date', 'status'], 'idx_sales_date_status');
            
            // Flag filter
            $table->index('has_manual_input', 'idx_sales_has_manual_input');
            $table->index('has_price_adjustment', 'idx_sales_has_price_adjustment');
        });

        // ========================================
        // SALE_DETAILS TABLE
        // ========================================
        Schema::table('sale_details', function (Blueprint $table) {
            // Foreign key ke header (sudah implicit, tapi explicit lebih baik)
            $table->index('sale_id', 'idx_sale_details_sale_id');
            
            // Filter jenis item
            $table->index('source_type', 'idx_sale_details_source_type');
            
            // Relasi ke produk
            $table->index('product_id', 'idx_sale_details_product_id');
            
            // Composite untuk filter cepat
            $table->index(['sale_id', 'source_type'], 'idx_sale_details_sale_source');
        });

        // ========================================
        // SALE_PAYMENTS TABLE
        // ========================================
        Schema::table('sale_payments', function (Blueprint $table) {
            $table->index('sale_id', 'idx_sale_payments_sale_id');
            $table->index('date', 'idx_sale_payments_date');
            $table->index('payment_method', 'idx_sale_payments_method');
            $table->index(['date', 'payment_method'], 'idx_sale_payments_date_method');
        });

        // ========================================
        // EXPENSES TABLE
        // ========================================
        Schema::table('expenses', function (Blueprint $table) {
            $table->index('date', 'idx_expenses_date');
            $table->index('category_id', 'idx_expenses_category_id');
            $table->index('user_id', 'idx_expenses_user_id');
            $table->index(['date', 'category_id'], 'idx_expenses_date_category');
        });

        // ========================================
        // PRODUCTS TABLE
        // ========================================
        Schema::table('products', function (Blueprint $table) {
            // Pencarian produk
            $table->index('product_name', 'idx_products_name');
            
            // Filter kategori & brand
            $table->index('category_id', 'idx_products_category_id');
            $table->index('brand_id', 'idx_products_brand_id');
            
            // Filter status aktif
            $table->index('is_active', 'idx_products_is_active');
            
            // Composite untuk listing produk
            $table->index(['is_active', 'category_id'], 'idx_products_active_category');
        });

        // ========================================
        // PRODUCT_SECONDS TABLE
        // ========================================
        Schema::table('product_seconds', function (Blueprint $table) {
            $table->index('status', 'idx_product_seconds_status');
            $table->index('category_id', 'idx_product_seconds_category_id');
            $table->index('brand_id', 'idx_product_seconds_brand_id');
        });

        // ========================================
        // PURCHASES TABLE
        // ========================================
        Schema::table('purchases', function (Blueprint $table) {
            $table->index('date', 'idx_purchases_date');
            $table->index('supplier_id', 'idx_purchases_supplier_id');
            $table->index('status', 'idx_purchases_status');
        });

        // ========================================
        // PURCHASE_DETAILS TABLE
        // ========================================
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->index('purchase_id', 'idx_purchase_details_purchase_id');
            $table->index('product_id', 'idx_purchase_details_product_id');
        });

        // ========================================
        // STOCK_MOVEMENTS TABLE
        // ========================================
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->index('product_id', 'idx_stock_movements_product_id');
            $table->index('productable_id', 'idx_stock_movements_productable_id');
            $table->index('type', 'idx_stock_movements_type');
            $table->index('created_at', 'idx_stock_movements_created_at');
            $table->index(['productable_type', 'productable_id'], 'idx_stock_movements_morph');
        });

        // ========================================
        // ADJUSTMENTS TABLE
        // ========================================
        Schema::table('adjustments', function (Blueprint $table) {
            $table->index('date', 'idx_adjustments_date');
            $table->index('status', 'idx_adjustments_status');
        });

        // ========================================
        // OWNER_NOTIFICATIONS TABLE
        // ========================================
        Schema::table('owner_notifications', function (Blueprint $table) {
            $table->index('user_id', 'idx_owner_notifications_user_id');
            $table->index('is_read', 'idx_owner_notifications_is_read');
            $table->index('created_at', 'idx_owner_notifications_created_at');
        });
    }

    public function down(): void
    {
        // Sales
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex('idx_sales_date');
            $table->dropIndex('idx_sales_status');
            $table->dropIndex('idx_sales_payment_status');
            $table->dropIndex('idx_sales_user_id');
            $table->dropIndex('idx_sales_customer_id');
            $table->dropIndex('idx_sales_date_status');
            $table->dropIndex('idx_sales_has_manual_input');
            $table->dropIndex('idx_sales_has_price_adjustment');
        });

        // Sale Details
        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropIndex('idx_sale_details_sale_id');
            $table->dropIndex('idx_sale_details_source_type');
            $table->dropIndex('idx_sale_details_product_id');
            $table->dropIndex('idx_sale_details_sale_source');
        });

        // Sale Payments
        Schema::table('sale_payments', function (Blueprint $table) {
            $table->dropIndex('idx_sale_payments_sale_id');
            $table->dropIndex('idx_sale_payments_date');
            $table->dropIndex('idx_sale_payments_method');
            $table->dropIndex('idx_sale_payments_date_method');
        });

        // Expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('idx_expenses_date');
            $table->dropIndex('idx_expenses_category_id');
            $table->dropIndex('idx_expenses_user_id');
            $table->dropIndex('idx_expenses_date_category');
        });

        // Products
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_name');
            $table->dropIndex('idx_products_category_id');
            $table->dropIndex('idx_products_brand_id');
            $table->dropIndex('idx_products_is_active');
            $table->dropIndex('idx_products_active_category');
        });

        // Product Seconds
        Schema::table('product_seconds', function (Blueprint $table) {
            $table->dropIndex('idx_product_seconds_status');
            $table->dropIndex('idx_product_seconds_category_id');
            $table->dropIndex('idx_product_seconds_brand_id');
        });

        // Purchases
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex('idx_purchases_date');
            $table->dropIndex('idx_purchases_supplier_id');
            $table->dropIndex('idx_purchases_status');
        });

        // Purchase Details
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->dropIndex('idx_purchase_details_purchase_id');
            $table->dropIndex('idx_purchase_details_product_id');
        });

        // Stock Movements
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex('idx_stock_movements_product_id');
            $table->dropIndex('idx_stock_movements_productable_id');
            $table->dropIndex('idx_stock_movements_type');
            $table->dropIndex('idx_stock_movements_created_at');
            $table->dropIndex('idx_stock_movements_morph');
        });

        // Adjustments
        Schema::table('adjustments', function (Blueprint $table) {
            $table->dropIndex('idx_adjustments_date');
            $table->dropIndex('idx_adjustments_status');
        });

        // Owner Notifications
        Schema::table('owner_notifications', function (Blueprint $table) {
            $table->dropIndex('idx_owner_notifications_user_id');
            $table->dropIndex('idx_owner_notifications_is_read');
            $table->dropIndex('idx_owner_notifications_created_at');
        });
    }
};
