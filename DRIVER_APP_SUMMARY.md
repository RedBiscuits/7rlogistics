# Driver Mobile App - Quick Reference Summary

**Date:** December 19, 2025  
**Status:** Requirements & Implementation Guide

---

## 📋 **What Was Created**

1. **SRS Document** (`DRIVER_APP_SRS.md`) - Complete Software Requirements Specification
2. **Implementation Guide** (`DRIVER_APP_IMPLEMENTATION.md`) - Detailed technical implementation
3. **Updated ShipmentStatus Enum** - Added 3 new statuses

---

## 🎯 **Core Requirements Summary**

### **1. Attendance Module** ⭐ NEW
- ✅ Check in/check out functionality
- ✅ GPS coordinates sent to backend
- ✅ Geofencing validation (500m radius from allowed locations)
- ✅ Attendance history with hours calculation
- ✅ Daily attendance records (check-in time, check-out time, hours worked)
- ✅ Monthly summaries
- ✅ Location-based validation
- ✅ Admin panel: Location management (name, lat, lng, radius)

### **2. Enhanced Authentication** ⭐ NEW
- ✅ Login by string (driver code/username) - not email
- ✅ Biometric authentication (fingerprint/face recognition)
- ✅ Secure token-based authentication
- ✅ Session management

### **3. Homepage Redesign** ⭐ NEW
- ✅ Modern design structure
- ✅ Color palette: White (primary), Red (secondary), Neon Yellow (accent)
- ✅ Attendance action button (Check In/Check Out)
- ✅ Quick access cards
- ✅ Statistics and summaries

### **4. Notification System**
- ✅ Push notification sent when shipment assigned to driver
- ✅ Tapping notification opens shipment details
- ✅ Notification stored in database

### **5. Shipment Acknowledgment**
- ✅ Driver can acknowledge viewing shipment
- ✅ Admin can see acknowledgment status
- ✅ Status changes to `DRIVER_ACKNOWLEDGED` (7)

### **3. Status Workflow**
```
ASSIGNED_MISSION (2)
    ↓ (Driver acknowledges)
DRIVER_ACKNOWLEDGED (7)
    ↓
DRIVER_ON_WAY_TO_COLLECT (8) - في الطريق الي التحميل
    ↓
AWAITING_COLLECTION (9) - في انتظار التحميل
    ↓
DRIVER_RECEIVED (3)
    ↓ (Requires proof upload)
ON_ITS_WAY (4)
    ↓ (Requires minimum 2 images)
DELIVERED (5)
```

### **4. Shipment Details View**
**Required Information:**
- From location (area, city) + Google Maps link
- To location (area, city) + Google Maps link
- Pickup location (address, GPS)
- Delivery address
- Contact person for pickup (name, phone)
- Receiver contact (name, phone, address)
- Package details
- Shipment date/time

### **5. Home Screen** (Redesigned)
**New Design Features:**
- Modern layout with hero section
- Attendance action button (Check In/Check Out)
- Quick action cards
- Statistics section
- Color scheme: White, Red, Neon Yellow

**Shipment Sections:**
1. **Upcoming Shipments** - Assigned but not acknowledged
2. **Current Shipments** - Acknowledged and in progress
3. **Completed Shipments** - Delivered shipments

### **6. Profile Section**
- Personal image
- License number
- License expiry date (read-only)
- License photos (front and back)
- ID number
- Notifications for expiry

### **7. License Validation**
- **30 days warning**: Admin sees warning when assigning driver
- **10 days blocking**: Driver cannot perform any actions
- Message: "License expires in X days. Please contact management for data update"

### **8. Image Upload Requirements**
- **ON_ITS_WAY**: Requires proof of receiving (uploaded before status change)
- **DELIVERED**: Requires **minimum 2 images**:
  1. Shipment photo (cargo itself)
  2. Toll receipts photo
  3. Proof of delivery (optional but recommended)

---

## 📊 **Database Changes Required**

### **New Tables:**
1. `shipment_delivery_proofs` - Store delivery proof images
2. `driver_notifications` - Store notification history
3. `driver_devices` - Store FCM device tokens
4. `locations` - Store allowed check-in locations (name, lat, lng, radius) ⭐ NEW
5. `driver_attendances` - Store attendance records (check-in, check-out, hours) ⭐ NEW

### **Shipments Table Additions:**
- `driver_acknowledged_at` (timestamp)
- `driver_on_way_to_collect_at` (timestamp)
- `awaiting_collection_at` (timestamp)

### **Status Enum Updates:**
- Added: `DRIVER_ACKNOWLEDGED = 7`
- Added: `DRIVER_ON_WAY_TO_COLLECT = 8`
- Added: `AWAITING_COLLECTION = 9`

---

## 🔧 **Implementation Checklist**

### **Backend (Laravel)**
- [ ] Create database migrations
- [ ] Update ShipmentStatus enum ✅ (Done)
- [ ] Create new models (ShipmentDeliveryProof, DriverNotification, DriverDevice)
- [ ] Create attendance models (Location, DriverAttendance) ⭐ NEW
- [ ] Create API controllers
- [ ] Implement status transition validation
- [ ] Implement license validation service
- [ ] Implement attendance system with geofencing ⭐ NEW
- [ ] Create geofencing service (500m radius validation) ⭐ NEW
- [ ] Create notification service
- [ ] Add shipment observer for notifications
- [ ] Update authentication (string-based login) ⭐ NEW
- [ ] Create API routes
- [ ] Add license warning to admin panel
- [ ] Create Location management resource in admin panel ⭐ NEW

