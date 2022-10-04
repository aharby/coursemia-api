<?php


namespace App\OurEdu\Contact\Repository;



use App\OurEdu\Contact\Contact;

class ContactRepository implements ContactInterface
{
    private $contact;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function get()
    {
        return $this->contact->get();
    }

    public function create($data)
    {
        return $this->contact->create($data);
    }

    public function paginate($perPage = 10)
    {
        return $this->contact->paginate($perPage);
    }

    public function findOrFail($id){

        return $this->contact->findOrFail($id);
    }


}
