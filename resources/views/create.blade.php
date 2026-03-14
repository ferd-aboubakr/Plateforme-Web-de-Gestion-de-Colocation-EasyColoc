<x-app-layout>
    <form action="{{ route('colocation.create') }}" method="post">
        @csrf
        <label for="name"><input type="text" name="name">name</label>
        <button type="submit">creer </button>
    </form>
</x-app-layout>