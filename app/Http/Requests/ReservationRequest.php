<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use App\Models\Room;
use App\Rules\AvailableRoom;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReservationRequest extends FormRequest
{
    /**
     * Authorization
     */
    public function authorize(): bool
    {
        return Auth::guard('web')->check();
    }

    /**
     * Validation Rules
     */
    public function rules(): array
    {
        return [
            'room_id' => [
                'required',
                'numeric',
                Rule::exists(Room::class, 'id'),
                new AvailableRoom(
                    $this->input('check_in_at') ?? '',
                    $this->input('check_out_at') ?? '',
                ),
            ],
            'check_in_at' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'check_out_at' => [
                'required',
                'date',
                'after:check_in_at',
            ],
            'guest' => [
                'required',
                'numeric',
                'min:1',
            ],
            'payment_method' => [
                'required',
                'string',
                Rule::enum(PaymentMethod::class),
            ],
        ];
    }

    /**
     * Custom Attribute Names
     */
    public function attributes(): array
    {
        return [
            'room_id' => 'room',
            'check_in_at' => 'check in date',
            'check_out_at' => 'check out date',
            'guest' => 'number of guests',
            'payment_method' => 'payment method',
        ];
    }

    /**
     * Additional Validation
     *
     * @param  mixed  $validator
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $room = Room::find($this->room_id);

            if ($room && $this->guest > $room->capacity) {
                $validator->errors()->add(
                    'guest',
                    "The number of guests must not exceed the room capacity of {$room->capacity}."
                );
            }
        });
    }
}
