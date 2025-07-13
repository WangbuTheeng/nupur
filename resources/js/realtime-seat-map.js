/**
 * Real-time Seat Map Component
 * Handles live seat updates using Laravel Echo and WebSockets
 */

class RealtimeSeatMap {
    constructor(scheduleId, containerId) {
        this.scheduleId = scheduleId;
        this.container = document.getElementById(containerId);
        this.selectedSeats = new Set();
        this.reservedSeats = new Set();
        this.bookedSeats = new Set();
        this.availableSeats = new Set();
        
        this.init();
        this.setupRealtimeListeners();
    }

    init() {
        this.loadSeatMap();
        this.setupSeatClickHandlers();
    }

    async loadSeatMap() {
        try {
            const response = await fetch(`/api/schedules/${this.scheduleId}/seat-map`);
            const data = await response.json();
            
            if (data.success) {
                this.renderSeatMap(data.seat_map);
                this.updateSeatStatus(data.seat_map.seats);
            }
        } catch (error) {
            console.error('Failed to load seat map:', error);
        }
    }

    renderSeatMap(seatMap) {
        const { layout_type, rows, columns, seats, driver_seat, door, has_back_row } = seatMap;

        let html = '<div class="seat-map-container">';

        // Legend
        html += '<div class="seat-legend">';
        html += '<div class="legend-item"><span class="seat available"></span> Available</div>';
        html += '<div class="legend-item"><span class="seat selected"></span> Selected</div>';
        html += '<div class="legend-item"><span class="seat booked"></span> Booked</div>';
        html += '<div class="legend-item"><span class="seat reserved"></span> Reserved</div>';
        html += '</div>';

        // Bus layout container
        html += '<div class="bus-layout-container">';

        // Bus frame with driver seat and door
        html += '<div class="bus-frame">';

        // Top section with driver seat and door
        html += '<div class="bus-top-section">';
        html += '<div class="bus-door" title="Front Door">🚪</div>';
        html += '<div class="bus-front-space"></div>';
        html += '<div class="driver-seat" title="Driver">👨‍✈️</div>';
        html += '</div>';

        // Main seating area
        html += this.renderMainSeatingArea(seatMap);

        html += '</div>'; // bus-frame
        html += '</div>'; // bus-layout-container
        html += '</div>'; // seat-map-container

        this.container.innerHTML = html;
    }

    renderMainSeatingArea(seatMap) {
        const { layout_type, rows, columns, seats, has_back_row, aisle_position } = seatMap;

        let html = '<div class="main-seating-area">';

        // Group seats by row
        const seatsByRow = {};
        seats.forEach(seat => {
            if (!seatsByRow[seat.row]) {
                seatsByRow[seat.row] = [];
            }
            seatsByRow[seat.row].push(seat);
        });

        // Render each row
        for (let rowNum = 1; rowNum <= rows; rowNum++) {
            const rowSeats = seatsByRow[rowNum] || [];
            const isBackRow = has_back_row && rowNum === rows;

            html += `<div class="seat-row ${isBackRow ? 'back-row' : 'regular-row'}" data-row="${rowNum}">`;

            if (isBackRow) {
                html += this.renderBackRow(rowSeats);
            } else {
                html += this.renderRegularRow(rowSeats, layout_type, aisle_position);
            }

            html += '</div>';
        }

        html += '</div>';
        return html;
    }

    renderRegularRow(rowSeats, layoutType, aislePosition) {
        let html = '';

        // Sort seats by column
        rowSeats.sort((a, b) => a.column - b.column);

        // Create a map of column positions to seats
        const seatsByColumn = {};
        rowSeats.forEach(seat => {
            seatsByColumn[seat.column] = seat;
        });

        // Get the maximum column number from the layout
        const maxColumns = Math.max(...rowSeats.map(seat => seat.column));

        // Render each column position
        for (let col = 1; col <= maxColumns; col++) {
            if (seatsByColumn[col]) {
                // Render seat
                const seat = seatsByColumn[col];
                const seatClass = this.getSeatClass(seat);
                const seatType = seat.type || 'regular';
                const isWindow = seat.is_window ? 'window-seat' : '';
                const isAisle = seat.is_aisle ? 'aisle-seat' : '';

                html += `<div class="seat ${seatClass} ${seatType} ${isWindow} ${isAisle}"
                              data-seat="${seat.number}"
                              data-row="${seat.row}"
                              data-column="${seat.column}"
                              title="Seat ${seat.number}${seat.is_window ? ' (Window)' : ''}${seat.is_aisle ? ' (Aisle)' : ''}">
                            ${seat.number}
                         </div>`;
            } else {
                // This is an aisle position - render aisle space
                html += '<div class="aisle-space"></div>';
            }
        }

        return html;
    }

