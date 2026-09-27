<?php
namespace App\Http\Requests\Booking;
use Illuminate\Foundation\Http\FormRequest;

final class SearchTripsRequest extends FormRequest {
    public function authorize(): bool { return true; }

    public function rules(): array {
        return [
            'from' => ['required','ulid','exists:locations,id'],
            'to' => ['required','ulid','different:from','exists:locations,id'],
            'date' => ['required','date_format:Y-m-d','after_or_equal:today'],
            'passengers' => ['sometimes','integer','min:1','max:6'],
        ];
    }
}
