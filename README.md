# Event Booking App

A simple web application for creating events and managing seat bookings.

## Technologies Used

- **PHP 8.2** – for backend logic and server-side validation.
- **SQLite** – for persistent data storage without requiring a separate database server.
- **HTML and CSS** – for the user interface.
- **PDO** – for secure database access using prepared statements.

I chose these technologies because they are lightweight, easy to set up, and suitable for a small web application.

## How to Run

### Requirements

- PHP 8.2 or newer
- PHP SQLite extensions (`pdo_sqlite` and `sqlite3`) enabled

No Docker, Composer, or separate database server is required.

### Setup

1. Clone the repository:

   ```bash
   git clone https://github.com/meribelinsler/event-booking-app.git
   cd event-booking-app
   ```

2. Initialize the database:

   ```bash
   php database/init.php
   ```

3. Start the PHP development server:

   ```bash
   php -S localhost:8000 -t public
   ```

4. Open http://localhost:8000 in your browser.

The SQLite database is created locally and is excluded from Git.

## Features

- Create events with a name, start date and time, and seat capacity.
- Display all events sorted by start time.
- Show the number of available seats for each event.
- Book seats using a name and email address.
- Prevent duplicate bookings with the same email address for an event, regardless of letter case.
- Prevent overbooking using SQLite transactions.
- View and cancel existing bookings.
- Prevent bookings and cancellations after an event has started.
- Validate user input on the server and display clear error messages.

## Assumptions and Limitations

- The application is intended for a single organizer, so no user accounts or authentication are required.
- Each booking reserves exactly one seat.
- Dates and times use the `Europe/Tallinn` timezone.
- Past events remain visible but cannot be booked or have bookings cancelled.
- All required functionality has been implemented.

## AI Usage

I used **ChatGPT (OpenAI, GPT-6)** to help plan the project, explore suitable programming languages, choose a development environment, and decide on a database and project structure.

I also used AI assistance to review parts of the code and discuss improvements to input validation, error handling, and booking logic.

I reviewed the suggestions, made adjustments where necessary, and manually checked that the application worked according to the requirements.