    renderBackRow(rowSeats) {
        let html = '<div class="back-row-container">';

        // Sort seats by column
        rowSeats.sort((a, b) => a.column - b.column);

        rowSeats.forEach(seat => {
            const seatClass = this.getSeatClass(seat);

            html += `<div class="seat ${seatClass} back-row-seat"
                          data-seat="${seat.number}"
                          data-row="${seat.row}"
                          data-column="${seat.column}"
                          title="Seat ${seat.number} (Back Row)">
                        ${seat.number}
                     </div>`;
        });

        html += '</div>';
        return html;
    }

    getSeatClass(seat) {
        // Handle both old and new seat number formats
        const seatNumber = seat.number || seat.seat_number;

        if (seat.is_booked) return 'booked';
        if (seat.is_reserved) return 'reserved';
        if (this.selectedSeats.has(seatNumber)) return 'selected';
        return 'available';
    }

    setupSeatClickHandlers() {
        this.container.addEventListener('click', (e) => {
            if (e.target.classList.contains('seat')) {
                this.handleSeatClick(e.target);
            }
        });
    }

    handleSeatClick(seatElement) {
        const seatNumber = seatElement.dataset.seat;
        
        // Don't allow selection of booked or reserved seats
        if (seatElement.classList.contains('booked') || 
            seatElement.classList.contains('reserved')) {
            return;
        }

        if (this.selectedSeats.has(seatNumber)) {
            // Deselect seat
            this.selectedSeats.delete(seatNumber);
            seatElement.classList.remove('selected');
            seatElement.classList.add('available');
        } else {
            // Select seat
            this.selectedSeats.add(seatNumber);
            seatElement.classList.remove('available');
            seatElement.classList.add('selected');
        }

        this.updateSelectedSeatsDisplay();
        this.triggerSeatSelectionEvent();
    }

    updateSeatStatus(seats) {
        seats.forEach(seat => {
            const seatElement = this.container.querySelector(`[data-seat="${seat.number}"]`);
            if (seatElement) {
                // Remove all status classes
                seatElement.classList.remove('available', 'booked', 'reserved', 'selected');
                
                // Add appropriate class
                const newClass = this.getSeatClass(seat);
                seatElement.classList.add(newClass);
                
                // Update internal state
                if (seat.is_booked) {
                    this.bookedSeats.add(seat.number);
                    this.selectedSeats.delete(seat.number);
                    this.reservedSeats.delete(seat.number);
                } else if (seat.is_reserved) {
                    this.reservedSeats.add(seat.number);
                    this.selectedSeats.delete(seat.number);
                    this.bookedSeats.delete(seat.number);
                } else {
                    this.bookedSeats.delete(seat.number);
                    this.reservedSeats.delete(seat.number);
                }
            }
        });
    }

    setupRealtimeListeners() {
        if (typeof window.Echo !== 'undefined') {
            // Listen for seat updates
            window.Echo.channel(`schedule.${this.scheduleId}`)
                .listen('.seat.updated', (e) => {
                    this.handleRealtimeSeatUpdate(e);
                });

            // Listen for booking status updates
            window.Echo.channel(`schedule.${this.scheduleId}`)
                .listen('.booking.status.updated', (e) => {
                    this.handleRealtimeBookingUpdate(e);
                });
        }
    }

    handleRealtimeSeatUpdate(event) {
        const { seat_number, status, user_id } = event;
        const seatElement = this.container.querySelector(`[data-seat="${seat_number}"]`);
        
        if (seatElement) {
            // Remove all status classes
            seatElement.classList.remove('available', 'booked', 'reserved', 'selected');
            
            // Add new status class
            seatElement.classList.add(status);
            
            // Update internal state
            this.updateInternalSeatState(seat_number, status);
            
            // Show notification if it's not the current user's action
            if (user_id !== this.getCurrentUserId()) {
                this.showSeatUpdateNotification(seat_number, status);
            }
        }
        
        // Update available seats counter
        this.updateAvailableSeatsCounter(event.available_seats);
    }

    handleRealtimeBookingUpdate(event) {
        const { booking_id, status, seat_numbers } = event;
        
        // Update seats based on booking status
        seat_numbers.forEach(seatNumber => {
            const seatElement = this.container.querySelector(`[data-seat="${seatNumber}"]`);
            if (seatElement) {
                seatElement.classList.remove('available', 'booked', 'reserved', 'selected');
                
                if (status === 'confirmed') {
                    seatElement.classList.add('booked');
                } else if (status === 'cancelled') {
                    seatElement.classList.add('available');
                }
            }
        });
    }

    updateInternalSeatState(seatNumber, status) {
        // Clear from all sets first
        this.selectedSeats.delete(seatNumber);
        this.bookedSeats.delete(seatNumber);
        this.reservedSeats.delete(seatNumber);
        this.availableSeats.delete(seatNumber);
        
        // Add to appropriate set
        switch (status) {
            case 'booked':
                this.bookedSeats.add(seatNumber);
                break;
            case 'reserved':
                this.reservedSeats.add(seatNumber);
                break;
            case 'available':
                this.availableSeats.add(seatNumber);
                break;
        }
    }

