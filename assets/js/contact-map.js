/**
 * Contact Map - Leaflet.js Implementation
 * Interactive map showing LEYECO III office location
 */

document.addEventListener('DOMContentLoaded', function () {
    // LEYECO III Office Coordinates
    // Brgy. San Roque, Tunga, Leyte
    const leyecoLat = 11.2543566;
    const leyecoLng = 124.7463373;

    // Initialize the map (zoom 16 shows ~500m area)
    const map = L.map('contact-map').setView([leyecoLat, leyecoLng], 14);

    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(map);

    // Custom icon for LEYECO III marker (using Font Awesome icon)
    const leyecoIcon = L.divIcon({
        className: 'custom-marker',
        html: '<div class="marker-pin"><i class="fas fa-bolt"></i></div>',
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -40]
    });

    // Add marker for LEYECO III office
    const marker = L.marker([leyecoLat, leyecoLng], { icon: leyecoIcon }).addTo(map);

    // Add popup with office information
    marker.bindPopup(`
        <div class="map-popup">
            <h3><i class="fas fa-bolt"></i> LEYECO III</h3>
            <p><i class="fas fa-map-marker-alt"></i> Brgy. San Roque, Tunga, Leyte</p>
            <div class="popup-actions">
                <a href="https://www.google.com/maps/dir/?api=1&destination=${leyecoLat},${leyecoLng}" 
                   target="_blank" 
                   class="directions-btn">
                    <i class="fas fa-directions"></i> Directions
                </a>
            </div>
        </div>
    `).openPopup();

    // Add zoom control position
    map.zoomControl.setPosition('topright');

    // Add scale control
    L.control.scale({
        position: 'bottomleft',
        imperial: false
    }).addTo(map);
});
