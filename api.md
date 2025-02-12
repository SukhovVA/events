# Формирование JWT Access_Token

**Пример JWT:**

```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwM2FiNmE3MC1jYTJlLTRjZjQtODAyNi05ZjE5Zjc4MTgwMDEiLCJleHAiOjE3MzkyNDU1MzB9.6gPennTQBuKlbyCZy9FNTIOa39JwF_Y_c-hoGkNbjwY
```

### Header (Base64URL)

```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9
```

Декодированное содержимое:

```json
{
    "alg": "HS256",
    "typ": "JWT"
}
```

### Payload (Base64URL)

```
eyJzdWIiOiIwM2FiNmE3MC1jYTJlLTRjZjQtODAyNi05ZjE5Zjc4MTgwMDEiLCJleHAiOjE3MzkyNDU1MzB9
```

Декодированное содержимое:

```json
{
    "sub": "03ab6a70-ca2e-4cf4-8026-9f19f7818001",
    "exp": 1739245530
}
```

> **Пояснение:**
> - **sub** — это UUID пользователя, который идентифицирует конкретного пользователя.
> - **exp** — это timestamp, по которому проверяется, что токен не истек. Если текущее время больше, чем значение `exp`,
    токен считается недействительным.

### Signature

```
6gPennTQBuKlbyCZy9FNTIOa39JwF_Y_c-hoGkNbjwY
```

Подпись создается по схеме:

```
HMACSHA256(base64UrlEncode(header) + "." + base64UrlEncode(payload), ACCESS_SECRET)
```

---

# Private/EventController API

Эти эндпоинты предназначены для приватного раздела API (под префиксом `/api/v1/private/events`). Для доступа требуется
авторизация (токен JWT).

## 1. Получение списка мероприятий (Index)

**Метод:** `GET`  
**URL:**

```
http://your-api-domain/api/v1/private/events?page=1
```

**Заголовки:**

- `Authorization: Bearer YOUR_ACCESS_TOKEN`
- `Accept: application/json`

**Пример запроса (cURL):**

```bash
curl --location --request GET 'http://your-api-domain/api/v1/private/events?page=1' \
  --header 'Authorization: Bearer YOUR_ACCESS_TOKEN' \
  --header 'Accept: application/json'
```

---

## 2. Получение деталей мероприятия (Show)

**Метод:** `GET`  
**URL:**

```
http://your-api-domain/api/v1/private/events/123
```

_где `123` — идентификатор мероприятия._

**Заголовки:**

- `Authorization: Bearer YOUR_ACCESS_TOKEN`
- `Accept: application/json`

**Пример запроса (cURL):**

```bash
curl --location --request GET 'http://your-api-domain/api/v1/private/events/123' \
  --header 'Authorization: Bearer YOUR_ACCESS_TOKEN' \
  --header 'Accept: application/json'
```

---

## 3. Создание нового мероприятия (Create)

**Метод:** `POST`  
**URL:**

```
http://your-api-domain/api/v1/private/events
```

**Заголовки:**

- `Authorization: Bearer YOUR_ACCESS_TOKEN`
- `Content-Type: application/json`
- `Accept: application/json`

**Тело запроса (raw JSON):**

```json
{
    "name": "Название мероприятия",
    "description": "Описание мероприятия",
    "startsAt": "2025-03-07T12:21:21+03:00",
    "endsAt": "2025-03-07T13:21:21+03:00",
    "remoteLink": "https://example.com/event",
    "academicHours": 2.0,
    "active": true
}
```

**Пример запроса (cURL):**

```bash
curl --location --request POST 'http://your-api-domain/api/v1/private/events' \
  --header 'Authorization: Bearer YOUR_ACCESS_TOKEN' \
  --header 'Content-Type: application/json' \
  --header 'Accept: application/json' \
  --data-raw '{
    "name": "Название мероприятия",
    "description": "Описание мероприятия",
    "startsAt": "2025-03-07T12:21:21+03:00",
    "endsAt": "2025-03-07T13:21:21+03:00",
    "remoteLink": "https://example.com/event",
    "academicHours": 2.0,
    "active": true
}'
```

