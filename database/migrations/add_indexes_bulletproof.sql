-- ========================================
-- DATABASE PERFORMANCE INDEXES (BULLETPROOF)
-- Go-Live Optimization - ProjectOmahBan
-- ========================================
-- Menggunakan stored procedure untuk CEK INDEX dulu
-- Dijamin TIDAK akan error duplikat!
-- ========================================

USE `project-omah_ban`;

-- Drop procedure jika sudah ada
DROP PROCEDURE IF EXISTS add_index_if_not_exists;

-- Buat procedure helper
DELIMITER $$
CREATE PROCEDURE add_index_if_not_exists(
    IN p_table_name VARCHAR(64),
    IN p_index_name VARCHAR(64),
    IN p_index_columns VARCHAR(255)
)
BEGIN
    DECLARE index_exists INT DEFAULT 0;
    
    -- Cek apakah index sudah ada
    SELECT COUNT(*) INTO index_exists
    FROM information_schema.statistics
    WHERE table_schema = DATABASE()
      AND table_name = p_table_name
      AND index_name = p_index_name;
    
    -- Jika belum ada, buat index
    IF index_exists = 0 THEN
        SET @sql = CONCAT('ALTER TABLE `', p_table_name, '` ADD INDEX `', p_index_name, '` (', p_index_columns, ')');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
        SELECT CONCAT('✓ Index ', p_index_name, ' berhasil ditambahkan ke ', p_table_name) AS result;
    ELSE
        SELECT CONCAT('⊘ Index ', p_index_name, ' sudah ada di ', p_table_name, ' - SKIP') AS result;
    END IF;
END$$
DELIMITER ;

-- ========================================
-- SALE_DETAILS
-- ========================================
CALL add_index_if_not_exists('sale_details', 'idx_sale_details_sale_id', 'sale_id');
CALL add_index_if_not_exists('sale_details', 'idx_sale_details_source_type', 'source_type');
CALL add_index_if_not_exists('sale_details', 'idx_sale_details_product_id', 'product_id');
CALL add_index_if_not_exists('sale_details', 'idx_sale_details_sale_source', 'sale_id, source_type');

-- ========================================
-- SALE_PAYMENTS
-- ========================================
CALL add_index_if_not_exists('sale_payments', 'idx_sale_payments_sale_id', 'sale_id');
CALL add_index_if_not_exists('sale_payments', 'idx_sale_payments_date', 'date');
CALL add_index_if_not_exists('sale_payments', 'idx_sale_payments_method', 'payment_method');
CALL add_index_if_not_exists('sale_payments', 'idx_sale_payments_date_method', 'date, payment_method');

-- ========================================
-- EXPENSES
-- ========================================
CALL add_index_if_not_exists('expenses', 'idx_expenses_date', 'date');
CALL add_index_if_not_exists('expenses', 'idx_expenses_category_id', 'category_id');
CALL add_index_if_not_exists('expenses', 'idx_expenses_user_id', 'user_id');

-- ========================================
-- PRODUCTS
-- ========================================
CALL add_index_if_not_exists('products', 'idx_products_name', 'product_name');
CALL add_index_if_not_exists('products', 'idx_products_category_id', 'category_id');
CALL add_index_if_not_exists('products', 'idx_products_brand_id', 'brand_id');
CALL add_index_if_not_exists('products', 'idx_products_is_active', 'is_active');
CALL add_index_if_not_exists('products', 'idx_products_active_category', 'is_active, category_id');

-- ========================================
-- PRODUCT_SECONDS
-- ========================================
CALL add_index_if_not_exists('product_seconds', 'idx_product_seconds_status', 'status');
CALL add_index_if_not_exists('product_seconds', 'idx_product_seconds_category_id', 'category_id');
CALL add_index_if_not_exists('product_seconds', 'idx_product_seconds_brand_id', 'brand_id');

-- ========================================
-- PURCHASES
-- ========================================
CALL add_index_if_not_exists('purchases', 'idx_purchases_date', 'date');
CALL add_index_if_not_exists('purchases', 'idx_purchases_supplier_id', 'supplier_id');
CALL add_index_if_not_exists('purchases', 'idx_purchases_status', 'status');

-- ========================================
-- PURCHASE_DETAILS
-- ========================================
CALL add_index_if_not_exists('purchase_details', 'idx_purchase_details_purchase_id', 'purchase_id');
CALL add_index_if_not_exists('purchase_details', 'idx_purchase_details_product_id', 'product_id');

-- ========================================
-- STOCK_MOVEMENTS
-- ========================================
CALL add_index_if_not_exists('stock_movements', 'idx_stock_movements_product_id', 'product_id');
CALL add_index_if_not_exists('stock_movements', 'idx_stock_movements_productable_id', 'productable_id');
CALL add_index_if_not_exists('stock_movements', 'idx_stock_movements_type', 'type');
CALL add_index_if_not_exists('stock_movements', 'idx_stock_movements_created_at', 'created_at');
CALL add_index_if_not_exists('stock_movements', 'idx_stock_movements_morph', 'productable_type, productable_id');

-- ========================================
-- ADJUSTMENTS
-- ========================================
CALL add_index_if_not_exists('adjustments', 'idx_adjustments_date', 'date');

-- ========================================
-- OWNER_NOTIFICATIONS
-- ========================================
CALL add_index_if_not_exists('owner_notifications', 'idx_owner_notifications_user_id', 'user_id');
CALL add_index_if_not_exists('owner_notifications', 'idx_owner_notifications_is_read', 'is_read');
CALL add_index_if_not_exists('owner_notifications', 'idx_owner_notifications_created_at', 'created_at');

-- ========================================
-- CLEANUP - Hapus procedure setelah selesai
-- ========================================
DROP PROCEDURE IF EXISTS add_index_if_not_exists;

-- ========================================
-- SELESAI!
-- ========================================
-- Script ini akan:
-- ✓ CEK apakah index sudah ada
-- ✓ Hanya tambahkan jika BELUM ada
-- ✓ SKIP jika sudah ada (tidak error)
-- ========================================
