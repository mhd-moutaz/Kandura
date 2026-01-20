// Addresses Page JavaScript

let map = null;
let marker = null;

function showOnMap(lat, lng, userName) {
    const modal = document.getElementById('mapModal');
    const mapTitle = document.getElementById('mapTitle');
    const infoLat = document.getElementById('infoLat');
    const infoLng = document.getElementById('infoLng');

    mapTitle.textContent = `${userName}'s Address Location`;
    infoLat.textContent = lat || 'Not available';
    infoLng.textContent = lng || 'Not available';

    modal.style.display = 'flex';

    // Initialize map if not already done
    setTimeout(() => {
        if (!map) {
            // Default coordinates if none provided
            const defaultLat = lat || 24.7136;
            const defaultLng = lng || 46.6753;

            map = L.map('map').setView([defaultLat, defaultLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
        }

        // Clear previous marker
        if (marker) {
            map.removeLayer(marker);
        }

        // Add marker if coordinates exist
        if (lat && lng) {
            marker = L.marker([lat, lng]).addTo(map)
                .bindPopup(`<b>${userName}</b><br>Lat: ${lat}<br>Lng: ${lng}`)
                .openPopup();
            map.setView([lat, lng], 15);
        } else {
            // Show message if no coordinates
            const bounds = map.getBounds();
            const center = bounds.getCenter();
            L.popup()
                .setLatLng(center)
                .setContent('<div style="text-align:center;padding:10px;"><i class="fas fa-exclamation-circle" style="color:#f59e0b;font-size:24px;margin-bottom:10px;"></i><p>No coordinates available for this address</p></div>')
                .openOn(map);
        }
    }, 100);
}

function closeMapModal() {
    document.getElementById('mapModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('mapModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeMapModal();
    }
});

// Clean up map on modal close
document.getElementById('mapModal')?.addEventListener('hidden', function() {
    if (map) {
        map.remove();
        map = null;
        marker = null;
    }
});
