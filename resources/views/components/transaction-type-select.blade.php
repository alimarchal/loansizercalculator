<div>
    <x-label for="transaction_type" value="Transaction Type" />
    <select name="filter[transaction_type_id]" id="transaction_type"
        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">-- Select Transaction Type --</option>
        @if(isset($transactionTypes))
        @foreach($transactionTypes as $transactionType)
        <option value="{{ $transactionType->id }}" {{ request('filter.transaction_type_id')==$transactionType->id ?
            'selected' : '' }}>
            {{ $transactionType->name }}
        </option>
        @endforeach
        @endif
    </select>
</div>