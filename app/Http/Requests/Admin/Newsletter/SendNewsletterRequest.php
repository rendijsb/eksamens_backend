<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Newsletter;

use Illuminate\Foundation\Http\FormRequest;

class SendNewsletterRequest extends FormRequest
{
    const SUBJECT = 'subject';
    const CONTENT = 'content';
    const SEND_TO_ALL = 'send_to_all';

    public function rules(): array
    {
        return [
            self::SUBJECT => 'required|string|max:255',
            self::CONTENT => 'required|string',
            self::SEND_TO_ALL => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            self::SUBJECT . '.required' => 'E-pasta temats ir obligāts',
            self::SUBJECT . '.max' => 'E-pasta temats nedrīkst būt garāks par 255 rakstzīmēm',
            self::CONTENT . '.required' => 'E-pasta saturs ir obligāts',
        ];
    }

    public function getSubject(): string
    {
        return $this->input(self::SUBJECT);
    }

    public function getContentText(): string
    {
        return $this->input(self::CONTENT);
    }

    public function getSendToAll(): bool
    {
        return $this->boolean(self::SEND_TO_ALL, true);
    }
}
