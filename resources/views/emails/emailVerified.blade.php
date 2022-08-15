<x-email>
    <x-slot name="title">
        {{__('messages.Email has been verified')}}
    </x-slot>
    <p>
        {{__("messages.Your email has been verified successfully. You could login with this email and continue access the market website")}}
    </p>
    <p>{{__("messages.Official website url :")}} {{env('CLIENT_URL')}}</p>
</x-email>