# User-to-User Adoption Feature Implementation Plan

## 1. Feature Overview
The goal is to transform the static adoption page into a fully functional "User-to-User Adoption Marketplace". Users and Shop Owners can list pets, and Admins will moderate these listings.

## 2. Database Schema Changes

### A. New Table: `adoption_listings`
Stores details of pets available for adoption.
```sql
CREATE TABLE IF NOT EXISTS adoption_listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,                      -- Linked to users table (Normal User)
    shop_id INT NULL,                      -- Linked to shop_applications (Shop Owner)
    pet_name VARCHAR(100) NOT NULL,
    pet_type VARCHAR(50) NOT NULL,         -- dog, cat, bird, rabbit, other
    breed VARCHAR(100),
    age VARCHAR(50),
    gender ENUM('Male', 'Female', 'Unknown') DEFAULT 'Unknown',
    vaccination_status ENUM('Vaccinated', 'Not Vaccinated', 'Unknown') DEFAULT 'Unknown',
    description TEXT,
    reason_for_adoption TEXT,
    image_url TEXT,
    status ENUM('pending_approval', 'active', 'adopted', 'rejected') DEFAULT 'pending_approval',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (shop_id) REFERENCES shop_applications(id) ON DELETE CASCADE
);
```

### B. Update Table: `adoption_applications`
Update the existing table to link to specific listings instead of just text names.
```sql
ALTER TABLE adoption_applications 
ADD COLUMN listing_id INT NOT NULL AFTER user_id,
ADD CONSTRAINT fk_listing FOREIGN KEY (listing_id) REFERENCES adoption_listings(id) ON DELETE CASCADE;
```
*(Note: We will migrate existing text-based data or clear it depending on project preference).*

---

## 3. Dashboard-Wise Functionality

### 👤 User Dashboard (Pet Owner & Adopter)
*   **List a Pet**: A dedicated page (`adoption-list-pet.php`) to upload pet details and visuals.
*   **My Listings**: A section to view pets I have listed and their status (Pending Admin Approval, Active, Adopted).
*   **Incoming Requests**: View who wants to adopt my pet. options: `Accept` (Reveals contact info) or `Reject`.
*   **My Applications**: Track status of pets I want to adopt.

### 🏪 Shop Owner Dashboard
*   **List a Store Pet**: Similar to users, but tagged as a "Shop Listing" (often implies higher trust).
*   **Manage Adoptions**: A unified view of all shop pets and incoming user applications.
*   **Verify Health**: Option to upload a "Vaccination Certificate" (image) for transparency.

### 🛡️ Admin Dashboard (Moderation)
*   **Adoption Approvals**: A queue of new `pending_approval` listings. Admin checks for spam/fake details.
*   **Action**: `Approve` (Goes live) or `Reject` (With reason).
*   **Oversight**: View all active listings and applications for auditing.

---

## 4. Implementation Steps

### Phase 1: Database Setup
1.  Run the Schema creation SQL.
2.  Update `adoption_applications` structure.

### Phase 2: Listing Creation (Frontend + Backend)
1.  Create `adoption-list-pet.php`: Form for users to submit data.
2.  Handle image upload and database insertion.
3.  Ensure default status is `pending_approval`.

### Phase 3: Public Adoption Page Update
1.  Refactor `adoption.php` to fetch **Real Data** from `adoption_listings`.
2.  Filter logic (search database instead of JS array).
3.  Add "List Your Pet" CTA button.

### Phase 4: Application Flow
1.  Update `apply-adoption.php` to insert `listing_id`.
2.  Create `adoption-my-listings.php` for users to manage their pets.

### Phase 5: Admin & Approvals
1.  Create `admin-adoption-approvals.php`.
2.  Implement Approve/Reject logic.

---

## 5. Optional Future Enhancements
*   **AI Matching**: Suggest pets based on user survey (living space, activity level).
*   **Adoption Agreement Gen**: PDF with digital signatures.
*   **Success Stories**: A "Happy Tails" section for completed adoptions.
