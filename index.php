<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Containers</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden; /* Prevent body scrolling */
        }
        .search-container {
            position: relative;
            padding: 20px;
            background-color: #f8f8f8;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 1;
        }
        .dropdown {
            position: absolute;
            top: -50px; /* Adjust based on button height */
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            gap: 10px;
            z-index: 2;
        }
        .dropdown-button {
            padding: 10px 20px;
            border: none;
            background-color: #333;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .dropdown-button:hover {
            background-color: #555;
        }
        .search-container input {
            width: 300px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .search-suggestions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 10px;
        }
        .search-suggestions .suggestion {
            padding: 10px 15px;
            background-color: #ddd;
            border-radius: 5px;
            cursor: pointer;
        }
        .container-wrapper {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            width: 100%;
            height: 100%;
            overflow-y: auto; /* Enable vertical scrolling */
            padding: 10px;
            box-sizing: border-box;
        }
        .container {
            width: 300px;
            margin: 10px;
            padding: 20px;
            background-color: #f0f0f0;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            cursor: pointer;
            box-sizing: border-box; /* Ensure padding and border are included in the container's width and height */
        }
        .container img {
            width: 100%;
            height: auto; /* Maintain aspect ratio */
            display: block;
        }
        .container h3 {
            margin: 10px 0 0;
        }
        .container p {
            margin: 5px 0 0;
        }
    </style>
</head>
<body>

<div class="search-container">
    <div class="dropdown">
        <button class="dropdown-button" id="prev-page">Previous</button>
        <button class="dropdown-button" id="next-page">Next</button>
    </div>
    <input type="text" id="search-input" placeholder="Search...">
    <div class="search-suggestions">
        <div class="suggestion">Airbnb</div>
        <div class="suggestion">Uber</div>
    </div>
</div>

<main>
    <div class="container-wrapper" id="container-wrapper">
        <!-- Containers will be dynamically inserted here -->
    </div>
</main>

<footer>
    <p>&copy; 2024 Your Website</p>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const containerWrapper = document.getElementById('container-wrapper');
        const prevPageButton = document.getElementById('prev-page');
        const nextPageButton = document.getElementById('next-page');
        const searchInput = document.getElementById('search-input');
        const suggestions = document.querySelectorAll('.suggestion');
        let currentPage = 0;
        const itemsPerPage = 6; // Number of containers per page
        let data = [];

        function fetchData() {
            fetch('path_to_your_php_script.php')
                .then(response => response.json())
                .then(fetchedData => {
                    data = fetchedData;
                    displayPage();
                })
                .catch(error => console.error('Error:', error));
        }

        function displayPage() {
            containerWrapper.innerHTML = '';
            const start = currentPage * itemsPerPage;
            const end = start + itemsPerPage;
            const pageData = data.slice(start, end);

            pageData.forEach(item => {
                const container = document.createElement('div');
                container.className = 'container';
                container.innerHTML = `
                    <img src="${item.image}" alt="${item.name}">
                    <h3>${item.name}</h3>
                    <p>${item.description}</p>
                `;
                container.onclick = () => {
                    window.location.href = `another_page.php?id=${item.id}`;
                };
                containerWrapper.appendChild(container);
            });

            // Adjust container sizes after images are loaded
            adjustContainerSizes();
        }

        function adjustContainerSizes() {
            // Find the tallest image
            const images = document.querySelectorAll('.container img');
            let maxHeight = 0;

            images.forEach(img => {
                img.onload = () => {
                    const imgHeight = img.clientHeight;
                    if (imgHeight > maxHeight) {
                        maxHeight = imgHeight;
                    }
                    img.style.height = 'auto'; // Ensure image fits correctly
                };
            });

            // Adjust container heights based on tallest image
            setTimeout(() => { // Ensure this runs after all images are loaded
                const containers = document.querySelectorAll('.container');
                containers.forEach(container => {
                    container.style.height = `${maxHeight + 40}px`; // +40 for padding and margins
                });
            }, 100); // Timeout ensures that all images are loaded
        }

        prevPageButton.addEventListener('click', () => {
            if (currentPage > 0) {
                currentPage--;
                displayPage();
            }
        });

        nextPageButton.addEventListener('click', () => {
            if ((currentPage + 1) * itemsPerPage < data.length) {
                currentPage++;
                displayPage();
            }
        });

        suggestions.forEach(suggestion => {
            suggestion.addEventListener('click', () => {
                searchInput.value = suggestion.textContent;
                filterData(searchInput.value);
            });
        });

        searchInput.addEventListener('input', () => {
            filterData(searchInput.value);
        });

        function filterData(searchTerm) {
            const searchTermLower = searchTerm.toLowerCase();
            const filteredData = data.filter(item => 
                item.name.toLowerCase().includes(searchTermLower) || 
                item.description.toLowerCase().includes(searchTermLower)
            );
            displayFilteredData(filteredData);
        }

        function displayFilteredData(filteredData) {
            containerWrapper.innerHTML = '';
            filteredData.forEach(item => {
                const container = document.createElement('div');
                container.className = 'container';
                container.innerHTML = `
                    <img src="${item.image}" alt="${item.name}">
                    <h3>${item.name}</h3>
                    <p>${item.description}</p>
                `;
                container.onclick = () => {
                    window.location.href = `another_page.php?id=${item.id}`;
                };
                containerWrapper.appendChild(container);
            });

            // Adjust container sizes after images are loaded
            adjustContainerSizes();
        }

        fetchData();
    });
</script>

</body>
</html>
