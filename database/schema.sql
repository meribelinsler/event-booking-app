CREATE TABLE IF NOT EXISTS events (
                                      id INTEGER PRIMARY KEY AUTOINCREMENT,
                                      name TEXT NOT NULL,
                                      start_time TEXT NOT NULL,
                                      total_seats INTEGER NOT NULL,
                                      CHECK (length(name) BETWEEN 1 AND 100),
    CHECK (total_seats BETWEEN 1 AND 1000)
    );

CREATE TABLE IF NOT EXISTS bookings (
                                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                                        event_id INTEGER NOT NULL,
                                        name TEXT NOT NULL,
                                        email TEXT NOT NULL COLLATE NOCASE,
                                        FOREIGN KEY (event_id) REFERENCES events(id),
    UNIQUE (event_id, email),
    CHECK (length(name) BETWEEN 1 AND 100)
    );