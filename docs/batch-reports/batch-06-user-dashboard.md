# Batch 6: User Dashboard (Premium & Secure Candidate Experience)

## 📋 Overview
Batch 6 focuses on building a premium, secure, and intuitive dashboard for candidates (users) on the InternHub platform. This batch transitions candidates from public visitors to active applicants capable of managing their professional profiles, tracking applications in real-time, and securely storing their sensitive documents (CVs and Portfolios).

## 🚀 Features Implemented

1. **Premium User Dashboard (`Dashboard.vue`)**
   - Implemented a modern, responsive "Gen Z" aesthetic layout with glassmorphism, soft gradients, and interactive components.
   - Dynamic profile completion tracking card.
2. **Profile Completion (`Profile/Edit.vue` & `UserDetail` Model)**
   - Form handling for comprehensive candidate data: Biodata, Phone Number, Address.
   - Dynamic array inputs for Education (School, Degree, Field, Years) and Skills.
3. **Secure File Uploads (`FileUpload.vue`)**
   - Reusable Vue component for handling file uploads with drag-and-drop support.
   - Implementation for uploading **CVs** and **Portfolios**.
4. **Internship Applications (`ApplicationController`)**
   - Core `apply` functionality tying a `User` to an `Internship`.
   - Point-in-time snapshots for CV and Portfolio paths at the time of application.
5. **Application Tracking & Timeline (`Applications/Index.vue`, `Applications/Show.vue`)**
   - Visual tracking of application status (Pending, Withdrawn, etc.).
   - Interactive application timeline displaying the history of status changes.
6. **Saved Internships (`SavedInternshipsController`)**
   - Toggle functionality to save/bookmark internships for later review.
7. **UI Enhancements (`StatusBadge.vue`)**
   - Standardized status badges with semantic colors for application states.

## 🔒 Security & Data Integrity

1. **Data Isolation (Tenancy)**
   - **`UserDetailPolicy`**: Strictly enforces that candidates can only view and update their own profile details.
   - **`ApplicationPolicy`**: Ensures candidates only see their own application tracking data, while allowing HR to view applications destined for their specific company.
2. **Private File Access (`FileController`)**
   - Candidate CVs and Portfolios are stored in the `private/` storage disk.
   - Access is restricted via **Temporary Signed URLs** (valid for 30 minutes) using `\Illuminate\Support\Facades\URL::temporarySignedRoute`.
   - Before serving the file, ownership is strictly re-validated against the authenticated user.
3. **Upload Validation (`ProfileUpdateRequest`)**
   - **MIME & Extension Rules**: Restricts CVs to PDF, and Portfolios to PDF, ZIP, or RAR.
   - **Size Limits**: Caps CVs at 2MB and Portfolios at 5MB to prevent storage exhaustion.
4. **Database Transactions**
   - The `apply` and `withdraw` actions in `ApplicationController` use `DB::transaction()` to ensure that record creation, timeline updates, and logging occur atomically.
5. **Audit & Access Logs**
   - Integration with `AuditService` to log critical events:
     - `profile_updated`: Captures old vs. new data changes.
     - `application_submitted` & `application_withdrawn`.
     - `document_accessed`: Specifically logs when a private CV or Portfolio URL is successfully requested.

## 🧪 Testing

Comprehensive Feature tests (`DashboardTest.php`) have been executed and verified:
- `authenticated user can update their own profile`
- `user cannot update another users profile`
- `user with cv can apply to a published internship`
- `owner can access their own private file`
- `another user cannot access someone elses private file`

**Status**: All 15 tests (37 assertions) are passing successfully.

## 🎯 Next Steps (Batch 7 Readiness)
With the candidate dashboard secure and fully functional, the foundation is laid for Batch 7, which will introduce the **HR Dashboard** and initial application review workflows.
