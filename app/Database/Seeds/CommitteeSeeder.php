<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CommitteeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['firstname'=>'Ronak','surname'=>'Paw','email'=>'info@lcnl.org','role'=>'PRESIDENT','display_order'=>1,'image'=>'/uploads/committee/ronak-paw.jpg','url'=>null],
            ['firstname'=>'Dhiru','surname'=>'Savani','email'=>'info@lcnl.org','role'=>'VICE PRESIDENT','display_order'=>2,'image'=>'/uploads/committee/dhiru-savani.jpg','url'=>null],
            ['firstname'=>'Jeet','surname'=>'Rughani','email'=>'info@lcnl.org','role'=>'SECRETARY','display_order'=>3,'image'=>'/uploads/committee/jeet-rughani.jpg','url'=>null],
            ['firstname'=>'Rishi','surname'=>'Raja','email'=>'info@lcnl.org','role'=>'ASSISTANT SECRETARY','display_order'=>4,'image'=>'/uploads/committee/rishi-raja.jpg','url'=>null],
            ['firstname'=>'Vishal','surname'=>'Sodha','email'=>'info@lcnl.org','role'=>'TREASURER','display_order'=>5,'image'=>'/uploads/committee/vishal-sodha.jpg','url'=>null],
            ['firstname'=>'Parag','surname'=>'Thacker','email'=>'info@lcnl.org','role'=>'ASSISTANT TREASURER','display_order'=>6,'image'=>'/uploads/committee/parag-thacker.jpg','url'=>null],
            ['firstname'=>'Madhu','surname'=>'Popat','email'=>'info@lcnl.org','role'=>'SOCIAL SECRETARY','display_order'=>7,'image'=>'/uploads/committee/madhu-popat.jpg','url'=>null],
            ['firstname'=>'Sheetal','surname'=>'Barai','email'=>'info@lcnl.org','role'=>'ASSISTANT SOCIAL SECRETARY','display_order'=>8,'image'=>'/uploads/committee/sheetal-barai.jpg','url'=>null],
            ['firstname'=>'Ashok','surname'=>'Dattani','email'=>'info@lcnl.org','role'=>'MEMBERSHIP SECRETARY','display_order'=>9,'image'=>'/uploads/committee/ashok-dattani.jpg','url'=>null],
            ['firstname'=>'Dilip','surname'=>'Manek','email'=>'info@lcnl.org','role'=>'ASSISTANT MEMBERSHIP SECRETARY','display_order'=>10,'image'=>'/uploads/committee/dilip-manek.jpg','url'=>null],
            ['firstname'=>'Amit','surname'=>'Karia','email'=>'info@lcnl.org','role'=>'WEBMASTER','display_order'=>11,'image'=>'/uploads/committee/amit-karia.jpg','url'=>null],
            ['firstname'=>'Dipen','surname'=>'Tanna','email'=>'info@lcnl.org','role'=>'ASSISTANT WEBMASTER','display_order'=>12,'image'=>'/uploads/committee/dipen-tanna.jpg','url'=>null],
            ['firstname'=>'Pratap','surname'=>'Khagram','email'=>'info@lcnl.org','role'=>'LCNL REP for RCT','display_order'=>13,'image'=>'/uploads/committee/pratap-khagram.jpg','url'=>null],
            ['firstname'=>'Dinesh','surname'=>'Shonchhatra','email'=>'info@lcnl.org','role'=>'LCNL REP for RCT','display_order'=>14,'image'=>'/uploads/committee/dinesh-shonchhatra.jpg','url'=>null],
            ['firstname'=>'Meena','surname'=>'Jasani','email'=>'info@lcnl.org','role'=>'IMMEDIATE PAST PRESIDENT','display_order'=>15,'image'=>'/uploads/committee/meena-jasani.jpg','url'=>null],
            ['firstname'=>'Amit','surname'=>'Chandarana','email'=>'info@lcnl.org','role'=>'IMMEDIATE PAST SECRETARY','display_order'=>16,'image'=>'/uploads/committee/amit-chandarana.jpg','url'=>null],
            ['firstname'=>'Sanjay','surname'=>'Rughani','email'=>'info@lcnl.org','role'=>'EXECUTIVE COMMITTEE','display_order'=>17,'image'=>'/uploads/committee/sanjay-rughani.jpg','url'=>null],
            ['firstname'=>'Sudhir','surname'=>'Jagsi','email'=>'info@lcnl.org','role'=>'EXECUTIVE COMMITTEE','display_order'=>18,'image'=>'/uploads/committee/sudhir-jagsi.jpg','url'=>null],
            ['firstname'=>'Jinesh','surname'=>'Chandarana','email'=>'info@lcnl.org','role'=>'EXECUTIVE COMMITTEE','display_order'=>19,'image'=>'/uploads/committee/jinesh-chandarana.jpg','url'=>null],
            ['firstname'=>'Sunny','surname'=>'Chotai','email'=>'info@lcnl.org','role'=>'EXECUTIVE COMMITTEE','display_order'=>20,'image'=>'/uploads/committee/sunny-chotai.jpg','url'=>null],
            ['firstname'=>'Pushpa','surname'=>'Karia','email'=>'info@lcnl.org','role'=>'EXECUTIVE COMMITTEE','display_order'=>21,'image'=>'/uploads/committee/pushpa-karia.jpg','url'=>null],
            ['firstname'=>'Sushma','surname'=>'Khagram','email'=>'info@lcnl.org','role'=>'EXECUTIVE COMMITTEE','display_order'=>22,'image'=>'/uploads/committee/sushma-khagram.jpg','url'=>null],
            ['firstname'=>'Geeta','surname'=>'Nathwani','email'=>'info@lcnl.org','role'=>'EXECUTIVE COMMITTEE','display_order'=>23,'image'=>'/uploads/committee/geeta-nathwani.jpg','url'=>null],
            ['firstname'=>'Kishan','surname'=>'Nathwani','email'=>'info@lcnl.org','role'=>'EXECUTIVE COMMITTEE','display_order'=>24,'image'=>'/uploads/committee/kishan-nathwani.jpg','url'=>null],
            ['firstname'=>'Bhavisha','surname'=>'Thakrar','email'=>'info@lcnl.org','role'=>'EXECUTIVE COMMITTEE','display_order'=>25,'image'=>'/uploads/committee/bhavisha-thakrar.jpg','url'=>null],
            ['firstname'=>'Ritesh','surname'=>'Thakrar','email'=>'info@lcnl.org','role'=>'EXECUTIVE COMMITTEE','display_order'=>26,'image'=>'/uploads/committee/ritesh-thakrar.jpg','url'=>null],
            ['firstname'=>'Prafulla','surname'=>'Chotai','email'=>'info@lcnl.org','role'=>'EXECUTIVE COMMITTEE (CO-OPT)','display_order'=>27,'image'=>'/uploads/committee/prafulla-chotai.jpg','url'=>null],
            ['firstname'=>'Vishali','surname'=>'Sodha','email'=>'info@lcnl.org','role'=>'EXECUTIVE COMMITTEE (CO-OPT)','display_order'=>28,'image'=>'/uploads/committee/vishali-sodha.jpg','url'=>null],
            ['firstname'=>'Vinod','surname'=>'Thakrar','email'=>'info@lcnl.org','role'=>'LCF CHAIRPERSON','display_order'=>29,'image'=>'/uploads/committee/vinod-thakrar.jpg','url'=>null],
            ['firstname'=>'Janu','surname'=>'Kotecha','email'=>'info@lcnl.org','role'=>'LCF SECRETARY','display_order'=>30,'image'=>'/uploads/committee/janu-kotecha.jpg','url'=>null],
            ['firstname'=>'Sailesh','surname'=>'Mehta','email'=>'info@lcnl.org','role'=>'RCT CHAIRPERSON','display_order'=>31,'image'=>'/uploads/committee/sailesh-mehta.jpg','url'=>null],
            ['firstname'=>'Rajeshree','surname'=>'Sodha','email'=>'info@lcnl.org','role'=>'RCT SECRETARY','display_order'=>32,'image'=>'/uploads/committee/rajeshree-sodha.jpg','url'=>null],
            ['firstname'=>'Naina','surname'=>'Raithatha','email'=>'info@lcnl.org','role'=>'MAHILA CHAIRPERSON','display_order'=>33,'image'=>'/uploads/committee/naina-raithatha.jpg','url'=>null],
            ['firstname'=>'Jayshree','surname'=>'Gadhia','email'=>'info@lcnl.org','role'=>'MAHILA SECRETARY','display_order'=>34,'image'=>'/uploads/committee/jayshree-gadhia.jpg','url'=>null],
            ['firstname'=>'Indira','surname'=>'Ondhia','email'=>'info@lcnl.org','role'=>'MAHILA TREASURER','display_order'=>35,'image'=>'/uploads/committee/indira-ondhia.jpg','url'=>null],
            ['firstname'=>'Chandu','surname'=>'Rughani','email'=>'info@lcnl.org','role'=>'SENIOR MEN CHAIRPERSON','display_order'=>36,'image'=>'/uploads/committee/chandu-rughani.jpg','url'=>null],
            ['firstname'=>'Pratibha','surname'=>'Lakhani','email'=>'info@lcnl.org','role'=>'SENIOR LADIES CHAIRPERSON','display_order'=>37,'image'=>'/uploads/committee/pratibha-lakhani.jpg','url'=>null],
            ['firstname'=>'Shyam','surname'=>'Kanani','email'=>'info@lcnl.org','role'=>'YLS CHAIRPERSON','display_order'=>38,'image'=>'/uploads/committee/shyam-kanani.jpg','url'=>null],
            ['firstname'=>'Shrey','surname'=>'Khakhria','email'=>'info@lcnl.org','role'=>'YLS SECRETARY','display_order'=>39,'image'=>'/uploads/committee/shrey-khakhria.jpg','url'=>null],
        ];

        $this->db->table('committee')->insertBatch($data);
    }
}
