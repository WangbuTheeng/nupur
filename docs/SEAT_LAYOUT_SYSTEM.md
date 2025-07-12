# BookNGO Dynamic Seat Layout System

## Overview

The BookNGO Dynamic Seat Layout System provides a comprehensive solution for managing bus seat configurations that closely match real-world bus designs. The system supports multiple layout types, includes driver seat and front door positioning, and features a continuous back row design.

## Key Features

### 🚌 Real-World Bus Design
- **Driver Seat**: Positioned at top-right corner (as in actual buses)
- **Front Door**: Located at top-left side for passenger entry
- **Continuous Back Row**: Full-width seats spanning the entire back of the bus
- **Aisle Positioning**: Proper aisle spacing between seat sections

### 🎯 Layout Types Supported

#### 2x2 Standard Layout
- **Configuration**: 2 seats | aisle | 2 seats
- **Recommended Seat Counts**: 27, 31, 35
- **Back Row Seats**: 5 continuous seats
- **Best For**: Standard city buses, most common configuration

#### 2x1 Compact Layout
- **Configuration**: 2 seats | aisle | 1 seat
- **Recommended Seat Counts**: 21, 25, 29
- **Back Row Seats**: 4 continuous seats
- **Best For**: Smaller buses, rural routes, cost-effective operations

#### 3x2 Large Layout
- **Configuration**: 3 seats | aisle | 2 seats
- **Recommended Seat Counts**: 35, 39, 45
- **Back Row Seats**: 6 continuous seats
- **Best For**: Large buses, long-distance routes, high-capacity needs

### 🎨 Visual Features
- **Color-Coded Seats**: Green (Available), Red (Booked), Yellow (Selected), Grey (Blocked)
- **Window Seat Indicators**: Visual markers for window seats
- **Real-time Updates**: Live seat availability changes
- **Responsive Design**: Works on desktop, tablet, and mobile devices

## Technical Implementation

### Database Structure

The seat layout is stored as JSON in the `buses.seat_layout` column with the following structure:

```json
{
  "layout_type": "2x2",
  "total_seats": 31,
  "rows": 7,
  "columns": 5,
  "aisle_position": 2,
  "has_back_row": true,
  "back_row_seats": 5,
  "driver_seat": {
    "position": "top-right",
    "row": 0,
    "column": 5
  },
  "door": {
    "position": "top-left",
    "row": 0,
    "column": 0
  },
  "seats": [
    {
      "number": "A1",
      "row": 1,
      "column": 1,
      "type": "regular",
      "is_window": true,
      "is_aisle": false,
      "is_available": true,
      "side": "left"
    }
  ]
}
```

### Core Components

#### 1. SeatLayoutService
**Location**: `app/Services/SeatLayoutService.php`

Main service class that handles:
- Seat layout generation
- Layout validation
- Configuration management
- Seat numbering logic

**Key Methods**:
- `generateSeatLayout($totalSeats, $layoutType, $hasBackRow)`
- `validateLayout($totalSeats, $layoutType, $hasBackRow)`
- `getLayoutTypes()` - Returns available layout types
- `getRecommendedSeatCounts()` - Returns recommended configurations

#### 2. Enhanced Bus Model
**Location**: `app/Models/Bus.php`

**New Methods**:
- `generateDynamicSeatLayout($layoutType, $hasBackRow)`
- `updateSeatLayout($layoutType, $hasBackRow)`
- `getSeatLayoutWithBookings($scheduleId)`
- `validateSeatLayout($layoutType, $hasBackRow)`

#### 3. Frontend Components

**JavaScript**: `resources/js/realtime-seat-map.js`
- Enhanced seat map rendering
- Driver seat and door visualization
- Back row handling
- Real-time updates

**CSS**: `resources/css/seat-map.css`
- Bus frame styling
- Seat state colors
- Responsive design
- Accessibility features

### API Endpoints

#### Seat Layout Management
- `POST /operator/buses/{bus}/seat-layout` - Update bus seat layout
- `POST /operator/buses/preview-seat-layout` - Preview layout configuration

