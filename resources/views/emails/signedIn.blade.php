<x-email>
    <x-slot name="title">
        {{__('messages.Login')}}
    </x-slot>
    <p>{{__("messages.You just logged in My-Trade.me with your account, Your IP is :")}}</p>
    <h3>{{$user->ip}}</h3>
    <p>{{__("messages.If you didn't do this, please reset your password as soon as possible")}}</p>
</x-email>