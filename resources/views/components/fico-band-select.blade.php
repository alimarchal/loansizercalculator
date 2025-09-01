<div>
    <x-label for="fico_band" value="FICO Band" />
    <select name="filter[fico_band_id]" id="fico_band"
        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">-- Select FICO Band --</option>
        @if(isset($ficoBands))
        @foreach($ficoBands as $ficoBand)
        <option value="{{ $ficoBand->id }}" {{ request('filter.fico_band_id')==$ficoBand->id ? 'selected' : '' }}>
            {{ $ficoBand->fico_range }}
        </option>
        @endforeach
        @endif
    </select>
</div>