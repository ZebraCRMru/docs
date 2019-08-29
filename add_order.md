Добавление заказа через API

Метод POST на адрес
account.zebracrm.ru/api/orders/add

Каждый запрос должен содержать два поля:
token - API-токен Вашего аккаунта в CRM
data - JSON-объект с данными заказа

{
   	"user_id": integer,
   	"unit_id": integer,
   	"customer_id": integer,
   	"contact_id": integer,
   	"company_id": integer,
   	"items": [
{
   			"product_id": integer,
   			"count": float,
   			"price": float,
   			"discount": float,
   			"description": string
},
…
],
   	"paid": [
{
   			"amount": float,
   			"payment_type": integer
},
…
],
   	"status_id": integer,
   	"description": string,
   	"estimated_time": timestamp
}


| Поле              | Название                               | Тип                      | Значение по-умолчанию            |
| ----------------- | -------------------------------------- | ------------------------ | -------------------------------- |
| user_id           | ID пользователя                        | Целое число              | Обязательное поле                |
| unit_id           |ID подразделения                        | Целое число              | Обязательное поле                |
| customer_id       | ID клиента                             | Целое число              | Обязательное поле                |
| contact_id2       | ID контакта клиента                    | Целое число              | Обязательное поле                |
| company_id3       | ID компании клиента                    | Целое число              | Не указывается                   |
| items             | Массив позиций заказа                  | Массив объектов          | Должен быть хотя бы один элемент |
| item.product_id   | 4ID продукта                           | Целое число              | Обязательное поле                |
| item.count        | Количество                             | Число с плавающей точкой | Обязательное поле                |
| item.price        | Стоимость единицы                      | Число с плавающей точкой | Обязательное поле                |
| item.discount     | Скидка/наценка                         | Число с плавающей точкой | 0,00                             |
| item.description  | Комментарий к позиции заказа           | Строка                   | Пустая строка                    |
| paid              | Массив с оплатами                      | Массив объектов          | Пустой массив                    |
| paid.amount       | Сумма оплаты                           | Число с плавающей точкой | Обязательное поле                |
| paid.payment_type | ID типа оплаты                         | Целое число              | Обязательное поле                |
| status_id         | ID статуса заказа                      | Целое число              | 0                                |
| description       | Комментарий к заказу                   | Строка                   | Пустая строка                    |
| estimated_time    | Предполагаемое время готовности заказа | Штамп времени            | Текущие дата и время             |

1 ID клиента получается при добавлении запросом /api/custmers/add (ID розничного покупателя 1)
2 ID контакта клиента получается при добавлении запросом /api/customers/{id}/contacts/add (ID контакта розничного покупателя - 1)
3 ID компании клиента получается при добавлении запросом /api/customers/{id}/company/add 
4 ID продукта берётся из справочника товаров и услуг. (ID произвольного товара 1)
5 ID статуса заказа для создаваемых заказов - 0, для выполненных заказов 1000001

После успешного добавления запроса возвращается JSON-объект с кодом http-ответа 200:
{
   "success": true,
   "order_id": 123
}

где success - статус выполнения, а order_id - номер созданного заказа

В случае ошибки возвращается JSON-объект с кодом http-ответа в зависимости от ошибки:
{
   "success": false,
   "error": "missing required parameter(s)"
}

где success - статус выполнения, а error содержит описание ошибки. 

В целях безопасности данных и сохранности token, для отправки заказа в CRM рекомендуется использовать функцию PHP CURL 

Пример:

<?php
$ch = curl_init();
$account = "sm";
$token = '67775B9811EA8ED73CC1A1B2BCB9000EA6483642';


$order = (object) [
    "user_id" => 1,
    "unit_id" => 1,
    "customer_id" => 1,
    "contact_id" => 1,
    "items" => [
        (object) [
            "product_id" => 1,
            "count" => 2,
            "price" => 1000,
            "discount" => -500,
            "description" => "Комментарий к позиции заказа"
        ]
    ],
    "paid" => [
        (object) [
            "amount" => 1000,
            "payment_type_id" => 1
        ],
        (object) [
            "amount" => 200,
            "payment_type_id" => 2
        ]
    ],
    "description" => "Комментарий к заказу",
    "estimated_time" => "2025-12-25 14:00:00",
    "status_id" => 0
];

$data = json_encode($order);
curl_setopt($ch, CURLOPT_URL, "https://" . $account . ".zebracrm.ru/api/orders/add");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(["token" => $token, "data" => $data]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);



