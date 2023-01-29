<?php

function printNonArchivedMessageSubjects(string $source): void {
    $messages = json_decode(file_get_contents($source), true);
    echo '<ul>';
    foreach ($messages as $message) {
        if ($message['is_archived']) {
            continue;
        }
        echo '<li';
        if ($message['is_read']) {
            echo ' color="grey"';
        }
        echo '>' . htmlspecialchars($message['subject']) . '</li>';
    }
    echo '</ul>';
}

printNonArchivedMessageSubjects('messages.json');