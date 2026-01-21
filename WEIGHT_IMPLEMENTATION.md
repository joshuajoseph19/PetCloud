# Weight Field Implementation - Adoption Center

## Overview
This document describes the implementation of the weight field (in kilograms) for the PetCloud adoption center.

## Changes Made

### 1. Database Schema
**File:** `database/add_weight_field.sql`
- Added `weight_kg` column to `pet_rehoming_listings` table
- Data type: `DECIMAL(5, 2)` - allows weights up to 999.99 kg with 2 decimal precision
- Added index on `weight_kg` for potential filtering

**Migration Script:** `migrate_add_weight.php`
- Run this file once to update your database
- Access via: `http://localhost/PetCloud/migrate_add_weight.php`
- Includes safety checks to prevent duplicate column creation

### 2. API Updates

#### `api/get_adoption_listings.php`
- Added `weight_kg` to SELECT query
- Included weight in JSON response for each pet listing
- Weight is returned as a float or null if not set

#### `api/submit_rehoming.php`
- Added `weight_kg` parameter handling
- Sanitizes input using `floatval()`
- Includes weight in database INSERT statement

### 3. Frontend Updates

#### `pet-rehoming-form.php`
- Added weight input field in the form
- Field type: `number` with `step="0.1"` for decimal values
- Placeholder: "e.g., 5.5"
- Positioned alongside the color field for better UX

#### `browse-adoptions.php`
- Updated pet card rendering to display weight
- Shows weight with "kg" suffix when available
- Uses Font Awesome weight icon (`fa-weight`)
- Only displays if weight data exists

## Usage

### For Users Listing Pets
1. Fill out the pet rehoming form
2. Enter weight in kilograms (e.g., 5.5 for 5.5 kg)
3. Field is optional - leave blank if unknown

### For Browsing Pets
- Weight is displayed on pet cards when available
- Format: "5.5 kg"
- Appears alongside age, gender, and size information

## Database Migration

**IMPORTANT:** Run the migration script before using the weight feature:

```bash
# Via browser:
http://localhost/PetCloud/migrate_add_weight.php

# Or via MySQL command line:
mysql -u your_username -p petcloud < database/add_weight_field.sql
```

## Technical Details

### Weight Storage
- **Unit:** Kilograms (kg)
- **Range:** 0.01 to 999.99 kg
- **Precision:** 2 decimal places
- **Nullable:** Yes (optional field)

### API Response Format
```json
{
  "id": 1,
  "pet_name": "Buddy",
  "weight_kg": 12.5,
  ...
}
```

### Form Input
```html
<input type="number" name="weight_kg" min="0" step="0.1" placeholder="e.g., 5.5">
```

## Future Enhancements
- Add weight range filtering in browse adoptions
- Display weight in different units (kg/lbs) based on user preference
- Add weight validation based on pet type and size
- Include weight in admin approval interface

## Files Modified
1. `database/add_weight_field.sql` (new)
2. `migrate_add_weight.php` (new)
3. `api/get_adoption_listings.php`
4. `api/submit_rehoming.php`
5. `pet-rehoming-form.php`
6. `browse-adoptions.php`

## Testing Checklist
- [ ] Run database migration
- [ ] Submit a new pet listing with weight
- [ ] Submit a new pet listing without weight
- [ ] Browse adoptions and verify weight displays correctly
- [ ] Verify weight doesn't display for pets without weight data
- [ ] Check API response includes weight_kg field

## Notes
- All weights are stored in kilograms for consistency
- The field is optional to accommodate existing listings
- Weight icon uses Font Awesome's `fa-weight` class
- Decimal precision allows for accurate weight tracking of small pets
