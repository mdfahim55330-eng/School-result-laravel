<?php

use Illuminate\\Support\\Facades\\Artisan;

Artisan::command('about:school-result', function () {
    $this->info('School Result Management System - Laravel + MySQL');
});
