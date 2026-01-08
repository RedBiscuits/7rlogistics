# Software Requirements Specification (SRS)
## 7R Logistics - Driver Mobile Application

**Version:** 1.0  
**Date:** December 19, 2025  
**Status:** Draft  
**Prepared For:** 7R Logistics

---

## Table of Contents

1. [Introduction](#1-introduction)
2. [Overall Description](#2-overall-description)
3. [System Features](#3-system-features)
4. [Functional Requirements](#4-functional-requirements)
5. [Non-Functional Requirements](#5-non-functional-requirements)
6. [User Interface Requirements](#6-user-interface-requirements)
7. [Data Requirements](#7-data-requirements)
8. [System Architecture](#8-system-architecture)
9. [Constraints](#9-constraints)
10. [Appendices](#10-appendices)

---

## 1. Introduction

### 1.1 Purpose
This document specifies the requirements for a mobile application that enables drivers to manage their assigned shipments, update delivery statuses, and maintain their profile information. The app integrates with the existing 7R Logistics Laravel backend system.

### 1.2 Scope
The driver mobile application will:
- Receive notifications when shipments are assigned
- Allow drivers to acknowledge and accept shipments
- Enable status updates throughout the shipment lifecycle
- Require proof uploads at specific stages
- Display driver profile with license information
- Enforce license expiry restrictions

### 1.3 Definitions, Acronyms, and Abbreviations
- **AWB**: Air Waybill Number
- **POD**: Proof of Delivery
- **FCM**: Firebase Cloud Messaging
- **API**: Application Programming Interface
- **SRS**: Software Requirements Specification

### 1.4 References
- 7R Logistics Business Logic Documentation
- Laravel 11 Framework Documentation
- Flutter Framework Documentation
- Firebase Cloud Messaging Documentation

---

## 2. Overall Description

### 2.1 Product Perspective
The driver mobile app is a new component of the 7R Logistics system, connecting drivers to the existing Laravel backend through RESTful APIs.

### 2.2 Product Functions
1. **Notification Management**: Receive and handle push notifications
2. **Shipment Management**: View, acknowledge, and update shipment statuses
3. **Proof Upload**: Capture and upload delivery proofs (images)
4. **Profile Management**: View driver profile and license information
5. **License Validation**: Enforce license expiry restrictions

### 2.3 User Classes and Characteristics
- **Primary User**: Drivers (fleet operators)
- **Characteristics**: 
  - Mobile device users (Android)
  - May have limited technical knowledge
  - Require Arabic language support
  - Need offline capability

### 2.4 Operating Environment
- **Mobile Platform**: Android 8.0+ (API level 26+)
- **Backend**: Laravel 11 on PHP 8.2+
- **Database**: MySQL
- **Push Notifications**: Firebase Cloud Messaging

### 2.5 Design and Implementation Constraints
- Must integrate with existing Laravel backend
- Must use existing database schema (with extensions)
- Must support Arabic (RTL) and English languages
- Must work offline with sync capability
- Must comply with data privacy regulations

---

## 3. System Features

### 3.1 Notification System
**Priority**: High  
**Description**: System sends push notifications to drivers when shipments are assigned. Tapping notification opens the related shipment details.

**Requirements**:
- F3.1.1: System shall send push notification when shipment status changes to ASSIGNED_MISSION
- F3.1.2: Notification shall contain shipment ID and brief details
- F3.1.3: Tapping notification shall open shipment details screen
- F3.1.4: Notification shall be stored in app notification history

### 3.2 Shipment Acknowledgment
**Priority**: High  
**Description**: Driver can confirm they have viewed and accepted the shipment assignment.

**Requirements**:
- F3.2.1: Driver shall be able to acknowledge shipment after viewing details
- F3.2.2: Acknowledgment timestamp shall be recorded
- F3.2.3: Admin panel shall display acknowledgment status
- F3.2.4: Acknowledgment changes status to DRIVER_ACKNOWLEDGED

### 3.3 Status Update Workflow
**Priority**: High  
**Description**: Driver can update shipment status through defined workflow stages.

**Status Flow**:
```
ASSIGNED_MISSION (2)
    ↓ (Driver acknowledges)
DRIVER_ACKNOWLEDGED (7)
    ↓ (Driver starts journey)
DRIVER_ON_WAY_TO_COLLECT (8) - في الطريق الي التحميل
    ↓ (Driver arrives)
AWAITING_COLLECTION (9) - في انتظار التحميل
    ↓ (Driver receives cargo)
DRIVER_RECEIVED (3)
    ↓ (Driver uploads proof + starts delivery)
ON_ITS_WAY (4)
    ↓ (Driver delivers + uploads proofs)
DELIVERED (5)
```

**Requirements**:
- F3.3.1: Driver can only update statuses for assigned shipments
- F3.3.2: Status transitions must follow defined workflow
- F3.3.3: ON_ITS_WAY status requires proof upload before activation
- F3.3.4: DELIVERED status requires minimum 2 images:
  - Image of shipment itself
  - Image of toll receipts
  - Proof of delivery (POD)
- F3.3.5: System shall validate status transitions
- F3.3.6: Invalid transitions shall be rejected with error message

### 3.4 Shipment Details View
**Priority**: High  
**Description**: Display comprehensive shipment information with location and contact details.

**Required Information**:
- From location (area, city)
- To location (area, city)
- Pickup location (address, GPS coordinates)
- Delivery address
- Contact person for pickup (name, phone)
- Receiver contact (name, phone, address)
- Google Maps links for from and to locations
- Package details
- Shipment date/time

**Requirements**:
- F3.4.1: Shipment details screen shall display all required information
- F3.4.2: Google Maps links shall open in external app or in-app browser
- F3.4.3: Contact numbers shall be clickable (call/WhatsApp)
- F3.4.4: GPS coordinates shall be displayed and clickable

### 3.5 Home Screen Dashboard
**Priority**: High  
**Description**: Three-section dashboard showing different shipment states.

**Sections**:
1. **Upcoming Shipments**: Shipments assigned but not yet acknowledged
   - Display: Shipment ID, date, from/to locations
   - Action: Tap to view details and acknowledge
   
2. **Current Shipments**: Shipments acknowledged and in progress
   - Display: Shipment ID, current status, progress indicator
   - Action: Tap to view details and update status
   
3. **Completed Shipments**: Delivered shipments
   - Display: Shipment ID, delivery date, status
   - Action: Tap to view details and proofs

**Requirements**:
- F3.5.1: Home screen shall display three distinct sections
- F3.5.2: Each section shall show relevant shipment count
- F3.5.3: Shipments shall be sorted by date (upcoming first)
- F3.5.4: Pull-to-refresh shall update shipment lists
- F3.5.5: Empty states shall display helpful messages

### 3.6 Profile Management
**Priority**: Medium  
**Description**: Driver profile with personal information and license details.

**Profile Information**:
- Personal image (photo)
- Driver name
- Driver code
- License number
- License expiry date
- License photos (front and back)
- ID number
- ID photo
- Contact numbers

**Requirements**:
- F3.6.1: Profile screen shall display all driver information
- F3.6.2: License expiry date shall be prominently displayed
- F3.6.3: License photos shall be viewable (front and back)
- F3.6.4: License expiry date shall be read-only (no edits)
- F3.6.5: System shall send notification when license expires soon
- F3.6.6: Profile data shall be synced from backend

### 3.7 License Expiry Validation
**Priority**: High  
**Description**: System enforces restrictions based on license expiry dates.

**Validation Rules**:
1. **Warning Threshold (30 days)**: 
   - Admin panel shows warning when assigning driver
   - Message: "Driver license expires in less than 1 month"
   
2. **Blocking Threshold (10 days)**:
   - Driver cannot perform any shipment actions
   - Display message: "License expires in 10 days. Please contact management for data update"
   - All shipment-related buttons disabled

**Requirements**:
- F3.7.1: System shall check license expiry on every action
- F3.7.2: Warning shall appear in admin panel at 30 days
- F3.7.3: Driver app shall block actions at 10 days
- F3.7.4: Blocking message shall be clear and actionable
- F3.7.5: License expiry notifications shall be sent

### 3.8 Image Upload System
**Priority**: High  
**Description**: Capture and upload images for delivery proofs.

**Image Requirements**:
- Minimum 2 images required for DELIVERED status
- Image types:
  1. Shipment photo (cargo itself)
  2. Toll receipts photo
  3. Proof of delivery (POD) - optional but recommended
- Images must be clear and valid
- Maximum file size: 5MB per image
- Supported formats: JPEG, PNG

**Requirements**:
- F3.8.1: Camera interface shall allow image capture
- F3.8.2: Gallery selection shall be available
- F3.8.3: Image preview before upload
- F3.8.4: Minimum 2 images validation
- F3.8.5: Upload progress indicator
- F3.8.6: Retry mechanism for failed uploads
- F3.8.7: Images stored with shipment association

---

## 4. Functional Requirements

### 4.1 Authentication
- **FR-1**: System shall authenticate drivers using driver code and password
- **FR-2**: System shall maintain session using secure tokens
- **FR-3**: System shall support logout functionality
- **FR-4**: System shall handle token refresh automatically

### 4.2 Notification Handling
- **FR-5**: System shall register device for push notifications
- **FR-6**: System shall handle notification tap events
- **FR-7**: System shall navigate to relevant screen on notification tap
- **FR-8**: System shall mark notifications as read
- **FR-9**: System shall display notification history

### 4.3 Shipment Operations
- **FR-10**: System shall fetch driver's assigned shipments
- **FR-11**: System shall display shipment details
- **FR-12**: System shall allow driver to acknowledge shipment
- **FR-13**: System shall allow status updates following workflow
- **FR-14**: System shall validate status transitions
- **FR-15**: System shall require proof uploads when specified
- **FR-16**: System shall display shipment history

### 4.4 Image Management
- **FR-17**: System shall capture images using device camera
- **FR-18**: System shall select images from gallery
- **FR-19**: System shall compress images before upload
- **FR-20**: System shall upload images to backend
- **FR-21**: System shall associate images with shipment
- **FR-22**: System shall display uploaded images

### 4.5 Profile Management
- **FR-23**: System shall display driver profile
- **FR-24**: System shall display license information
- **FR-25**: System shall check license expiry on app launch
- **FR-26**: System shall enforce license expiry restrictions
- **FR-27**: System shall display license expiry warnings

### 4.6 Offline Support
- **FR-28**: System shall cache shipment data locally
- **FR-29**: System shall queue status updates when offline
- **FR-30**: System shall sync data when connection restored
- **FR-31**: System shall indicate offline/online status

---

## 5. Non-Functional Requirements

### 5.1 Performance
- **NFR-1**: App shall launch within 3 seconds
- **NFR-2**: API responses shall be received within 2 seconds
- **NFR-3**: Images shall upload within 10 seconds (on 4G)
- **NFR-4**: App shall handle 100+ shipments without performance degradation

### 5.2 Reliability
- **NFR-5**: App shall have crash rate < 1%
- **NFR-6**: Data loss shall not occur during app crashes
- **NFR-7**: Offline queue shall persist across app restarts

### 5.3 Usability
- **NFR-8**: App shall support Arabic (RTL) and English
- **NFR-9**: UI shall be intuitive for non-technical users
- **NFR-10**: Error messages shall be clear and actionable
- **NFR-11**: App shall provide loading indicators

### 5.4 Security
- **NFR-12**: All API communications shall use HTTPS
- **NFR-13**: Authentication tokens shall be stored securely
- **NFR-14**: Images shall be validated before upload
- **NFR-15**: Driver can only access their own shipments

### 5.5 Compatibility
- **NFR-16**: App shall support Android 8.0+ (API 26+)
- **NFR-17**: App shall work on screen sizes 4.5" to 7"
- **NFR-18**: App shall support portrait and landscape orientations

---

## 6. User Interface Requirements

### 6.1 Screen Layouts

#### 6.1.1 Home Screen
- **Layout**: Three sections (Upcoming, Current, Completed)
- **Navigation**: Bottom navigation bar
- **Actions**: Pull-to-refresh, tap to view details

#### 6.1.2 Shipment Details Screen
- **Layout**: Scrollable details with sections
- **Sections**: 
  - Header (Shipment ID, Status)
  - Route Information (From/To with maps)
  - Contact Information
  - Package Details
  - Actions (Status update buttons)
- **Actions**: Acknowledge, Update Status, Upload Proofs

#### 6.1.3 Profile Screen
- **Layout**: Personal info at top, license section below
- **Sections**:
  - Profile Photo
  - Personal Information
  - License Information (read-only)
  - Documents Gallery
- **Actions**: View documents, contact management

### 6.2 Visual Design
- **Color Scheme**: Consistent with brand colors
- **Typography**: Clear, readable fonts (Arabic and English)
- **Icons**: Material Design icons
- **Images**: High-quality, properly sized

### 6.3 Interaction Design
- **Gestures**: Swipe, pull-to-refresh, tap
- **Feedback**: Loading spinners, success/error messages
- **Navigation**: Bottom navigation, back button support

---

## 7. Data Requirements

### 7.1 Database Schema Changes

#### 7.1.1 Shipments Table Additions
```sql
ALTER TABLE shipments ADD COLUMN driver_acknowledged_at TIMESTAMP NULL;
ALTER TABLE shipments ADD COLUMN driver_on_way_to_collect_at TIMESTAMP NULL;
ALTER TABLE shipments ADD COLUMN awaiting_collection_at TIMESTAMP NULL;
```

#### 7.1.2 New Table: shipment_delivery_proofs
```sql
CREATE TABLE shipment_delivery_proofs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    shipment_id BIGINT UNSIGNED NOT NULL,
    proof_type ENUM('shipment_photo', 'toll_receipt', 'pod') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shipment_id) REFERENCES shipments(id) ON DELETE CASCADE
);
```

#### 7.1.3 New Table: driver_notifications
```sql
CREATE TABLE driver_notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    driver_id BIGINT UNSIGNED NOT NULL,
    shipment_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    type ENUM('shipment_assigned', 'license_expiry', 'general') NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE CASCADE,
    FOREIGN KEY (shipment_id) REFERENCES shipments(id) ON DELETE SET NULL
);
```

#### 7.1.4 New Table: driver_devices
```sql
CREATE TABLE driver_devices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    driver_id BIGINT UNSIGNED NOT NULL,
    device_token VARCHAR(255) NOT NULL UNIQUE,
    device_type ENUM('android', 'ios') NOT NULL,
    app_version VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    last_used_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE CASCADE
);
```

### 7.2 Data Validation Rules
- License expiry date must be valid date
- Status transitions must follow defined workflow
- Minimum 2 images required for DELIVERED status
- Driver can only update their own shipments

### 7.3 Data Synchronization
- Shipment data synced on app launch
- Status updates synced immediately
- Images uploaded with retry mechanism
- Offline changes queued for sync

---

## 8. System Architecture

### 8.1 Architecture Overview
```
[Driver Mobile App (Flutter)]
         ↕ HTTPS
[Laravel REST API]
         ↕
[MySQL Database]
         ↕
[Firebase Cloud Messaging]
```

### 8.2 Component Diagram
- **Mobile App**: Flutter application
- **API Layer**: Laravel controllers and services
- **Business Logic**: Status validation, license checking
- **Data Layer**: MySQL database
- **Notification Service**: Firebase FCM

### 8.3 API Endpoints (Summary)
- `POST /api/v1/auth/login`
- `GET /api/v1/shipments` (driver's shipments)
- `GET /api/v1/shipments/{id}`
- `POST /api/v1/shipments/{id}/acknowledge`
- `POST /api/v1/shipments/{id}/status`
- `POST /api/v1/shipments/{id}/proofs`
- `GET /api/v1/driver/profile`
- `GET /api/v1/notifications`

---

## 9. Constraints

### 9.1 Technical Constraints
- Must use existing Laravel backend
- Must maintain backward compatibility
- Must support Android 8.0+
- Limited to RESTful API (no WebSocket initially)

### 9.2 Business Constraints
- License expiry rules must be strictly enforced
- Status workflow cannot be bypassed
- Proof uploads are mandatory for certain statuses

### 9.3 Regulatory Constraints
- Must comply with data privacy laws
- Must secure personal information
- Must handle driver data responsibly

---

## 10. Appendices

### 10.1 Status Enum Values
```
PENDING = 0
CANCELLED = 1
ASSIGNED_MISSION = 2
DRIVER_RECEIVED = 3
ON_ITS_WAY = 4
DELIVERED = 5
APPROVED = 6
DRIVER_ACKNOWLEDGED = 7
DRIVER_ON_WAY_TO_COLLECT = 8
AWAITING_COLLECTION = 9
```

### 10.2 Error Codes
- `E001`: Invalid status transition
- `E002`: License expired or expiring soon
- `E003`: Insufficient proof images
- `E004`: Unauthorized access
- `E005`: Network error

### 10.3 Glossary
- **Acknowledgment**: Driver confirms viewing shipment
- **Proof**: Image evidence of delivery stage
- **POD**: Proof of Delivery document/image

---

**Document End**












