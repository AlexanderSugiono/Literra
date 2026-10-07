<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-navy leading-tight">{{ __('Promo & Kupon') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if ($coupons->isEmpty())
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <p class="text-gray-500 text-lg">Belum ada promo yang tersedia saat ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($coupons as $c)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 flex flex-col">
                            <div class="text-center mb-4">
                                <p class="text-2xl font-bold font-mono text-navy tracking-wider">{{ $c->code }}</p>
                                <button type="button" onclick="copyCode('{{ $c->code }}', this)" class="mt-1 text-xs text-indigo-500 hover:text-indigo-700 font-medium">Salin Kode</button>
                            </div>
                            <div class="text-center mb-3">
                                <span class="text-3xl font-bold text-gray-900">{{ $c->discount_type === 'percent' ? $c->value . '%' : 'IDR ' . number_format($c->value, 0, ',', '.') }}</span>
                                <p class="text-sm text-gray-500 mt-1">{{ $c->discount_type === 'percent' ? 'DISKON' : 'POTONGAN' }}</p>
                            </div>
                            <div class="border-t pt-3 mt-auto space-y-1 text-xs text-gray-500">
                                @if ($c->min_purchase && $c->min_purchase > 0)
                                    <p>Min. belanja: <strong>IDR {{ number_format($c->min_purchase, 0, ',', '.') }}</strong></p>
                                @endif
                                @if ($c->expired_at)
                                    <p>Berlaku hingga: <strong>{{ \Carbon\Carbon::parse($c->expired_at)->format('d M Y') }}</strong></p>
                                @else
                                    <p>Berlaku: <strong>Tanpa batas waktu</strong></p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

<script>
function copyCode(code, btn) {
    navigator.clipboard.writeText(code).then(function() {
        var orig = btn.textContent;
        btn.textContent = 'Tersalin!';
        setTimeout(function() { btn.textContent = orig; }, 2000);
    }).catch(function() {
        prompt('Salin manual kode berikut:', code);
    });
}
</script>
</x-app-layout>
