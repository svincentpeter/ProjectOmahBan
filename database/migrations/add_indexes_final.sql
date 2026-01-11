-- ========================================
-- DATABASE PERFORMANCE INDEXES (FINAL CLEAN)
-- Go-Live Optimization - ProjectOmahBan
-- ========================================
-- Berdasarkan analisis index existing di database
-- HANYA menambahkan index yang BELUM ADA
-- ========================================

USE `project-omah_ban`;

-- ========================================
-- SALES TABLE
-- ========================================
-- Index yang SUDAH ADA dan dilewati:
-- - idx_sales_date
-- - idx_sales_status
-- - idx_sales_payment_status
-- - sales_user_id_foreign (user_id)
-- - idx_customer_id (customer_id)
-- - idx_has_manual_input (has_manual_input)
-- - idx_sales_has_adjustment (has_price_adjustment, date)
-- - sales_date_user_status_payment_idx (composite)

-- SKIP SEMUA - Sales table sudah punya index yang cukup!

-- ========================================
-- SALE_DETAILS TABLE
-- ========================================
ALTER TABLE `sale_details`
  ADD INDEX `idx_sale_details_sale_id` (`sale_id`),
  ADD INDEX `idx_sale_details_source_type` (`source_type`),
  ADD INDEX `idx_sale_details_product_id` (`product_id`),
  ADD INDEX `idx_sale_details_sale_source` (`sale_id`, `source_type`);

-- ========================================
-- SALE_PAYMENTS TABLE
-- ========================================
ALTER TABLE `sale_payments`
  ADD INDEX `idx_sale_payments_sale_id` (`sale_id`),
  ADD INDEX `idx_sale_payments_date` (`date`),
  ADD INDEX `idx_sale_payments_method` (`payment_method`),
  ADD INDEX `idx_sale_payments_date_method` (`date`, `payment_method`);

-- ========================================
-- EXPENSES TABLE
-- ========================================
-- SKIP: expenses_date_category_idx sudah ada (date, category_id)
ALTER TABLE `expenses`
  ADD INDEX `idx_expenses_date` (`date`),
  ADD INDEX `idx_expenses_category_id` (`category_id`),
  ADD INDEX `idx_expenses_user_id` (`user_id`);

-- ========================================
-- PRODUCTS TABLE
-- ========================================
ALTER TABLE `products`
  ADD INDEX `idx_products_name` (`product_name`),
  ADD INDEX `idx_products_category_id` (`category_id`),
  ADD INDEX `idx_products_brand_id` (`brand_id`),
  ADD INDEX `idx_products_is_active` (`is_active`),
  ADD INDEX `idx_products_active_category` (`is_active`, `category_id`);

-- ========================================
-- PRODUCT_SECONDS TABLE
-- ========================================
ALTER TABLE `product_seconds`
  ADD INDEX `idx_product_seconds_status` (`status`),
  ADD INDEX `idx_product_seconds_category_id` (`category_id`),
  ADD INDEX `idx_product_seconds_brand_id` (`brand_id`);

-- ========================================
-- PURCHASES TABLE
-- ========================================
ALTER TABLE `purchases`
  ADD INDEX `idx_purchases_date` (`date`),
  ADD INDEX `idx_purchases_supplier_id` (`supplier_id`),
  ADD INDEX `idx_purchases_status` (`status`);

-- ========================================
-- PURCHASE_DETAILS TABLE
-- ========================================
ALTER TABLE `purchase_details`
  ADD INDEX `idx_purchase_details_purchase_id` (`purchase_id`),
  ADD INDEX `idx_purchase_details_product_id` (`product_id`);

-- ========================================
-- STOCK_MOVEMENTS TABLE
-- ========================================
ALTER TABLE `stock_movements`
  ADD INDEX `idx_stock_movements_product_id` (`product_id`),
  ADD INDEX `idx_stock_movements_productable_id` (`productable_id`),
  ADD INDEX `idx_stock_movements_type` (`type`),
  ADD INDEX `idx_stock_movements_created_at` (`created_at`),
  ADD INDEX `idx_stock_movements_morph` (`productable_type`, `productable_id`);

-- ========================================
-- ADJUSTMENTS TABLE
-- ========================================
-- SKIP: idx_adjustments_status, idx_adjustments_date_status sudah ada
ALTER TABLE `adjustments`
  ADD INDEX `idx_adjustments_date` (`date`);

-- ========================================
-- OWNER_NOTIFICATIONS TABLE
-- ========================================
ALTER TABLE `owner_notifications`
  ADD INDEX `idx_owner_notifications_user_id` (`user_id`),
  ADD INDEX `idx_owner_notifications_is_read` (`is_read`),
  ADD INDEX `idx_owner_notifications_created_at` (`created_at`);

-- ========================================
-- SELESAI
-- ========================================
-- Estimasi: ~35 index baru akan ditambahkan
-- 
-- CATATAN PENTING:
-- Tabel SALES tidak ditambahkan index karena sudah lengkap!
-- ========================================
