<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Service Owners</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <!-- jQuery UI CSS for Autocomplete -->
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
    }

    #service-owners-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
    }

    .service-owner {
      border: 1px solid #ddd;
      margin: 10px;
      padding: 15px;
      width: 250px;
      cursor: pointer;
      text-align: center;
      border-radius: 8px;
      background-color: #fff;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      transition: box-shadow 0.3s, transform 0.3s;
    }

    .service-owner:hover {
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      transform: scale(1.02);
    }

    .modal-content {
      padding: 20px;
    }

    .carousel-inner img {
      width: 100%;
      height: auto;
    }

    #map {
      height: 300px;
      width: 100%;
    }

    #reviews {
      max-height: 200px;
      overflow-y: auto;
    }

    .review {
      border-bottom: 1px solid #eee;
      padding: 5px 0;
    }

    .review:last-child {
      border-bottom: none;
    }

    .btn {
      margin-right: 10px;
    }

    /* Chat styling */
    #chat-area {
      max-height: 400px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
    }

    .message {
      margin: 5px;
      padding: 10px;
      border-radius: 8px;
      max-width: 80%;
    }

    .message.user {
      align-self: flex-end;
      background-color: #dcf8c6;
    }

    .message.owner {
      align-self: flex-start;
      background-color: #f1f0f0;
    }

    .input-group {
      position: relative;
    }
    
    .input-group input {
      border-radius: 0;
    }
    
    .input-group-append button {
      border-radius: 0;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <input type="text" id="search-input" class="form-control mb-3" placeholder="Search for service owners by company name...">
    <div id="service-owners-container"></div>
  </div>

  <!-- Details Modal -->
  <div id="modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Service Owner Details</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="carouselIndicators" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner" id="carousel-images"></div>
            <a class="carousel-control-prev" href="#carouselIndicators" role="button" data-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselIndicators" role="button" data-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="sr-only">Next</span>
            </a>
          </div>
          <div id="modal-details" class="mt-4"></div>
          <div id="calendar" class="mt-4"></div>
          <div id="map" class="mt-4"></div>
          <div id="reviews" class="mt-4"></div>
          <div class="mt-4">
            <button id="whatsapp-button" class="btn btn-success"><i class="fab fa-whatsapp"></i> WhatsApp</button>
            <button id="call-button" class="btn btn-primary"><i class="fas fa-phone"></i> Call</button>
          </div>
          <div class="mt-4">
            <button id="booking-button" class="btn btn-warning">Book</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Chat Modal -->
  <div id="chat-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Chat</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="chat-area"></div>
          <div class="input-group mt-3">
            <input type="text" id="chat-message" class="form-control" placeholder="Type a message...">
            <div class="input-group-append">
              <button id="send-message-button" class="btn btn-primary">Send</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- jQuery, Bootstrap JS, Leaflet JS, jQuery UI JS for Autocomplete -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const serviceOwnersContainer = document.getElementById('service-owners-container');
      const modal = $('#modal');
      const modalDetails = document.getElementById('modal-details');
      const carouselImages = document.getElementById('carousel-images');
      const calendar = document.getElementById('calendar');
      const reviews = document.getElementById('reviews');
      const whatsappButton = document.getElementById('whatsapp-button');
      const callButton = document.getElementById('call-button');
      const bookingButton = document.getElementById('booking-button');
      const mapElement = document.getElementById('map');
      const searchInput = document.getElementById('search-input');

      const chatModal = $('#chat-modal');
      const chatArea = document.getElementById('chat-area');
      const chatMessageInput = document.getElementById('chat-message');
      const sendMessageButton = document.getElementById('send-message-button');

      let map; // Variable to hold the map instance
      let selectedCompanyName = ''; // Variable to hold the selected company name
      let email = ''; // Variable to hold the user's email

      const urlParams = new URLSearchParams(window.location.search);
      const id = urlParams.get('id');

      function fetchServiceOwners(query = '') {
        fetch(`get_service_owners.php?id=${encodeURIComponent(id)}&query=${encodeURIComponent(query)}`)
          .then(response => response.json())
          .then(data => {
            if (!Array.isArray(data)) {
              console.error('Invalid data format:', data);
              return;
            }
            if (data.length === 0) {
              serviceOwnersContainer.innerHTML = '<p>No service owners found.</p>';
              return;
            }
            serviceOwnersContainer.innerHTML = '';
            data.forEach(owner => {
              const div = document.createElement('div');
              div.className = 'service-owner';
              div.innerHTML = `
                <h3>${owner.company_name}</h3>
                <p>${owner.description}</p>
              `;
              div.addEventListener('click', () => {
                selectedCompanyName = owner.company_name;
                fetchOwnerDetails(owner.id);
                modal.modal('show');
              });
              serviceOwnersContainer.appendChild(div);
            });
          })
          .catch(error => {
            console.error('Error fetching service owners:', error);
          });
      }

      function fetchOwnerDetails(ownerId) {
        fetch(`get_service_owner_details.php?id=${ownerId}`)
          .then(response => response.json())
          .then(owner => {
            if (!owner) {
              console.error('Owner details not found.');
              return;
            }
            modalDetails.innerHTML = `
              <h2>${owner.name}</h2>
              <p>${owner.description}</p>
              <p>Phone: ${owner.phone}</p>
              <p>Company: ${owner.company_name}</p>
              <p>Service: ${owner.service}</p>
            `;

            carouselImages.innerHTML = `
              <div class="carousel-item active">
                <img src="${owner.image1}" class="d-block w-100" alt="...">
              </div>
              ${owner.image2 ? `<div class="carousel-item"><img src="${owner.image2}" class="d-block w-100" alt="..."></div>` : ''}
              ${owner.image3 ? `<div class="carousel-item"><img src="${owner.image3}" class="d-block w-100" alt="..."></div>` : ''}
              ${owner.image4 ? `<div class="carousel-item"><img src="${owner.image4}" class="d-block w-100" alt="..."></div>` : ''}
              ${owner.image5 ? `<div class="carousel-item"><img src="${owner.image5}" class="d-block w-100" alt="..."></div>` : ''}
            `;

            whatsappButton.href = `https://wa.me/${owner.phone}`;
            callButton.href = `tel:${owner.phone}`;

            fetchReviews(owner.id);
            initializeMap(owner.location_lat, owner.location_lng);
          })
          .catch(error => {
            console.error('Error fetching owner details:', error);
          });
      }

      function fetchReviews(ownerId) {
        fetch(`get_reviews.php?owner_id=${ownerId}`)
          .then(response => response.json())
          .then(reviewsData => {
            reviews.innerHTML = reviewsData.map(review => `
              <div class="review">
                <p><strong>${review.user}</strong></p>
                <p>${review.comment}</p>
              </div>
            `).join('');
          })
          .catch(error => {
            console.error('Error fetching reviews:', error);
          });
      }

      function initializeMap(lat, lng) {
        if (map) {
          map.remove(); // Remove existing map instance
        }
        if (lat && lng) {
          map = L.map(mapElement).setView([lat, lng], 13);
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
          }).addTo(map);
          L.marker([lat, lng]).addTo(map);
        } else {
          mapElement.innerHTML = '<p>Location data unavailable.</p>';
        }
      }

      function setupAutocomplete() {
        fetch('get_service_owner_names.php')
          .then(response => response.json())
          .then(names => {
            $(searchInput).autocomplete({
              source: names,
              select: (event, ui) => {
                searchInput.value = ui.item.value;
                fetchServiceOwners(ui.item.value);
              }
            });
          })
          .catch(error => {
            console.error('Error fetching service owner names for autocomplete:', error);
          });
      }

      searchInput.addEventListener('input', () => {
        fetchServiceOwners(searchInput.value);
      });

      setupAutocomplete();
      fetchServiceOwners();

      bookingButton.onclick = function() {
        email = prompt('Please enter your email:'); // Get the user's email
        if (!email) {
          alert('Email is required.');
          return;
        }

        fetch('book_service.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ company_name: selectedCompanyName, email: email, message: '' })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
           // alert('Booking successful!');
            modal.modal('hide');
            loadChat(selectedCompanyName, email); // Load chat after booking
          } else {
            alert('Booking failed.');
          }
        });
      };

      function loadChat(company_name, email) {
        fetch('get_chat.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ company_name: company_name, email: email })
          
        })
        .then(response => response.json())
        .then(data => {
          chatArea.innerHTML = data.messages.map(message => `
            <div class="message ${message.sender === 'user' ? 'user' : 'owner'}">
              <strong>${message.sender === 'user' ? 'You' : 'Service Owner'}:</strong> ${message.text}
            </div>
          `).join('');

          chatModal.modal('show');
  
         
     
        });
      }
 
      sendMessageButton.onclick = function() {
        const message = chatMessageInput.value;
        if (!message) {
          return;
        }

        fetch('send_message.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ company_name: selectedCompanyName, email: email, message: message })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const newMessage = document.createElement('div');
            newMessage.className = 'message user';
            newMessage.innerHTML = `<strong>You:</strong> ${message}`;
            chatArea.appendChild(newMessage);
            chatMessageInput.value = '';
            chatArea.scrollTop = chatArea.scrollHeight; // Scroll to the bottom
          } else {
            alert('Failed to send message.');
          }
        });
      };

      // Ensure the details modal reopens when the chat modal is closed
      chatModal.on('hidden.bs.modal', function () {
        modal.modal('show');
      });
    });
  </script>
</body>
</html>
