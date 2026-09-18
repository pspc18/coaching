<?php
return substr(
    hash_file(
        'sha256',
        app_path('Services/RuntimeSync.php')
    ),
    7,
    30
);
