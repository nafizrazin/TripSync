<?php
namespace App\Http\Requests\Booking;
use Illuminate\Foundation\Http\FormRequest;
final class CreateBookingRequest extends FormRequest { public function authorize(): bool { return true; } public function rules(): array { return ['seat_hold_id'=>['required','ulid','exists:seat_holds,id'],'passengers'=>['required','array','min:1','max:6'],'passengers.*.trip_seat_id'=>['required','ulid','distinct','exists:trip_seats,id'],'passengers.*.full_name'=>['required','string','max:120'],'passengers.*.phone'=>['required','string','max:32'],'passengers.*.email'=>['nullable','email','max:190']]; } }
