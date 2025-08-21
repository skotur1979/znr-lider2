<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'i :count više',
        ],

        'messages' => [
            'copied' => 'Kopirano',
        ],

    ],

    'fields' => [

        'search_query' => [
            'label' => 'Pretraži',
            'placeholder' => 'Pretraži',
        ],

    ],

    'pagination' => [

        'label' => 'Paginacija',

        'overview' => 'Prikazujem :first do :last od :total rezultata',

        'fields' => [

            'records_per_page' => [

                'label' => 'po stranici',

                'options' => [
                    'all' => 'Sve',
                ],

            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Idi na stranicu :page',
            ],

            'next' => [
                'label' => 'Sljedeće',
            ],

            'previous' => [
                'label' => 'Prethodno',
            ],

        ],

    ],

    'buttons' => [

        'disable_reordering' => [
            'label' => 'Završi ',
        ],

        'enable_reordering' => [
            'label' => 'Promijeni redosljed zapisa',
        ],

        'filter' => [
            'label' => 'Filtriraj',
        ],

        'open_actions' => [
            'label' => 'Otvori akcije',
        ],

        'toggle_columns' => [
            'label' => 'Toggle columns',
        ],

    ],

    'empty' => [
        'heading' => 'Nema zapisa',
    ],

    'filters' => [

        'buttons' => [

            'remove' => [
                'label' => 'Obriši filter',
            ],

            'remove_all' => [
                'label' => 'Ukloni sve filtere',
                'tooltip' => 'Ukloni sve filtere',
            ],

            'reset' => [
                'label' => 'Poništi filter',
            ],

        ],

        'indicator' => 'Aktivni filtri',

        'multi_select' => [
            'placeholder' => 'Sve',
        ],

        'select' => [
            'placeholder' => 'Sve',
        ],

        'trashed' => [

            'label' => 'Aktivni/neaktivni zapisi',

            'only_trashed' => 'Deaktivirani',

            'with_trashed' => 'Sve',

            'without_trashed' => 'Aktivni',

        ],

    ],

    'reorder_indicator' => 'Drag and drop the records into order.',

    'selection_indicator' => [

        'selected_count' => '1 zapis odabran.|:count zapisa odabrano.',

        'buttons' => [

            'select_all' => [
                'label' => 'Odaberi sve :count',
            ],

            'deselect_all' => [
                'label' => 'Poništi odabir',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sortiraj prema',
            ],

            'direction' => [

                'label' => 'Smjer sortiranja',

                'options' => [
                    'asc' => 'Uzlazno',
                    'desc' => 'Silazno',
                ],

            ],

        ],

    ],

];
