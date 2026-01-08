# Driver Mobile App - Workflow Diagrams

**Version:** 1.0  
**Date:** December 19, 2025

---

## 1. Shipment Assignment & Acknowledgment Flow

```
┌─────────────────┐
│ Admin Assigns   │
│ Shipment to     │
│ Driver          │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Status:         │
│ ASSIGNED_MISSION│
│ (2)             │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Push Notification│
│ Sent to Driver  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Driver Taps     │
│ Notification    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Shipment Details│
│ Screen Opens    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Driver Views    │
│ Details         │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Driver Clicks   │
│ "Acknowledge"   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Status:         │
│ DRIVER_ACKNOWLEDGED│
│ (7)             │
│                 │
│ Admin sees:     │
│ ✓ Acknowledged│
└─────────────────┘
```

---

## 2. Complete Status Workflow

```
┌──────────────────────┐
│ ASSIGNED_MISSION (2) │
│ Admin assigns driver │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│ DRIVER_ACKNOWLEDGED  │
│ (7)                  │
│ Driver confirms view │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│ DRIVER_ON_WAY_TO_    │
│ COLLECT (8)           │
│ في الطريق الي التحميل │
│ Driver starts journey│
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│ AWAITING_COLLECTION  │
│ (9)                  │
│ في انتظار التحميل    │
│ Driver arrives       │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│ DRIVER_RECEIVED (3)  │
│ Cargo picked up      │
└──────────┬───────────┘
           │
           │ ⚠️ Requires Proof Upload
           ▼
┌──────────────────────┐
│ ON_ITS_WAY (4)       │
│ Delivery in progress │
│ (Only available after│
│  proof uploaded)     │
└──────────┬───────────┘
           │
           │ ⚠️ Requires Min 2 Images
           ▼
┌──────────────────────┐
│ DELIVERED (5)        │
│ Delivery completed   │
│ (Requires:           │
│  - Shipment photo    │
│  - Toll receipts     │
│  - POD optional)     │
└──────────────────────┘
```

---

## 3. License Validation Flow

```
┌─────────────────────┐
│ Driver Action       │
│ Requested           │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ Check License       │
│ Expiry Date         │
└──────────┬──────────┘
           │
    ┌──────┴──────┐
    │               │
    ▼               ▼
┌─────────┐   ┌─────────┐
│ ≤ 10    │   │ > 10    │
│ days?   │   │ days?   │
└────┬────┘   └────┬────┘
     │            │
     │            ▼
     │     ┌──────────────┐
     │     │ ≤ 30 days?   │
     │     └────┬─────────┘
     │          │
     │     ┌────┴────┐
     │     │         │
     │     ▼         ▼
     │ ┌──────┐  ┌──────┐
     │ │ Yes  │  │ No   │
     │ └──┬───┘  └──┬───┘
     │    │         │
     │    ▼         ▼
     │ ┌──────────────┐
     │ │ Show Warning │
     │ │ (Admin only) │
     │ └──────────────┘
     │
     ▼
┌─────────────────────┐
│ BLOCK ACTION        │
│                     │
│ Show Message:       │
│ "License expires    │
│  in X days.         │
│  Contact management │
│  for data update"   │
└─────────────────────┘
```

---

## 4. Image Upload Flow (DELIVERED Status)

```
┌─────────────────────┐
│ Driver Selects     │
│ "Mark as Delivered" │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ System Checks:      │
│ - License valid?    │
│ - Status = ON_ITS_WAY?│
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ Show Image Upload   │
│ Screen              │
│                     │
│ Requirements:       │
│ - Min 2 images      │
│ - Types:           │
│   1. Shipment photo│
│   2. Toll receipts │
│   3. POD (optional) │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ Driver Selects     │
│ Images              │
│ (Camera/Gallery)    │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ Validate Images:    │
│ - Count >= 2?      │
│ - Format valid?    │
│ - Size < 5MB?      │
└──────────┬──────────┘
           │
    ┌──────┴──────┐
    │             │
    ▼             ▼
┌────────┐   ┌────────┐
│ Valid  │   │Invalid │
└───┬────┘   └───┬────┘
    │            │
    │            ▼
    │     ┌──────────────┐
    │     │ Show Error   │
    │     │ "Min 2 images│
    │     │  required"   │
    │     └──────────────┘
    │
    ▼
┌─────────────────────┐
│ Upload Images       │
│ to Server           │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ Update Status to    │
│ DELIVERED (5)       │
│                     │
│ Store Proofs:       │
│ - shipment_photo    │
│ - toll_receipt      │
│ - pod (if provided) │
└─────────────────────┘
```

---

## 5. Home Screen Structure

