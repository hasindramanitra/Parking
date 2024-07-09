<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ContactDTO
{

    #[Assert\NotBlank()]
    #[Assert\Length(min: 3, max: 50)]
    public string $firstname = '';


    #[Assert\NotBlank()]
    #[Assert\Length(min: 3, max: 50)]
    public string $lastname = '';

    #[Assert\NotBlank()]
    #[Assert\Length(min: 3, max: 50)]
    public string $subject = '';
    
    #[Assert\NotBlank()]
    #[Assert\Email()]
    public string $email = '';

    #[Assert\NotBlank()]
    #[Assert\Length(min: 10, max: 1000)]
    public string $message = '';
}