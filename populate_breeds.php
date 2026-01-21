<?php
require_once 'db_connect.php';

echo "Adding comprehensive breed list...\n";

// Helper to get ID
function getId($pdo, $slug)
{
    try {
        $stmt = $pdo->prepare("SELECT id FROM adoption_pet_types WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        return null;
    }
}

function getCatId($pdo, $petTypeId, $name)
{
    $stmt = $pdo->prepare("SELECT id FROM breed_categories WHERE pet_type_id = ? AND name = ?");
    $stmt->execute([$petTypeId, $name]);
    $id = $stmt->fetchColumn();
    if (!$id) {
        $pdo->prepare("INSERT INTO breed_categories (pet_type_id, name) VALUES (?, ?)")->execute([$petTypeId, $name]);
        return $pdo->lastInsertId();
    }
    return $id;
}

$dogId = getId($pdo, 'dog');
$catId = getId($pdo, 'cat');

if ($dogId) {
    // DOGS
    $groups = [
        'Sporting' => ['Golden Retriever', 'Labrador Retriever', 'Cocker Spaniel', 'German Shorthaired Pointer', 'Irish Setter'],
        'Herding' => ['German Shepherd', 'Border Collie', 'Australian Shepherd', 'Corgi', 'Shetland Sheepdog'],
        'Working' => ['Husky', 'Great Dane', 'Rottweiler', 'Boxer', 'Doberman Pinscher', 'St. Bernard'],
        'Terrier' => ['Bull Terrier', 'Jack Russell Terrier', 'Scottish Terrier', 'West Highland White Terrier'],
        'Toy' => ['Chihuahua', 'Pug', 'Pomeranian', 'Shih Tzu', 'Yorkshire Terrier', 'Maltese'],
        'Non-Sporting' => ['Bulldog', 'French Bulldog', 'Poodle', 'Dalmatian', 'Chow Chow'],
        'Hounds' => ['Beagle', 'Dachshund', 'Greyhound', 'Bloodhound', 'Basset Hound'],
        'Indian Native' => ['Indian Pariah (Desi)', 'Mudhol Hound', 'Rajapalayam', 'Combai', 'Gaddi Kutta']
    ];

    foreach ($groups as $gName => $breeds) {
        $gId = getCatId($pdo, $dogId, $gName . ' Group');
        foreach ($breeds as $b) {
            $pdo->prepare("INSERT IGNORE INTO adoption_breeds (category_id, name) VALUES (?, ?)")->execute([$gId, $b]);
        }
    }
}

if ($catId) {
    // CATS
    $groups = [
        'Domestic' => ['Domestic Short Hair', 'Domestic Medium Hair', 'Domestic Long Hair'],
        'Asian' => ['Siamese', 'Persian', 'Bengal', 'Birman', 'Burmese', 'Oriental Shorthair'],
        'American/European' => ['Maine Coon', 'Ragdoll', 'British Shorthair', 'American Shorthair', 'Scottish Fold', 'Sphynx'],
        'Exotic' => ['Abyssinian', 'Russian Blue', 'Norwegian Forest Cat', 'Siberian']
    ];

    foreach ($groups as $gName => $breeds) {
        $gId = getCatId($pdo, $catId, $gName);
        foreach ($breeds as $b) {
            $pdo->prepare("INSERT IGNORE INTO adoption_breeds (category_id, name) VALUES (?, ?)")->execute([$gId, $b]);
        }
    }
}

echo "[SUCCESS] Added 50+ breeds!";
?>