```
┌─────────────────────────────────────┐
│         HOME SCREEN                 │
├─────────────────────────────────────┤
│                                     │
│  ┌───────────────────────────────┐ │
│  │  UPCOMING SHIPMENTS            │ │
│  │  (Not Acknowledged)            │ │
│  │                                 │ │
│  │  ┌─────────────────────────┐   │ │
│  │  │ Shipment #123           │   │ │
│  │  │ Cairo → Alexandria     │   │ │
│  │  │ Date: 20/12/2025       │   │ │
│  │  │ [View Details]         │   │ │
│  │  └─────────────────────────┘   │ │
│  │                                 │ │
│  │  ┌─────────────────────────┐   │ │
│  │  │ Shipment #124           │   │ │
│  │  │ Giza → Mansoura         │   │ │
│  │  │ Date: 21/12/2025       │   │ │
│  │  │ [View Details]         │   │ │
│  │  └─────────────────────────┘   │ │
│  └───────────────────────────────┘ │
│                                     │
│  ┌───────────────────────────────┐ │
│  │  CURRENT SHIPMENTS             │ │
│  │  (In Progress)                │ │
│  │                                 │ │
│  │  ┌─────────────────────────┐   │ │
│  │  │ Shipment #120           │   │ │
│  │  │ Status: ON_ITS_WAY      │   │ │
│  │  │ Progress: ████████░░   │   │ │
│  │  │ [View Details]         │   │ │
│  │  └─────────────────────────┘   │ │
│  └───────────────────────────────┘ │
│                                     │
│  ┌───────────────────────────────┐ │
│  │  COMPLETED SHIPMENTS           │ │
│  │  (Delivered)                   │ │
│  │                                 │ │
│  │  ┌─────────────────────────┐   │ │
│  │  │ Shipment #115           │   │ │
│  │  │ Delivered: 18/12/2025   │   │ │
│  │  │ [View Details]         │   │ │
│  │  └─────────────────────────┘   │ │
│  └───────────────────────────────┘ │
│                                     │
└─────────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────┐
│    BOTTOM NAVIGATION BAR            │
│  [Home] [Shipments] [Profile]       │
└─────────────────────────────────────┘
```

---

## 6. Shipment Details Screen Layout

```
┌─────────────────────────────────────┐
│  Shipment #123          [Status Badge]│
├─────────────────────────────────────┤
│                                     │
│  ┌───────────────────────────────┐ │
│  │  ROUTE INFORMATION             │ │
│  │                               │ │
│  │  From:                        │ │
│  │  Cairo, Nasr City             │ │
│  │  📍 [Open in Maps]            │ │
│  │                               │ │
│  │  To:                          │ │
│  │  Alexandria, Downtown         │ │
│  │  📍 [Open in Maps]            │ │
│  └───────────────────────────────┘ │
│                                     │
│  ┌───────────────────────────────┐ │
│  │  PICKUP INFORMATION           │ │
│  │                               │ │
│  │  Contact: Ahmed Mohamed       │ │
│  │  Phone: 01234567890           │ │
│  │  📞 [Call]  💬 [WhatsApp]     │ │
│  │                               │ │
│  │  Location:                    │ │
│  │  123 Main St, Nasr City       │ │
│  │  📍 [Open in Maps]            │ │
│  └───────────────────────────────┘ │
│                                     │
│  ┌───────────────────────────────┐ │
│  │  DELIVERY INFORMATION         │ │
│  │                               │ │
│  │  Receiver: Mohamed Ali        │ │
│  │  Phone: 01234567891           │ │
│  │  📞 [Call]  💬 [WhatsApp]     │ │
│  │                               │ │
│  │  Address:                    │ │
│  │  456 Corniche St, Downtown    │ │
│  │  📍 [Open in Maps]            │ │
│  └───────────────────────────────┘ │
│                                     │
│  ┌───────────────────────────────┐ │
│  │  PACKAGE DETAILS              │ │
│  │                               │ │
│  │  • 5 Boxes, 200kg            │ │
│  │  • Dimensions: 100x50x30cm   │ │
│  └───────────────────────────────┘ │
│                                     │
│  ┌───────────────────────────────┐ │
│  │  ACTIONS                      │ │
│  │                               │ │
│  │  [Acknowledge] (if not done)  │ │
│  │  [Update Status]              │ │
│  │  [Upload Proofs]              │ │
│  └───────────────────────────────┘ │
│                                     │
└─────────────────────────────────────┘
```

---

## 7. Profile Screen Layout

```
┌─────────────────────────────────────┐
│         PROFILE                     │
├─────────────────────────────────────┤
│                                     │
│  ┌───────────────────────────────┐ │
│  │    [Driver Photo]             │ │
│  │                               │ │
│  │    Ahmed Mohamed              │ │
│  │    Code: DRV-001              │ │
│  └───────────────────────────────┘ │
│                                     │
│  ┌───────────────────────────────┐ │
│  │  PERSONAL INFORMATION         │ │
│  │                               │ │
│  │  ID Number: 12345678901234   │ │
│  │  Phone: 01234567890          │ │
│  └───────────────────────────────┘ │
│                                     │
│  ┌───────────────────────────────┐ │
│  │  LICENSE INFORMATION          │ │
│  │  (Read-Only)                  │ │
│  │                               │ │
│  │  License #: ABC123456        │ │
│  │  Expiry Date: 15/01/2026     │ │
│  │  Days Remaining: 27          │ │
│  │                               │ │
│  │  ┌─────────────────────────┐  │ │
│  │  │ [View License Front]   │  │ │
│  │  │ [View License Back]    │  │ │
│  │  └─────────────────────────┘  │ │
│  │                               │ │
│  │  ⚠️ License expires in 27 days│ │
│  └───────────────────────────────┘ │
│                                     │
│  ┌───────────────────────────────┐ │
│  │  DOCUMENTS                    │ │
│  │                               │ │
│  │  • ID Photo                  │ │
│  │  • License Front             │ │
│  │  • License Back              │ │
│  └───────────────────────────────┘ │
│                                     │
└─────────────────────────────────────┘
```

