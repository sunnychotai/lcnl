<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class MemberSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create('en_GB'); // UK-based addresses, postcodes, etc.

        $passwordHash = password_hash('aaaaaaaa', PASSWORD_DEFAULT);
        $statuses = ['pending', 'active', 'disabled'];

        for ($i = 0; $i < 50; $i++) {
            // Random LCNL-style Gujarati / Indian first + last names
            $firstNames = ['Amit', 'Bhavna', 'Chetan', 'Deepa', 'Rakesh', 'Sunita', 'Kaajal', 'Paresh', 'Rajesh', 'Hina', 'Nimesh', 'Manisha', 'Suresh', 'Kiran', 'Nirali', 'Rita', 'Jiten', 'Anita', 'Meera', 'Vikesh', 'Ravi', 'Poonam', 'Harsha', 'Dipesh', 'Nayna', 'Mahesh', 'Smita', 'Hemal', 'Rekha', 'Kishor', 'Kalpesh', 'Pushpa', 'Lina', 'Dinesh', 'Jasmin', 'Vipul', 'Darshna', 'Mehul', 'Nalini', 'Hitesh', 'Priti'];
            $lastNames  = ['Chotai', 'Patel', 'Shah', 'Lakhani', 'Varsani', 'Mistry', 'Kotecha', 'Kansara', 'Gohil', 'Bhudia', 'Pujara', 'Savani', 'Brahmbhatt', 'Soni', 'Dodhia', 'Kakadia', 'Ruparelia', 'Panchal', 'Chauhan', 'Bhetariya'];

            $firstName = $faker->randomElement($firstNames);
            $lastName  = $faker->randomElement($lastNames);
            $email     = strtolower($firstName . '.' . $lastName . $faker->numberBetween(1, 99)) . '@example.com';
            $mobile    = '+44' . $faker->numberBetween(7400000000, 7999999999);
            $status    = $faker->randomElement($statuses);

            $verifiedAt = null;
            $consentAt  = null;
            $verifiedBy = null;
            $lastLogin  = null;

            if ($status === 'active') {
                $verifiedAt = $faker->dateTimeBetween('-2 years', 'now');
                $consentAt  = $faker->dateTimeBetween('-2 years', 'now');
                $verifiedBy = $faker->numberBetween(1, 5);
                $lastLogin  = $faker->dateTimeBetween('-6 months', 'now');
            } elseif ($status === 'disabled') {
                $verifiedAt = $faker->dateTimeBetween('-3 years', '-1 years');
            }

            $data = [
                'first_name'   => $firstName,
                'last_name'    => $lastName,
                'email'        => $email,
                'mobile'       => $mobile,
                'address1'     => $faker->buildingNumber . ' ' . $faker->streetName,
                'address2'     => '',
                'city'         => 'London',
                'postcode'     => $faker->postcode,
                'password_hash' => $passwordHash,
                'status'       => $status,
                'verified_at'  => $verifiedAt ? $verifiedAt->format('Y-m-d H:i:s') : null,
                'verified_by'  => $verifiedBy,
                'consent_at'   => $consentAt ? $consentAt->format('Y-m-d H:i:s') : null,
                'last_login'   => $lastLogin ? $lastLogin->format('Y-m-d H:i:s') : null,
                'source'       => $faker->randomElement(['web', 'admin', 'import']),
                'created_at'   => $faker->dateTimeBetween('-3 years', 'now')->format('Y-m-d H:i:s'),
                'updated_at'   => $faker->dateTimeBetween('-1 years', 'now')->format('Y-m-d H:i:s'),
                'deleted_at'   => null,
            ];

            $this->db->table('members')->insert($data);
        }
    }
}
