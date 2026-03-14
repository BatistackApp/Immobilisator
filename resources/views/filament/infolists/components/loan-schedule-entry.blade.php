<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    <div {{ $getExtraAttributeBag() }}>
        <div class="fi-in-loan-schedule-entry">
            <div class="space-y-6">
                @php
                    $loan = $getViewData()['loan'];
                    $schedule = $getViewData()['schedule'];
                @endphp

                    <!-- Panneau de Statistiques Clés -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl border border-primary-200 bg-primary-50 dark:border-primary-800 dark:bg-primary-900/20 shadow-sm">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400">Échéance Mensuelle</p>
                        <p class="text-2xl font-black text-primary-700 dark:text-primary-300">
                            {{ number_format($schedule[0]['payment'], 2, ',', ' ') }} €
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-danger-200 bg-danger-50 dark:border-danger-800 dark:bg-danger-900/20 shadow-sm">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-danger-600 dark:text-danger-400">Total Intérêts</p>
                        <p class="text-2xl font-black text-danger-700 dark:text-danger-300">
                            {{ number_format(collect($schedule)->sum('interest'), 2, ',', ' ') }} €
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50 shadow-sm">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Coût Total du Crédit</p>
                        <p class="text-2xl font-black text-gray-800 dark:text-white">
                            {{ number_format(collect($schedule)->sum('payment'), 2, ',', ' ') }} €
                        </p>
                    </div>
                </div>

                <!-- Tableau d'échéances -->
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="max-h-[400px] overflow-y-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-800">
                            <tr class="text-[10px] font-bold uppercase text-gray-500 dark:text-gray-400">
                                <th class="px-4 py-3 border-b dark:border-gray-700">Mois</th>
                                <th class="px-4 py-3 border-b dark:border-gray-700 text-right">Échéance</th>
                                <th class="px-4 py-3 border-b dark:border-gray-700 text-right">Intérêts</th>
                                <th class="px-4 py-3 border-b dark:border-gray-700 text-right">Capital</th>
                                <th class="px-4 py-3 border-b dark:border-gray-700 text-right">Solde</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($schedule as $item)
                                <tr class="hover:bg-primary-50/30 dark:hover:bg-primary-900/10 transition-colors">
                                    <td class="px-4 py-2">
                                    <span class="flex h-6 w-6 items-center justify-center rounded bg-gray-100 text-[10px] font-bold dark:bg-gray-800">
                                        {{ $item['period'] }}
                                    </span>
                                    </td>
                                    <td class="px-4 py-2 text-right font-bold text-gray-900 dark:text-white">
                                        {{ number_format($item['payment'], 2, ',', ' ') }} €
                                    </td>
                                    <td class="px-4 py-2 text-right text-danger-600">
                                        {{ number_format($item['interest'], 2, ',', ' ') }} €
                                    </td>
                                    <td class="px-4 py-2 text-right text-success-600">
                                        {{ number_format($item['capital'], 2, ',', ' ') }} €
                                    </td>
                                    <td class="px-4 py-2 text-right font-mono text-[11px] text-gray-500">
                                        {{ number_format($item['remaining_balance'], 2, ',', ' ') }} €
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
