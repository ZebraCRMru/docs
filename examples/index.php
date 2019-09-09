<?php

function request($method, $query) {
    $ch = curl_init();
    $data = json_encode($query);
    echo $method . '<br>';
    echo $data . '<br>';
    curl_setopt($ch, CURLOPT_URL, "https://sm.zebracrm.ru/api" . $method);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(["token" => "67775B9811EA8ED73CC1A1B2BCB9000EA6483642", "data" => $data]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
};

$user_id = 17;
$unit_id = 1;
$customer_title = 'Название клиента';
$customer_description = 'Комментарий к клиенту';
$first_name = "Имя";
$middle_name = "Отчество";
$last_name = "Фамилия";
$phone = "79991234567";
$email = "info@example.com";
$order_title = "Заказа БСО с Mosblanki.ru";
$order_description = "Параметры заказа и комментарий";
$product_id = 1052;//БСО
$amount = 1234; //Сумма заказа

$customer = (object)[
    "user_id" => $user_id,
    "unit_id" => $unit_id,
    "title" => $customer_title,
    "description" => $customer_description
];

$customer_id = json_decode(request('/customers/add', $customer))->customer_id;

$contact = (object)[
    "user_id" => $user_id,
    "unit_id" => $unit_id,
    "first_name" => $first_name,
    "middle_name" => $middle_name,
    "last_name" => $last_name,   
    "contacts" => (object)[
        "phone" => $phone,
        "email" => $email
    ]
];
$contact_id = json_decode(request('/customers/'.$customer_id.'/contacts/add', $contact))->contact_id;

$order = (object)[
    "user_id" => $user_id,
    "unit_id" => $unit_id,
    "title" => $order_title,
    "description" => $order_description,
    "customer_id" => intval($customer_id),
    "contact_id" => intval($contact_id),
    "items" => [
        (object) [
            "product_id" => intval($product_id),
            "count" => intval($amount),
            "amount" => intval($amount)
        ]
    ]
];

$order_json = request('/orders/add', $order);

$order_id = json_decode($order_json)->order_id;