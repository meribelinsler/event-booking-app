
<?php

require_once __DIR__ . '/../src/EventRepository.php';
require_once __DIR__ . '/../src/BookingRepository.php';

$events = getAllEvents();

$success = isset($_GET['success']);

$message = isset($_GET['message']) && is_string($_GET['message'])
        ? $_GET['message']
        : '';

$error = isset($_GET['error']) && is_string($_GET['error'])
        ? $_GET['error']
        : '';

function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Booking App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<main class="container">

    <h1>Event Booking App</h1>
    <p>Create events and manage bookings.</p>

    <?php if ($success): ?>
        <div class="message success">
            Event created successfully!
        </div>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="message success">
            <?= escapeHtml($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message error">
            <?= escapeHtml($error) ?>
        </div>
    <?php endif; ?>

    <section class="card">
        <h2>Create an Event</h2>

        <form action="create-event.php" method="POST">

            <label for="name">Event name</label>
            <input
                type="text"
                id="name"
                name="name"
                minlength="1"
                maxlength="100"
                required
            >

            <label for="start_time">Start date and time</label>
            <input
                type="datetime-local"
                id="start_time"
                name="start_time"
                required
            >

            <label for="total_seats">Number of seats</label>
            <input
                type="number"
                id="total_seats"
                name="total_seats"
                min="1"
                max="1000"
                required
            >

            <button type="submit">Create Event</button>
        </form>
    </section>

    <section>
        <h2>Events</h2>

        <?php if (empty($events)): ?>
            <p>No events available yet.</p>
        <?php endif; ?>

        <?php foreach ($events as $event): ?>

            <?php
            $eventId = (int) $event['id'];
            $hasStarted = strtotime($event['start_time']) <= time();
            $bookings = getBookingsByEvent($eventId);
            $availableSeats = getAvailableSeats($eventId);
            ?>

            <div class="card">

                <h3><?= escapeHtml($event['name']) ?></h3>

                <p>
                    <strong>Start:</strong>
                    <?= escapeHtml($event['start_time']) ?>
                </p>

                <p>
                    <strong>Total seats:</strong>
                    <?= (int) $event['total_seats'] ?>
                </p>

                <p>
                    <strong>Available seats:</strong>
                    <?= $availableSeats ?>
                </p>

                <?php if ($hasStarted): ?>

                    <span class="status ended">Event started / ended</span>

                <?php elseif ($availableSeats === 0): ?>

                    <span class="status ended">Fully booked</span>

                <?php else: ?>

                    <span class="status available">Upcoming</span>

                    <h4>Book a seat</h4>

                    <form action="book.php" method="POST">

                        <input
                            type="hidden"
                            name="event_id"
                            value="<?= $eventId ?>"
                        >

                        <label>Name</label>
                        <input
                            type="text"
                            name="name"
                            maxlength="100"
                            required
                        >

                        <label>Email</label>
                        <input
                            type="email"
                            name="email"
                            required
                        >

                        <button type="submit">Book seat</button>

                    </form>

                <?php endif; ?>

                <h4>Bookings (<?= count($bookings) ?>)</h4>

                <?php if (empty($bookings)): ?>
                    <p>No bookings yet.</p>
                <?php endif; ?>

                <?php foreach ($bookings as $booking): ?>

                    <div class="booking-item">

                        <span>
                            <?= escapeHtml($booking['name']) ?>
                            (<?= escapeHtml($booking['email']) ?>)
                        </span>

                        <?php if (!$hasStarted): ?>

                            <form action="cancel.php" method="POST">

                                <input
                                    type="hidden"
                                    name="booking_id"
                                    value="<?= (int) $booking['id'] ?>"
                                >

                                <button type="submit" class="cancel-button">
                                    Cancel
                                </button>

                            </form>

                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endforeach; ?>

    </section>

</main>

</body>
</html>
