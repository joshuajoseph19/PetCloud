<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Pets for Adoption - PetCloud</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f7fafc;
            color: #2d3748;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 3rem 1rem;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Main Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .content-wrapper {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
        }

        /* Filters Sidebar */
        .filters-sidebar {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            height: fit-content;
            position: sticky;
            top: 2rem;
        }

        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .filters-header h2 {
            font-size: 1.25rem;
            color: #2d3748;
        }

        .clear-filters {
            color: #4299e1;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
        }

        .clear-filters:hover {
            text-decoration: underline;
        }

        .filter-group {
            margin-bottom: 1.5rem;
        }

        .filter-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 0.5rem;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 0.625rem;
            font-size: 0.95rem;
            border: 2px solid #e2e8f0;
            border-radius: 6px;
            background-color: #fff;
            color: #2d3748;
            transition: all 0.2s ease;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: #4299e1;
        }

        .apply-filters-btn {
            width: 100%;
            padding: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .apply-filters-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(102, 126, 234, 0.3);
        }

        /* Listings Grid */
        .listings-section {
            min-height: 400px;
        }

        .listings-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .results-count {
            font-size: 1.1rem;
            color: #4a5568;
        }

        .listings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        /* Pet Card */
        .pet-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .pet-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }

        .pet-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .pet-details {
            padding: 1.25rem;
        }

        .pet-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.75rem;
        }

        .pet-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2d3748;
        }

        .pet-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.625rem;
            background-color: #ebf8ff;
            color: #2c5282;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .pet-info {
            margin-bottom: 0.5rem;
        }

        .pet-info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #718096;
            margin-bottom: 0.375rem;
        }

        .pet-info-item i {
            width: 16px;
            color: #4299e1;
        }

        .pet-breed {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 0.85rem;
            color: #4a5568;
            margin-top: 0.5rem;
        }

        .pet-location {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid #e2e8f0;
            font-size: 0.85rem;
            color: #718096;
        }

        .pet-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }

        .adoption-fee {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2d3748;
        }

        .view-details-btn {
            padding: 0.5rem 1rem;
            background-color: #4299e1;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .view-details-btn:hover {
            background-color: #3182ce;
        }

        /* Loading State */
        .loading {
            text-align: center;
            padding: 3rem;
            color: #718096;
        }

        .loading i {
            font-size: 2rem;
            color: #4299e1;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state i {
            font-size: 4rem;
            color: #cbd5e0;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            color: #4a5568;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #718096;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .pagination button {
            padding: 0.5rem 1rem;
            background-color: #fff;
            border: 2px solid #e2e8f0;
            border-radius: 6px;
            color: #4a5568;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .pagination button:hover:not(:disabled) {
            border-color: #4299e1;
            color: #4299e1;
        }

        .pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination .page-info {
            padding: 0.5rem 1rem;
            color: #4a5568;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .content-wrapper {
                grid-template-columns: 1fr;
            }

            .filters-sidebar {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.75rem;
            }

            .listings-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1><i class="fas fa-paw"></i> Find Your Perfect Companion</h1>
        <p>Browse pets looking for loving homes</p>
    </div>

    <div class="container">
        <div class="content-wrapper">
            <!-- Filters Sidebar -->
            <aside class="filters-sidebar">
                <div class="filters-header">
                    <h2><i class="fas fa-filter"></i> Filters</h2>
                    <a href="#" class="clear-filters" id="clear-filters">Clear All</a>
                </div>

                <form id="filters-form">
                    <div class="filter-group">
                        <label for="filter-pet-type">Pet Type</label>
                        <select id="filter-pet-type" name="pet_type_id">
                            <option value="">All Types</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="filter-breed-group">Breed Category</label>
                        <select id="filter-breed-group" name="breed_group_id">
                            <option value="">All Categories</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="filter-breed">Specific Breed</label>
                        <select id="filter-breed" name="breed_id" disabled>
                            <option value="">Select pet type first</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="filter-gender">Gender</label>
                        <select id="filter-gender" name="gender">
                            <option value="">All Genders</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Unknown">Unknown</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="filter-size">Size</label>
                        <select id="filter-size" name="size">
                            <option value="">All Sizes</option>
                            <option value="Small">Small</option>
                            <option value="Medium">Medium</option>
                            <option value="Large">Large</option>
                            <option value="Extra Large">Extra Large</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="filter-city">City</label>
                        <input type="text" id="filter-city" name="city" placeholder="Enter city">
                    </div>

                    <div class="filter-group">
                        <label for="filter-state">State</label>
                        <input type="text" id="filter-state" name="state" placeholder="Enter state">
                    </div>

                    <button type="submit" class="apply-filters-btn">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                </form>
            </aside>

            <!-- Listings Section -->
            <main class="listings-section">
                <div class="listings-header">
                    <div class="results-count" id="results-count">Loading...</div>
                </div>

                <div class="listings-grid" id="listings-grid">
                    <div class="loading">
                        <i class="fas fa-spinner"></i>
                        <p>Loading pets...</p>
                    </div>
                </div>

                <div class="pagination" id="pagination" style="display: none;">
                    <button id="prev-page" disabled>
                        <i class="fas fa-chevron-left"></i> Previous
                    </button>
                    <span class="page-info" id="page-info">Page 1 of 1</span>
                    <button id="next-page" disabled>
                        Next <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </main>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let currentFilters = {};

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadPetTypes();
            loadBreedGroups();
            loadListings();
            initializeFilters();
        });

        async function loadPetTypes() {
            try {
                const response = await fetch('api/get_pet_types.php');
                const data = await response.json();

                if (data.success) {
                    const select = document.getElementById('filter-pet-type');
                    data.data.forEach(petType => {
                        const option = document.createElement('option');
                        option.value = petType.id;
                        option.textContent = petType.name;
                        select.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error loading pet types:', error);
            }
        }

        async function loadBreedGroups() {
            try {
                // Hardcoded breed groups (or fetch from API if you create one)
                const breedGroups = [
                    { id: 1, name: 'Pure Breed' },
                    { id: 2, name: 'Mixed Breed' },
                    { id: 3, name: 'Indie / Local' },
                    { id: 4, name: 'Unknown' }
                ];

                const select = document.getElementById('filter-breed-group');
                breedGroups.forEach(group => {
                    const option = document.createElement('option');
                    option.value = group.id;
                    option.textContent = group.name;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading breed groups:', error);
            }
        }

        async function loadBreeds(petTypeId) {
            const breedSelect = document.getElementById('filter-breed');
            breedSelect.innerHTML = '<option value="">Loading...</option>';
            breedSelect.disabled = true;

            try {
                const response = await fetch(`api/get_breeds.php?pet_type_id=${petTypeId}`);
                const data = await response.json();

                if (data.success) {
                    breedSelect.innerHTML = '<option value="">All Breeds</option>';

                    data.data.forEach(group => {
                        const optgroup = document.createElement('optgroup');
                        optgroup.label = group.group_name;

                        group.breeds.forEach(breed => {
                            const option = document.createElement('option');
                            option.value = breed.id;
                            option.textContent = breed.name;
                            optgroup.appendChild(option);
                        });

                        breedSelect.appendChild(optgroup);
                    });

                    breedSelect.disabled = false;
                }
            } catch (error) {
                console.error('Error loading breeds:', error);
                breedSelect.innerHTML = '<option value="">Error loading breeds</option>';
            }
        }

        function initializeFilters() {
            const form = document.getElementById('filters-form');
            const petTypeSelect = document.getElementById('filter-pet-type');
            const clearFilters = document.getElementById('clear-filters');

            // Load breeds when pet type changes
            petTypeSelect.addEventListener('change', (e) => {
                const petTypeId = e.target.value;
                if (petTypeId) {
                    loadBreeds(petTypeId);
                } else {
                    const breedSelect = document.getElementById('filter-breed');
                    breedSelect.innerHTML = '<option value="">Select pet type first</option>';
                    breedSelect.disabled = true;
                }
            });

            // Apply filters on form submit
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                currentPage = 1;
                currentFilters = getFormFilters();
                loadListings();
            });

            // Clear filters
            clearFilters.addEventListener('click', (e) => {
                e.preventDefault();
                form.reset();
                currentFilters = {};
                currentPage = 1;
                document.getElementById('filter-breed').disabled = true;
                loadListings();
            });

            // Pagination
            document.getElementById('prev-page').addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    loadListings();
                }
            });

            document.getElementById('next-page').addEventListener('click', () => {
                currentPage++;
                loadListings();
            });
        }

        function getFormFilters() {
            const formData = new FormData(document.getElementById('filters-form'));
            const filters = {};

            for (let [key, value] of formData.entries()) {
                if (value) {
                    filters[key] = value;
                }
            }

            return filters;
        }

        async function loadListings() {
            const grid = document.getElementById('listings-grid');
            grid.innerHTML = '<div class="loading"><i class="fas fa-spinner"></i><p>Loading pets...</p></div>';

            try {
                const params = new URLSearchParams({
                    ...currentFilters,
                    page: currentPage,
                    limit: 12
                });

                const response = await fetch(`api/get_adoption_listings.php?${params}`);
                const data = await response.json();

                if (data.success) {
                    renderListings(data.data);
                    updatePagination(data.pagination);
                    updateResultsCount(data.pagination.total_records);
                } else {
                    grid.innerHTML = `<div class="empty-state"><i class="fas fa-exclamation-circle"></i><h3>Error</h3><p>${data.error}</p></div>`;
                }
            } catch (error) {
                console.error('Error loading listings:', error);
                grid.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><h3>Error</h3><p>Failed to load listings</p></div>';
            }
        }

        function renderListings(listings) {
            const grid = document.getElementById('listings-grid');

            if (listings.length === 0) {
                grid.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h3>No pets found</h3>
                        <p>Try adjusting your filters</p>
                    </div>
                `;
                return;
            }

            grid.innerHTML = listings.map(pet => `
                <div class="pet-card" onclick="viewPetDetails(${pet.id})">
                    <img src="${pet.image || 'images/placeholder-pet.jpg'}" alt="${pet.pet_name}" class="pet-image">
                    <div class="pet-details">
                        <div class="pet-header">
                            <h3 class="pet-name">${pet.pet_name}</h3>
                            <span class="pet-type-badge">
                                <i class="fas ${pet.pet_type.icon}"></i>
                                ${pet.pet_type.name}
                            </span>
                        </div>
                        
                        <div class="pet-info">
                            <div class="pet-info-item">
                                <i class="fas fa-birthday-cake"></i>
                                <span>${pet.age.display}</span>
                            </div>
                            <div class="pet-info-item">
                                <i class="fas fa-venus-mars"></i>
                                <span>${pet.gender}</span>
                            </div>
                            ${pet.size ? `
                            <div class="pet-info-item">
                                <i class="fas fa-ruler"></i>
                                <span>${pet.size}</span>
                            </div>
                            ` : ''}
                            ${pet.weight_kg ? `
                            <div class="pet-info-item">
                                <i class="fas fa-weight"></i>
                                <span>${pet.weight_kg} kg</span>
                            </div>
                            ` : ''}
                        </div>
                        
                        <div class="pet-breed">
                            ${pet.breed.name} • ${pet.breed.group}
                        </div>
                        
                        <div class="pet-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${pet.location.city}, ${pet.location.state}</span>
                        </div>
                        
                        <div class="pet-footer">
                            <div class="adoption-fee">
                                ${pet.adoption_fee > 0 ? `₹${pet.adoption_fee.toFixed(2)}` : 'Free'}
                            </div>
                            <button class="view-details-btn">View Details</button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function updatePagination(pagination) {
            const paginationDiv = document.getElementById('pagination');
            const prevBtn = document.getElementById('prev-page');
            const nextBtn = document.getElementById('next-page');
            const pageInfo = document.getElementById('page-info');

            if (pagination.total_pages > 1) {
                paginationDiv.style.display = 'flex';
                prevBtn.disabled = !pagination.has_prev;
                nextBtn.disabled = !pagination.has_next;
                pageInfo.textContent = `Page ${pagination.current_page} of ${pagination.total_pages}`;
            } else {
                paginationDiv.style.display = 'none';
            }
        }

        function updateResultsCount(total) {
            const resultsCount = document.getElementById('results-count');
            resultsCount.textContent = `${total} pet${total !== 1 ? 's' : ''} available for adoption`;
        }

        function viewPetDetails(petId) {
            // Redirect to pet details page
            window.location.href = `pet-details.php?id=${petId}`;
        }
    </script>
</body>

</html>