---

## 4. Обновление мероприятия (Update)

**Метод:** `PUT`  
**URL:**

```
http://your-api-domain/api/v1/private/events/123
```

_где `123` — идентификатор мероприятия._

**Заголовки:**

- `Authorization: Bearer YOUR_ACCESS_TOKEN`
- `Content-Type: application/json`
- `Accept: application/json`

**Тело запроса (raw JSON):**

```json
{
    "name": "Обновленное название мероприятия",
    "description": "Обновленное описание",
    "startsAt": "2025-03-08T10:00:00+03:00",
    "endsAt": "2025-03-08T12:00:00+03:00",
    "remoteLink": "https://example.com/updated-event",
    "academicHours": 3.0,
    "active": true
}
```

**Пример запроса (cURL):**

```bash
curl --location --request PUT 'http://your-api-domain/api/v1/private/events/123' \
  --header 'Authorization: Bearer YOUR_ACCESS_TOKEN' \
  --header 'Content-Type: application/json' \
  --header 'Accept: application/json' \
  --data-raw '{
    "name": "Обновленное название мероприятия",
    "description": "Обновленное описание",
    "startsAt": "2025-03-08T10:00:00+03:00",
    "endsAt": "2025-03-08T12:00:00+03:00",
    "remoteLink": "https://example.com/updated-event",
    "academicHours": 3.0,
    "active": true
}'
```

---

## 5. Удаление мероприятия (Delete)

**Метод:** `DELETE`  
**URL:**

```
http://your-api-domain/api/v1/private/events/123
```

_где `123` — идентификатор мероприятия._

**Заголовки:**

- `Authorization: Bearer YOUR_ACCESS_TOKEN`
- `Accept: application/json`

**Пример запроса (cURL):**

```bash
curl --location --request DELETE 'http://your-api-domain/api/v1/private/events/123' \
  --header 'Authorization: Bearer YOUR_ACCESS_TOKEN' \
  --header 'Accept: application/json'
```

---

# Public/EventController API

Эти эндпоинты относятся к публичной части API и могут быть доступны без авторизации.

## 1. Получение списка мероприятий (Index)

**Метод:** `GET`  
**URL:**

```
http://your-api-domain/api/v1/events?page=1
```

**Заголовки:**

- `Accept: application/json`

**Пример запроса (cURL):**

```bash
curl --location --request GET 'http://your-api-domain/api/v1/events?page=1' \
  --header 'Accept: application/json'
```

---

## 2. Получение деталей мероприятия (Show)

**Метод:** `GET`  
**URL:**

```
http://your-api-domain/api/v1/events/123
```

**Заголовки:**

- `Accept: application/json`

**Пример запроса (cURL):**

```bash
curl --location --request GET 'http://your-api-domain/api/v1/events/123' \
  --header 'Accept: application/json'
```

---

## 3. Оценка мероприятия (Rate)

**Описание:**  
Эндпоинт для отправки оценки посещённого мероприятия. Обычно требует авторизации.

**Метод:** `POST`  
**URL:**

```
http://your-api-domain/api/v1/events/123/rate
```

**Заголовки:**

- `Authorization: Bearer YOUR_ACCESS_TOKEN`
- `Content-Type: application/json`
- `Accept: application/json`

**Тело запроса (raw JSON):**

```json
{
    "rating": 5
}
```

**Пример запроса (cURL):**

```bash
curl --location --request POST 'http://your-api-domain/api/v1/events/123/rate' \
  --header 'Authorization: Bearer YOUR_ACCESS_TOKEN' \
  --header 'Content-Type: application/json' \
  --header 'Accept: application/json' \
  --data-raw '{
    "rating": 5
}'
```
