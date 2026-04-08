<?php

if (empty($statsInfo)) {
    echo "<li>" . Label::getLabel('LBL_NO_RECORD_FOUND') . "</li>";
    exit;
}
foreach ($statsInfo as $row) {
    echo '<li>' . $row['language'] . ' <span class="count">' . $row['totalsold'] . ' sold</span></li>';
}
