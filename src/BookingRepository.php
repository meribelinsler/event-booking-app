
<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/EventRepository.php';

/**
 * Get all bookings for a specific event.
 */
function getBookingsByEvent(int $eventId): array
{
    $pdo = getDatabase();

    $statement = $pdo->prepare(
        'SELECT * FROM bookings
         WHERE event_id = :event_id
         ORDER BY id'
    );

    $statement->execute(['event_id' => $eventId]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get the number of available seats for an event.
 */
function getAvailableSeats(int $eventId): int
{
    $pdo = getDatabase();

    $statement = $pdo->prepare(
        'SELECT e.total_seats - COUNT(b.id) AS available_seats
         FROM events e
         LEFT JOIN bookings b ON e.id = b.event_id
         WHERE e.id = :event_id
         GROUP BY e.id, e.total_seats'
    );

    $statement->execute(['event_id' => $eventId]);

    $availableSeats = $statement->fetchColumn();

    if ($availableSeats === false) {
        throw new InvalidArgumentException('Event not found.');
    }

    return (int) $availableSeats;
}

/**
 * Create a booking for an event.
 */
function createBooking(int $eventId, string $name, string $email): void
{
    $name = trim($name);
    $email = strtolower(trim($email));

    if ($eventId < 1) {
        throw new InvalidArgumentException('Invalid event ID.');
    }

    $nameLength = characterCount($name);

    if ($nameLength < 1 || $nameLength > 100) {
        throw new InvalidArgumentException(
            'Name must be between 1 and 100 characters.'
        );
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException('Invalid email address.');
    }

    $pdo = getDatabase();

    // Prevent concurrent requests from overbooking.
    $pdo->exec('BEGIN IMMEDIATE TRANSACTION');

    try {
        $statement = $pdo->prepare(
            'SELECT * FROM events WHERE id = :id'
        );

        $statement->execute(['id' => $eventId]);
        $event = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            throw new InvalidArgumentException('Event not found.');
        }

        if (strtotime($event['start_time']) <= time()) {
            throw new InvalidArgumentException(
                'This event has already started.'
            );
        }

        // Check whether this email already has a booking.
        $statement = $pdo->prepare(
            'SELECT COUNT(*) FROM bookings
             WHERE event_id = :event_id AND email = :email'
        );

        $statement->execute([
            'event_id' => $eventId,
            'email' => $email
        ]);

        if ((int) $statement->fetchColumn() > 0) {
            throw new InvalidArgumentException(
                'This email already has a booking for this event.'
            );
        }

        // Check whether any seats are available.
        $statement = $pdo->prepare(
            'SELECT COUNT(*) FROM bookings
             WHERE event_id = :event_id'
        );

        $statement->execute(['event_id' => $eventId]);

        $bookingCount = (int) $statement->fetchColumn();

        if ($bookingCount >= (int) $event['total_seats']) {
            throw new InvalidArgumentException('Event is full.');
        }

        // Create the booking.
        $statement = $pdo->prepare(
            'INSERT INTO bookings (event_id, name, email)
             VALUES (:event_id, :name, :email)'
        );

        $statement->execute([
            'event_id' => $eventId,
            'name' => $name,
            'email' => $email
        ]);

        $pdo->exec('COMMIT');

    } catch (Throwable $exception) {
        $pdo->exec('ROLLBACK');
        throw $exception;
    }
}

/**
 * Cancel an existing booking.
 */
function cancelBooking(int $bookingId): void
{
    if ($bookingId < 1) {
        throw new InvalidArgumentException('Invalid booking ID.');
    }

    $pdo = getDatabase();

    // Lock the database while cancelling the booking.
    $pdo->exec('BEGIN IMMEDIATE TRANSACTION');

    try {
        $statement = $pdo->prepare(
            'SELECT e.start_time
             FROM bookings b
             JOIN events e ON b.event_id = e.id
             WHERE b.id = :booking_id'
        );

        $statement->execute(['booking_id' => $bookingId]);

        $startTime = $statement->fetchColumn();

        if ($startTime === false) {
            throw new InvalidArgumentException('Booking not found.');
        }

        if (strtotime($startTime) <= time()) {
            throw new InvalidArgumentException(
                'Cannot cancel a booking after the event has started.'
            );
        }

        // Delete the booking and free up the seat.
        $statement = $pdo->prepare(
            'DELETE FROM bookings WHERE id = :booking_id'
        );

        $statement->execute(['booking_id' => $bookingId]);

        $pdo->exec('COMMIT');

    } catch (Throwable $exception) {
        $pdo->exec('ROLLBACK');
        throw $exception;
    }
}
