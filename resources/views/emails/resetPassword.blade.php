<x-email>
    <x-slot name="title">
        {{__("messages.Reset Password")}}
    </x-slot>
    <p>{{__("messages.You've requested to reset the password linked with your My-Trade account. Please note taht to confirm your request, please use the 6-digit code below:")}} {{__("messages.After updating your password,we'll disable withdrawls for 24 hours' in reset password email")}}</p>
    <h2>{{$otp}}</h2>
    <p>
        {{__('messages.The verification code will be valid for 30 minutes. Please do not share this code with anyone.')}}
    </p>
</x-email>