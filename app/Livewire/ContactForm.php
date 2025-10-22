<?php

namespace App\Livewire;

use App\Models\Enquiry;
use App\Models\Property;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use App\Mail\PropertyEnquiry as PropertyEnquiryMail;

class ContactForm extends Component
{
    public Property $property;

    public $name = '';
    public $email = '';
    public $message = '';
    public $phone = '';
    public $showSuccess = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'message' => 'required|string',
        'phone' => 'nullable|string|max:20',
    ];

    public function mount(Property $property)
    {
        $this->property = $property;
        $this->message = "Hello, I am interested in the property {$property->title} listed at {$property->formatted_price}. Could you please provide more information?";
    }
    
    public function submit()
    {
        $this->validate();

        try{
            $enquiry = Enquiry::create([
                'property_id' => $this->property->id,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'message' => $this->message,
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),

            ]);

            if($this->property->agent_email)
            {
                Mail::to($this->property->contact_email)->queue(new PropertyEnquiryMail($enquiry));
            }

            $this->showSuccess = true;
            $this->reset(['name', 'email', 'phone', 'message']);

            $this->dispatch('showSuccess');
        } catch (\Exception $e) {
            $this->addError('form', 'There was an error sending your message. Please try again later.');
        }
    }

    public function hideSuccess()
    {
        $this->showSuccess = false;
    }
    
    public function render()
    {
        return view('livewire.contact-form');
    }
}
