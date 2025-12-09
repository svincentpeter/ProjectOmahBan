<?php

namespace App\Services;

class CartService
{
    private const CART_SESSION_KEY = 'pos_cart';
    
    /**
     * Get current cart from session
     */
    public function getCart()
    {
        return session(self::CART_SESSION_KEY, [
            'items' => [],
            'subtotal' => 0,
            'discount' => 0,
            'discount_type' => 'nominal',
            'discount_reason' => null,
            'total' => 0,
            'customer_id' => null,
            'customer_name' => null,
        ]);
    }
    
    /**
     * Add item to cart
     */
    public function addItem($productId, $name, $price, $qty = 1, $type = 'product', $additionalData = [])
    {
        $cart = $this->getCart();
        
        // Generate unique item ID
        $itemId = $type . '_' . $productId . '_' . time();
        
        // Check if item already exists (same product)
        $existingKey = null;
        foreach ($cart['items'] as $key => $item) {
            if ($item['product_id'] == $productId && $item['type'] == $type) {
                $existingKey = $key;
                break;
            }
        }
        
        if ($existingKey !== null) {
            // Update quantity
            $cart['items'][$existingKey]['qty'] += $qty;
        } else {
            // Add new item
            $cart['items'][$itemId] = array_merge([
                'id' => $itemId,
                'product_id' => $productId,
                'name' => $name,
                'price' => $price,
                'price_original' => $price,
                'price_adjusted' => $price,
                'qty' => $qty,
                'type' => $type,
                'discount' => 0,
                'discount_reason' => null,
            ], $additionalData);
        }
        
        $this->recalculate($cart);
        session([self::CART_SESSION_KEY => $cart]);
        
        return $cart;
    }
    
    /**
     * Update item quantity
     */
    public function updateQty($itemId, $qty)
    {
        $cart = $this->getCart();
        
        if (isset($cart['items'][$itemId])) {
            if ($qty <= 0) {
                unset($cart['items'][$itemId]);
            } else {
                $cart['items'][$itemId]['qty'] = $qty;
            }
            
            $this->recalculate($cart);
            session([self::CART_SESSION_KEY => $cart]);
        }
        
        return $cart;
    }
    
    /**
     * Update item price (with discount/adjustment)
     */
    public function updateItemPrice($itemId, $discount, $reason = null)
    {
        $cart = $this->getCart();
        
        if (isset($cart['items'][$itemId])) {
            $item = &$cart['items'][$itemId];
            $item['discount'] = $discount;
            $item['price_adjusted'] = $item['price_original'] - $discount;
            $item['discount_reason'] = $reason;
            
            $this->recalculate($cart);
            session([self::CART_SESSION_KEY => $cart]);
        }
        
        return $cart;
    }
    
    /**
     * Remove item from cart
     */
    public function removeItem($itemId)
    {
        $cart = $this->getCart();
        
        if (isset($cart['items'][$itemId])) {
            unset($cart['items'][$itemId]);
            
            $this->recalculate($cart);
            session([self::CART_SESSION_KEY => $cart]);
        }
        
        return $cart;
    }
    
    /**
     * Apply discount to transaction
     */
    public function applyDiscount($value, $type = 'nominal', $reason = null)
    {
        $cart = $this->getCart();
        
        if ($type === 'percentage') {
            $cart['discount'] = ($cart['subtotal'] * $value) / 100;
        } else {
            $cart['discount'] = $value;
        }
        
        $cart['discount_type'] = $type;
        $cart['discount_reason'] = $reason;
        
        $this->recalculate($cart);
        session([self::CART_SESSION_KEY => $cart]);
        
        return $cart;
    }
    
    /**
     * Set customer
     */
    public function setCustomer($customerId = null, $customerName = null)
    {
        $cart = $this->getCart();
        $cart['customer_id'] = $customerId;
        $cart['customer_name'] = $customerName;
        
        session([self::CART_SESSION_KEY => $cart]);
        
        return $cart;
    }
    
    /**
     * Clear cart
     */
    public function clear()
    {
        session()->forget(self::CART_SESSION_KEY);
        return $this->getCart();
    }
    
    /**
     * Recalculate cart totals
     */
    private function recalculate(&$cart)
    {
        // Calculate subtotal (sum of all items)
        $subtotal = 0;
        foreach ($cart['items'] as $item) {
            $subtotal += $item['price_adjusted'] * $item['qty'];
        }
        
        $cart['subtotal'] = $subtotal;
        
        // Ensure discount doesn't exceed subtotal
        if ($cart['discount'] > $subtotal) {
            $cart['discount'] = $subtotal;
        }
        
        // Calculate total (no PPN for UMKM)
        $cart['total'] = $subtotal - $cart['discount'];
    }
    
    /**
     * Get cart summary
     */
    public function getSummary()
    {
        $cart = $this->getCart();
        
        return [
            'count' => count($cart['items']),
            'items' => array_values($cart['items']),
            'subtotal' => $cart['subtotal'],
            'discount' => $cart['discount'],
            'total' => $cart['total'],
            'customer_id' => $cart['customer_id'],
            'customer_name' => $cart['customer_name'],
        ];
    }
}
