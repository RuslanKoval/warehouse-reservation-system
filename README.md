# Warehouse Reservation System (Laravel)

## Overview
This project implements a **simplified event-driven warehouse reservation system** using Laravel.  
The system handles the following workflows:
- Order creation
- Inventory reservation
- Supplier integration
- Delayed delivery confirmation via asynchronous jobs

### Core Principles
- **Event-driven architecture**
- **Asynchronous processing**
- **Idempotent and retry-safe jobs**
- **Clear order state machine**

---

## Event Flow Description

### 1. Order Creation
- **Endpoint:**
  ```
  POST /api/order
  ```

- **Flow:**
    1. Client submits `sku` and `qty`.
    2. The system creates an **Order** with status `pending`.
    3. The `OrderCreated` event is emitted.
    4. Event listener dispatches the `ReserveInventoryJob`.

---

### 2. Inventory Reservation (Async Job)
- **Job:** `ReserveInventoryJob`

- **Flow:**
    - Job runs inside a DB transaction:
        1. The inventory row is locked using `SELECT ... FOR UPDATE`.
        2. **If stock is sufficient:**
            - Inventory quantity is decremented.
            - Inventory movement record is created.
            - Order status → `reserved`.
        3. **If stock is insufficient:**
            - A supplier reserve request is sent.
            - Order status → `awaiting_restock`.
            - Supplier reference is stored.
            - `CheckSupplierStatusJob` is scheduled with a **15s delay**.

---

### 3. Supplier Integration
- **Reserve Endpoint:**
  ```
  POST /supplier/reserve
  ```

- **Response Example:**
  ```json
  { "accepted": true, "ref": "1234" }
  ```

- Notes:
    - `accepted = true` does **NOT** guarantee actual delivery.
    - Integration is mocked using `Http::fake()` for testability and isolation.

---

### 4. Supplier Status Check (Delayed Job)
- **Job:** `CheckSupplierStatusJob`

- **Flow:**  
  Job checks the supplier status by reference:
    - Possible responses:
        - `ok` → Order status → `reserved`
        - `fail` → Order status → `failed`
        - `delayed` → Retry after **15 seconds**

    - **Maximum Retries for `delayed`:** 2  
      After 2 retries → Order status → `failed`.

---

### 5. Read Endpoints
- **Get order details and status:**
  ```
  GET /api/orders/{id}
  ```

- **Get inventory movement history:**
  ```
  GET /api/inventory/{sku}/movements
  ```

---

## Error Handling Strategy

### 1. Asynchronous Error Isolation
- Heavy business logic is executed in **queued jobs**.
- Failures do **not** block HTTP requests.
- Queue retry mechanism is used for transient failures.

### 2. Database Consistency
- Inventory reservation is wrapped in a **DB transaction**.
- Row-level locking (`lockForUpdate`) prevents race conditions.
- Inventory movements are written atomically with stock changes.

### 3. External Service Failures
- Supplier integration is treated as **eventually consistent**.
- No assumption is made that `accepted = true` guarantees delivery.
- Delayed responses are retried with **backoff (15s delay)**.
- Hard stop after max retry count to avoid infinite loops.

### 4. Idempotency
- Order status transitions are controlled explicitly.
- Jobs can be retried safely without duplicating inventory movements.
- State-based logic prevents double reservation.

---

## Production Improvements

For a production-ready environment, the following improvements would be implemented:

### 1. State Machine / Domain Layer
- Introduce an explicit **Order State Machine**.
- Enforce valid state transitions only.
- Move business logic out of Jobs into **domain services**.

### 2. Observability
- Add **structured logging** for each state transition.
- Implement **distributed tracing** for async flows.
- Track metrics for:
    - Reservation success rate
    - Supplier delays
    - Job retry counts

### 3. Reliability & Scalability
- Use **Redis queue driver**.
- Add **dead-letter queues** for failed jobs.
- Implement **exponential backoff** instead of a fixed delay.
- Add **idempotency keys** for external integrations.

### 4. Data Integrity
- Add **database constraints** for order state transitions.
- Introduce **inventory versioning**.
- Add **optimistic locking** for inventory rows.

### 5. Testing
- Feature tests for the full order lifecycle.
- Contract tests for supplier integration.
- Load tests for concurrent reservations.

### 6. Security & API Hardening
- Rate limiting on order creation.
- **Authentication** & **authorization**.
- Input validation and **SKU normalization**.

---

## Summary
This project demonstrates:
- **Event-driven Laravel architecture**
- **Asynchronous workflow design**
- **Safe inventory reservation under concurrency**
- **Real-world handling of unreliable external systems**

The implementation prioritizes **correctness**, **clarity**, and **extensibility** over premature optimization.