#### Real-time Seat Data
- `GET /api/schedules/{schedule}/seat-map` - Get seat map with booking status
- `POST /api/schedules/{schedule}/reserve-seats` - Reserve seats temporarily
- `DELETE /api/schedules/{schedule}/release-seats` - Release reserved seats

## Usage Guide

### For Operators

#### Creating a New Bus
1. Navigate to **Buses > Add New Bus**
2. Fill in basic bus information
3. Select **Total Seats** (recommended: 27, 31, 35, or 39)
4. Choose **Layout Type** (2x2, 2x1, or 3x2)
5. Enable/disable **Back Row** option
6. Save the bus - seat layout is generated automatically

#### Updating Seat Layout
1. Go to **Buses > [Select Bus] > Edit**
2. Modify seat count or layout type
3. Save changes - layout will be regenerated if needed

#### Viewing Seat Layout
1. Go to **Buses > [Select Bus]**
2. View the **Seat Layout** section
3. See real-time layout preview with driver seat and door

### For Customers

#### Booking Seats
1. Search and select a route
2. Choose travel date and schedule
3. View the interactive seat map with:
   - Driver seat (top-right)
   - Front door (top-left)
   - Available seats (green)
   - Booked seats (red)
   - Window seat indicators
4. Click seats to select/deselect
5. Proceed with booking

## Configuration Options

### Layout Type Selection
```php
// Available layout types
$layoutTypes = [
    '2x2' => '2x2 (Standard)',
    '2x1' => '2x1 (Compact)', 
    '3x2' => '3x2 (Large)'
];
```

### Seat Count Recommendations
```php
$recommendations = [
    '2x2' => [27, 31, 35],
    '2x1' => [21, 25, 29],
    '3x2' => [35, 39, 45]
];
```

### Validation Rules
- Minimum seats: 10
- Maximum seats: 60
- Layout type must be valid
- Seat count must be feasible for selected layout

## Migration and Backward Compatibility

### Automatic Migration
The system includes a migration (`2025_07_12_000001_update_bus_seat_layouts_to_new_format.php`) that:
- Updates existing buses to new format
- Preserves existing seat data
- Determines optimal layout type based on seat count
- Maintains backward compatibility

### Legacy Support
- Old seat layout format is still supported
- API responses handle both old and new formats
- Gradual migration without service interruption

## Testing

### Unit Tests
**Location**: `tests/Unit/SeatLayoutServiceTest.php`

Tests cover:
- Layout generation for all types
- Seat numbering accuracy
- Window/aisle seat identification
- Back row generation
- Validation logic
- Configuration methods

### Running Tests
```bash
php artisan test tests/Unit/SeatLayoutServiceTest.php
```

## Demo and Examples

### Interactive Demo
Visit `/demo/seat-layouts` to see:
- All layout types in action
- Interactive layout builder
- Real-time configuration changes
- Feature demonstrations

### Example Configurations

#### Small Bus (25 seats, 2x1 layout)
- 7 regular rows with 3 seats each
- 1 back row with 4 seats
- Compact and efficient

#### Standard Bus (31 seats, 2x2 layout)
- 6 regular rows with 4 seats each
- 1 back row with 5 seats
- Most popular configuration

#### Large Bus (39 seats, 3x2 layout)
- 6 regular rows with 5 seats each
- 1 back row with 6 seats
- High capacity for busy routes

## Troubleshooting

### Common Issues

1. **Seat layout not displaying**
   - Check if CSS file is loaded
   - Verify JavaScript is not blocked
   - Ensure seat_layout data exists

2. **Layout generation fails**
   - Verify seat count is within limits
   - Check layout type is valid
   - Ensure back row configuration is correct

3. **Real-time updates not working**
   - Check WebSocket connection
   - Verify broadcasting is configured
   - Ensure user permissions are correct

### Support
For technical support or feature requests, please contact the development team or create an issue in the project repository.
