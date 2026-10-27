
<?php

require_once __DIR__ . '/../src/EventRepository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

try {
    $name = $_POST['name'] ?? '';
    $startTime = $_POST['start_time'] ?? '';
    $seatsInput = $_POST['total_seats'] ?? null;

    if (!is_string($name) || !is_string($startTime)) {
        throw new InvalidArgumentException('Invalid event details.');
    }

    $totalSeats = filter_var($seatsInput, FILTER_VALIDATE_INT);

    if ($totalSeats === false || $totalSeats === null) {
        throw new InvalidArgumentException(
            'Number of seats must be a whole number.'
        );
    }

    createEvent($name, $startTime, $totalSeats);

    header('Location: index.php?success=1');
    exit;

} catch (InvalidArgumentException $exception) {
    header('Location: index.php?error=' . urlencode($exception->getMessage()));
    exit;

} catch (Throwable $exception) {
    error_log($exception->getMessage());

    header('Location: index.php?error=' . urlencode(
            'Something went wrong while creating the event.'
        ));
    exit;
}
