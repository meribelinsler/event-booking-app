
<?php

require_once __DIR__ . '/../config/database.php';

/**
 * Count UTF-8 characters instead of bytes.
 */
function characterCount(string $value): int
{
    $count = preg_match_all('/./us', $value);

    if ($count === false) {
        throw new InvalidArgumentException('Invalid text encoding.');
    }

    return $count;
}

/**
 * Get all events sorted by start time.
 */
function getAllEvents(): array
{
    $pdo = getDatabase();

    $statement = $pdo->query(
        'SELECT * FROM events ORDER BY start_time ASC'
    );

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Create a new event.
 */
function createEvent(string $name, string $startTime, int $totalSeats): void
{
    $name = trim($name);

    $nameLength = characterCount($name);

    if ($nameLength < 1 || $nameLength > 100) {
        throw new InvalidArgumentException(
            'Event name must be between 1 and 100 characters.'
        );
    }

    if ($totalSeats < 1 || $totalSeats > 1000) {
        throw new InvalidArgumentException(
            'Number of seats must be between 1 and 1000.'
        );
    }

    $date = DateTimeImmutable::createFromFormat(
        '!Y-m-d\TH:i',
        $startTime
    );

    $errors = DateTimeImmutable::getLastErrors();

    if (
        !$date ||
        ($errors !== false && (
                $errors['warning_count'] > 0 ||
                $errors['error_count'] > 0
            ))
    ) {
        throw new InvalidArgumentException('Invalid event date.');
    }

    $now = new DateTimeImmutable();

    if ($date <= $now) {
        throw new InvalidArgumentException(
            'Event must be in the future.'
        );
    }

    if ($date > $now->modify('+1 year')) {
        throw new InvalidArgumentException(
            'Event cannot be more than one year ahead.'
        );
    }

    $pdo = getDatabase();

    $statement = $pdo->prepare(
        'INSERT INTO events (name, start_time, total_seats)
         VALUES (:name, :start_time, :total_seats)'
    );

    $statement->execute([
        'name' => $name,
        'start_time' => $date->format('Y-m-d H:i:s'),
        'total_seats' => $totalSeats
    ]);
}