    showSeatUpdateNotification(seatNumber, status) {
        const message = `Seat ${seatNumber} is now ${status}`;
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'seat-update-notification';
        notification.textContent = message;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    updateAvailableSeatsCounter(availableSeats) {
        const counter = document.getElementById('available-seats-counter');
        if (counter) {
            counter.textContent = availableSeats;
        }
    }

    updateSelectedSeatsDisplay() {
        const display = document.getElementById('selected-seats-display');
        if (display) {
            const selectedArray = Array.from(this.selectedSeats);
            display.textContent = selectedArray.length > 0 ? 
                `Selected: ${selectedArray.join(', ')}` : 
                'No seats selected';
        }
    }

    triggerSeatSelectionEvent() {
        const event = new CustomEvent('seatSelectionChanged', {
            detail: {
                selectedSeats: Array.from(this.selectedSeats),
                count: this.selectedSeats.size
            }
        });
        document.dispatchEvent(event);
    }

    getCurrentUserId() {
        // Get current user ID from meta tag or global variable
        const meta = document.querySelector('meta[name="user-id"]');
        return meta ? meta.getAttribute('content') : null;
    }

    getSelectedSeats() {
        return Array.from(this.selectedSeats);
    }

    clearSelection() {
        this.selectedSeats.clear();
        this.container.querySelectorAll('.seat.selected').forEach(seat => {
            seat.classList.remove('selected');
            seat.classList.add('available');
        });
        this.updateSelectedSeatsDisplay();
    }
}

/**
 * Seat Layout Preview Component
 * For displaying static seat layouts without real-time functionality
 */
class SeatLayoutPreview {
    constructor(layout, container) {
        this.layout = layout;
        this.container = container;
    }

    render() {
        const { layout_type, rows, seats, driver_seat, door, has_back_row } = this.layout;

        let html = '<div class="seat-map-container">';

        // Bus layout container
        html += '<div class="bus-layout-container">';
        html += '<div class="bus-frame">';

        // Top section with driver seat and door
        html += '<div class="bus-top-section">';
        html += '<div class="bus-door" title="Front Door">🚪</div>';
        html += '<div class="bus-front-space"></div>';
        html += '<div class="driver-seat" title="Driver">👨‍✈️</div>';
        html += '</div>';

        // Main seating area
        html += this.renderMainSeatingArea();

        html += '</div></div></div>';

        this.container.innerHTML = html;
    }

    renderMainSeatingArea() {
        const { rows, seats, has_back_row, aisle_position } = this.layout;

        let html = '<div class="main-seating-area">';

        // Group seats by row
        const seatsByRow = {};
        seats.forEach(seat => {
            if (!seatsByRow[seat.row]) {
                seatsByRow[seat.row] = [];
            }
            seatsByRow[seat.row].push(seat);
        });

        // Render each row
        const maxRow = Math.max(...seats.map(s => s.row));
        for (let rowNum = 1; rowNum <= maxRow; rowNum++) {
            const rowSeats = seatsByRow[rowNum] || [];
            const isBackRow = has_back_row && rowNum === maxRow;

            html += `<div class="seat-row ${isBackRow ? 'back-row' : 'regular-row'}" data-row="${rowNum}">`;

            if (isBackRow) {
                html += this.renderBackRow(rowSeats);
            } else {
                html += this.renderRegularRow(rowSeats, aisle_position);
            }

            html += '</div>';
        }

        html += '</div>';
        return html;
    }

    renderRegularRow(rowSeats, aislePosition) {
        let html = '';

        rowSeats.sort((a, b) => a.column - b.column);

        let currentColumn = 1;

        rowSeats.forEach(seat => {
            if (currentColumn === aislePosition + 1) {
                html += '<div class="aisle-space"></div>';
            }

            const isWindow = seat.is_window ? 'window-seat' : '';

            html += `<div class="seat available ${isWindow}" title="Seat ${seat.number}">
                        ${seat.number}
                     </div>`;

            currentColumn = seat.column + 1;
        });

        return html;
    }

    renderBackRow(rowSeats) {
        let html = '<div class="back-row-container">';

        rowSeats.sort((a, b) => a.column - b.column);

        rowSeats.forEach(seat => {
            html += `<div class="seat available back-row-seat" title="Seat ${seat.number}">
                        ${seat.number}
                     </div>`;
        });

        html += '</div>';
        return html;
    }
}

// Export for use in other files
window.RealtimeSeatMap = RealtimeSeatMap;
window.SeatLayoutPreview = SeatLayoutPreview;
