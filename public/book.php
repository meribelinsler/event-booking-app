
<?php

require_once __DIR__ . '/../src/BookingRepository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

try {
    $eventId = filter_var(
        $_POST['event_id'] ?? null,
        FILTER_VALIDATE_INT
    );

    if ($eventId === false || $eventId === null || $eventId < 1) {
        throw new InvalidArgumentException('Invalid event ID.');
    }

    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';

    if (!is_string($name) || !is_string($email)) {
        throw new InvalidArgumentException('Invalid booking details.');
    }

    createBooking($eventId, $name, $email);

    header('Location: index.php?message=' . urlencode(
            'Booking created successfully!'
        ));
    exit;

} catch (InvalidArgumentException $exception) {
    header('Location: index.php?error=' . urlencode($exception->getMessage()));
    exit;

} catch (Throwable $exception) {
    error_log($exception->getMessage());

    header('Location: index.php?error=' . urlencode(
            'Something went wrong while creating the booking.'
        ));
    exit;
}
