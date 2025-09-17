<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FaqModel;

class FaqAdmin extends BaseController
{
    private $faqGroups = [
    'Bereavement',
    'Membership',
    'Events',
    'Committee',
    'YLA & Youth',
    'DLC & RCT'
];
    public function index()
{
    $faqModel = new \App\Models\FaqModel();

    // Fetch all FAQs ordered by group + order
    $faqs = $faqModel->orderBy('faq_group ASC, faq_order ASC')->findAll();

    // Group by faq_group
    $groupedFaqs = [];
    foreach ($faqs as $faq) {
        $groupedFaqs[$faq['faq_group']][] = $faq;
    }

    return view('admin/content/faqs/index', [
        'groupedFaqs' => $groupedFaqs
    ]);
}


    public function create()
{
    return view('admin/content/faqs/create', [
        'groups' => $this->faqGroups
    ]);
}


    public function store()
    {
        $faqModel = new FaqModel();
        $data = $this->request->getPost();

        if (! $faqModel->insert($data)) {
            return redirect()->back()->with('errors', $faqModel->errors())->withInput();
        }

        return redirect()->to('/admin/content/faqs')->with('success', 'FAQ added successfully.');
    }

    public function edit($id)
{
    $faqModel = new \App\Models\FaqModel();
    $data['faq'] = $faqModel->find($id);
    $data['groups'] = $this->faqGroups;

    return view('admin/content/faqs/edit', $data);
}


    public function update($id)
    {
        $faqModel = new FaqModel();
        $data = $this->request->getPost();

        if (! $faqModel->update($id, $data)) {
            return redirect()->back()->with('errors', $faqModel->errors())->withInput();
        }

        return redirect()->to('/admin/content/faqs')->with('success', 'FAQ updated successfully.');
    }

    public function delete($id)
    {
        $faqModel = new FaqModel();
        $faqModel->delete($id);

        return redirect()->to('/admin/content/faqs')->with('success', 'FAQ deleted.');
    }

public function reorder()
{
    $faqModel = new \App\Models\FaqModel();
    $order = $this->request->getPost('order');
    $group = $this->request->getPost('group');

    if ($order && is_array($order)) {
        foreach ($order as $position => $id) {
            $faqModel->update($id, [
                'faq_order' => $position + 1,
                'faq_group' => $group // keep consistency
            ]);
        }
    }

    return $this->response->setJSON(['status' => 'success', 'group' => $group, 'order' => $order]);
}


}
