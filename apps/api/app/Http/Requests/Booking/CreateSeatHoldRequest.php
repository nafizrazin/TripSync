<?php
namespace App\Http\Requests\Booking;
use Illuminate\Foundation\Http\FormRequest;
final class CreateSeatHoldRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array { return ['trip_id'=>['required','ulid','exists:trips,id'],'seat_ids'=>['required','array','min:1','max:6'],'seat_ids.*'=>['required','ulid','distinct','exists:trip_seats,id']]; }
}
