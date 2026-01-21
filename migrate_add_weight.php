<?php
/**
 * Migration Script: Add weight_kg field to pet_rehoming_listings table
 * Run this file once to update the database schema
 */

require_once 'db_connect.php';

try {
    echo "<h2>Database Migration: Adding weight_kg field</h2>";

    // Check if column already exists
    $checkQuery = "SHOW COLUMNS FROM pet_rehoming_listings LIKE 'weight_kg'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        echo "<p style='color: orange;'>✓ Column 'weight_kg' already exists. No migration needed.</p>";
    } else {
        // Add the weight_kg column
        $alterQuery = "ALTER TABLE pet_rehoming_listings 
                      ADD COLUMN weight_kg DECIMAL(5, 2) DEFAULT NULL COMMENT 'Weight in kilograms' 
                      AFTER size";

        if ($conn->query($alterQuery) === TRUE) {
            echo "<p style='color: green;'>✓ Successfully added 'weight_kg' column to pet_rehoming_listings table.</p>";

            // Add index for potential filtering
            $indexQuery = "ALTER TABLE pet_rehoming_listings ADD INDEX idx_weight (weight_kg)";
            if ($conn->query($indexQuery) === TRUE) {
                echo "<p style='color: green;'>✓ Successfully added index on 'weight_kg' column.</p>";
            } else {
                echo "<p style='color: orange;'>⚠ Warning: Could not add index (may already exist): " . $conn->error . "</p>";
            }
        } else {
            throw new Exception("Error adding column: " . $conn->error);
        }
    }

    echo "<h3>Migration completed successfully!</h3>";
    echo "<p><a href='browse-adoptions.php'>Go to Browse Adoptions</a></p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

$conn->close();
?>