### **Mobile App (Flutter)**
- [ ] Setup Flutter project
- [ ] Implement authentication (string-based + biometric) ⭐ NEW
- [ ] Create redesigned home screen ⭐ NEW
- [ ] Implement attendance check-in/out ⭐ NEW
- [ ] Create attendance history screen ⭐ NEW
- [ ] Add location services integration ⭐ NEW
- [ ] Create home screen with 3 sections
- [ ] Create shipment details screen
- [ ] Implement status update screen
- [ ] Add image upload functionality
- [ ] Implement license validation checks
- [ ] Add notification handling
- [ ] Create profile screen
- [ ] Add Google Maps integration
- [ ] Implement offline support

### **Testing**
- [ ] Unit tests for status transitions
- [ ] Unit tests for license validation
- [ ] Integration tests for API endpoints
- [ ] Manual testing checklist

---

## 📱 **Key API Endpoints**

### **Authentication**
```
POST   /api/v1/auth/login          (string-based, supports biometric)
POST   /api/v1/auth/logout
GET    /api/v1/auth/profile
```

### **Shipments**
```
GET    /api/v1/shipments?status=upcoming|current|completed
GET    /api/v1/shipments/{id}
POST   /api/v1/shipments/{id}/acknowledge
POST   /api/v1/shipments/{id}/status
POST   /api/v1/shipments/{id}/proofs
```

### **Attendance** ⭐ NEW
```
POST   /api/v1/attendance/check-in
POST   /api/v1/attendance/check-out
GET    /api/v1/attendance/history
GET    /api/v1/attendance/status
GET    /api/v1/attendance/today
```

### **Profile & Notifications**
```
GET    /api/v1/driver/profile
GET    /api/v1/driver/license-status
GET    /api/v1/notifications
POST   /api/v1/notifications/{id}/read
```

---

## ⚠️ **Business Rules**

### **Status Transition Rules:**
1. Driver can only update statuses for assigned shipments
2. Status transitions must follow defined workflow
3. ON_ITS_WAY requires proof upload before activation
4. DELIVERED requires minimum 2 images

### **License Rules:**
1. Warning shown at 30 days before expiry
2. Actions blocked at 10 days before expiry
3. License expiry date is read-only in app
4. Driver must contact management to update

### **Image Rules:**
1. Minimum 2 images for DELIVERED status
2. Images must be JPEG or PNG
3. Maximum 5MB per image
4. Images stored with shipment association

---

## 📄 **Documentation Files**

1. **DRIVER_APP_SRS.md** - Complete Software Requirements Specification
   - Functional requirements
   - Non-functional requirements
   - UI requirements
   - Data requirements
   - System architecture

2. **DRIVER_APP_IMPLEMENTATION.md** - Technical Implementation Guide
   - Database migrations
   - Backend code examples
   - Mobile app structure
   - API specifications
   - Testing requirements

3. **DRIVER_APP_SUMMARY.md** - This file (Quick reference)

---

## 🚀 **Next Steps**

1. **Review Documents** - Read SRS and Implementation guide
2. **Approve Requirements** - Confirm all features are correct
3. **Create Database Migrations** - Run migrations for new tables
4. **Start Backend Development** - Implement API endpoints
5. **Start Mobile Development** - Build Flutter app
6. **Integration Testing** - Test end-to-end workflows
7. **Deployment** - Deploy to production

---

## 📞 **Questions to Clarify**

Before starting implementation, confirm:

1. **Pickup Contact Person**: Where is this stored? Is it the `user_id` who created the shipment?
2. **Pickup Location**: Is this the `from_area` address or a separate field?
3. **Google Maps Links**: Should they open in external app or in-app browser?
4. **Notification Frequency**: Only on assignment, or also on status changes?
5. **License Photos**: Are these already stored via Spatie Media Library?

---

---

## 💰 **Cost & Timeline**

### **Base Functionality**
- **Cost:** 17,500 EGP
- **Timeline:** 2-4 weeks (average 3 weeks)

### **New Features Added**
- **Cost:** 13,000 EGP
- **Timeline:** 6.25 weeks

### **Total Project**
- **Cost:** 30,500 EGP
- **Timeline:** ~10 weeks (2.5 months)

**See `DRIVER_APP_FINAL_ESTIMATION.md` for detailed breakdown.**

---

**Document Version:** 2.0  
**Last Updated:** December 19, 2025





LEAVE THIS HERE

ok that's some progress but the boxes now should be instead of dark to be a lil off white on the white background, remove the borders colors from all boxes and denst the fonts a bit as they are a bit inbisible. 
the boxes heights should be as was in the image one large like 60% on top right then below a smaller one, on the other hand a small one with like 40% height and the other below it a 60% height
the coloring is kinda acceptable but lets have a dark mode and have it defaulted 
and lets add an animation to page opening, with data loaders and other animation like the page is water drop splash but in opposite as like they are gathering into the middle 