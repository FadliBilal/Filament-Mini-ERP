<x-filament-panels::page>
    <div class="flex flex-col h-screen">
        {{-- Container utama chatbot --}}
        <div class="flex flex-col flex-grow bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">

            {{-- Area Tampilan Chat Scrollable --}}
            <div class="flex-1 p-6 space-y-4 overflow-y-auto">
                @foreach ($chatHistory as $chat)
                    @if ($chat['role'] === 'user')
                        <div class="flex items-start justify-end">
                            <div class="px-4 py-2 text-white bg-primary-600 rounded-lg max-w-lg">
                                {{ $chat['content'] }}
                            </div>
                        </div>
                    @else
                        <div class="flex items-start">
                            <div class="px-4 py-2 text-gray-700 bg-gray-200 dark:bg-gray-700 dark:text-gray-200 rounded-lg max-w-lg">
                                @if (isset($chat['loading']) && $chat['loading'])
                                    <div class="flex items-center space-x-2">
                                        <div class="w-2 h-2 bg-gray-500 rounded-full animate-pulse"></div>
                                        <div class="w-2 h-2 bg-gray-500 rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
                                        <div class="w-2 h-2 bg-gray-500 rounded-full animate-pulse" style="animation-delay: 0.4s;"></div>
                                    </div>
                                @else
                                    {!! \Illuminate\Support\Str::markdown($chat['content']) !!}
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Area Input Pesan --}}
            <div class="p-4 bg-gray-100 dark:bg-gray-900 border-t dark:border-gray-700">
                <form wire:submit.prevent="sendMessage" class="flex gap-2 items-center w-full">
                    <x-filament::input.wrapper class="w-full">
                        <x-filament::input
                            type="text"
                            wire:model="userInput"
                            placeholder="Ketik pertanyaan Anda di sini..."
                        />
                    </x-filament::input.wrapper>

                    <x-filament::button type="submit" icon="heroicon-m-paper-airplane" class="px-4 py-2">
                        Kirim
                    </x-filament::button>
                </form>
            </div>

        </div>
    </div>
</x-filament-panels::page>
