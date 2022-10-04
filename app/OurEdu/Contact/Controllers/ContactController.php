<?php

namespace App\OurEdu\Contact\Controllers;



use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Contact\Repository\ContactInterface;

class ContactController extends BaseController
{
    private $module;
    private $contactRepo;
    private $title;
    private $parent;


    public function __construct(ContactInterface $contactRepo
    )
    {
        $this->module = 'contact';
        $this->title = trans('contact.contact');
        $this->parent = ParentEnum::ADMIN;
        $this->contactRepo = $contactRepo;
    }

    public function getIndex()
    {
        $data['rows'] = $this->contactRepo->paginate();
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function view($id) {

        $data['row'] = $this->contactRepo->findOrFail($id);
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.view', $data);
    }
}

