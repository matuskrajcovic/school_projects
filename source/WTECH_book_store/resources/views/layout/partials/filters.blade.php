<form method="GET">
	<ul class="attributes">
		<li class="form-group">
			<label for="min-price">Cena od:</label>
			<input type="number" class="form-control" name="price_min" step="0.01" placeholder="min" id="min-price" value="{{ $query['price_min'] ?? '' }}">
		</li>
		<li class="form-group">
			<label for="max-price">do:</label>
			<input type="number" class="form-control" name="price_max" step="0.01" placeholder="max" id="max-price" value="{{ $query['price_max'] ?? '' }}">
		</li>
		<li class="form-group">
			<label for="lang">Jazyk:</label>
            <select class="form-control" name="lang" id="lang">
				<option value="" {{ !isset($query['lang']) ? 'selected' : '' }}>Jazyk</option>
                @foreach ($languages as $lang)
                    <option value="{{ $lang->id }}" {{ isset($query['lang']) && $lang->id == $query['lang'] ? 'selected' : '' }}>{{ $lang->name }}</option>
                @endforeach
			</select>
		</li>
		<li class="form-group">
			<label for="year">Rok vydania:</label>
			<input type="number" class="form-control" name="year" placeholder="rok" id="year" value="{{ $query['year'] ?? '' }}">
		</li>
		<li class="form-group">
			<label for="per-page">Na stránku:</label>
			<input type="number" class="form-control" name="per_page" placeholder="počet" id="per-page" value="{{ $query['per_page'] ?? '' }}">
		</li>
		<li>
			<label for="sort">Zoradiť:</label>
			<select class="form-control" id="sort" name="sort">
				<option value="" selected>Zoradiť</option>
				<option value="1" {{ isset($query['sort']) && 1 == $query['sort'] ? 'selected' : '' }}>Najlacnejšie</option>
				<option value="2" {{ isset($query['sort']) && 2 == $query['sort'] ? 'selected' : '' }}>Najdrahšie</option>
			</select>
		</li>
		<li>
			<button type="submit" class="btn btn-secondary">Filtrovať</button>
		</li>
        <li>
			<input type="hidden" name="search" value="{{ $query['search'] ?? '' }}">
		</li>
        </li>
	</ul>
</form>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
