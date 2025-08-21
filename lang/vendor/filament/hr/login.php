<?php

return [

    'title' => 'Prijava',

    'heading' => 'Prijavi se na svoj račun',

    'buttons' => [

        'submit' => [
            'label' => 'Prijavi',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'Email',
        ],

        'password' => [
            'label' => 'Lozinka',
        ],

        'remember' => [
            'label' => 'Zapamti me',
        ],
    ],

    'messages' => [
        'failed' => 'Pogrešan email ili lozinka.',
        'throttled' => 'Previše pokušaja. Molimo pokušajte ponovo za :seconds sekundi.',
    ],

];
