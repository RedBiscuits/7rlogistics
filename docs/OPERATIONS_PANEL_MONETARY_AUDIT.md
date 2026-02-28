# Operations Panel – Monetary Audit & Production Readiness

**Date:** 2026-02-25  
**Scope:** Ensure no monetary data (prices, amounts, revenue, EGP, etc.) is visible or editable by users with the **Operations** role in the operations Filament panel and related APIs.

---

## 1. Dashboard & widgets

| Item | Status | Notes |
|------|--------|------|
| Monetary widgets not registered | OK | `_3ShipmentsStatusOverview` and `_7ProfitChart` are **not** in `OperationsPanelProvider::widgets()`; only non-monetary widgets are registered. |
| Latest Shipments widget | OK | `_4LatestShipments` has **no price column**; only company, areas, status. |
| Remaining widgets | OK | `_1ShipmentsOverview`, `_2ShipmentsValuesOverview`, `_5ShipmentsOperationsOverview`, `_8ShipmentsChart`, `AttendanceReports`, `DriverLicenseWarnings` use **counts only**, no money. |

**Note:** The widget classes `_3ShipmentsStatusOverview.php` and `_7ProfitChart.php` still exist on disk but are not registered for the operations panel, so they do not appear on the dashboard.

---

## 2. Shipments (operations)

| Item | Status | Notes |
|------|--------|------|
| Shipment create form – price | OK | No `price` field; price is set server-side from `Destination` in `CreateShipment::mutateFormDataBeforeCreate()`. |
| Shipment create – status | OK | Status forced to **Pending** in `mutateFormDataBeforeCreate()`; operations cannot create in any other status. |
| Shipment create – driver | OK | `driver_id` is **not** cleared when Pending; operations can assign a driver on create. |
| Shipment table (ShipmentResource) | OK | **No price column** in the operations table. |
| Company ShipmentRelationManager | OK | No price column; no total_price placeholder; CreateAction still sets price from destination server-side. |
| User ShipmentsRelationManager | OK | No price column; no total_price placeholder; CreateAction sets price from destination server-side. |
| Claim section – amount | OK | **Amount** and **Total Claim Amount** are hidden for Operations in `ClaimInformationComponent` (`auth()->user()->hasRole('Operations')`). Default amount `0` when hidden; `EditShipment` uses `$claim['amount'] ?? 0`. |
| Download Invoice action | OK | **Download Invoice** header action is hidden for Operations (invoice contains monetary data). |

---

## 3. Driver API (shipment visibility)

| Item | Status | Notes |
|------|--------|------|
| List shipments | OK | `ShipmentController::index()` uses `->visibleToDriver()`; Pending and Cancelled are excluded. |
| Show single shipment | OK | Returns 403 if shipment status is Pending or Cancelled. |
| Accept / acknowledge | OK | Returns 403 if status is Pending or Cancelled. |
| updateStatus / uploads | OK | All relevant actions check Pending/Cancelled and return 403 when not allowed. |
| countPending / countActive / countDeliveredToday | OK | All use `->visibleToDriver()`. |
| API response body | OK | `App\Http\Resources\ShipmentResource` (API) does **not** include `price` or any monetary field in the JSON. |

**Model:** `Shipment::scopeVisibleToDriver()` excludes `PENDING` and `CANCELLED`; drivers only see shipments after admin approval (e.g. APPROVED / ASSIGNEDMISSION or later).

---

## 4. Destinations & truck destinations

| Item | Status | Notes |
|------|--------|------|
| `destinations.price` | OK | Column already nullable; no UI field in operations. |
| `trucks_destinations.price` | OK | Migration `2026_02_25_120000_make_truck_destinations_price_nullable.php` makes it nullable. |
| DestinationsRelationManager | OK | No price form field; no price column; CreateAction does not require price (creates with `price = null` when omitted); does not update price on existing rows. |
| TrucksDestinationsRelationManager | OK | Same: no price field/column; create with `price` null when omitted; no price update on existing. |
| Tabs (Available / Pending) | OK | Filter by `whereNotNull('price')` / `whereNull('price')` only; no monetary values displayed. |

---

## 5. Missions (operations)

| Item | Status | Notes |
|------|--------|------|
| MissionResource form | OK | No `price` field. |
| MissionResource table | OK | No price column. |
| CreateMission | OK | `mutateFormDataBeforeCreate()` sets `price` to `0` when missing (DB column non-nullable). |
| DriverResource MissionsRelationManager | OK | No price column. |

---

## 6. EditShipment – server-side only (no UI leak)

- Invoice creation on delivery uses `$record->price` (server-side).
- Mission creation uses `TruckDestination::...->price` (server-side).
- Claim creation uses `$claim['amount'] ?? 0` (Operations do not see/edit amount; default 0).

None of these values are displayed to the operations user in the form.

---

## 7. Pre-production checklist

- [ ] Run migrations: `php artisan migrate` (ensures `trucks_destinations.price` is nullable).  
  - If using `->change()` on columns, ensure `doctrine/dbal` is installed if required by your Laravel version.
- [ ] Log in as a user with **Operations** role and verify:
  - Dashboard: no revenue, “money spent”, or price columns in any widget.
  - Shipments: create (only Pending, driver assignable), list and edit without seeing price or claim amount; no “Download Invoice” button.
  - Destinations / Truck destinations: create and list without any price field or column.
  - Missions: list and create without price field or column.
- [ ] As **Super Admin** or **Finance**, confirm pricing and invoice/claim amounts still work in their panels.
- [ ] Driver app: confirm Pending shipments do not appear in the list; after admin approval, shipment appears and driver can accept and complete flow.

---

## 8. Summary

- **Operations panel:** No monetary stats on the dashboard; no price/amount in shipment, destination, truck destination, or mission forms/tables; claim amount and Download Invoice hidden for Operations.
- **Driver API:** Pending (and Cancelled) shipments are excluded from list/detail and actions; API resource does not expose price.
- **Database:** Destination and truck destination prices remain or are made nullable so operations can create routes without setting price; pricing is managed by Finance/Super Admin.

**Linter:** Warnings about `auth()->user()` in `ClaimInformationComponent` and `EditShipment` are static-analysis false positives; `auth()->user()` is valid at runtime in Laravel.
