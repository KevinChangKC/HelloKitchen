/* 根據運送方式修改欄位設定 -- by KevinC 20210203 */
function mxp_checkout_fields_modify_by_shipping_method($fields) {

    //讀取運送方式
    $chosen_methods = WC()->session->get('chosen_shipping_methods');
    $chosen_shipping = current(explode(':', $chosen_methods[0]));
	
	//每個佈景主題參數可能不一樣,印出來確認
	//print_f($chosen_shipping);
    
	//判斷是否為超取,這邊用exclude的方式,未來有新增其他運送方法可能會需要修改
    if ($chosen_shipping != 'flat_rate') {
        
        $fields['billing']['billing_postcode']['required'] = false;
        $fields['billing']['billing_postcode']['class'] = array('hidden');
        $fields['billing']['billing_postcode']['type'] = 'hidden';
        $fields['billing']['billing_address_1']['required'] = false;
        $fields['billing']['billing_address_1']['class'] = array('hidden');
        $fields['billing']['billing_address_1']['type'] = 'hidden';
        $fields['billing']['billing_state']['required'] = false;
        $fields['billing']['billing_state']['class'] = array('hidden');
        $fields['billing']['billing_state']['type'] = 'hidden';
        $fields['billing']['billing_city']['required'] = false;
        $fields['billing']['billing_city']['class'] = array('hidden');
        $fields['billing']['billing_city']['type'] = 'hidden';
		$fields['shipping']['shipping_email']['required'] = false;
        $fields['shipping']['shipping_email']['class'] = array('hidden');
        $fields['shipping']['shipping_email']['type'] = 'hidden';
    }
    return $fields;
}

add_filter('woocommerce_checkout_fields', 'mxp_checkout_fields_modify_by_shipping_method', 999, 1);



/* 如果使用者更改運送方式,即時調整欄位 -- by KevinC 20210203 */
function mxp_hidden_checkout_fields_by_shipping_method($value) {
    
	//判斷當前運送方式
    $chosen_methods = WC()->session->get('chosen_shipping_methods');
    $chosen_shipping = current(explode(':', $chosen_methods[0]));
	
	//判斷是否為超取,這邊用exclude的方式,未來有新增其他運送方法可能會需要修改
    if ($chosen_shipping != 'flat_rate') {
        $fields = WC()->checkout->get_checkout_fields('billing');
        $html = "";
        foreach ($fields as $key => $field) {
            $field['return'] = true;
            // 決定哪些欄位需要調整,以後有新增可自行加入
            if ($key == 'billing_postcode' || $key == 'billing_address_1' || $key == 'billing_state' || $key == 'billing_city') {
                $field['class'] = array('hidden');
                $field['type'] = 'hidden';
                $field['required'] = false;
            }
            $html .= woocommerce_form_field($key, $field, WC()->checkout->get_value($key));
        }
        $value['.woocommerce-billing-fields__field-wrapper'] = '<div class="woocommerce-billing-fields__field-wrapper">' . $html . '</div>';
    } else {
        // 不用修改,正常輸出
        $fields = WC()->checkout->get_checkout_fields('billing');
        $html = "";
        foreach ($fields as $key => $field) {
            $field['return'] = true;
            $html .= woocommerce_form_field($key, $field, WC()->checkout->get_value($key));
        }
        $value['.woocommerce-billing-fields__field-wrapper'] = '<div class="woocommerce-billing-fields__field-wrapper">' . $html . '</div>';
    }

    return $value;
}
add_filter('woocommerce_update_order_review_fragments', 'mxp_hidden_checkout_fields_by_shipping_method', 999, 1);
