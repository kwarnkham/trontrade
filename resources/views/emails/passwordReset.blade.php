<x-email>
    <x-slot name="title">
        {{__('messages.Password has been reset')}}
    </x-slot>
    <p>{{__("messages.Your My-Trade password has been reset, action IP:")}}</p>
    <h3>{{$ip}}</h3>
    <p>{{__("messages.If you didn't do this, please reset your password as soon as possible")}}</p>
</x-email>