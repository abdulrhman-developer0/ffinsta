<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'accepted'             => 'The :attribute field must be accepted.',
    'alpha'                => 'The :attribute field must only contain letters.',
    'alpha_dash'           => 'The :attribute field must only contain letters, numbers, dashes, and underscores.',
    'alpha_num'            => 'The :attribute field must only contain letters and numbers.',
    'array'                => 'The :attribute field must be an array.',
    'boolean'              => 'The :attribute field must be true or false.',
    'confirmed'            => 'The :attribute field confirmation does not match.',
    'date'                 => 'The :attribute field must be a valid date.',
    'email'                => 'The :attribute field must be a valid email address.',
    'exists'               => 'The selected :attribute is invalid.',
    'integer'              => 'The :attribute field must be an integer.',
    'max'                  => [
        'numeric' => 'The :attribute field must not be greater than :max.',
        'file'    => 'The :attribute field must not be greater than :max kilobytes.',
        'string'  => 'The :attribute field must not be greater than :max characters.',
        'array'   => 'The :attribute field must not have more than :max items.',
    ],
    'min'                  => [
        'numeric' => 'The :attribute field must be at least :min.',
        'file'    => 'The :attribute field must be at least :min kilobytes.',
        'string'  => 'The :attribute field must be at least :min characters.',
        'array'   => 'The :attribute field must have at least :min items.',
    ],
    'numeric'              => 'The :attribute field must be a number.',
    'required'             => 'The :attribute field is required.',
    'string'               => 'The :attribute field must be a string.',
    'unique'               => 'The :attribute has already been taken.',
    'url'                  => 'The :attribute field must be a valid URL.',

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    'attributes' => [],

];
