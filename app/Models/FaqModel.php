<?php

namespace App\Models;

use CodeIgniter\Model;

class FaqModel extends Model
{
    protected $table = 'faqs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['faq_group', 'question', 'answer', 'faq_order', 'valid'];
    protected $useTimestamps = true;

    public function getByGroup($group = null)
    {
        $builder = $this->where('valid', 1)->orderBy('faq_order', 'ASC');
        if ($group) {
            $builder->where('faq_group', $group);
        }
        return $builder->findAll();
    }

    public function getGrouped()
    {
        $faqs = $this->where('valid', 1)
                     ->orderBy('faq_group ASC, faq_order ASC')
                     ->findAll();

        $grouped = [];
        foreach ($faqs as $faq) {
            $grouped[$faq['faq_group']][] = $faq;
        }
        return $grouped;
    }
}
