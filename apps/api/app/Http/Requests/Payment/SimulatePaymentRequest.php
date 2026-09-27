<?php
namespace App\Http\Requests\Payment;
use Illuminate\Foundation\Http\FormRequest;
final class SimulatePaymentRequest extends FormRequest { public function authorize(): bool { return true; } public function rules(): array { return ['outcome'=>['required','in:success,failed,cancelled']]; } }
