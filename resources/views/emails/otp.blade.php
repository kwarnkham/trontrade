<x-email>
    <x-slot name="title">
        {{__('messages.OTP sent')}}
    </x-slot>
    <p>{{__("messages.Your OTP is")}}</p>
    <h3>{{$otp}}</h3>
</x-email>