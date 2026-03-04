# Translation Service API Documentation

## Overview

The Translation Service API is a RESTful API for managing translations across multiple locales with support for tagging and full-text search.

## Quick Start

### Access the API Documentation

**Swagger UI:** `http://localhost:8000/docs`

**OpenAPI Spec:** `http://localhost:8000/api-docs`

## Authentication

All endpoints (except `/login`) require Bearer token authentication using Laravel Sanctum.

### Login

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password123"
  }'
```

**Response:**
```json
{
  "message": "Login successful",
  "token": "1|abc123xyz...",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com"
  }
}
```

### Using the Token

Include the token in all subsequent requests:

```bash
curl -H "Authorization: Bearer 1|abc123xyz..." \
  http://localhost:8000/api/translations
```

### Logout

```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Endpoints

### Translations

#### List Translations
- **Endpoint:** `GET /api/translations`
- **Auth:** Required
- **Query Parameters:**
  - `locale` - Filter by locale code (en, fr, es, etc.)
  - `key` - Search by translation key
  - `content` - Full-text search in content
  - `tag` - Filter by tag name
  - `page` - Pagination page number

**Example:**
```bash
curl -H "Authorization: Bearer TOKEN" \
  "http://localhost:8000/api/translations?locale=en&tag=important"
```

#### Create Translation
- **Endpoint:** `POST /api/translations`
- **Auth:** Required
- **Body:**
  ```json
  {
    "locale_id": 1,
    "key": "app.welcome",
    "content": "Welcome to our app",
    "tags": ["web", "mobile"]
  }
  ```

#### Get Translation
- **Endpoint:** `GET /api/translations/{id}`
- **Auth:** Required

#### Update Translation
- **Endpoint:** `PUT /api/translations/{id}`
- **Auth:** Required
- **Body:**
  ```json
  {
    "content": "Updated content",
    "tags": ["mobile"]
  }
  ```

#### Delete Translation
- **Endpoint:** `DELETE /api/translations/{id}`
- **Auth:** Required

#### Export Translations
- **Endpoint:** `GET /api/export/{locale}`
- **Auth:** Required
- **Response:** JSON object with keys as translation keys and values as content

**Example:**
```bash
curl -H "Authorization: Bearer TOKEN" \
  http://localhost:8000/api/export/en
```

**Response:**
```json
{
  "app.title": "My Application",
  "app.welcome": "Welcome",
  "app.goodbye": "Goodbye"
}
```

## Examples

### Complete Workflow

```bash
# 1. Login
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password123"
  }' | jq -r '.token')

# 2. Create a translation
curl -X POST http://localhost:8000/api/translations \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "locale_id": 1,
    "key": "app.welcome",
    "content": "Welcome to our app",
    "tags": ["web"]
  }'

# 3. List translations
curl -H "Authorization: Bearer $TOKEN" \
  "http://localhost:8000/api/translations?locale=en"

# 4. Export translations
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/export/en

# 5. Logout
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer $TOKEN"
```

### Search Examples

```bash
# Search by key
curl -H "Authorization: Bearer TOKEN" \
  "http://localhost:8000/api/translations?key=app"

# Search by content (full-text)
curl -H "Authorization: Bearer TOKEN" \
  "http://localhost:8000/api/translations?content=welcome"

# Filter by tag
curl -H "Authorization: Bearer TOKEN" \
  "http://localhost:8000/api/translations?tag=important"

# Combine filters
curl -H "Authorization: Bearer TOKEN" \
  "http://localhost:8000/api/translations?locale=en&tag=web&content=welcome"

# Pagination
curl -H "Authorization: Bearer TOKEN" \
  "http://localhost:8000/api/translations?page=2"
```

## Response Format

All successful responses follow this format:

**Collection Responses (list):**
```json
{
  "data": [
    {
      "id": 1,
      "locale_id": 1,
      "locale": "en",
      "key": "app.title",
      "content": "My Application",
      "tags": ["web", "mobile"],
      "created_at": "2026-03-04T10:20:00Z",
      "updated_at": "2026-03-04T10:20:00Z"
    }
  ],
  "links": {
    "first": "http://localhost:8000/api/translations?page=1",
    "last": "http://localhost:8000/api/translations?page=5",
    "prev": null,
    "next": "http://localhost:8000/api/translations?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 15,
    "to": 15,
    "total": 75
  }
}
```

**Single Resource Response:**
```json
{
  "data": {
    "id": 1,
    "locale_id": 1,
    "locale": "en",
    "key": "app.title",
    "content": "My Application",
    "tags": ["web", "mobile"],
    "created_at": "2026-03-04T10:20:00Z",
    "updated_at": "2026-03-04T10:20:00Z"
  }
}
```

## Error Responses

### Validation Error (422)
```json
{
  "message": "The given data was invalid",
  "errors": {
    "locale_id": ["The locale_id field is required"],
    "key": ["The key field is required"]
  }
}
```

### Unauthorized (401)
```json
{
  "message": "Unauthenticated"
}
```

### Not Found (404)
```json
{
  "message": "Not found"
}
```

## Rate Limiting

API requests are rate-limited to **60 requests per minute** per user/IP.

## Supported Locales

By default, the system supports:
- `en` - English
- `fr` - French
- `es` - Spanish
- `de` - German
- `it` - Italian
- `pt` - Portuguese
- `ja` - Japanese
- `zh` - Chinese
- `ar` - Arabic
- `ru` - Russian

## Testing

Run tests with coverage:

```bash
php artisan test --coverage
```

Run specific test suite:

```bash
php artisan test tests/Feature/TranslationApiTest.php
```

## Development

### Start the server

```bash
php artisan serve
```

Access documentation at: `http://localhost:8000/docs`

### Database setup

```bash
php artisan migrate:fresh --seed
```

## License

MIT
