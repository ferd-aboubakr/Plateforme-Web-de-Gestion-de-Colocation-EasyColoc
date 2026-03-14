<x-app-layout>


    <div>
        <form action="{{ route('invitations.store') }} }}" method="post">


            @csrf
            {{-- <label for="email"><input type="email" name="email">Email:</label> --}}

            <x-text-input name="email"></x-text-input>
            <x-primary-button>{{ __('Inviter') }}</x-primary-button>
        </form>

    </div>
</x-app-layout>