---

## 8. Notification Flow

```
┌─────────────────────┐
│ Admin Assigns       │
│ Shipment            │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ System Creates      │
│ Notification Record │
│ in Database         │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ Get Driver's        │
│ Active Devices      │
│ (FCM Tokens)        │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ Send Push            │
│ Notification via FCM │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ Notification        │
│ Appears on Device   │
│                     │
│ Title: "New         │
│  Shipment Assigned" │
│ Body: "Shipment #123│
│  - Cairo to Alex"   │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ Driver Taps         │
│ Notification        │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ App Opens           │
│ Shipment Details    │
│ Screen              │
│                     │
│ Mark Notification   │
│ as Read             │
└─────────────────────┘
```

---

## 9. Status Update Validation Flow

```
┌─────────────────────┐
│ Driver Requests     │
│ Status Update       │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ Check 1:            │
│ License Valid?      │
│ (> 10 days)        │
└──────────┬──────────┘
           │
    ┌──────┴──────┐
    │             │
    ▼             ▼
┌────────┐   ┌────────┐
│ Valid  │   │Blocked │
└───┬────┘   └───┬────┘
    │            │
    │            ▼
    │     ┌──────────────┐
    │     │ Return Error │
    │     │ "License     │
    │     │  expires in  │
    │     │  X days"     │
    │     └──────────────┘
    │
    ▼
┌─────────────────────┐
│ Check 2:            │
│ Valid Transition?   │
│ (Workflow check)    │
└──────────┬──────────┘
           │
    ┌──────┴──────┐
    │             │
    ▼             ▼
┌────────┐   ┌────────┐
│ Valid  │   │Invalid │
└───┬────┘   └───┬────┘
    │            │
    │            ▼
    │     ┌──────────────┐
    │     │ Return Error │
    │     │ "Invalid     │
    │     │  transition" │
    │     └──────────────┘
    │
    ▼
┌─────────────────────┐
│ Check 3:            │
│ Proof Required?     │
│ (ON_ITS_WAY or      │
│  DELIVERED)         │
└──────────┬──────────┘
           │
    ┌──────┴──────┐
    │             │
    ▼             ▼
┌────────┐   ┌────────┐
│ Yes    │   │ No     │
└───┬────┘   └───┬────┘
    │            │
    │            ▼
    │     ┌──────────────┐
    │     │ Update Status│
    │     │ Successfully │
    │     └──────────────┘
    │
    ▼
┌─────────────────────┐
│ Check 4:            │
│ Proofs Uploaded?    │
│ (Min 2 for DELIVERED)│
└──────────┬──────────┘
           │
    ┌──────┴──────┐
    │             │
    ▼             ▼
┌────────┐   ┌────────┐
│ Yes    │   │ No     │
└───┬────┘   └───┬────┘
    │            │
    │            ▼
    │     ┌──────────────┐
    │     │ Return Error │
    │     │ "Min 2       │
    │     │  images      │
    │     │  required"   │
    │     └──────────────┘
    │
    ▼
┌─────────────────────┐
│ Update Status       │
│ Successfully        │
│                     │
│ Store Timestamps    │
│ Store Proofs        │
└─────────────────────┘
```

---

## 10. Admin Panel Warning Flow

```
┌─────────────────────┐
│ Admin Opens         │
│ Shipment Edit Form  │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ Admin Selects      │
│ Driver from Dropdown│
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ System Checks       │
│ Driver License      │
│ Expiry Date         │
└──────────┬──────────┘
           │
    ┌──────┴──────┐
    │             │
    ▼             ▼
┌────────┐   ┌────────┐
│ ≤ 30   │   │ > 30   │
│ days?  │   │ days?  │
└───┬────┘   └───┬────┘
    │            │
    │            ▼
    │     ┌──────────────┐
    │     │ No Warning  │
    │     │ Continue    │
    │     └──────────────┘
    │
    ▼
┌─────────────────────┐
│ Display Warning     │
│ Notification:       │
│                     │
│ ⚠️ "Driver license  │
│  expires in less    │
│  than 1 month"      │
│                     │
│ [Dismiss] [Continue]│
└─────────────────────┘
```

---

**End of Workflow Diagrams**












