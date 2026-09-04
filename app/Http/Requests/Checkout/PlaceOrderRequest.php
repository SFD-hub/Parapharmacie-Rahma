<?php

namespace App\Http\Requests\Checkout;

use App\Enums\PaymentMethod;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'address_id' => [
                'required',
                'integer',
                $this->user()
                    ? Rule::exists('addresses', 'id')->where('user_id', $this->user()->id)
                    : Rule::exists('addresses', 'id')->where('guest_token', Order::currentGuestToken()),
            ],
            'payment_method' => [
                'required',
                // "points" is reserved for gift redemption orders, not selectable at regular checkout.
                Rule::in(collect(PaymentMethod::cases())->reject(fn (PaymentMethod $m) => $m === PaymentMethod::Points)->map->value),
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
            'points_used' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
