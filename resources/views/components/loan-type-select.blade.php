<div>
    <x-label for="loan_type" value="Loan Type" />
    <select name="filter[loan_type_id]" id="loan_type"
        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">-- Select Loan Type --</option>
        @if(isset($loanTypes))
        @foreach($loanTypes as $loanType)
        <option value="{{ $loanType->id }}" {{ request('filter.loan_type_id')==$loanType->id ? 'selected' : '' }}>
            {{ $loanType->name }}
        </option>
        @endforeach
        @endif
    </select>
</div>