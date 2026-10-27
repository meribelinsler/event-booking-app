
<?php

require_once __DIR__ . '/../src/BookingRepository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

try {
    $bookingId = filter_var(
        $_POST['booking_id'] ?? null,
        FILTER_VALIDATE_INT
    );

    if ($bookingId === false || $bookingId === null || $bookingId < 1) {
        throw new InvalidArgumentException('Invalid booking ID.');
    }

    cancelBooking($bookingId);

    header('Location: index.php?message=' . urlencode(
            'Booking cancelled successfully!'
        ));
    exit;

} catch (InvalidArgumentException $exception) {
    header('Location: index.php?error=' . urlencode($exception->getMessage()));
    exit;

} catch (Throwable $exception) {
    error_log($exception->getMessage());

    header('Location: index.php?error=' . urlencode(
            'Something went wrong while cancelling the booking.'
        ));
    exit;
}
