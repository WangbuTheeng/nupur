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
        const { rows, columns, seats } = seatMap;
        
        let html = '<div class="seat-map-container">';
        html += '<div class="seat-legend">';
        html += '<div class="legend-item"><span class="seat available"></span> Available</div>';
        html += '<div class="legend-item"><span class="seat selected"></span> Selected</div>';
        html += '<div class="legend-item"><span class="seat booked"></span> Booked</div>';
        html += '<div class="legend-item"><span class="seat reserved"></span> Reserved</div>';
        html += '</div>';
        
        html += `<div class="seat-grid" style="grid-template-columns: repeat(${columns}, 1fr);">`;
        
        seats.forEach(seat => {
            const seatClass = this.getSeatClass(seat);
            html += `<div class="seat ${seatClass}" data-seat="${seat.number}" data-row="${seat.row}" data-column="${seat.column}">
                        ${seat.number}
                     </div>`;
        });
        
        html += '</div></div>';
        
        this.container.innerHTML = html;
    }

    getSeatClass(seat) {
        if (seat.is_booked) return 'booked';
        if (seat.is_reserved) return 'reserved';
        if (this.selectedSeats.has(seat.number)) return 'selected';
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

// Export for use in other files
window.RealtimeSeatMap = RealtimeSeatMap;
