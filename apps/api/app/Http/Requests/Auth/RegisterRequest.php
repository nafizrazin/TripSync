<?php
namespace App\Http\Requests\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
final class RegisterRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array { return ['name'=>['required','string','max:120'],'email'=>['required','email:rfc','max:190','unique:users,email'],'phone'=>['required','string','max:32','unique:users,phone'],'password'=>['required','confirmed',Password::min(10)->letters()->numbers()]]; }
}
