@extends('layouts.admin')
@section('title', 'Add Skill')

@section('content')
<div class="card" style="max-width:560px;">
  <form method="POST" action="{{ route('admin.skills.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
      <label>Category *</label>
      <select name="skill_category_id" class="form-control {{ $errors->has('skill_category_id') ? 'error' : '' }}" required>
        <option value="">— Select —</option>
        @foreach($categories as $cat)
          <option value="{{ $cat->id }}" {{ old('skill_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
      </select>
      @error('skill_category_id')<p class="error-msg">{{ $message }}</p>@enderror
    </div>

    <div class="form-group">
      <label>Skill Name *</label>
      <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'error' : '' }}" value="{{ old('name') }}" required />
      @error('name')<p class="error-msg">{{ $message }}</p>@enderror
    </div>

    <div class="form-group">
      <label>Icon Source</label>
      <div style="display:flex;gap:20px;margin-top:6px;">
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-weight:400;">
          <input type="radio" name="icon_source" value="url" id="src_url" {{ old('icon_source', 'none') === 'url' ? 'checked' : '' }} /> URL (CDN)
        </label>
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-weight:400;">
          <input type="radio" name="icon_source" value="upload" id="src_upload" {{ old('icon_source') === 'upload' ? 'checked' : '' }} /> Upload File
        </label>
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-weight:400;">
          <input type="radio" name="icon_source" value="none" id="src_none" {{ old('icon_source', 'none') === 'none' ? 'checked' : '' }} /> No Icon
        </label>
      </div>
      @error('icon_source')<p class="error-msg">{{ $message }}</p>@enderror
    </div>

    <div class="form-group" id="panel_url" style="display:none;">
      <label>Icon URL (CDN)</label>
      <input type="text" name="icon_url" class="form-control {{ $errors->has('icon_url') ? 'error' : '' }}"
             value="{{ old('icon_url') }}" placeholder="https://cdn.jsdelivr.net/..." />
      <p class="form-hint">Contoh: devicon, icons8, simpleicons.</p>
      @error('icon_url')<p class="error-msg">{{ $message }}</p>@enderror
    </div>

    <div class="form-group" id="panel_upload" style="display:none;">
      <label>Upload Icon</label>
      <input type="file" name="icon_upload" class="form-control {{ $errors->has('icon_upload') ? 'error' : '' }}" accept="image/*" />
      <p class="form-hint">PNG / JPEG / WEBP, max 10MB.</p>
      @error('icon_upload')<p class="error-msg">{{ $message }}</p>@enderror
    </div>

    <div class="form-group">
      <label>Order</label>
      <input type="number" name="order" class="form-control" value="{{ old('order', 0) }}" min="0" />
    </div>

    <div style="display:flex;gap:10px;">
      <button type="submit" class="btn-admin btn-primary">Create</button>
      <a href="{{ route('admin.skills.index') }}" class="btn-admin btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
  var radios = document.querySelectorAll('input[name="icon_source"]');
  var panels = { url: document.getElementById('panel_url'), upload: document.getElementById('panel_upload') };

  function sync(val) {
    panels.url.style.display    = val === 'url'    ? '' : 'none';
    panels.upload.style.display = val === 'upload' ? '' : 'none';
  }

  radios.forEach(function (r) {
    r.addEventListener('change', function () { sync(r.value); });
  });

  var checked = document.querySelector('input[name="icon_source"]:checked');
  sync(checked ? checked.value : 'none');
})();
</script>
@endpush
