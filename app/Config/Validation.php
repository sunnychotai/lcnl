<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the rules that are available.
     *
     * @var list<string>
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    /**
     * Specifies the views that are used to display the errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list' => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Named Rule Sets
    // --------------------------------------------------------------------

    /**
     * Public member registration (mobile-first, minimal fields)
     */
    public array $memberRegister = [
        'first_name' => ['label' => 'First name', 'rules' => 'required|min_length[2]|max_length[100]'],
        'last_name' => ['label' => 'Surname', 'rules' => 'required|min_length[2]|max_length[100]'],
        'email' => ['label' => 'Email', 'rules' => 'required|valid_email|is_unique[members.email]'],
        // E.164-ish: allow optional + and 7–15 digits. Unique only if provided.
        'mobile' => ['label' => 'Mobile', 'rules' => 'permit_empty|regex_match[/^\+?\d{7,15}$/]'],
        'password' => ['label' => 'Password', 'rules' => 'required|min_length[8]'],
        'pass_confirm' => ['label' => 'Confirm Password', 'rules' => 'required|matches[password]'],
        'address1' => ['label' => 'Address Line 1', 'rules' => 'required|min_length[3]|max_length[150]'],
        'address2' => ['label' => 'Address Line 2', 'rules' => 'permit_empty|max_length[150]'],
        'city' => ['label' => 'City', 'rules' => 'required|min_length[2]|max_length[100]'],
        'postcode' => ['label' => 'Postcode', 'rules' => 'required|max_length[12]'],
        'consent' => ['label' => 'Consent', 'rules' => 'required'],
    ];

    /**
     * Custom error messages for member registration
     */
    public array $memberRegister_errors = [
        'email' => [
            'required' => 'Please enter your email address.',
            'valid_email' => 'Please enter a valid email address.',
            'is_unique' => 'That email is already registered.',
        ],
        'mobile' => [
            'regex_match' => 'Please enter a valid mobile number (e.g. +447XXXXXXXXX).',
            'is_unique' => 'That mobile number is already registered.',
        ],
        'password' => [
            'min_length' => 'Use at least 8 characters for your password.',
        ],
        'pass_confirm' => [
            'matches' => 'Passwords do not match.',
        ],
        'first_name' => [
            'required' => 'Please enter your first name.',
        ],
        'last_name' => [
            'required' => 'Please enter your surname.',
        ],
        'consent' => [
            'required' => 'Please tick to consent to LCNL storing your details.',
        ],
        'address1' => [
            'required' => 'Please enter the first line of your address.',
            'min_length' => 'Address Line 1 must be at least 3 characters long.'
        ],
        'city' => [
            'required' => 'Please enter your town or city.',
        ],
        'postcode' => [
            'required' => 'Please enter your postcode.',
        ],

    ];

    public array $memberRegisterV2 = [
        'first_name' => ['label' => 'First name', 'rules' => 'trim|required|min_length[2]|max_length[100]'],
        'last_name' => ['label' => 'Surname', 'rules' => 'trim|required|min_length[2]|max_length[100]'],
        'email' => ['label' => 'Email', 'rules' => 'trim|required|valid_email|is_unique[members.email]'],
        'mobile' => ['label' => 'Mobile', 'rules' => 'permit_empty|regex_match[/^\+?\d{7,15}$/]'],
        'password' => ['label' => 'Password', 'rules' => 'required|min_length[8]'],
        'pass_confirm' => ['label' => 'Confirm Password', 'rules' => 'required|matches[password]'],

        'address1' => ['label' => 'Address Line 1', 'rules' => 'trim|required|min_length[3]|max_length[150]'],
        'address2' => ['label' => 'Address Line 2', 'rules' => 'trim|permit_empty|max_length[150]'],
        'city' => ['label' => 'City/Town', 'rules' => 'trim|required|min_length[2]|max_length[100]'],
        'postcode' => ['label' => 'Postcode', 'rules' => 'trim|required|max_length[12]'],

        'date_of_birth' => ['label' => 'Date of birth', 'rules' => 'permit_empty|valid_date[Y-m-d]'],
        'gender' => ['label' => 'Gender', 'rules' => 'permit_empty|in_list[male,female,other,prefer_not_to_say]'],

        // Consent
        'consent' => ['label' => 'Consent', 'rules' => 'required|in_list[1]'],
    ];


    // --------------------------------------------------------------------
    // (Add more named rule sets here as the project grows)
    // --------------------------------------------------------------------
}
