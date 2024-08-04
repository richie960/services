<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Containers</title>
    <style>
        @keyframes blink {
            0% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
            100% {
                opacity: 1;
            }
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden; /* Prevent body scrolling */
        }

        .header-container {
            padding: 10px 20px;
            background-color: #f8f8f8;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .search-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
            max-width: 600px; /* Limit width */
            position: relative;
        }

        .search-container input {
            width: 100%;
            padding: 10px;
            padding-right: 40px; /* Space for search icon */
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            margin-bottom: 10px; /* Space for suggestions */
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .search-container input:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .search-suggestions {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            z-index: 10;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            max-height: 150px; /* Limited height */
            overflow-y: auto; /* Enable vertical scrolling */
            padding: 5px;
            box-sizing: border-box;
            width: 100%;
            display: none; /* Initially hidden */
        }

        .search-suggestions .suggestion {
            padding: 5px 10px;
            cursor: pointer;
            font-size: 14px; /* Smaller font size */
            text-align: center;
        }

        .search-suggestions .suggestion:hover {
            background-color: #ddd;
        }

        .search-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            fill: #007bff; /* Blue color for the icon */
            animation: blink 1.5s infinite; /* Apply blinking effect */
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden; /* Prevent main container overflow */
            background-color: #f0f0f0;
        }

        .container-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            width: 100%;
            height: 100%;
            overflow-y: auto; /* Enable vertical scrolling */
            padding: 10px;
            box-sizing: border-box;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px;
            width: 100%; /* Full width */
            height: 300px; /* Fixed height */
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .container:hover {
            transform: scale(1.05); /* Slightly enlarge on hover */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2); /* Darker shadow on hover */
        }

        .container img {
            width: 100%;
            height: 200px; /* Fixed height for images */
            object-fit: cover; /* Ensure image covers the container */
            border-radius: 8px;
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

<div class="header-container">
    <div class="search-container">
        <input type="text" id="search-input" placeholder="Search...">
        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
           <rect width="8" height="2" x="8.278" y="11.278" fill="#2583ef" transform="rotate(45.001 12.278 12.278)"></rect>
           <circle cx="7" cy="7" r="7" fill="#36c8f6"></circle>
           <path fill="#a2e4f4" d="M5.439,7.561c-0.586-0.586-0.586-1.536,0-2.121c0.586-0.586,1.536-0.586,2.121,0l1.414-1.414v0 c-1.367-1.367-3.583-1.367-4.95,0s-1.367,3.583,0,4.95L5.439,7.561z"></path>
        </svg>
        
        <div class="search-suggestions" id="search-suggestions">
            <!-- Suggestions will be dynamically inserted here -->
        </div>
    </div>
</div>

<main>
    <div class="container-wrapper" id="container-wrapper">
        <!-- Containers will be dynamically inserted here -->
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const containerWrapper = document.getElementById('container-wrapper');
    const searchInput = document.getElementById('search-input');
    const searchSuggestions = document.getElementById('search-suggestions');
    let data = [];

    function fetchData() {
        fetch('path_to_your_php_script.php')
            .then(response => response.json())
            .then(fetchedData => {
                data = fetchedData;
                displayFilteredData(data);
            })
            .catch(error => console.error('Error:', error));
    }

    function updateSuggestions(filteredData) {
        searchSuggestions.innerHTML = '';
        const suggestions = Array.from(new Set(filteredData.map(item => item.name))); // Unique suggestions
        suggestions.forEach(name => {
            const suggestion = document.createElement('div');
            suggestion.className = 'suggestion';
            suggestion.textContent = name;
            suggestion.onclick = () => {
                searchInput.value = name;
                filterData(name);
            };
            searchSuggestions.appendChild(suggestion);
        });
    }

    function filterData(searchTerm) {
        const searchTermLower = searchTerm.toLowerCase();
        const filteredData = data.filter(item => 
            item.name.toLowerCase().includes(searchTermLower) || 
            item.description.toLowerCase().includes(searchTermLower)
        );
        updateSuggestions(filteredData);
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

        // Smooth scroll to the top of the containerWrapper
        containerWrapper.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value;
        filterData(searchTerm);
    });

    searchInput.addEventListener('focus', () => {
        const searchTerm = searchInput.value;
        if (!searchTerm) {
            updateSuggestions(data);
        }
        searchSuggestions.style.display = 'flex'; // Show suggestions
    });

    searchInput.addEventListener('blur', () => {
        // Delay hiding suggestions to allow for clicking on suggestions
        setTimeout(() => {
            searchSuggestions.style.display = 'none'; // Hide suggestions
        },
            200);
    });

    fetchData();
});
</script>

</body>
</html>
